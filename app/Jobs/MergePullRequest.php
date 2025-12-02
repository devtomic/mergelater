<?php

namespace App\Jobs;

use App\Exceptions\GitHubMergeException;
use App\Models\ScheduledMerge;
use App\Services\GitHubService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MergePullRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ScheduledMerge $scheduledMerge,
    ) {}

    public function handle(): void
    {
        $user = $this->scheduledMerge->user;
        $github = new GitHubService($user->github_token);

        try {
            $github->mergePullRequest(
                $this->scheduledMerge->owner,
                $this->scheduledMerge->repo,
                $this->scheduledMerge->pull_number,
                $this->scheduledMerge->merge_method,
            );

            $this->scheduledMerge->update([
                'status' => 'completed',
                'merged_at' => now(),
            ]);
        } catch (GitHubMergeException $e) {
            $this->scheduledMerge->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
