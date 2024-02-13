<?php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Task',
            'icon' => 'heroicon-o-document-check',
            'color' => '#00FFFF',
            'is_default' => true
        ],
        [
            'name' => 'Evolution',
            'icon' => 'heroicon-o-arrow-trending-up',
            'color' => '#008000',
            'is_default' => false
        ],
        [
            'name' => 'Bug',
            'icon' => 'heroicon-o-bug-ant',
            'color' => '#ff0000',
            'is_default' => false
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
            TicketType::firstOrCreate($item);
        }
    }
}
