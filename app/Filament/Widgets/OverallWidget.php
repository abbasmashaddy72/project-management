<?php

namespace App\Filament\Widgets;

use Kenepa\MultiWidget\MultiWidget;
use Awcodes\Overlook\Widgets\OverlookWidget;

class OverallWidget  extends MultiWidget
{
    public array $widgets = [
        OverlookWidget::class,
        SiteStatsWidget::class,
        FavoriteProjects::class,
        LatestActivities::class,
        LatestComments::class,
        LatestProjects::class,
        LatestTickets::class,
        TicketsByPriority::class,
        TicketsByType::class,
        TicketTimeLogged::class,
        UserTimeLogged::class,
    ];

    public function shouldPersistMultiWidgetTabsInSession(): bool
    {
        return true;
    }
}
