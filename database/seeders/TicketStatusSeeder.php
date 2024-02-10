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
            'name' => 'In progress',
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
            TicketStatus::firstOrCreate($item);
        }
    }
}
