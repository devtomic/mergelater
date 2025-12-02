<?php

use App\Models\ScheduledMerge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
