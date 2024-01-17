<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ExceptionLogStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Repositories\SiteRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use App\Contracts\SiteVigilanceExceptionLog;
use App\Database\Factories\ExceptionLogFactory;
use App\Repositories\ExceptionLogGroupRepository;

class ExceptionLog extends Model implements SiteVigilanceExceptionLog
{
    use HasFactory;

    protected $fillable = [
        'message',
        'type',
        'file',
        'status',
        'trace',
        'request',
        'line',
        'thrown_at',
        'exception_log_group_id',
    ];

    protected $casts = [
        'status' => ExceptionLogStatus::class,
        'trace' => 'array',
        'request' => 'array',
        'thrown_at' => 'immutable_datetime',
    ];

    public function site(): HasOneThrough
    {
        return $this->hasOneThrough(
            SiteRepository::resolveModelClass(),
            ExceptionLogGroupRepository::resolveModelClass()
        );
    }

    public function exceptionLogGroup(): BelongsTo
    {
        return $this->belongsTo(ExceptionLogGroupRepository::resolveModelClass());
    }

    protected static function newFactory(): Factory
    {
        return ExceptionLogFactory::new();
    }
}
