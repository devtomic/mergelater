<?php

use App\Jobs\MergePullRequest;
use App\Models\ScheduledMerge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('command exists', function () {
    $this->artisan('merges:process')
        ->assertExitCode(0);
});

it('dispatches jobs for due pending merges', function () {
    Queue::fake();

    $user = User::factory()->create();
    $dueMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'scheduled_at' => now()->subMinute(),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    Queue::assertPushed(MergePullRequest::class, function ($job) use ($dueMerge) {
        return $job->scheduledMerge->id === $dueMerge->id;
    });

    $dueMerge->refresh();
    expect($dueMerge->status)->toBe('processing');
});

it('does not dispatch jobs for future merges', function () {
    Queue::fake();

    $user = User::factory()->create();
    $futureMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'scheduled_at' => now()->addHour(),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    Queue::assertNotPushed(MergePullRequest::class);

    $futureMerge->refresh();
    expect($futureMerge->status)->toBe('pending');
});

it('does not dispatch jobs for non-pending merges', function () {
    Queue::fake();

    $user = User::factory()->create();
    $completedMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'scheduled_at' => now()->subMinute(),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    Queue::assertNotPushed(MergePullRequest::class);
});
