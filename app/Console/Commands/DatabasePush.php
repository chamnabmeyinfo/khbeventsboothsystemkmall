<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabasePush extends Command
{
    protected $signature = 'db:push
                            {--force : Skip confirmation}
                            {--ssh : Use SSH (mysql on remote server)}';

    protected $description = 'Push local database to remote (cPanel)';

    public function handle()
    {
        $remoteHost = env('REMOTE_DB_HOST');
        $sshHost = env('REMOTE_SSH_HOST');
        $sshUser = env('REMOTE_SSH_USER');

        if (! $remoteHost && ! $sshHost) {
            $this->error('Remote not configured. Set REMOTE_DB_HOST (direct) or REMOTE_SSH_HOST + REMOTE_SSH_USER (SSH) in .env');
            $this->comment('See docs/XAMPP-SETUP.md section "Direct push/pull".');

            return 1;
        }

        $useSsh = $this->option('ssh') || $sshHost;

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->error('Local database connection failed: '.$e->getMessage());

            return 1;
        }

        $localDb = config('database.connections.mysql.database');
        $remoteDb = env('REMOTE_DB_DATABASE');
        if (! $remoteDb) {
            $this->error('Set REMOTE_DB_DATABASE in .env');

            return 1;
        }

        $this->warn("This will overwrite remote database \"{$remoteDb}\" with local \"{$localDb}\".");
        if (! $this->option('force') && ! $this->confirm('Continue?', false)) {
            return 0;
        }

        $mysqlPath = $this->mysqlBinPath();
        $mysqldumpPath = $this->mysqldumpBinPath();
        if (! $mysqlPath || ! $mysqldumpPath) {
            $this->error('mysql/mysqldump not found. Add XAMPP bin to PATH or set DB_MYSQL_PATH in .env (e.g. /opt/lampp/bin).');

            return 1;
        }

        $local = [
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port') ?? 3306,
            'database' => $localDb,
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];

        $remote = [
            'host' => $remoteHost ?: '127.0.0.1',
            'port' => env('REMOTE_DB_PORT', '3306'),
            'database' => $remoteDb,
            'username' => env('REMOTE_DB_USERNAME'),
            'password' => env('REMOTE_DB_PASSWORD', ''),
        ];

        if ($useSsh && $sshHost && $sshUser) {
            return $this->pushViaSsh($mysqldumpPath, $mysqlPath, $local, $remote);
        }

        return $this->pushDirect($mysqldumpPath, $mysqlPath, $local, $remote);
    }

    protected function pushDirect($mysqldumpPath, $mysqlPath, array $local, array $remote): int
    {
        $this->info('Pushing to remote MySQL (direct)...');

        $dumpCmd = $this->mysqldumpCmd($mysqldumpPath, $local);
        $importCmd = $this->mysqlImportCmd($mysqlPath, $remote);

        $fullCmd = $dumpCmd.' 2>/dev/null | '.$importCmd.' 2>&1';
        $output = [];
        $returnVar = 0;
        exec($fullCmd, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Push failed.');
            $this->error(implode("\n", $output));
            $this->comment('Ensure Remote MySQL is enabled in cPanel and your IP is allowed.');

            return 1;
        }

        $this->info('Push completed. Remote database updated.');

        return 0;
    }

    protected function pushViaSsh($mysqldumpPath, $mysqlPath, array $local, array $remote): int
    {
        $this->info('Pushing via SSH (mysql on server)...');

        $sshTarget = env('REMOTE_SSH_USER').'@'.env('REMOTE_SSH_HOST');
        $remoteMysql = env('REMOTE_MYSQL_PATH', 'mysql');
        $importPart = $this->mysqlImportCmdForRemote($remoteMysql, $remote);
        $dumpCmd = $this->mysqldumpCmd($mysqldumpPath, $local).' 2>/dev/null';
        $fullCmd = $dumpCmd.' | ssh '.escapeshellarg($sshTarget).' '.escapeshellarg($importPart).' 2>&1';
        $output = [];
        $returnVar = 0;
        exec($fullCmd, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Push failed.');
            $this->error(implode("\n", $output));
            $this->comment('Ensure SSH access and that mysql exists on the server.');

            return 1;
        }

        $this->info('Push completed. Remote database updated.');

        return 0;
    }

    protected function mysqldumpCmd(string $bin, array $c): string
    {
        $pass = $c['password'] !== '' ? ' -p'.escapeshellarg($c['password']) : '';

        return sprintf(
            '%s -h %s -P %s -u %s%s --single-transaction --routines --triggers --default-character-set=utf8mb4 %s',
            escapeshellarg($bin),
            escapeshellarg($c['host']),
            $c['port'],
            escapeshellarg($c['username']),
            $pass,
            escapeshellarg($c['database'])
        );
    }

    protected function mysqlImportCmd(string $bin, array $c): string
    {
        $pass = $c['password'] !== '' ? ' -p'.escapeshellarg($c['password']) : '';

        return sprintf(
            '%s -h %s -P %s -u %s%s --default-character-set=utf8mb4 %s',
            escapeshellarg($bin),
            escapeshellarg($c['host']),
            $c['port'],
            escapeshellarg($c['username']),
            $pass,
            escapeshellarg($c['database'])
        );
    }

    protected function mysqlImportCmdForRemote(string $bin, array $c): string
    {
        $pass = $c['password'] !== '' ? ' -p'.escapeshellarg($c['password']) : '';

        return sprintf(
            '%s -h 127.0.0.1 -P %s -u %s%s --default-character-set=utf8mb4 %s',
            $bin,
            $c['port'],
            escapeshellarg($c['username']),
            $pass,
            escapeshellarg($c['database'])
        );
    }

    protected function mysqlBinPath(): ?string
    {
        $base = env('DB_MYSQL_PATH', '/opt/lampp/bin');
        $paths = [$base.'/mysql', 'mysql'];
        foreach ($paths as $p) {
            if ($p === 'mysql') {
                $out = [];
                exec('which mysql 2>/dev/null', $out);
                if (! empty($out[0]) && is_executable($out[0])) {
                    return $out[0];
                }

                continue;
            }
            if (file_exists($p) && is_executable($p)) {
                return $p;
            }
        }

        return null;
    }

    protected function mysqldumpBinPath(): ?string
    {
        $base = env('DB_MYSQL_PATH', '/opt/lampp/bin');
        $paths = [$base.'/mysqldump', 'mysqldump'];
        foreach ($paths as $p) {
            if ($p === 'mysqldump') {
                $out = [];
                exec('which mysqldump 2>/dev/null', $out);
                if (! empty($out[0]) && is_executable($out[0])) {
                    return $out[0];
                }

                continue;
            }
            if (file_exists($p) && is_executable($p)) {
                return $p;
            }
        }

        return null;
    }
}
