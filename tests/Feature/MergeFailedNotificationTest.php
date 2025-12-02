<?php

use App\Models\User;
use App\Notifications\Channels\SlackWebhookChannel;
use App\Notifications\MergeFailed;
use App\Models\ScheduledMerge;

it('can be instantiated with a scheduled merge', function () {
    $scheduledMerge = new ScheduledMerge();

    $notification = new MergeFailed($scheduledMerge);

    expect($notification)->toBeInstanceOf(MergeFailed::class);
});

it('sends via mail channel when email_notifications is enabled', function () {
    $user = new User(['email_notifications' => true]);
    $scheduledMerge = new ScheduledMerge();
    $notification = new MergeFailed($scheduledMerge);

    expect($notification->via($user))->toContain('mail');
});

it('does not send via mail when user has email_notifications disabled', function () {
    $user = new User(['email_notifications' => false]);
    $scheduledMerge = new ScheduledMerge();
    $notification = new MergeFailed($scheduledMerge);

    expect($notification->via($user))->not->toContain('mail');
});

it('sends to Slack when user has slack_webhook_url configured', function () {
    $user = new User([
        'email_notifications' => false,
        'slack_webhook_url' => 'https://hooks.slack.com/services/xxx',
    ]);
    $scheduledMerge = new ScheduledMerge();
    $notification = new MergeFailed($scheduledMerge);

    expect($notification->via($user))->toContain(SlackWebhookChannel::class);
});

it('does not send to Slack when user has no slack_webhook_url', function () {
    $user = new User([
        'email_notifications' => false,
        'slack_webhook_url' => null,
    ]);
    $scheduledMerge = new ScheduledMerge();
    $notification = new MergeFailed($scheduledMerge);

    expect($notification->via($user))->not->toContain(SlackWebhookChannel::class);
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
