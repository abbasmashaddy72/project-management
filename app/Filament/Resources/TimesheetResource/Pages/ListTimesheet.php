<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\TimesheetOverview;
use App\Filament\Resources\TimesheetResource;

class ListTimesheet extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    public static function getWidgets(): array
    {
        return [
            TimesheetOverview::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TimesheetOverview::class,
        ];
    }

    protected function getActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'this_week' => Tab::make(__('This Week'))->query(fn ($query) => $query->thisWeek()),
            'last_week' => Tab::make(__('Last Week'))->query(fn ($query) => $query->lastWeek()),
            'last_month' => Tab::make(__('Last Month'))->query(fn ($query) => $query->lastMonth()),
            'last_quarter' => Tab::make(__('Last Quarter'))->query(fn ($query) => $query->lastQuarter()),
            'this_year' => Tab::make(__('This Year'))->query(fn ($query) => $query->thisYear()),
            'all' => Tab::make(__('All')),
        ];
    }
}
