<?php

namespace App\Filament\Widgets;

use App\Contracts\TimesheetService;
use App\Models\TicketHour;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TimesheetOverview extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected function getCards(): array
    {
        $timesheetsThisWeek = TicketHour::query()->thisWeek()->sum('value');
        $timesheetsThisMonth = TicketHour::query()->thisMonth()->sum('value');
        $timesheetsThisQuater = TicketHour::query()->thisQuarter()->sum('value');

        return [
            Stat::make(__('This Week'), (new TimesheetService)->decimalToTime($timesheetsThisWeek)),
            Stat::make(__('This Month'), (new TimesheetService)->decimalToTime($timesheetsThisMonth)),
            Stat::make(__('This Quarter'), (new TimesheetService)->decimalToTime($timesheetsThisQuater)),
        ];
    }
}
