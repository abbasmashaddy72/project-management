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
            'color' => '#1D4ED8',
            'is_default' => true
        ],
        [
            'name' => 'Evolution',
            'icon' => 'heroicon-o-arrow-trending-up',
            'color' => '#15803D',
            'is_default' => false
        ],
        [
            'name' => 'Bug',
            'icon' => 'heroicon-o-bug-ant',
            'color' => '#DC2626',
            'is_default' => false
        ],
        [
            'name' => 'Feature Request',
            'icon' => 'heroicon-o-light-bulb',
            'color' => '#FFD700',
            'is_default' => false
        ],
        [
            'name' => 'Enhancement',
            'icon' => 'heroicon-o-star',
            'color' => '#F59E0B',
            'is_default' => false
        ],
        [
            'name' => 'Support Request',
            'icon' => 'heroicon-o-lifebuoy',
            'color' => '#059669',
            'is_default' => false
        ],
        [
            'name' => 'Documentation',
            'icon' => 'heroicon-o-document-text',
            'color' => '#4B5563',
            'is_default' => false
        ],
        [
            'name' => 'Design Task',
            'icon' => 'heroicon-o-pencil-square',
            'color' => '#9333EA',
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
