<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    private array $data = [
        ['name' => 'Created', 'color' => '#3498db', 'is_default' => true], // Blue (Initiation)
        ['name' => 'In Progress', 'color' => '#2ecc71', 'is_default' => false], // Green (Active)
        ['name' => 'Archived', 'color' => '#f39c12', 'is_default' => false], // Orange (Not Active)
        ['name' => 'Finished', 'color' => '#27ae60', 'is_default' => false], // Green (Positive)
        ['name' => 'On Hold', 'color' => '#95a5a6', 'is_default' => false], // Grey (Paused)
        ['name' => 'Cancelled', 'color' => '#e74c3c', 'is_default' => false], // Red (Negative)
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $item) {
            $item['team_id'] = 1;
            ProjectStatus::firstOrCreate($item);
        }
    }
}
