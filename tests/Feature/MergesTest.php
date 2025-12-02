<?php

use App\Models\ScheduledMerge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('creates a scheduled merge', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $response = $this->actingAs($user)->post('/merges', [
        'github_pr_url' => 'https://github.com/owner/repo/pull/123',
        'merge_method' => 'squash',
        'scheduled_at' => '2025-12-15 10:00:00',
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('scheduled_merges', [
        'user_id' => $user->id,
        'github_pr_url' => 'https://github.com/owner/repo/pull/123',
        'owner' => 'owner',
        'repo' => 'repo',
        'pull_number' => 123,
        'merge_method' => 'squash',
        'status' => 'pending',
    ]);
});

it('deletes a scheduled merge', function () {
    $user = User::factory()->create();
    $merge = ScheduledMerge::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->delete("/merges/{$merge->id}");

    $response->assertRedirect('/dashboard');
    $this->assertDatabaseMissing('scheduled_merges', ['id' => $merge->id]);
});

it('prevents deleting another users merge', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $merge = ScheduledMerge::factory()->create([
        'user_id' => $otherUser->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->delete("/merges/{$merge->id}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('scheduled_merges', ['id' => $merge->id]);
});

it('creates a scheduled merge with user timezone conversion', function () {
    // User is in America/New_York (UTC-5 in winter)
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    // Freeze time at 2025-12-02 21:00:00 UTC (4:00 PM EST)
    $this->travelTo('2025-12-02 21:00:00');

    // User submits 5:00 PM in their local time (EST)
    // This should be valid because 5:00 PM EST = 10:00 PM UTC, which is after 9:00 PM UTC
    $response = $this->actingAs($user)->post('/merges', [
        'github_pr_url' => 'https://github.com/owner/repo/pull/456',
        'merge_method' => 'squash',
        'scheduled_at' => '2025-12-02T17:00', // 5:00 PM EST (user's local time)
    ]);

    $response->assertRedirect('/dashboard');
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('scheduled_merges', [
        'user_id' => $user->id,
        'pull_number' => 456,
    ]);
});

// Validation endpoint tests

it('validates PR URL format before calling GitHub API', function () {
    Http::fake();

    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $response = $this->actingAs($user)->post('/merges/validate', [
        'github_pr_url' => 'not-a-valid-url',
        'merge_method' => 'squash',
        'scheduled_at' => '2025-12-15 10:00:00',
    ]);

    $response->assertSessionHasErrors('github_pr_url');
    Http::assertNothingSent();
});

it('returns PR not found error for non-existent PR', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/999' => Http::response([
            'message' => 'Not Found',
        ], 404),
    ]);

    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
        'github_token' => 'test-token',
    ]);

    $response = $this->actingAs($user)->post('/merges/validate', [
        'github_pr_url' => 'https://github.com/owner/repo/pull/999',
        'merge_method' => 'squash',
        'scheduled_at' => '2025-12-15 10:00:00',
    ]);

    $response->assertSessionHasErrors([
        'github_pr_url' => 'PR not found. Please check the URL and try again.',
    ]);
});

it('returns access denied error when user cannot access repo', function () {
    Http::fake([
        'api.github.com/repos/owner/private-repo/pulls/123' => Http::response([
            'message' => 'Not Found',
        ], 403),
    ]);

    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
        'github_token' => 'test-token',
    ]);

    $response = $this->actingAs($user)->post('/merges/validate', [
        'github_pr_url' => 'https://github.com/owner/private-repo/pull/123',
        'merge_method' => 'squash',
        'scheduled_at' => '2025-12-15 10:00:00',
    ]);

    $response->assertSessionHasErrors([
        'github_pr_url' => 'You don\'t have access to this repository.',
    ]);
});

it('returns error when PR is already merged', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123' => Http::response([
            'number' => 123,
            'state' => 'closed',
            'merged' => true,
            'title' => 'Already merged PR',
        ], 200),
    ]);

    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
        'github_token' => 'test-token',
    ]);

    $response = $this->actingAs($user)->post('/merges/validate', [
        'github_pr_url' => 'https://github.com/owner/repo/pull/123',
        'merge_method' => 'squash',
        'scheduled_at' => '2025-12-15 10:00:00',
    ]);

    $response->assertSessionHasErrors([
        'github_pr_url' => 'This PR has already been merged.',
    ]);
});

it('returns error when PR is closed without merging', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123' => Http::response([
            'number' => 123,
            'state' => 'closed',
            'merged' => false,
            'title' => 'Closed PR',
        ], 200),
    ]);

    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
        'github_token' => 'test-token',
    ]);

    $response = $this->actingAs($user)->post('/merges/validate', [
        'github_pr_url' => 'https://github.com/owner/repo/pull/123',
        'merge_method' => 'squash',
        'scheduled_at' => '2025-12-15 10:00:00',
    ]);

    $response->assertSessionHasErrors([
        'github_pr_url' => 'This PR has been closed without merging.',
    ]);
});

it('stores PR data in session and redirects to preview on success', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123' => Http::response([
            'number' => 123,
            'state' => 'open',
            'merged' => false,
            'title' => 'Add new feature',
            'user' => [
                'login' => 'author',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/123',
            ],
            'base' => ['ref' => 'main'],
            'head' => ['ref' => 'feature/new-thing'],
        ], 200),
    ]);

    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
        'github_token' => 'test-token',
    ]);

    $response = $this->actingAs($user)->post('/merges/validate', [
        'github_pr_url' => 'https://github.com/owner/repo/pull/123',
        'merge_method' => 'squash',
        'scheduled_at' => '2025-12-15 10:00:00',
    ]);

    $response->assertRedirect('/merges/preview');
    $response->assertSessionHas('pending_merge.github_pr_url', 'https://github.com/owner/repo/pull/123');
    $response->assertSessionHas('pending_merge.pr_data.title', 'Add new feature');
});

// Preview page tests

it('displays PR preview page with stored data', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $response = $this->actingAs($user)
        ->withSession([
            'pending_merge' => [
                'github_pr_url' => 'https://github.com/owner/repo/pull/123',
                'owner' => 'owner',
                'repo' => 'repo',
                'pull_number' => 123,
                'merge_method' => 'squash',
                'scheduled_at' => '2025-12-15 15:00:00',
                'pr_data' => [
                    'title' => 'Add new feature',
                    'state' => 'open',
                    'user' => ['login' => 'author', 'avatar_url' => 'https://avatars.githubusercontent.com/u/123'],
                    'base' => ['ref' => 'main'],
                    'head' => ['ref' => 'feature/new-thing'],
                ],
            ],
        ])
        ->get('/merges/preview');

    $response->assertStatus(200);
    $response->assertSee('Add new feature');
    $response->assertSee('owner/repo#123');
});

it('redirects to dashboard if no PR data in session', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $response = $this->actingAs($user)->get('/merges/preview');

    $response->assertRedirect('/dashboard');
});

// Confirm endpoint tests

it('creates merge from session data on confirmation', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    $response = $this->actingAs($user)
        ->withSession([
            'pending_merge' => [
                'github_pr_url' => 'https://github.com/owner/repo/pull/123',
                'owner' => 'owner',
                'repo' => 'repo',
                'pull_number' => 123,
                'merge_method' => 'squash',
                'scheduled_at' => '2025-12-15 15:00:00',
                'pr_data' => [
                    'title' => 'Add new feature',
                    'state' => 'open',
                ],
            ],
        ])
        ->post('/merges');

    $response->assertRedirect('/dashboard');
    $response->assertSessionMissing('pending_merge');

    $this->assertDatabaseHas('scheduled_merges', [
        'user_id' => $user->id,
        'github_pr_url' => 'https://github.com/owner/repo/pull/123',
        'owner' => 'owner',
        'repo' => 'repo',
        'pull_number' => 123,
        'merge_method' => 'squash',
        'status' => 'pending',
    ]);
});

it('returns validation error when session expired and no form data', function () {
    $user = User::factory()->create([
        'onboarding_completed_at' => now(),
        'timezone' => 'America/New_York',
    ]);

    // POST without session data and without form data
    $response = $this->actingAs($user)->post('/merges');

    $response->assertSessionHasErrors(['github_pr_url', 'merge_method', 'scheduled_at']);
});
