<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateDataToPostgres extends Command
{
    protected $description = 'Migrate data from MariaDB to PostgreSQL with integrity verification';

    protected $signature = 'migrate:data-to-postgres
        {--dry-run : Show what would be migrated without executing}
        {--verify : Verify data integrity after migration}';

    private array $stats = [];

    private array $tables = [];

    public function handle()
    {
        $this->info('Starting data migration from MariaDB to PostgreSQL...');

        if ($this->option('verify')) {
            return $this->verifyDataIntegrity();
        }

        $this->getTables();

        if ($this->option('dry-run')) {
            return $this->showMigrationPlan();
        }

        $this->confirmMigration();
        $this->setupConnections();
        $this->migrateData();
        $this->resetPostgresSequences();
        $this->showResults();

        $this->info('Data migration completed successfully!');
        $this->info('Run with --verify flag to check data integrity.');
    }

    private function confirmMigration(): void
    {
        $this->warn('This will copy all data from MariaDB to PostgreSQL.');
        $this->warn('WARNING: Each table will be TRUNCATED before data is copied!');

        if (! $this->confirm('Do you want to continue?')) {
            $this->info('Migration cancelled.');
            exit(0);
        }
    }

    private function getTables(): void
    {
        $allTables = collect(DB::connection('mysql_source')->select('SHOW TABLES'))
            ->map(fn ($table) => array_values((array) $table)[0])
            ->toArray();

        // Sort tables to respect foreign key dependencies
        $this->tables = $this->sortTablesByDependencies($allTables);

        $this->info(sprintf('Found %d tables to migrate', count($this->tables)));
    }

    private function migrateData(): void
    {
        $mysql = DB::connection('mysql_source');
        $pgsql = DB::connection('pgsql_target');

        $bar = $this->output->createProgressBar(count($this->tables));
        $bar->start();

        foreach ($this->tables as $table) {
            $this->stats[$table] = [
                'source_count' => 0,
                'migrated_count' => 0,
                'errors' => [],
            ];

            try {
                $sourceCount = $mysql->table($table)->count();
                $this->stats[$table]['source_count'] = $sourceCount;

                if ($sourceCount === 0) {
                    $bar->advance();

                    continue;
                }

                $pgsql->table($table)->truncate();

                $chunkSize = 1000;
                $migrated = 0;

                // Get primary key or first column for ordering
                $columns = collect(DB::connection('mysql_source')
                    ->getSchemaBuilder()
                    ->getColumnListing($table));
                $orderBy = $columns->contains('id') ? 'id' : $columns->first();

                $mysql->table($table)
                    ->orderBy($orderBy)
                    ->chunk($chunkSize, function ($rows) use ($pgsql, $table, &$migrated) {
                        $data = $rows->map(fn ($row) => (array) $row)->toArray();
                        $pgsql->table($table)->insert($data);
                        $migrated += count($data);
                    });

                $this->stats[$table]['migrated_count'] = $migrated;

            } catch (\Exception $e) {
                $this->stats[$table]['errors'][] = $e->getMessage();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function resetPostgresSequences(): void
    {
        $this->info('Resetting PostgreSQL sequences...');
        $pgsql = DB::connection('pgsql_target');

        $sequencesReset = 0;
        $errors = [];

        foreach ($this->tables as $table) {
            try {
                $columns = $pgsql->getSchemaBuilder()->getColumnListing($table);

                if (! in_array('id', $columns)) {
                    continue;
                }

                $maxId = $pgsql->table($table)->max('id');

                if ($maxId === null) {
                    continue;
                }

                $sequenceName = $table.'_id_seq';
                $sequenceCheck = $pgsql->select('
                    SELECT sequence_name
                    FROM information_schema.sequences
                    WHERE sequence_name = ?
                ', [$sequenceName]);

                if (empty($sequenceCheck)) {
                    continue;
                }

                $pgsql->statement("SELECT setval('{$sequenceName}', ?)", [$maxId]);
                $sequencesReset++;

            } catch (\Exception $e) {
                $errors[] = sprintf('%s: %s', $table, $e->getMessage());
            }
        }

        $this->info(sprintf('Reset %d sequences', $sequencesReset));

        if (! empty($errors)) {
            $this->warn('Some sequences could not be reset:');
            foreach ($errors as $error) {
                $this->line('  - '.$error);
            }
        }
    }

    private function setupConnections(): void
    {
        $mysql = DB::connection('mysql_source');
        $pgsql = DB::connection('pgsql_target');

        $this->info('Testing database connections...');

        try {
            $mysql->getPdo();
            $this->info('MariaDB connection successful');
        } catch (\Exception $e) {
            $this->error('MariaDB connection failed: '.$e->getMessage());
            exit(1);
        }

        try {
            $pgsql->getPdo();
            $this->info('PostgreSQL connection successful');
        } catch (\Exception $e) {
            $this->error('PostgreSQL connection failed: '.$e->getMessage());
            exit(1);
        }
    }

    private function showMigrationPlan(): void
    {
        $this->info('Migration Plan (DRY RUN):');
        $this->newLine();

        $mysql = DB::connection('mysql_source');
        $totalRows = 0;

        foreach ($this->tables as $table) {
            $count = $mysql->table($table)->count();
            $totalRows += $count;

            $this->line(sprintf(
                '  %s: %s rows',
                str_pad($table, 30),
                number_format($count)
            ));
        }

        $this->newLine();
        $this->info(sprintf('Total rows to migrate: %s', number_format($totalRows)));
    }

    private function showResults(): void
    {
        $this->info('Migration Results:');
        $this->newLine();

        $totalSource = 0;
        $totalMigrated = 0;
        $errors = 0;

        foreach ($this->stats as $table => $stats) {
            $totalSource += $stats['source_count'];
            $totalMigrated += $stats['migrated_count'];

            $status = empty($stats['errors']) ? 'OK' : 'ERROR';

            if (! empty($stats['errors'])) {
                $errors++;
            }

            $this->line(sprintf(
                '  [%s] %s: %s -> %s rows',
                $status,
                str_pad($table, 26),
                number_format($stats['source_count']),
                number_format($stats['migrated_count'])
            ));

            if (! empty($stats['errors'])) {
                foreach ($stats['errors'] as $error) {
                    $this->line('    Error: '.$error);
                }
            }
        }

        $this->newLine();
        $this->info(sprintf(
            'Summary: %s tables, %s -> %s rows, %s errors',
            count($this->tables),
            number_format($totalSource),
            number_format($totalMigrated),
            $errors
        ));
    }

    private function sortTablesByDependencies(array $tables): array
    {
        // Define table dependency order (parents first, children after)
        $dependencyOrder = [
            // Core Laravel tables first
            'migrations',
            'cache',
            'cache_locks',
            'job_batches',
            'jobs',
            'failed_jobs',
            'password_reset_tokens',
            'sessions',

            // Users and authentication
            'users',

            // Application tables (children of users)
            'scheduled_merges',
        ];

        $sortedTables = [];

        // Add tables in dependency order first
        foreach ($dependencyOrder as $table) {
            if (in_array($table, $tables)) {
                $sortedTables[] = $table;
            }
        }

        // Add remaining tables
        foreach ($tables as $table) {
            if (! in_array($table, $sortedTables)) {
                $sortedTables[] = $table;
            }
        }

        return $sortedTables;
    }

    private function verifyDataIntegrity(): void
    {
        $this->info('Verifying data integrity between MariaDB and PostgreSQL...');
        $this->newLine();

        $mysql = DB::connection('mysql_source');
        $pgsql = DB::connection('pgsql_target');

        $this->getTables();
        $mismatches = 0;

        foreach ($this->tables as $table) {
            $mysqlCount = $mysql->table($table)->count();
            $pgsqlCount = $pgsql->table($table)->count();

            $match = $mysqlCount === $pgsqlCount;
            $status = $match ? 'OK' : 'MISMATCH';

            if (! $match) {
                $mismatches++;
            }

            $this->line(sprintf(
                '  [%s] %s: MySQL=%s, PostgreSQL=%s',
                $status,
                str_pad($table, 24),
                number_format($mysqlCount),
                number_format($pgsqlCount)
            ));
        }

        $this->newLine();

        if ($mismatches === 0) {
            $this->info('All tables have matching row counts!');
        } else {
            $this->error(sprintf('%d tables have mismatched row counts', $mismatches));
        }
    }
}
