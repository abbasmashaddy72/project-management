<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\ExceptionLog;
use Illuminate\Support\Carbon;
use App\Models\ExceptionLogGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExceptionLogFactory extends Factory
{
    protected $model = ExceptionLog::class;

    public function definition(): array
    {
        $trace = [
            [
                'file' => 'path/to/file1.php',
                'line' => 42,
                'function' => 'exampleFunction',
                'class' => 'ExampleClass',
            ],
            [
                'file' => 'path/to/file2.php',
                'line' => 23,
                'function' => 'anotherFunction',
                'class' => 'AnotherClass',
            ],
        ];

        return [
            'exception_log_group_id' => fn () => ExceptionLogGroup::factory(),
            'message' => $this->faker->word(),
            'type' => $this->faker->word(),
            'file' => $this->faker->word(),
            'line' => $this->faker->randomNumber(),
            'trace' => $trace,
            'request' => $this->faker->word,
            'thrown_at' => Carbon::now(),
            'team_id' => Team::first()->id
        ];
    }
}
