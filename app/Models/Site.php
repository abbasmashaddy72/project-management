<?php

namespace App\Models;

use Spatie\Url\Url;
use App\Traits\HasTenantScope;
use App\Casts\RequestDurationCast;
use App\Collections\SiteCollection;
use App\Contracts\SiteVigilanceSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\UptimeCheckRepository;
use App\Repositories\ExceptionLogRepository;
use App\Repositories\ExceptionLogGroupRepository;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Repositories\SslCertificateCheckRepository;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Site extends Model implements SiteVigilanceSite
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'url',
        'name',
        'uptime_check_enabled',
        'ssl_certificate_check_enabled',
        'max_request_duration_ms',
        'down_for_maintenance',
        'down_for_maintenance_at',
        'api_token_enabled',
        'api_token',
        'cpu_limit',
        'ram_limit',
        'disk_limit',
        'server_monitoring_notification_enabled',
    ];

    protected $casts = [
        'max_request_duration_ms' => RequestDurationCast::class,
        'down_for_maintenance_at' => 'immutable_datetime',
        'uptime_check_enabled' => 'boolean',
        'ssl_certificate_check_enabled' => 'boolean',
        'down_for_maintenance' => 'boolean',
        'api_token_enabled' => 'boolean',
        'server_monitoring_notification_enabled' => 'boolean',
    ];

    public function scopeWhereUptimeCheckEnabled(Builder $query): Builder
    {
        return $query->where('uptime_check_enabled', true);
    }

    public function scopeWhereSslCertificateCheckEnabled(Builder $query): Builder
    {
        return $query->where('ssl_certificate_check_enabled', true);
    }

    public function scopeWhereIsNotOnMaintenance(Builder $query): Builder
    {
        return $query->whereNull('down_for_maintenance_at');
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Url::fromString($this->attributes['url']),
        );
    }

    public function uptimeCheck(): HasOne
    {
        return $this->hasOne(UptimeCheckRepository::resolveModelClass());
    }

    public function sslCertificateCheck(): HasOne
    {
        return $this->hasOne(SslCertificateCheckRepository::resolveModelClass());
    }

    public function exceptionLogs(): HasManyThrough
    {
        return $this->hasManyThrough(
            ExceptionLogRepository::resolveModelClass(),
            ExceptionLogGroupRepository::resolveModelClass()
        );
    }

    public function exceptionLogGroups(): HasMany
    {
        return $this->hasMany(ExceptionLogGroupRepository::resolveModelClass());
    }

    public function latestServerMetric(): HasOne
    {
        return $this->hasOne(ServerMetric::class)->latestOfMany();
    }

    public function newCollection(array $models = []): SiteCollection
    {
        return new SiteCollection($models);
    }
}
