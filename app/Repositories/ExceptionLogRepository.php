<?php

namespace App\Repositories;

use App\Contracts\SiteVigilanceExceptionLog;

class ExceptionLogRepository extends ModelRepository
{
    protected static string $contract = SiteVigilanceExceptionLog::class;

    protected static string $modelClassConfigKey = 'sitevigilance.exceptions.exception_log.model';

    public static function isEnabled(): bool
    {
        return config('sitevigilance.exceptions.enabled');
    }

    public static function create(array $attributes = []): SiteVigilanceExceptionLog
    {
        return static::resolveModelClass()::create($attributes);
    }

    public static function resolveModel(): SiteVigilanceExceptionLog
    {
        $modelClass = static::resolveModelClass();

        return new $modelClass();
    }
}
