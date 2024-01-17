<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Contracts\MoonGuardUser;

class UserRepository extends ModelRepository
{
    protected static string $contract = MoonGuardUser::class;

    protected static string $modelClassConfigKey = 'moonguard.user.model';

    public static function all(): Collection
    {
        return self::resolveModelClass()::all();
    }

    public static function resolveModel(): MoonGuardUser
    {
        $modelClass = static::resolveModelClass();

        return new $modelClass();
    }
}
