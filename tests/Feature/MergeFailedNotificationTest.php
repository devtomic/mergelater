<?php

use App\Notifications\MergeFailed;
use App\Models\ScheduledMerge;

it('can be instantiated with a scheduled merge', function () {
    $scheduledMerge = new ScheduledMerge();

    $notification = new MergeFailed($scheduledMerge);

    expect($notification)->toBeInstanceOf(MergeFailed::class);
});

it('sends via mail channel', function () {
    $scheduledMerge = new ScheduledMerge();
    $notification = new MergeFailed($scheduledMerge);

    expect($notification->via(null))->toContain('mail');
});

it('builds mail with failure subject', function () {
    $scheduledMerge = new ScheduledMerge([
        'owner' => 'acme',
        'repo' => 'project',
        'pull_number' => 42,
    ]);
    $notification = new MergeFailed($scheduledMerge);

    $mail = $notification->toMail(null);

    expect($mail->subject)->toBe('PR #42 merge failed');
});

it('includes error message in body', function () {
    $scheduledMerge = new ScheduledMerge([
        'owner' => 'acme',
        'repo' => 'project',
        'pull_number' => 42,
        'error_message' => 'Pull request is not mergeable',
    ]);
    $notification = new MergeFailed($scheduledMerge);

    $mail = $notification->toMail(null);

    expect($mail->introLines)->toContain('Error: Pull request is not mergeable');
});
