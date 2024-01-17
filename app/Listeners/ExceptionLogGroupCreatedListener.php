<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Notifications\SlackNotifiable;
use App\Events\ExceptionLogGroupCreatedEvent;
use App\Notifications\ExceptionLogGroupNotification;

class ExceptionLogGroupCreatedListener
{
    public function handle(ExceptionLogGroupCreatedEvent $event): void
    {
        $channels = config('moonguard.notifications.channels');

        foreach ($channels as $channel) {
            $notifiables = ($channel === 'slack') ? new SlackNotifiable() : UserRepository::all();

            Notification::send(
                $notifiables,
                new ExceptionLogGroupNotification($event->exceptionLogGroup, $channel)
            );
        }
    }
}
