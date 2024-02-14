<?php

namespace App\Filament\Widgets;

use Kenepa\MultiWidget\MultiWidget;
use Awcodes\Overlook\Widgets\OverlookWidget;

class OverallWidget  extends MultiWidget
{
    public array $widgets = [
        OverlookWidget::class,
        SiteStatsWidget::class,
    ];

    public function shouldPersistMultiWidgetTabsInSession(): bool
    {
        return true;
    }
}
