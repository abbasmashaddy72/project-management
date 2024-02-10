<?php

namespace App\Traits;

use App\Models\Team;
use App\Traits\TenantScope;

trait HasTenantScope
{
    public static function booted(): void
    {
        parent::booted();

        static::addGlobalScope(new TenantScope);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
