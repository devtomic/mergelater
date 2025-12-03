<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CacheVersion extends Command
{
    protected $signature = 'app:cache-version {--tag= : Manual version tag to use instead of git tag}';

    protected $description = 'Cache the current git version to a file';

    public function handle(): int
    {
        $tag = $this->option('tag') ?: trim(shell_exec('git describe --tags --abbrev=0 2>/dev/null') ?? '');
        $hash = trim(shell_exec('git rev-parse --short=8 HEAD 2>/dev/null') ?? '');

        $version = ($tag ?: 'dev') . ' ' . $hash;

        file_put_contents(base_path('.version'), $version);

        return Command::SUCCESS;
    }
}
