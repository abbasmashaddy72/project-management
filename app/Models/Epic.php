<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Epic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'epic_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Epic::class, 'parent_id', 'id');
    }

    public function sprint(): HasOne
    {
        return $this->hasOne(Sprint::class, 'epic_id', 'id');
    }
}
