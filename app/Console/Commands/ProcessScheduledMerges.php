<?php

namespace App\Console\Commands;

use App\Jobs\MergePullRequest;
use App\Models\ScheduledMerge;
use Illuminate\Console\Command;

class ProcessScheduledMerges extends Command
{
    protected $signature = 'merges:process';

    protected $description = 'Process scheduled merges that are due';

    public function handle(): int
    {
        $dueMerges = ScheduledMerge::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($dueMerges as $merge) {
            $merge->update(['status' => 'processing']);
            MergePullRequest::dispatch($merge);
        }

        return Command::SUCCESS;
    }
}
