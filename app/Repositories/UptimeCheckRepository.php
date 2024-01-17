<?php

namespace App\Repositories;

use App\Contracts\SiteVigilanceUptimeCheck;

class UptimeCheckRepository extends ModelRepository
{
    protected static string $contract = SiteVigilanceUptimeCheck::class;

    protected static string $modelClassConfigKey = 'sitevigilance.uptime_check.model';

    public static function isEnabled(): bool
    {
        return config('sitevigilance.uptime_check.enabled');
    }

    public static function resolveModel(): SiteVigilanceUptimeCheck
    {
        $modelClass = static::resolveModelClass();

        return new $modelClass();
    }
}
