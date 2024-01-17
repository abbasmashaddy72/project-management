<?php

namespace App\Repositories;

use App\Contracts\SiteVigilanceSite;

class SiteRepository extends ModelRepository
{
    protected static string $contract = SiteVigilanceSite::class;

    protected static string $modelClassConfigKey = 'sitevigilance.site.model';

    public static function findOrFail(string|int $id): SiteVigilanceSite
    {
        return static::resolveModelClass()::findOrFail($id);
    }

    public static function resolveModel(): SiteVigilanceSite
    {
        $modelClass = static::resolveModelClass();

        return new $modelClass();
    }
}
