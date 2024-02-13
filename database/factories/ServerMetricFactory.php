<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\ServerMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerMetricFactory extends Factory
{
    protected $model = ServerMetric::class;

    public function definition(): array
    {
        return [
            'cpu_load' => $this->faker->randomFloat(2, 0, 100),
            'memory_usage' => $this->faker->randomFloat(2, 0, 100),
            'disk_usage' => $this->faker->randomFloat(2, 0, 100),
            'site_id' => Site::pluck('id')->random(),
            'created_at' => $this->faker->dateTimeBetween('-6 days', 'now'),
        ];
    }
}
