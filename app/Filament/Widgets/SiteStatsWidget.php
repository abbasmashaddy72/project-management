<?php

namespace App\Filament\Widgets;

use App\Repositories\SiteRepository;

class SiteStatsWidget extends PollableWidget
{
    protected static ?string $pollingInterval = '10s';

    protected static string $view = 'widgets.site-stats-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'sites' => SiteRepository::query()
                ->with(['uptimeCheck', 'sslCertificateCheck', 'latestServerMetric'])
                ->withCount(['exceptionLogs' => function ($query) {
                    $query->where('status', 'unresolved');
                }])
                ->get(),
        ];
    }
}
