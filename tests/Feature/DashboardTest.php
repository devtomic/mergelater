<?php

use App\Models\ScheduledMerge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests to login', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

it('displays dashboard for authenticated users', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Schedule a Merge');
});

it('displays fallback error message when error_message is empty', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'failed',
        'error_message' => null,
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Failed 0 seconds ago: Unknown error');
});

it('displays delete button for failed merges', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $merge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'failed',
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('action="/merges/' . $merge->id . '"', false);
});

it('displays retry button for failed merges', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $merge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'failed',
        'github_pr_url' => 'https://github.com/owner/repo/pull/123',
        'merge_method' => 'squash',
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('title="Retry"', false);
});
