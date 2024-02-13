<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Events\RequestTookLongerThanMaxDurationEvent;
use App\Notifications\RequestTookLongerThanMaxDurationNotification;

class RequestTookLongerThanMaxDurationListener
{
    public function handle(RequestTookLongerThanMaxDurationEvent $event): void
    {
        $channels = config('sitevigilance.notifications.channels');

        foreach ($channels as $channel) {
            $notifiables = UserRepository::all();

            Notification::send(
                $notifiables,
                new RequestTookLongerThanMaxDurationNotification(
                    $event->uptimeCheck,
                    $event->maxRequestDuration,
                    $channel
                )
            );
        }
    }
}
