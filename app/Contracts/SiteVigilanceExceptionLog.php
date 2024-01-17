<?php

namespace App\Contracts;

use Illuminate\Support\Carbon;
use App\Enums\ExceptionLogStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property string|int $site_id
 * @property string $type
 * @property string $file
 * @property ExceptionLogStatus $status
 * @property array $trace
 * @property array $request
 * @property Carbon $thrown_at
 * @property SiteVigilanceSite $site
 * @property SiteVigilanceExceptionLogGroup $exceptionLogGroup
 */
interface SiteVigilanceExceptionLog
{
    public function site(): HasOneThrough;

    public function exceptionLogGroup(): BelongsTo;
}
