<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\Team;
use Illuminate\Support\Str;
use App\ValueObjects\RequestDuration;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'name' => $this->faker->words(2, true),
            'max_request_duration_ms' => RequestDuration::from(1000),
            'uptime_check_enabled' => true,
            'ssl_certificate_check_enabled' => true,
            'api_token' => Str::random(60),
            'cpu_limit' => $this->faker->randomNumber(),
            'ram_limit' => $this->faker->randomNumber(),
            'disk_limit' => $this->faker->randomNumber(),
            'team_id' => Team::first()->id,
        ];
    }
}
