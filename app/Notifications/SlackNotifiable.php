<?php

namespace App\Notifications;

use Illuminate\Notifications\Notifiable;

class SlackNotifiable
{
    use Notifiable;

    public function routeNotificationForSlack(): string
    {
        return config('sitevigilance.notifications.slack.webhook_url');
    }
}
