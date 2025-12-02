<?php

namespace App\Console\Commands;

use App\Jobs\MergePullRequest;
use App\Models\ScheduledMerge;
use App\Notifications\MergeFailed;
use Illuminate\Console\Command;

class ProcessScheduledMerges extends Command
{
    protected $signature = 'merges:process';

    protected $description = 'Process scheduled merges that are due';

    public function handle(): int
    {
        // Mark stale processing merges as failed
        $staleMerges = ScheduledMerge::where('status', 'processing')
            ->where('updated_at', '<=', now()->subSeconds(60))
            ->get();

        foreach ($staleMerges as $merge) {
            $merge->update([
                'status' => 'failed',
                'error_message' => 'Merge timed out',
            ]);

            $merge->user->notify(new MergeFailed($merge));
        }

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
