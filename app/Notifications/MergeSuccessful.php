<?php

namespace App\Notifications;

use App\Models\ScheduledMerge;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MergeSuccessful extends Notification
{
    public function __construct(
        public ScheduledMerge $scheduledMerge,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $merge = $this->scheduledMerge;

        return (new MailMessage)
            ->subject("PR #{$merge->pull_number} merged successfully")
            ->line("Your pull request {$merge->owner}/{$merge->repo}#{$merge->pull_number} has been merged successfully.");
    }
}
