<?php

namespace App\Contracts;

use Spatie\Url\Url;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\ValueObjects\RequestDuration;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property string|int $id
 * @property string $name
 * @property string $api_token
 * @property RequestDuration $max_request_duration_ms
 * @property Url $url
 * @property SiteVigilanceUptimeCheck $uptimeCheck
 * @property SiteVigilanceSslCertificateCheck $sslCertificateCheck
 * @property SiteVigilanceExceptionLog $exceptionLogs
 * @property int $cpu_limit
 * @property int $ram_limit
 * @property int $disk_limit
 * @property bool $server_monitoring_notification_enabled
 */
interface SiteVigilanceSite
{
    public function scopeWhereUptimeCheckEnabled(Builder $query): Builder;

    public function scopeWhereSslCertificateCheckEnabled(Builder $query): Builder;

    public function scopeWhereIsNotOnMaintenance(Builder $query): Builder;

    public function uptimeCheck(): HasOne;

    public function sslCertificateCheck(): HasOne;

    public function exceptionLogs(): HasManyThrough;

    public function exceptionLogGroups(): HasMany;

    public function url(): Attribute;
}
