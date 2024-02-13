<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Events\SslCertificateExpiresSoonEvent;
use App\Notifications\SslCertificateExpiresSoonNotification;

class SslCertificateExpiresSoonListener
{
    public function handle(SslCertificateExpiresSoonEvent $event): void
    {
        $channels = config('sitevigilance.notifications.channels');

        foreach ($channels as $channel) {
            $notifiables = UserRepository::all();

            Notification::send(
                $notifiables,
                new SslCertificateExpiresSoonNotification($event->sslCertificateCheck, $channel)
            );
        }
    }
}
