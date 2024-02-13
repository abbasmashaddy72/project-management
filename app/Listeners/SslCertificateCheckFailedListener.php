<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Events\SslCertificateCheckFailedEvent;
use App\Notifications\SslCertificateCheckFailedNotification;

class SslCertificateCheckFailedListener
{
    public function handle(SslCertificateCheckFailedEvent $event): void
    {
        $channels = config('sitevigilance.notifications.channels');

        foreach ($channels as $channel) {
            $notifiables = UserRepository::all();

            Notification::send(
                $notifiables,
                new SslCertificateCheckFailedNotification($event->sslCertificateCheck, $channel)
            );
        }
    }
}
