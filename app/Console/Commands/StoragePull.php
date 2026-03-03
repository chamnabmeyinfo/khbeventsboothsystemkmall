<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StoragePull extends Command
{
    protected $signature = 'storage:pull
                            {--force : Skip confirmation}
                            {--ftp : Use FTP (default)}
                            {--rsync : Use rsync over SSH (requires REMOTE_SSH_*)}';

    protected $description = 'Pull storage/app/public and public/images from cPanel to local';

    public function handle()
    {
        $useRsync = $this->option('rsync') || (env('REMOTE_SSH_HOST') && env('REMOTE_SSH_USER'));
        $ftpHost = env('REMOTE_FTP_HOST') ?: env('REMOTE_DB_HOST');
        $ftpUser = env('REMOTE_FTP_USER');
        $ftpPass = env('REMOTE_FTP_PASSWORD');
        $ftpPath = env('REMOTE_FTP_PATH', '');

        if ($useRsync && env('REMOTE_SSH_HOST') && env('REMOTE_SSH_USER')) {
            return $this->pullViaRsync();
        }

        if (! $ftpHost || ! $ftpUser) {
            $this->error('Remote storage not configured.');
            $this->line('');
            $this->comment('Add to .env:');
            $this->comment('  REMOTE_FTP_HOST=system.khbevents.com');
            $this->comment('  REMOTE_FTP_USER=your_cpanel_username');
            $this->comment('  REMOTE_FTP_PASSWORD=your_password');
            $this->comment('  REMOTE_FTP_PATH=/home/username/system.khbevents.com  (project root on server)');
            $this->line('');
            $this->comment('Or use rsync (if SSH available): set REMOTE_SSH_HOST + REMOTE_SSH_USER, then run with --rsync');
            $this->comment('');
            $this->comment('Manual option: cPanel File Manager → storage/app/public → Compress → Download');

            return 1;
        }

        if (! $this->option('force') && ! $this->confirm('Pull storage from '.$ftpHost.' into local?', true)) {
            return 0;
        }

        return $this->pullViaFtp($ftpHost, $ftpUser, $ftpPass ?: '', $ftpPath);
    }

    protected function pullViaRsync(): int
    {
        $sshUser = env('REMOTE_SSH_USER');
        $sshHost = env('REMOTE_SSH_HOST');
        $remotePath = env('REMOTE_RSYNC_PATH', '~/system.khbevents.com');

        $this->info('Pulling storage via rsync...');

        $localStorage = storage_path('app/public');
        $localImages = public_path('images');

        if (! is_dir($localStorage)) {
            File::makeDirectory($localStorage, 0755, true);
        }
        if (! is_dir($localImages)) {
            File::makeDirectory($localImages, 0755, true);
        }

        $target = $sshUser.'@'.$sshHost.':'.rtrim($remotePath, '/').'/';

        $cmd1 = sprintf(
            'rsync -avz --progress %sstorage/app/public/ %s',
            escapeshellarg($target),
            escapeshellarg($localStorage.'/')
        );
        $cmd2 = sprintf(
            'rsync -avz --progress %spublic/images/ %s 2>/dev/null || true',
            escapeshellarg($target),
            escapeshellarg($localImages.'/')
        );

        $process = \Symfony\Component\Process\Process::fromShellCommandline($cmd1);
        $process->setTimeout(300);
        $process->run(fn ($t, $b) => $this->output->write($b));

        if (! $process->isSuccessful()) {
            $this->error('rsync failed: '.$process->getErrorOutput());

            return 1;
        }

        $process2 = \Symfony\Component\Process\Process::fromShellCommandline($cmd2);
        $process2->setTimeout(120);
        $process2->run();

        $this->info('Storage pull completed.');

        return 0;
    }

    protected function pullViaFtp(string $host, string $user, string $pass, string $remotePath): int
    {
        if (! extension_loaded('ftp')) {
            $this->error('PHP FTP extension not loaded. Install it or use manual download.');
            $this->comment('Ubuntu: sudo apt install php-ftp');
            $this->comment('Manual: cPanel File Manager → storage/app/public → Compress → Download');

            return 1;
        }

        $this->info('Connecting via FTP...');

        $conn = @ftp_connect($host, env('REMOTE_FTP_PORT', 21), 30);
        if (! $conn) {
            $this->error('FTP connection failed to '.$host);

            return 1;
        }

        if (! @ftp_login($conn, $user, $pass)) {
            $this->error('FTP login failed. Check REMOTE_FTP_USER and REMOTE_FTP_PASSWORD.');
            ftp_close($conn);

            return 1;
        }

        ftp_pasv($conn, true);

        $base = rtrim($remotePath, '/');
        $remoteStorage = $base.'/storage/app/public';
        $localStorage = storage_path('app/public');

        if (! is_dir($localStorage)) {
            File::makeDirectory($localStorage, 0755, true);
        }

        $this->info('Pulling storage/app/public...');
        $count = $this->ftpDownloadRecursive($conn, $remoteStorage, $localStorage, $remoteStorage);

        ftp_close($conn);

        $this->info("Storage pull completed. {$count} files downloaded.");
        $this->comment('Run: php artisan storage:link (if not already done)');

        return 0;
    }

    protected function ftpDownloadRecursive($conn, string $remoteDir, string $localDir, string $baseRemote): int
    {
        $count = 0;
        $items = @ftp_nlist($conn, $remoteDir);
        if (! is_array($items)) {
            return 0;
        }

        foreach ($items as $item) {
            $remotePath = $remoteDir.'/'.basename($item);
            $localPath = $localDir.'/'.basename($item);

            if ($this->ftpIsDir($conn, $remotePath)) {
                if (! is_dir($localPath)) {
                    File::makeDirectory($localPath, 0755, true);
                }
                $count += $this->ftpDownloadRecursive($conn, $remotePath, $localPath, $baseRemote);
            } else {
                if (@ftp_get($conn, $localPath, $remotePath, FTP_BINARY)) {
                    $count++;
                    $this->output->write('.');
                }
            }
        }

        return $count;
    }

    protected function ftpIsDir($conn, string $path): bool
    {
        $cur = ftp_pwd($conn);
        $chg = @ftp_chdir($conn, $path);
        if ($chg) {
            ftp_chdir($conn, $cur);
        }

        return $chg;
    }
}
