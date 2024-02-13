<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Events\UptimeCheckRecoveredEvent;
use App\Notifications\UptimeCheckRecoveredNotification;

class UptimeCheckRecoveredListener
{
    public function handle(UptimeCheckRecoveredEvent $event): void
    {
        $channels = config('sitevigilance.notifications.channels');

        foreach ($channels as $channel) {
            $notifiables = UserRepository::all();

            Notification::send(
                $notifiables,
                new UptimeCheckRecoveredNotification(
                    $event->uptimeCheck,
                    $event->downtimePeriod,
                    $channel
                )
            );
        }
    }
}
