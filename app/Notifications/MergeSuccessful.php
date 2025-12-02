<?php

namespace App\Notifications;

use App\Models\ScheduledMerge;
use App\Notifications\Channels\SlackWebhookChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MergeSuccessful extends Notification
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
            ->subject("PR #{$merge->pull_number} merged successfully")
            ->line("Your pull request {$merge->owner}/{$merge->repo}#{$merge->pull_number} has been merged successfully.");
    }

    public function toSlack($notifiable): array
    {
        $merge = $this->scheduledMerge;

        return [
            'text' => "✅ PR #{$merge->pull_number} merged successfully",
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => "✅ *<{$merge->github_pr_url}|{$merge->owner}/{$merge->repo}#{$merge->pull_number}>* merged successfully",
                    ],
                ],
            ],
        ];
    }
}
