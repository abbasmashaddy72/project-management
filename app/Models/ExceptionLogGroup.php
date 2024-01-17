<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Repositories\SiteRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Repositories\ExceptionLogRepository;
use App\Contracts\SiteVigilanceExceptionLogGroup;
use App\Database\Factories\ExceptionLogGroupFactory;

class ExceptionLogGroup extends Model implements SiteVigilanceExceptionLogGroup
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'message',
        'type',
        'file',
        'line',
        'first_seen',
        'last_seen',
    ];

    protected $casts = [
        'first_seen' => 'immutable_datetime',
        'last_seen' => 'immutable_datetime',
    ];

    public function exceptionLogs(): HasMany
    {
        return $this->hasMany(ExceptionLogRepository::resolveModelClass());
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteRepository::resolveModelClass());
    }

    protected static function newFactory(): Factory
    {
        return ExceptionLogGroupFactory::new();
    }
}
