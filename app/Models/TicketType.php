<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketType extends Model
{
    use HasFactory, SoftDeletes, HasTenantScope;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'is_default'
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'type_id', 'id')->withTrashed();
    }
}
