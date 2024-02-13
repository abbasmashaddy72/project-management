<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketPriority extends Model
{
    use HasFactory, SoftDeletes, HasTenantScope;

    protected $fillable = [
        'name',
        'color',
        'is_default'
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'priority_id', 'id')->withTrashed();
    }
}
