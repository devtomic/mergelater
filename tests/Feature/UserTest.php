<?php

use App\Models\User;
use App\Models\ScheduledMerge;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('hasCompletedOnboarding', function () {
    it('returns false when onboarding_completed_at is null', function () {
        $user = User::factory()->create(['onboarding_completed_at' => null]);

        expect($user->hasCompletedOnboarding())->toBeFalse();
    });

    it('returns true when onboarding_completed_at is set', function () {
        $user = User::factory()->create(['onboarding_completed_at' => now()]);

        expect($user->hasCompletedOnboarding())->toBeTrue();
    });
});

describe('scheduledMerges relationship', function () {
    it('returns scheduled merges for a user', function () {
        $user = User::factory()->create();
        $merge = ScheduledMerge::create([
            'user_id' => $user->id,
            'github_pr_url' => 'https://github.com/owner/repo/pull/1',
            'owner' => 'owner',
            'repo' => 'repo',
            'pull_number' => 1,
            'merge_method' => 'squash',
            'scheduled_at' => now()->addHour(),
            'status' => 'pending',
        ]);

        expect($user->scheduledMerges)->toHaveCount(1);
        expect($user->scheduledMerges->first()->id)->toBe($merge->id);
    });

    it('returns the user from a scheduled merge', function () {
        $user = User::factory()->create();
        $merge = ScheduledMerge::create([
            'user_id' => $user->id,
            'github_pr_url' => 'https://github.com/owner/repo/pull/1',
            'owner' => 'owner',
            'repo' => 'repo',
            'pull_number' => 1,
            'merge_method' => 'squash',
            'scheduled_at' => now()->addHour(),
            'status' => 'pending',
        ]);

        expect($merge->user->id)->toBe($user->id);
    });
});
