<?php

use App\Models\ScheduledMerge;
use App\Models\User;
use App\Notifications\MergeFailed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('command exists', function () {
    $this->artisan('merges:process')
        ->assertExitCode(0);
});

it('processes due pending merges', function () {
    Http::fake([
        'api.github.com/*' => Http::response(['sha' => 'abc123', 'merged' => true], 200),
    ]);
    Notification::fake();

    $user = User::factory()->create(['github_token' => 'fake-token']);
    $dueMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'scheduled_at' => now()->subMinute(),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    $dueMerge->refresh();
    expect($dueMerge->status)->toBe('completed');
});

it('does not process future merges', function () {
    Http::fake();

    $user = User::factory()->create(['github_token' => 'fake-token']);
    $futureMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'scheduled_at' => now()->addHour(),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    Http::assertNothingSent();

    $futureMerge->refresh();
    expect($futureMerge->status)->toBe('pending');
});

it('does not process non-pending merges', function () {
    Http::fake();

    $user = User::factory()->create(['github_token' => 'fake-token']);
    $completedMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'scheduled_at' => now()->subMinute(),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    Http::assertNothingSent();
});

it('marks stale processing merges as failed', function () {
    $user = User::factory()->create();
    $staleMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'processing',
        'scheduled_at' => now()->subMinutes(5),
        'updated_at' => now()->subSeconds(61),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    $staleMerge->refresh();
    expect($staleMerge->status)->toBe('failed');
});

it('does not mark recent processing merges as failed', function () {
    $user = User::factory()->create();
    $recentMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'processing',
        'scheduled_at' => now()->subMinutes(5),
        'updated_at' => now()->subSeconds(30),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    $recentMerge->refresh();
    expect($recentMerge->status)->toBe('processing');
});

it('sets error message for timed out merges', function () {
    $user = User::factory()->create();
    $staleMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'processing',
        'scheduled_at' => now()->subMinutes(5),
        'updated_at' => now()->subSeconds(61),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    $staleMerge->refresh();
    expect($staleMerge->error_message)->toBe('Merge timed out');
});

it('sends MergeFailed notification for timed out merges', function () {
    Notification::fake();

    $user = User::factory()->create();
    $staleMerge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'processing',
        'scheduled_at' => now()->subMinutes(5),
        'updated_at' => now()->subSeconds(61),
    ]);

    $this->artisan('merges:process')
        ->assertExitCode(0);

    Notification::assertSentTo($user, MergeFailed::class, function ($notification) use ($staleMerge) {
        return $notification->scheduledMerge->id === $staleMerge->id;
    });
});
