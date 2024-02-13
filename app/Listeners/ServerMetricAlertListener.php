<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Events\ServerMetricAlertEvent;
use App\Notifications\ServerMetricNotification;

class ServerMetricAlertListener
{
    use InteractsWithQueue;

    public function handle(ServerMetricAlertEvent $event)
    {
        $channels = config('sitevigilance.notifications.channels');

        $notifiablesForMail = UserRepository::all();

        foreach ($channels as $channel) {
            if ($event->server_monitoring_notification_enabled) {
                $notifiables = $notifiablesForMail;

                Notification::send(
                    $notifiables,
                    new ServerMetricNotification($event->site, $event->resource, $event->usage, $channel)
                );
            }
        }
    }
}
