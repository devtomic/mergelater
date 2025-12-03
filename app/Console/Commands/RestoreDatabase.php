<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;

class RestoreDatabase extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore a PostgreSQL database from a backup file';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore
                            {file : Path to the backup file to restore}
                            {--force : Force execution even in production-like environments}
                            {--no-drop : Keep existing database (merge with backup data)}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database restoration...');

        // Perform environment safety checks
        if (! $this->performSafetyChecks()) {
            return Command::FAILURE;
        }

        $backupFile = $this->argument('file');

        // Validate backup file exists
        if (! file_exists($backupFile)) {
            $this->error("Backup file not found: {$backupFile}");

            return Command::FAILURE;
        }

        // Get database configuration
        $connection = Config::get('database.connections.pgsql');
        $host = $connection['host'];
        $port = $connection['port'];
        $database = $connection['database'];
        $username = $connection['username'];
        $password = $connection['password'];

        // Validate connection settings
        if (empty($host) || empty($database) || empty($username)) {
            $this->error('Database configuration is incomplete.');

            return Command::FAILURE;
        }

        $this->info("Restoring to database: {$database}@{$host}:{$port}");

        // Determine backup format
        $format = $this->detectBackupFormat($backupFile);
        $this->info("Detected backup format: {$format}");

        // Drop and recreate database by default (unless --no-drop is specified)
        $shouldDrop = ! $this->option('no-drop');

        if ($shouldDrop) {
            $this->warn('⚠️  Database will be completely replaced with backup data');
            $this->info('Use --no-drop to merge with existing data instead');

            if (! $this->dropAndRecreateDatabase($host, $port, $username, $password, $database)) {
                return Command::FAILURE;
            }
        } else {
            $this->warn('⚠️  Merging backup data with existing database (may cause conflicts)');
        }

        // Restore the backup
        try {
            $success = $this->restoreBackup($backupFile, $format, $host, $port, $username, $password, $database);

            if ($success) {
                $this->info('✓ Database restoration completed successfully!');
                $this->info("Restored from: {$backupFile}");

                return Command::SUCCESS;
            } else {
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Exception during restoration: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Detect backup file format
     */
    private function detectBackupFormat(string $file): string
    {
        if (str_ends_with($file, '.pgsql')) {
            return 'custom';
        } elseif (str_ends_with($file, '.sql.gz')) {
            return 'plain_compressed';
        } elseif (str_ends_with($file, '.sql')) {
            return 'plain';
        } elseif (str_ends_with($file, '.tar')) {
            return 'tar';
        }

        // Try to detect by file content
        $handle = fopen($file, 'rb');
        $header = fread($handle, 100);
        fclose($handle);

        if (str_contains($header, 'PGDMP')) {
            return 'custom';
        } elseif (str_contains($header, '\x1f\x8b')) { // gzip magic number
            return 'plain_compressed';
        }

        return 'plain';
    }

    /**
     * Drop and recreate database
     */
    private function dropAndRecreateDatabase(string $host, int $port, string $username, string $password, string $database): bool
    {
        $this->info('Dropping and recreating database...');

        $env = ['PGPASSWORD' => $password];

        // Terminate existing connections
        // Note: We connect to a different database to terminate connections and drop the target database
        // If dropping 'mergelater', connect to 'mergelater_testing' (or vice versa) since both are available in PgBouncer
        $adminDb = ($database === 'mergelater') ? 'mergelater_testing' : 'mergelater';

        $terminateQuery = "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '{$database}' AND pid <> pg_backend_pid();";
        $terminateResult = Process::env($env)->run([
            'psql', '-h', $host, '-p', (string) $port, '-U', $username, '-d', $adminDb, '-c', $terminateQuery,
        ]);

        // Drop database
        $dropResult = Process::env($env)->run([
            'psql', '-h', $host, '-p', (string) $port, '-U', $username, '-d', $adminDb, '-c', "DROP DATABASE IF EXISTS {$database};",
        ]);

        if (! $dropResult->successful()) {
            $errorOutput = $dropResult->errorOutput();
            $this->error('Failed to drop database: '.$errorOutput);

            // Check if it's a missing psql issue and provide installation help
            if (str_contains($errorOutput, 'psql') && (str_contains($errorOutput, 'not found') || str_contains($errorOutput, 'No such file') || str_contains($errorOutput, 'command not found'))) {
                $this->showPostgreSQLInstallInstructions('psql');
            }

            return false;
        }

        // Create database
        $createResult = Process::env($env)->run([
            'psql', '-h', $host, '-p', (string) $port, '-U', $username, '-d', $adminDb, '-c', "CREATE DATABASE {$database};",
        ]);

        if (! $createResult->successful()) {
            $errorOutput = $createResult->errorOutput();
            $this->error('Failed to create database: '.$errorOutput);

            // Check if it's a missing psql issue and provide installation help
            if (str_contains($errorOutput, 'psql') && (str_contains($errorOutput, 'not found') || str_contains($errorOutput, 'No such file') || str_contains($errorOutput, 'command not found'))) {
                $this->showPostgreSQLInstallInstructions('psql');
            }

            return false;
        }

        return true;
    }

    /**
     * Perform environment safety checks
     */
    private function performSafetyChecks(): bool
    {
        // Check if we're in production environment
        $environment = app()->environment();
        $hostname = gethostname();
        $isProduction = $environment === 'production';

        if ($isProduction) {
            if (! $this->option('force')) {
                $this->error('❌ SAFETY CHECK FAILED');
                $this->error('This command cannot be run in production environments!');
                $this->error("Current environment: {$environment}");
                $this->error("Current hostname: {$hostname}");
                $this->error('Use --force to override (NOT recommended)');

                return false;
            }

            // Even with --force, require confirmation
            $this->warn('⚠️  WARNING: You are forcing execution in a production-like environment!');
            $this->warn("Environment: {$environment}");
            $this->warn("Hostname: {$hostname}");

            if (! $this->confirm('Are you ABSOLUTELY SURE you want to restore the database? This will overwrite existing data!')) {
                $this->info('Database restoration cancelled.');

                return false;
            }
        }

        return true;
    }

    /**
     * Restore backup based on format
     */
    private function restoreBackup(string $file, string $format, string $host, int $port, string $username, string $password, string $database): bool
    {
        $env = ['PGPASSWORD' => $password];

        $this->info("Restoring backup using format: {$format}");

        switch ($format) {
            case 'custom':
                return $this->restoreCustomFormat($file, $env, $host, $port, $username, $database);

            case 'plain':
                return $this->restorePlainFormat($file, $env, $host, $port, $username, $database);

            case 'plain_compressed':
                return $this->restorePlainCompressedFormat($file, $env, $host, $port, $username, $database);

            case 'tar':
                return $this->restoreTarFormat($file, $env, $host, $port, $username, $database);

            default:
                $this->error("Unsupported backup format: {$format}");

                return false;
        }
    }

    /**
     * Restore custom format backup
     */
    private function restoreCustomFormat(string $file, array $env, string $host, int $port, string $username, string $database): bool
    {
        $result = Process::env($env)
            ->timeout(1800) // 30 minutes timeout
            ->run([
                'pg_restore',
                '-h', $host,
                '-p', (string) $port,
                '-U', $username,
                '-d', $database,
                '--no-owner',
                '--no-privileges',
                '--jobs=4',
                '--verbose',
                $file,
            ]);

        if (! $result->successful()) {
            $errorOutput = $result->errorOutput();
            $this->error('pg_restore failed: '.$errorOutput);

            // Check if it's a missing pg_restore issue and provide installation help
            if (str_contains($errorOutput, 'pg_restore') && (str_contains($errorOutput, 'not found') || str_contains($errorOutput, 'No such file') || str_contains($errorOutput, 'command not found'))) {
                $this->showPostgreSQLInstallInstructions('pg_restore');
            }

            return false;
        }

        return true;
    }

    /**
     * Restore compressed plain SQL format backup
     */
    private function restorePlainCompressedFormat(string $file, array $env, string $host, int $port, string $username, string $database): bool
    {
        // Decompress and pipe to psql
        $result = Process::env($env)
            ->timeout(1800)
            ->run("gunzip -c '{$file}' | psql -h '{$host}' -p '{$port}' -U '{$username}' -d '{$database}' --quiet");

        if (! $result->successful()) {
            $errorOutput = $result->errorOutput();
            $this->error('Compressed restore failed: '.$errorOutput);

            // Check if it's a missing gunzip or psql issue and provide installation help
            if ((str_contains($errorOutput, 'psql') || str_contains($errorOutput, 'gunzip')) && (str_contains($errorOutput, 'not found') || str_contains($errorOutput, 'No such file') || str_contains($errorOutput, 'command not found'))) {
                $this->showPostgreSQLInstallInstructions('psql/gunzip');
            }

            return false;
        }

        return true;
    }

    /**
     * Restore plain SQL format backup
     */
    private function restorePlainFormat(string $file, array $env, string $host, int $port, string $username, string $database): bool
    {
        $result = Process::env($env)
            ->timeout(1800)
            ->input(file_get_contents($file))
            ->run([
                'psql',
                '-h', $host,
                '-p', (string) $port,
                '-U', $username,
                '-d', $database,
                '--quiet',
            ]);

        if (! $result->successful()) {
            $errorOutput = $result->errorOutput();
            $this->error('psql failed: '.$errorOutput);

            // Check if it's a missing psql issue and provide installation help
            if (str_contains($errorOutput, 'psql') && (str_contains($errorOutput, 'not found') || str_contains($errorOutput, 'No such file') || str_contains($errorOutput, 'command not found'))) {
                $this->showPostgreSQLInstallInstructions('psql');
            }

            return false;
        }

        return true;
    }

    /**
     * Restore tar format backup
     */
    private function restoreTarFormat(string $file, array $env, string $host, int $port, string $username, string $database): bool
    {
        $result = Process::env($env)
            ->timeout(1800)
            ->run([
                'pg_restore',
                '-h', $host,
                '-p', (string) $port,
                '-U', $username,
                '-d', $database,
                '--no-owner',
                '--no-privileges',
                '--jobs=4',
                '--verbose',
                '-Ft',
                $file,
            ]);

        if (! $result->successful()) {
            $this->error('pg_restore (tar) failed: '.$result->errorOutput());

            return false;
        }

        return true;
    }

    /**
     * Show PostgreSQL client installation instructions
     */
    private function showPostgreSQLInstallInstructions(string $command): void
    {
        $this->error('');
        $this->error("🔧 PostgreSQL client ({$command}) not found!");
        $this->error('');
        $this->error('To install PostgreSQL client:');
        $this->error('');
        $this->error('Ubuntu/Debian:');
        $this->error('  sudo sh -c \'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list\'');
        $this->error('  curl -fsSL https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo gpg --dearmor -o /etc/apt/trusted.gpg.d/postgresql.gpg');
        $this->error('  sudo apt update && sudo apt install postgresql-client-17');
        $this->error('');
        $this->error('macOS:');
        $this->error('  brew install postgresql@17');
        $this->error('');
        $this->error('CentOS/RHEL:');
        $this->error('  sudo yum install postgresql17');
    }
}
