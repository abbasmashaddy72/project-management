<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Contracts\SiteVigilanceExceptionLogGroup;

class ExceptionLogGroupNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SiteVigilanceExceptionLogGroup $exceptionLogGroup,
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
            ->subject("[{$this->exceptionLogGroup->last_seen}]: {$this->exceptionLogGroup->type} | {$this->exceptionLogGroup->site->name}")
            ->greeting($this->exceptionLogGroup->type)
            ->line("Site: {$this->exceptionLogGroup->site->name}")
            ->line("Url: {$this->exceptionLogGroup->site->url}")
            ->line('Message: ')
            ->line($this->exceptionLogGroup->message)
            ->line("Seen at: {$this->exceptionLogGroup->last_seen->toDayDateTimeString()}")
            ->action('Review', $this->getActionUrl());
    }

    protected function getActionUrl(): string
    {
        return route('filament.admin.resources.exceptions.show', ['record' => $this->exceptionLogGroup->id, 'tenant' => $this->exceptionLogGroup->site->tenant->id]);
    }
}
