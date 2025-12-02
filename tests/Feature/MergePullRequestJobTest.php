<?php

use App\Jobs\MergePullRequest;
use App\Models\ScheduledMerge;
use App\Models\User;
use App\Notifications\MergeFailed;
use App\Notifications\MergeSuccessful;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('can be instantiated with a scheduled merge', function () {
    $scheduledMerge = new ScheduledMerge();

    $job = new MergePullRequest($scheduledMerge);

    expect($job)->toBeInstanceOf(MergePullRequest::class);
});

it('merges a pull request successfully', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123/merge' => Http::response([
            'sha' => 'abc123',
            'merged' => true,
        ], 200),
    ]);

    $user = User::factory()->create(['github_token' => 'test-token']);
    $scheduledMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'owner' => 'owner',
        'repo' => 'repo',
        'pull_number' => 123,
        'merge_method' => 'squash',
        'status' => 'processing',
    ]);

    $job = new MergePullRequest($scheduledMerge);
    $job->handle();

    $scheduledMerge->refresh();
    expect($scheduledMerge->status)->toBe('completed');
    expect($scheduledMerge->merged_at)->not->toBeNull();
});

it('marks merge as failed when GitHub API fails', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123/merge' => Http::response([
            'message' => 'Pull Request is not mergeable',
        ], 405),
    ]);

    $user = User::factory()->create(['github_token' => 'test-token']);
    $scheduledMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'owner' => 'owner',
        'repo' => 'repo',
        'pull_number' => 123,
        'merge_method' => 'squash',
        'status' => 'processing',
    ]);

    $job = new MergePullRequest($scheduledMerge);
    $job->handle();

    $scheduledMerge->refresh();
    expect($scheduledMerge->status)->toBe('failed');
    expect($scheduledMerge->error_message)->toBe('Pull Request is not mergeable');
});

it('sends success notification after merge', function () {
    Notification::fake();

    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123/merge' => Http::response([
            'sha' => 'abc123',
            'merged' => true,
        ], 200),
    ]);

    $user = User::factory()->create(['github_token' => 'test-token']);
    $scheduledMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'owner' => 'owner',
        'repo' => 'repo',
        'pull_number' => 123,
        'merge_method' => 'squash',
        'status' => 'processing',
    ]);

    $job = new MergePullRequest($scheduledMerge);
    $job->handle();

    Notification::assertSentTo($user, MergeSuccessful::class);
});

it('sends failure notification when merge fails', function () {
    Notification::fake();

    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123/merge' => Http::response([
            'message' => 'Pull Request is not mergeable',
        ], 405),
    ]);

    $user = User::factory()->create(['github_token' => 'test-token']);
    $scheduledMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'owner' => 'owner',
        'repo' => 'repo',
        'pull_number' => 123,
        'merge_method' => 'squash',
        'status' => 'processing',
    ]);

    $job = new MergePullRequest($scheduledMerge);
    $job->handle();

    Notification::assertSentTo($user, MergeFailed::class);
});
