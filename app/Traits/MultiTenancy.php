<?php

namespace App\Traits;

use App\Models\Team;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

trait MultiTenancy
{
    public static function bootMultiTenancy(): void
    {
        parent::booted();

        static::addGlobalScope(function (Builder $builder) {
            // Check if the model has a team_id attribute before applying the condition
            if (Schema::hasColumn((new self)->getTable(), 'team_id')) {
                $builder->where('team_id', Filament::getTenant()->id);
            }
        });
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // New method to conditionally apply global scope
    public function scopeWithoutMultiTenancy(Builder $builder): void
    {
        $builder->withoutGlobalScope(self::class);
    }
}
