<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Todo',
            'color' => '#6E6E6E', // Gray
            'is_default' => true,
            'order' => 1
        ],
        [
            'name' => 'In Progress',
            'color' => '#FF7F00', // Orange
            'is_default' => false,
            'order' => 2
        ],
        [
            'name' => 'Done',
            'color' => '#008000', // Green
            'is_default' => false,
            'order' => 3
        ],
        [
            'name' => 'Archived',
            'color' => '#FF0000', // Red
            'is_default' => false,
            'order' => 4
        ],
        [
            'name' => 'Blocked',
            'color' => '#FFD700', // Gold
            'is_default' => false,
            'order' => 5
        ],
        [
            'name' => 'Review',
            'color' => '#4169E1', // Royal Blue
            'is_default' => false,
            'order' => 6
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
            $item['team_id'] = 1;
            TicketStatus::firstOrCreate($item);
        }
    }
}
