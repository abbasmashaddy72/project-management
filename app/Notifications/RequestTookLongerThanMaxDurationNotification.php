<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\ValueObjects\RequestDuration;
use App\Contracts\SiteVigilanceUptimeCheck;

class RequestTookLongerThanMaxDurationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SiteVigilanceUptimeCheck $uptimeCheck,
        public RequestDuration $maxRequestDuration,
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
            ->line($this->uptimeCheck->site->name);
    }

    protected function getMessageText(): string
    {
        return "{$this->uptimeCheck->site->url} request took longer than {$this->maxRequestDuration->toMilliseconds()} (took {$this->uptimeCheck->request_duration_ms->toMilliseconds()})";
    }
}
