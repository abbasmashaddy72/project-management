<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\BarChartWidget;

class UserTimeLogged extends BarChartWidget
{
    protected static ?string $heading = 'Time logged by users';
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '300px';
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 3
    ];

    protected function getData(): array
    {
        $query = User::query();
        $query->has('hours');
        $query->limit(10);
        return [
            'datasets' => [
                [
                    'label' => __('Total time logged (hours)'),
                    'data' => $query->get()->pluck('totalLoggedInHours')->toArray(),
                    'backgroundColor' => [
                        'rgba(54, 162, 235, .6)'
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, .8)'
                    ],
                ],
            ],
            'labels' => $query->get()->pluck('name')->toArray(),
        ];
    }
}
