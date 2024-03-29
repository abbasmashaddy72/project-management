<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Events\UptimeCheckFailedEvent;
use App\Notifications\UptimeCheckFailedNotification;

class UptimeCheckFailedListener
{
    public function handle(UptimeCheckFailedEvent $event): void
    {
        $channels = config('sitevigilance.notifications.channels');

        foreach ($channels as $channel) {
            $notifiables = UserRepository::all();

            Notification::send(
                $notifiables,
                new UptimeCheckFailedNotification(
                    $event->uptimeCheck,
                    $event->downtimePeriod,
                    $channel
                )
            );
        }
    }
}
