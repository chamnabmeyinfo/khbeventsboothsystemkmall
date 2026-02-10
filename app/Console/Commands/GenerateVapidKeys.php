<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateVapidKeys extends Command
{
    protected $signature = 'webpush:vapid
                            {--show : Print keys to console (default)}
                            {--dotenv : Print as .env lines to copy}';

    protected $description = 'Generate VAPID key pair for Web Push notifications (P-256, base64url)';

    public function handle(): int
    {
        $tmpDir = sys_get_temp_dir().'/vapid_'.Str::random(8);
        $pemFile = $tmpDir.'/key.pem';

        if (! is_dir($tmpDir) && ! @mkdir($tmpDir, 0700, true)) {
            $this->error('Could not create temp directory.');

            return 1;
        }

        try {
            // Generate EC P-256 key pair
            $ok = @exec(
                'openssl ecparam -genkey -name prime256v1 -out '.escapeshellarg($pemFile).' 2>/dev/null',
                $out,
                $code
            );
            if ($code !== 0 || ! is_file($pemFile)) {
                $this->error('OpenSSL is required. Install openssl and ensure "openssl" is in your PATH.');
                $this->line('Alternatively, generate keys with:');
                $this->line('  npx web-push generate-vapid-keys');
                $this->line('  or use an online VAPID key generator.');

                return 1;
            }

            // Public key: uncompressed point (0x04 + x + y = 65 bytes) as base64url
            $pubDer = @shell_exec('openssl ec -in '.escapeshellarg($pemFile).' -pubout -outform DER 2>/dev/null');
            if ($pubDer === null || strlen($pubDer) < 65) {
                $this->error('Failed to export public key.');

                return 1;
            }
            $rawPublic = substr($pubDer, -65);
            if (strlen($rawPublic) !== 65 || $rawPublic[0] !== "\x04") {
                $this->error('Unexpected public key format.');

                return 1;
            }
            $publicKey = $this->base64UrlEncode($rawPublic);

            // Private key: extract 32-byte scalar from SEC1 DER
            $privDer = trim((string) @shell_exec('openssl ec -in '.escapeshellarg($pemFile).' -outform DER 2>/dev/null'));
            if ($privDer === '' || strlen($privDer) < 36) {
                $this->error('Failed to export private key.');

                return 1;
            }
            // SEC1 private key DER: sequence, then octet string with 32 bytes (often at offset 7 or so)
            $privRaw = $this->extractEcPrivateKeyBytes($privDer);
            if ($privRaw === null || strlen($privRaw) !== 32) {
                $this->error('Could not extract 32-byte private key.');

                return 1;
            }
            $privateKey = $this->base64UrlEncode($privRaw);

            $this->newLine();
            $this->info('VAPID keys generated. Add these to your .env (never commit the private key):');
            $this->newLine();

            if ($this->option('dotenv')) {
                $this->line('PUSH_NOTIFICATIONS_ENABLED=true');
                $this->line('PUSH_VAPID_PUBLIC_KEY='.$publicKey);
                $this->line('PUSH_VAPID_PRIVATE_KEY='.$privateKey);
            } else {
                $this->line('Public key:  '.$publicKey);
                $this->line('Private key: '.$privateKey);
                $this->newLine();
                $this->line('Example .env:');
                $this->line('  PUSH_VAPID_PUBLIC_KEY='.$publicKey);
                $this->line('  PUSH_VAPID_PRIVATE_KEY='.$privateKey);
            }

            $this->newLine();
            $this->comment('Store the public key in .env or in Settings > Push Notifications. Keep the private key only in .env.');

            return 0;
        } finally {
            if (is_file($pemFile)) {
                @unlink($pemFile);
            }
            if (is_dir($tmpDir)) {
                @rmdir($tmpDir);
            }
        }
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Extract 32-byte private key from EC DER (SEC1 or PKCS#8).
     */
    private function extractEcPrivateKeyBytes(string $der): ?string
    {
        $len = strlen($der);
        // SEC1: 0x30 [total] 0x02 0x01 0x01 0x04 0x20 [32 bytes]
        if ($len >= 39 && $der[0] === "\x30" && $der[2] === "\x02" && $der[5] === "\x04" && $der[6] === "\x20") {
            return substr($der, 7, 32);
        }
        // Some exports have longer header; find 0x04 0x20 (OCTET STRING 32)
        for ($i = 0; $i < $len - 35; $i++) {
            if ($der[$i] === "\x04" && $der[$i + 1] === "\x20") {
                return substr($der, $i + 2, 32);
            }
        }

        return null;
    }
}
