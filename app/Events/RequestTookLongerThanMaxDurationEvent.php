<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\ValueObjects\RequestDuration;
use App\Contracts\MoonGuardUptimeCheck;

class RequestTookLongerThanMaxDurationEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public MoonGuardUptimeCheck $uptimeCheck, public RequestDuration $maxRequestDuration)
    {
    }
}
