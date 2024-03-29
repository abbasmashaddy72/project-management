<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\Team;
use App\Models\ExceptionLogGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExceptionLogGroupFactory extends Factory
{
    protected $model = ExceptionLogGroup::class;

    public function definition(): array
    {
        return [
            'message' => $this->faker->text(),
            'type' => $this->faker->randomElement(['TypeError', 'Error', 'Exception', 'InvalidArgumentException']),
            'file' => '/' . implode('/', $this->faker->words($this->faker->numberBetween(0, 4))),
            'line' => $this->faker->randomNumber(2),
            'first_seen' => now(),
            'last_seen' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
            'site_id' => Site::pluck('id')->random(),
            'team_id' => Team::first()->id
        ];
    }
}
