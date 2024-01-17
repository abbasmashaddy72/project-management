<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;
use App\Notifications\SlackNotifiable;
use App\Events\SslCertificateExpiresSoonEvent;
use App\Notifications\SslCertificateExpiresSoonNotification;

class SslCertificateExpiresSoonListener
{
    public function handle(SslCertificateExpiresSoonEvent $event): void
    {
        $channels = config('moonguard.notifications.channels');

        foreach ($channels as $channel) {
            $notifiables = ($channel === 'slack') ? new SlackNotifiable() : UserRepository::all();

            Notification::send(
                $notifiables,
                new SslCertificateExpiresSoonNotification($event->sslCertificateCheck, $channel)
            );
        }
    }
}
