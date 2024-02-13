<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\SiteVigilanceSite;
use Illuminate\Notifications\Messages\MailMessage;

class ServerMetricNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public SiteVigilanceSite $site;

    public string $resource;

    public array | string $usage;

    public string $channel;

    public function __construct($site, $resource, $usage, $channel)
    {
        $this->site = $site;
        $this->resource = $resource;
        $this->usage = $usage;
        $this->channel = $channel;
    }

    public function via($notifiable): string
    {
        return $this->channel;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Resource usage alert')
            ->greeting("{$this->site->name}")
            ->line("url: {$this->site->url}")
            ->line("The established {$this->resource} usage limit has been reached at {$this->usage}%.");
    }
}
