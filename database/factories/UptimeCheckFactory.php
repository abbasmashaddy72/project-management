<?php

namespace Database\Factories;

use App\Models\Site;
use App\Enums\UptimeStatus;
use App\Models\UptimeCheck;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\ValueObjects\RequestDuration;

class UptimeCheckFactory extends Factory
{
    protected $model = UptimeCheck::class;

    public function definition(): array
    {
        return [
            'look_for_string' => '',
            'status' => UptimeStatus::NOT_YET_CHECKED,
            'check_failure_reason' => null,
            'check_times_failed_in_a_row' => 0,
            'status_last_change_date' => null,
            'last_check_date' => null,
            'check_failed_event_fired_on_date' => null,
            'request_duration_ms' => RequestDuration::from(null),
            'check_method' => 'get',
            'check_payload' => null,
            'check_additional_headers' => null,
            'check_response_checker' => null,
            'site_id' => Site::pluck('id')->random(),
        ];
    }
}
