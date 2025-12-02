<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SlackWebhookChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $webhookUrl = $notifiable->slack_webhook_url;

        if (! $webhookUrl) {
            return;
        }

        $message = $notification->toSlack($notifiable);

        Http::post($webhookUrl, $message);
    }
}
