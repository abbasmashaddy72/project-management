<?php

namespace App\Traits;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereHas('team', function ($builder) use ($model) {
            $builder->where("{$model->getTable()}.team_id", Filament::getTenant()->id ?? 1);
        });
    }
}
