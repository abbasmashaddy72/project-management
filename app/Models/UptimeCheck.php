<?php

namespace App\Models;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Database\Eloquent\Model;
use App\Enums\UptimeStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Casts\RequestDurationCast;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Repositories\SiteRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\ValueObjects\RequestDuration;
use App\Contracts\SiteVigilanceUptimeCheck;
use App\Repositories\UptimeCheckRepository;

class UptimeCheck extends Model implements SiteVigilanceUptimeCheck
{
    use HasFactory;

    protected $fillable = [
        'look_for_string',
        'status',
        'check_failure_reason',
        'check_times_failed_in_a_row',
        'status_last_change_date',
        'last_check_date',
        'check_failed_event_fired_on_date',
        'request_duration_ms',
        'check_method',
        'check_payload',
        'check_additional_headers',
        'check_response_checker',
    ];

    protected $casts = [
        'status' => UptimeStatus::class,
        'status_last_change_date' => 'immutable_datetime',
        'last_check_date' => 'immutable_datetime',
        'check_failed_event_fired_on_date' => 'immutable_datetime',
        'request_duration_ms' => RequestDurationCast::class,
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteRepository::resolveModelClass());
    }

    public function saveSuccessfulCheck(Response $response): void
    {
        $this->status = UptimeStatus::UP;
        $this->check_failure_reason = '';
        $this->check_times_failed_in_a_row = 0;
        $this->last_check_date = now();
        $this->request_duration_ms = RequestDuration::from(
            round(data_get($response->handlerStats(), 'total_time_us') / 1000)
        );

        $this->save();
    }

    public function saveFailedCheck(Response|Exception $response): void
    {
        $this->status = UptimeStatus::DOWN;
        $this->check_times_failed_in_a_row++;
        $this->last_check_date = now();
        $this->check_failure_reason = $response instanceof Response ? $response->reason() : $response->getMessage();
        $this->request_duration_ms = RequestDuration::from(null);
        $this->save();
    }

    public function requestTookTooLong(): bool
    {
        /** @var RequestDuration $maxRequestDuration */
        $maxRequestDuration = $this->site->max_request_duration_ms;

        return $this->request_duration_ms->toRawMilliseconds() >= $maxRequestDuration->toRawMilliseconds();
    }

    public function wasFailing(): Attribute
    {
        return Attribute::make(
            get: fn () => !is_null($this->check_failed_event_fired_on_date),
        );
    }

    public function isEnabled(): Attribute
    {
        return Attribute::make(
            get: fn () => UptimeCheckRepository::isEnabled(),
        );
    }

    protected static function booted()
    {
        static::saving(function (self $uptime) {
            if (is_null($uptime->status_last_change_date)) {
                $uptime->status_last_change_date = now();

                return;
            }

            if ($uptime->getOriginal('status') != $uptime->status) {
                $uptime->status_last_change_date = now();
            }
        });
    }
}
