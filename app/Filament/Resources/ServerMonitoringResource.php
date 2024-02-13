<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Resources\ServerMonitoringResource\Widgets\CpuLoadChart;
use App\Filament\Resources\ServerMonitoringResource\Widgets\DiskSpaceChart;
use App\Filament\Resources\ServerMonitoringResource\Widgets\MemoryLoadChart;
use App\Filament\Resources\ServerMonitoringResource\Pages\ServerMonitoringPage;

class ServerMonitoringResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Server Monitoring';

    protected static ?string $breadcrumb = '';

    protected static ?string $slug = 'server-monitoring';

    public static function getPages(): array
    {
        return [
            'index' => ServerMonitoringPage::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            CpuLoadChart::class,
            MemoryLoadChart::class,
            DiskSpaceChart::class,
        ];
    }
}
