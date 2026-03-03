<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

class DatabasePull extends Command
{
    protected $signature = 'db:pull
                            {--force : Skip confirmation}
                            {--ssh : Use SSH tunnel (mysqldump on remote server)}
                            {--test : Only test remote connection, do not pull}';

    protected $description = 'Pull database from remote (cPanel) into local XAMPP';

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

        $mysqlPath = $this->mysqlBinPath();
        $mysqldumpPath = $this->mysqldumpBinPath();
        if (! $mysqlPath || ! $mysqldumpPath) {
            $this->error('mysql/mysqldump not found. Add XAMPP bin to PATH or set DB_MYSQL_PATH in .env (e.g. /opt/lampp/bin).');

            return 1;
        }

        $remote = [
            'host' => $remoteHost ?: '127.0.0.1',
            'port' => env('REMOTE_DB_PORT', '3306'),
            'database' => $remoteDb,
            'username' => env('REMOTE_DB_USERNAME'),
            'password' => env('REMOTE_DB_PASSWORD', ''),
        ];

        if ($this->option('test')) {
            return $this->testRemoteConnection($mysqldumpPath, $remote, $useSsh);
        }

        $this->line('');
        $this->info('What will happen:');
        $this->line("  • Pulls a <fg=cyan>full dump</> of remote database \"<fg=cyan>{$remoteDb}</>\" (all tables, all rows, triggers, routines)");
        $this->line("  • <fg=yellow>Replaces</> your local database \"<fg=cyan>{$localDb}</>\" completely");
        $this->line("  • One database only — not all databases on the server");
        $this->line('');
        $this->warn("This will overwrite local \"{$localDb}\" with remote \"{$remoteDb}\".");
        if (! $this->option('force') && ! $this->confirm('Continue?', false)) {
            return 0;
        }

        $local = [
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port') ?? 3306,
            'database' => $localDb,
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];

        if ($useSsh && $sshHost && $sshUser) {
            return $this->pullViaSsh($mysqldumpPath, $mysqlPath, $remote, $local);
        }

        return $this->pullDirect($mysqldumpPath, $mysqlPath, $remote, $local);
    }

    protected function testRemoteConnection(string $mysqldumpPath, array $remote, bool $useSsh): int
    {
        $this->info('Testing remote database connection...');

        $noData = ' --no-data ';
        if ($useSsh && env('REMOTE_SSH_HOST') && env('REMOTE_SSH_USER')) {
            $sshTarget = env('REMOTE_SSH_USER').'@'.env('REMOTE_SSH_HOST');
            $remoteMysqldump = env('REMOTE_MYSQLDUMP_PATH', 'mysqldump');
            $dumpPart = $this->mysqldumpCmdForRemote($remoteMysqldump, $remote);
            $dumpPart = str_replace(' --single-transaction ', ' --single-transaction '.$noData, $dumpPart);
            $cmd = 'ssh '.escapeshellarg($sshTarget).' '.escapeshellarg($dumpPart).' 2>&1';
        } else {
            $cmd = str_replace(' --single-transaction ', ' --single-transaction '.$noData, $this->mysqldumpCmd($mysqldumpPath, $remote)).' 2>&1';
        }

        $process = Process::fromShellCommandline($cmd);
        $process->setTimeout(90);
        $process->run();
        $output = trim($process->getOutput().$process->getErrorOutput());

        if (! $process->isSuccessful()) {
            $this->error('Connection failed.');
            $this->line($output ?: '(no output)');
            $this->comment('');
            $this->comment('For direct MySQL: Enable Remote MySQL in cPanel and add your IP.');
            $this->comment('For SSH: Ensure REMOTE_SSH_HOST and REMOTE_SSH_USER are correct and SSH keys work.');

            return 1;
        }

        $this->info('Connection OK. Remote database is reachable.');

        return 0;
    }

    protected function pullDirect($mysqldumpPath, $mysqlPath, array $remote, array $local): int
    {
        $this->info('Pulling from remote MySQL (direct)...');
        $this->comment('This may take 30 seconds to several minutes for large databases.');

        $dumpFile = sys_get_temp_dir().'/db_pull_'.md5($remote['database'].microtime()).'.sql';

        // Step 1: Dump remote to temp file (so we can see connection errors)
        $dumpCmd = $this->mysqldumpCmd($mysqldumpPath, $remote).' > '.escapeshellarg($dumpFile).' 2>&1';
        $dumpProcess = Process::fromShellCommandline($dumpCmd);
        $dumpProcess->setTimeout(600);
        $dumpProcess->run();

        if (! $dumpProcess->isSuccessful()) {
            $err = trim($dumpProcess->getOutput().$dumpProcess->getErrorOutput());
            $this->error('Dump failed. Remote connection may be blocked.');
            $this->error($err ?: '(no output)');
            $this->comment('');
            $this->comment('Enable Remote MySQL in cPanel and add your IP. Or use SSH: set REMOTE_SSH_HOST + REMOTE_SSH_USER and run with --ssh');
            if (file_exists($dumpFile)) {
                @unlink($dumpFile);
            }

            return 1;
        }

        if (! file_exists($dumpFile) || filesize($dumpFile) < 100) {
            $this->error('Dump produced no data. Check remote database name and credentials.');
            if (file_exists($dumpFile)) {
                @unlink($dumpFile);
            }

            return 1;
        }

        // Step 2: Import into local
        $this->info('Importing into local database...');
        $importCmd = escapeshellarg($mysqlPath).' -h '.escapeshellarg($local['host']).' -P '.$local['port'].' -u '.escapeshellarg($local['username']);
        if ($local['password'] !== '') {
            $importCmd .= ' -p'.escapeshellarg($local['password']);
        }
        $importCmd .= ' --default-character-set=utf8mb4 '.escapeshellarg($local['database']).' < '.escapeshellarg($dumpFile).' 2>&1';
        $importProcess = Process::fromShellCommandline($importCmd);
        $importProcess->setTimeout(600);
        $importProcess->run();

        @unlink($dumpFile);

        if (! $importProcess->isSuccessful()) {
            $this->error('Import failed.');
            $this->error(trim($importProcess->getOutput().$importProcess->getErrorOutput()));

            return 1;
        }

        $this->info('Pull completed. Local database updated.');
        $this->showTableSummary($local['database']);
        $this->showBoothSummary();

        return 0;
    }

    protected function pullViaSsh($mysqldumpPath, $mysqlPath, array $remote, array $local): int
    {
        $this->info('Pulling via SSH (mysqldump on server)...');
        $this->comment('This may take 30 seconds to several minutes for large databases.');

        $dumpFile = sys_get_temp_dir().'/db_pull_'.md5($remote['database'].microtime()).'.sql';

        // Step 1: Dump remote via SSH to temp file
        $sshTarget = env('REMOTE_SSH_USER').'@'.env('REMOTE_SSH_HOST');
        $remoteMysqldump = env('REMOTE_MYSQLDUMP_PATH', 'mysqldump');
        $dumpPart = $this->mysqldumpCmdForRemote($remoteMysqldump, $remote);
        $sshCmd = 'ssh '.escapeshellarg($sshTarget).' '.escapeshellarg($dumpPart).' > '.escapeshellarg($dumpFile).' 2>&1';
        $dumpProcess = Process::fromShellCommandline($sshCmd);
        $dumpProcess->setTimeout(600);
        $dumpProcess->run();

        if (! $dumpProcess->isSuccessful()) {
            $err = trim($dumpProcess->getOutput().$dumpProcess->getErrorOutput());
            $this->error('Dump failed.');
            $this->error($err ?: '(no output)');
            $this->comment('Ensure SSH access and that mysqldump exists on the server.');
            if (file_exists($dumpFile)) {
                @unlink($dumpFile);
            }

            return 1;
        }

        if (! file_exists($dumpFile) || filesize($dumpFile) < 100) {
            $this->error('Dump produced no data.');
            if (file_exists($dumpFile)) {
                @unlink($dumpFile);
            }

            return 1;
        }

        // Step 2: Import into local
        $this->info('Importing into local database...');
        $importCmd = escapeshellarg($mysqlPath).' -h '.escapeshellarg($local['host']).' -P '.$local['port'].' -u '.escapeshellarg($local['username']);
        if ($local['password'] !== '') {
            $importCmd .= ' -p'.escapeshellarg($local['password']);
        }
        $importCmd .= ' --default-character-set=utf8mb4 '.escapeshellarg($local['database']).' < '.escapeshellarg($dumpFile).' 2>&1';
        $importProcess = Process::fromShellCommandline($importCmd);
        $importProcess->setTimeout(600);
        $importProcess->run();

        @unlink($dumpFile);

        if (! $importProcess->isSuccessful()) {
            $this->error('Import failed.');
            $this->error(trim($importProcess->getOutput().$importProcess->getErrorOutput()));

            return 1;
        }

        $this->info('Pull completed. Local database updated.');
        $this->showTableSummary($local['database']);
        $this->showBoothSummary();

        return 0;
    }

    protected function showTableSummary(string $database): void
    {
        try {
            $tables = DB::select('SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = ?', [$database]);
            $count = $tables[0]->cnt ?? 0;
            $this->line("  → {$count} tables in local \"{$database}\".");
        } catch (\Exception $e) {
            // Ignore — summary is optional
        }
    }

    protected function showBoothSummary(): void
    {
        try {
            if (! Schema::hasTable('floor_plans') || ! Schema::hasTable('booth')) {
                return;
            }
            $plans = DB::select("
                SELECT fp.id, fp.name, fp.project_name, COUNT(b.id) as booth_count
                FROM floor_plans fp
                LEFT JOIN booth b ON b.floor_plan_id = fp.id
                GROUP BY fp.id, fp.name, fp.project_name
                ORDER BY fp.id
            ");
            if (! empty($plans)) {
                $this->line('');
                $this->info('Floor plans & booths:');
                foreach ($plans as $p) {
                    $name = $p->project_name ?: $p->name;
                    $this->line("  • {$name}: {$p->booth_count} booths");
                }
            }
        } catch (\Exception $e) {
            // Ignore
        }
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

    protected function mysqldumpCmdForRemote(string $bin, array $c): string
    {
        $pass = $c['password'] !== '' ? ' -p'.escapeshellarg($c['password']) : '';

        return sprintf(
            '%s -h 127.0.0.1 -P %s -u %s%s --single-transaction --routines --triggers --default-character-set=utf8mb4 %s',
            $bin,
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
