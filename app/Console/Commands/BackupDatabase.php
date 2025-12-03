<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;

class BackupDatabase extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the PostgreSQL database';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup
                            {--path=storage/database/backups : Directory to save backup files}
                            {--format=custom : Backup format (custom, plain, tar)}
                            {--compress : Compress the backup file}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

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

        // Create backup directory
        $backupPath = $this->option('path');
        if (! is_dir($backupPath)) {
            if (! mkdir($backupPath, 0755, true)) {
                $this->error("Failed to create backup directory: {$backupPath}");

                return Command::FAILURE;
            }
            $this->info("Created backup directory: {$backupPath}");
        }

        // Verify directory is writable
        if (! is_writable($backupPath)) {
            $this->error("Backup directory is not writable: {$backupPath}");

            return Command::FAILURE;
        }

        // Generate backup filename with timestamp
        $timestamp = date('Ymd-His');
        $format = $this->option('format');
        $compress = $this->option('compress');

        $extension = match ($format) {
            'custom' => '.pgsql',
            'plain' => '.sql',
            'tar' => '.tar',
            default => '.pgsql'
        };

        if ($compress && $format === 'plain') {
            $extension .= '.gz';
        }

        $backupFile = "{$backupPath}/backup-{$timestamp}{$extension}";

        // Build pg_dump command
        $command = [
            'pg_dump',
            '-h', $host,
            '-p', (string) $port,
            '-U', $username,
            '-d', $database,
            '--no-owner',
            '--no-privileges',
            '--verbose',
        ];

        // Add format-specific options
        switch ($format) {
            case 'custom':
                $command[] = '-Fc';
                break;
            case 'tar':
                $command[] = '-Ft';
                break;
            case 'plain':
            default:
                // Plain format is default
                break;
        }

        // Set environment variables
        $env = ['PGPASSWORD' => $password];

        try {
            $this->info("Creating backup: {$backupFile}");

            if ($compress && $format === 'plain') {
                // For plain format with compression, pipe through gzip
                $result = Process::env($env)
                    ->timeout(1800) // 30 minutes timeout
                    ->run(implode(' ', array_map('escapeshellarg', $command)).' | gzip > '.escapeshellarg($backupFile));
            } else {
                // Direct output to file
                $command[] = '-f';
                $command[] = $backupFile;

                $result = Process::env($env)
                    ->timeout(1800) // 30 minutes timeout
                    ->run($command);
            }

            if ($result->successful()) {
                // Verify backup file exists and has content
                if (file_exists($backupFile) && filesize($backupFile) > 0) {
                    $size = $this->formatBytes(filesize($backupFile));
                    $this->info("✓ Backup completed successfully: {$backupFile} ({$size})");
                    $this->info("Database: {$database}@{$host}:{$port}");

                    return Command::SUCCESS;
                } else {
                    $this->error('Backup file is empty or does not exist.');

                    return Command::FAILURE;
                }
            } else {
                $this->error('Database backup failed:');
                $errorOutput = $result->errorOutput();
                $this->error($errorOutput);

                // Check if it's a missing pg_dump issue and provide installation help
                if (str_contains($errorOutput, 'pg_dump') && (str_contains($errorOutput, 'not found') || str_contains($errorOutput, 'No such file') || str_contains($errorOutput, 'command not found'))) {
                    $this->error('');
                    $this->error('🔧 PostgreSQL client (pg_dump) not found!');
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

                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('Exception during backup: '.$e->getMessage());

            // Check if it's a missing pg_dump issue and provide installation help
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'pg_dump') && (str_contains($errorMessage, 'not found') || str_contains($errorMessage, 'No such file') || str_contains($errorMessage, 'command not found'))) {
                $this->error('');
                $this->error('🔧 PostgreSQL client (pg_dump) not found!');
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

            return Command::FAILURE;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }
}
