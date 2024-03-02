<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\HtmlString;

class FavoriteProjects extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isDiscovered = false;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 6
    ];

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getCards(): array
    {
        $favoriteProjects = auth()->user()->favoriteProjects;
        $cards = [];
        foreach ($favoriteProjects as $project) {
            $ticketsCount = $project->tickets()->count();
            $contributorsCount = $project->contributors->count();
            $cards[] = Card::make('', new HtmlString('
                    <div class="flex items-center gap-2 -mt-2 text-lg">
                        <div style=\'background-image: url("' . $project->cover . '")\'
                             class="w-8 h-8 bg-center bg-no-repeat bg-cover"></div>
                        <span>' . $project->name . '</span>
                    </div>
                '))
                ->color('success')
                ->extraAttributes([
                    'class' => 'hover:shadow-lg'
                ])
                ->description(new HtmlString('
                        <div class="flex items-center w-full gap-2 mt-2 font-normal text-gray-500">'
                    . $ticketsCount
                    . ' '
                    . __($ticketsCount > 1 ? 'Tickets' : 'Ticket')
                    . ' '
                    . __('and')
                    . ' '
                    . $contributorsCount
                    . ' '
                    . __($contributorsCount > 1 ? 'Contributors' : 'Contributor')
                    . '</div>
                        <div class="flex items-center w-full gap-2 mt-2 text-xs">
                            <a class="text-primary-400 hover:text-primary-500 hover:cursor-pointer"
                               href="' . route('filament.admin.resources.projects.view', ['record' => $project->id, 'tenant' => \Filament\Facades\Filament::getTenant()?->id]) . '">
                                ' . __('View details') . '
                            </a>
                            <span class="text-gray-300">|</span>
                            <a class="text-primary-400 hover:text-primary-500 hover:cursor-pointer"
                               href="' . route('filament.admin.pages.kanban.{project?}', ['project' => $project->id, 'tenant' => \Filament\Facades\Filament::getTenant()?->id]) . '">
                                ' . __('Tickets') . '
                            </a>
                        </div>
                    '));
        }
        return $cards;
    }
}
