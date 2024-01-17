<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\ValueObjects\Period;
use Illuminate\Foundation\Events\Dispatchable;
use App\Contracts\SiteVigilanceUptimeCheck;

class UptimeCheckRecoveredEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public SiteVigilanceUptimeCheck $uptimeCheck, public Period $downtimePeriod)
    {
    }
}
