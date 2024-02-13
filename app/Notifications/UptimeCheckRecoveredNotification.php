<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\ValueObjects\Period;
use Illuminate\Notifications\Messages\MailMessage;
use App\Contracts\SiteVigilanceUptimeCheck;

class UptimeCheckRecoveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SiteVigilanceUptimeCheck $uptime,
        public Period $downtimePeriod,
        public String $channel
    ) {
    }

    public function via(): string
    {
        return $this->channel;
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->success()
            ->subject($this->getMessageText())
            ->line($this->getMessageText())
            ->line($this->uptime->site->name);
    }

    protected function getMessageText(): string
    {
        return "{$this->uptime->site->url} has recovered after {$this->downtimePeriod->duration()}";
    }
}
