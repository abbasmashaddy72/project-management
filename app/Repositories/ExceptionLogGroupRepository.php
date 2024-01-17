<?php

namespace App\Repositories;

use App\Contracts\SiteVigilanceExceptionLogGroup;

class ExceptionLogGroupRepository extends ModelRepository
{
    protected static string $contract = SiteVigilanceExceptionLogGroup::class;

    protected static string $modelClassConfigKey = 'sitevigilance.exceptions.exception_log_group.model';

    public static function isEnabled(): bool
    {
        return config('sitevigilance.exceptions.enabled');
    }

    public static function findOrFail(string|int $id): SiteVigilanceExceptionLogGroup
    {
        return static::resolveModelClass()::findOrFail($id);
    }

    public static function create(array $attributes = []): SiteVigilanceExceptionLogGroup
    {
        return static::resolveModelClass()::create($attributes);
    }

    public static function resolveModel(): SiteVigilanceExceptionLogGroup
    {
        $modelClass = static::resolveModelClass();

        return new $modelClass();
    }
}
