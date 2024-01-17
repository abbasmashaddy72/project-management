<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Contracts\SiteVigilanceExceptionLogGroup;

class ExceptionLogGroupCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public SiteVigilanceExceptionLogGroup $exceptionLogGroup)
    {
    }
}
