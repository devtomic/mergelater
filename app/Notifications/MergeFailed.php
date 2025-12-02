<?php

namespace App\Notifications;

use App\Models\ScheduledMerge;
use App\Notifications\Channels\SlackWebhookChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MergeFailed extends Notification
{
    public function __construct(
        public ScheduledMerge $scheduledMerge,
    ) {}

    public function via($notifiable): array
    {
        $channels = [];

        if ($notifiable->email_notifications) {
            $channels[] = 'mail';
        }

        if ($notifiable->slack_webhook_url) {
            $channels[] = SlackWebhookChannel::class;
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $merge = $this->scheduledMerge;

        return (new MailMessage)
            ->subject("PR #{$merge->pull_number} merge failed")
            ->line("Your pull request {$merge->owner}/{$merge->repo}#{$merge->pull_number} could not be merged.")
            ->line("Error: {$merge->error_message}");
    }

    public function toSlack($notifiable): array
    {
        $merge = $this->scheduledMerge;

        return [
            'text' => "❌ PR #{$merge->pull_number} merge failed",
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => "❌ *<{$merge->github_pr_url}|{$merge->owner}/{$merge->repo}#{$merge->pull_number}>* merge failed\n\nError: {$merge->error_message}",
                    ],
                ],
            ],
        ];
    }
}
