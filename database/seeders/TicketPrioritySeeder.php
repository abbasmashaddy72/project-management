<?php

namespace Database\Seeders;

use App\Models\TicketPriority;
use Illuminate\Database\Seeder;

class TicketPrioritySeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Low',
            'color' => '#4CAF50', // Green
            'is_default' => false
        ],
        [
            'name' => 'Normal',
            'color' => '#2196F3', // Blue
            'is_default' => true
        ],
        [
            'name' => 'High',
            'color' => '#FFC107', // Amber
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
            TicketPriority::firstOrCreate($item);
        }
    }
}
