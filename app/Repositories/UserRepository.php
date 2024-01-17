<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Contracts\SiteVigilanceUser;

class UserRepository extends ModelRepository
{
    protected static string $contract = SiteVigilanceUser::class;

    protected static string $modelClassConfigKey = 'sitevigilance.user.model';

    public static function all(): Collection
    {
        return self::resolveModelClass()::all();
    }

    public static function resolveModel(): SiteVigilanceUser
    {
        $modelClass = static::resolveModelClass();

        return new $modelClass();
    }
}
