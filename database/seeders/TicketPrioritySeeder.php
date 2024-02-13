<?php

namespace Database\Seeders;

use App\Models\TicketPriority;
use Illuminate\Database\Seeder;

class TicketPrioritySeeder extends Seeder
{
    private array $data = [
        ['name' => 'Low', 'color' => '#16A34A', 'is_default' => false], // Green
        ['name' => 'Normal', 'color' => '#2563EB', 'is_default' => true], // Blue
        ['name' => 'High', 'color' => '#DC2626', 'is_default' => false], // Red
        ['name' => 'Urgent', 'color' => '#FFA500', 'is_default' => false], // Orange
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
            TicketPriority::firstOrCreate($item);
        }
    }
}
