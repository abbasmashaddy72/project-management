<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Notifications\SlackNotifiable;
use App\Events\SslCertificateCheckFailedEvent;
use App\Notifications\SslCertificateCheckFailedNotification;

class SslCertificateCheckFailedListener
{
    public function handle(SslCertificateCheckFailedEvent $event): void
    {
        $channels = config('moonguard.notifications.channels');

        foreach ($channels as $channel) {
            $notifiables = ($channel === 'slack') ? new SlackNotifiable() : UserRepository::all();

            Notification::send(
                $notifiables,
                new SslCertificateCheckFailedNotification($event->sslCertificateCheck, $channel)
            );
        }
    }
}
