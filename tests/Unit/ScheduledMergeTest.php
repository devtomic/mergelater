<?php

use App\Models\ScheduledMerge;

describe('parseGitHubUrl', function () {
    it('parses a valid GitHub PR URL', function () {
        $result = ScheduledMerge::parseGitHubUrl('https://github.com/owner/repo/pull/123');

        expect($result)->toBe([
            'owner' => 'owner',
            'repo' => 'repo',
            'pull_number' => 123,
        ]);
    });

    it('parses a GitHub PR URL with http', function () {
        $result = ScheduledMerge::parseGitHubUrl('http://github.com/owner/repo/pull/456');

        expect($result)->toBe([
            'owner' => 'owner',
            'repo' => 'repo',
            'pull_number' => 456,
        ]);
    });

    it('returns null for invalid URL', function () {
        $result = ScheduledMerge::parseGitHubUrl('https://example.com/not-a-pr');

        expect($result)->toBeNull();
    });

    it('returns null for GitHub non-PR URL', function () {
        $result = ScheduledMerge::parseGitHubUrl('https://github.com/owner/repo/issues/123');

        expect($result)->toBeNull();
    });
});

describe('status methods', function () {
    it('returns true for isPending when status is pending', function () {
        $merge = new ScheduledMerge(['status' => 'pending']);

        expect($merge->isPending())->toBeTrue();
    });

    it('returns false for isPending when status is not pending', function () {
        $merge = new ScheduledMerge(['status' => 'completed']);

        expect($merge->isPending())->toBeFalse();
    });

    it('returns true for isCompleted when status is completed', function () {
        $merge = new ScheduledMerge(['status' => 'completed']);

        expect($merge->isCompleted())->toBeTrue();
    });

    it('returns true for isFailed when status is failed', function () {
        $merge = new ScheduledMerge(['status' => 'failed']);

        expect($merge->isFailed())->toBeTrue();
    });
});
