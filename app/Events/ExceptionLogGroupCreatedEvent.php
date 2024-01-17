<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Contracts\MoonGuardExceptionLogGroup;

class ExceptionLogGroupCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public MoonGuardExceptionLogGroup $exceptionLogGroup)
    {
    }
}
