<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Timesheet\ActivitiesReport;
use App\Filament\Widgets\Timesheet\MonthlyReport;
use App\Filament\Widgets\Timesheet\WeeklyReport;
use Filament\Pages\Page;

class TimesheetDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $slug = 'timesheet-dashboard';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.timesheet-dashboard';

    protected function getColumns(): int | array
    {
        return 6;
    }

    public static function getNavigationLabel(): string
    {
        return __('Timesheet Dashboard');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MonthlyReport::class,
            ActivitiesReport::class,
            WeeklyReport::class
        ];
    }
}
