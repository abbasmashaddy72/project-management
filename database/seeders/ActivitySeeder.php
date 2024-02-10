<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Programming',
            'description' => 'Programming related activities'
        ],
        [
            'name' => 'Testing',
            'description' => 'Testing related activities'
        ],
        [
            'name' => 'Learning',
            'description' => 'Activities related to learning and training'
        ],
        [
            'name' => 'Research',
            'description' => 'Activities related to research'
        ],
        [
            'name' => 'Other',
            'description' => 'Other activities'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $item) {
            // Merge team_id into $item
            $itemWithTeamId = array_merge($item, ['team_id' => 1]);

            Activity::firstOrCreate(
                $itemWithTeamId // Conditions and values
            );
        }
    }
}
