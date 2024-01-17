<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\ValueObjects\RequestDuration;
use App\Contracts\SiteVigilanceUptimeCheck;

class RequestTookLongerThanMaxDurationEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public SiteVigilanceUptimeCheck $uptimeCheck, public RequestDuration $maxRequestDuration)
    {
    }
}
