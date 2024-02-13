<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Events\ExceptionLogGroupUpdatedEvent;
use App\Notifications\ExceptionLogGroupNotification;

class ExceptionLogGroupUpdatedListener
{
    public function handle(ExceptionLogGroupUpdatedEvent $event): void
    {
        $channels = config('sitevigilance.notifications.channels');

        foreach ($channels as $channel) {
            $notifiables = UserRepository::all();

            Notification::send(
                $notifiables,
                new ExceptionLogGroupNotification($event->exceptionLogGroup, $channel)
            );
        }
    }
}
