<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Created',
            'color' => '#4CAF50', // Green
            'is_default' => true
        ],
        [
            'name' => 'In Progress',
            'color' => '#2196F3', // Blue
            'is_default' => false
        ],
        [
            'name' => 'Archived',
            'color' => '#FFC107', // Amber
            'is_default' => false
        ],
        [
            'name' => 'Finished',
            'color' => '#FF5722', // Deep Orange
            'is_default' => false
        ]
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

            ProjectStatus::firstOrCreate(
                $itemWithTeamId // Conditions and values
            );
        }
    }
}
