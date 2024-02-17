<?php

namespace App\Filament\Pages;

use App\Models\Site;
use Filament\Pages\Page;
use App\Filament\Widgets\CpuLoadChart;
use App\Filament\Widgets\DiskSpaceChart;
use App\Filament\Widgets\MemoryLoadChart;
use Illuminate\Database\Eloquent\Collection;

class ServerMonitoring extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.server-monitoring';

    protected static ?string $navigationGroup = 'Site Vigilance';

    protected static ?int $navigationSort = 3;

    public bool $metricsAvailable = false;

    public bool $sitesAvailable = false;

    public bool $needsToSelect = true;

    public $siteId = null;

    protected $queryString = ['siteId'];

    public function mount(): void
    {
        $sites = Site::all();

        if (!$sites->isEmpty()) {
            $this->sitesAvailable = true;
        }
    }

    public function siteChanged(): void
    {
        if ($this->siteId !== '') {
            $this->dispatch('selected-site-changed', siteId: $this->siteId);
            $this->needsToSelect = false;
        }
    }

    protected function getViewData(): array
    {
        /** @var Collection */
        $sites = Site::all();

        return [
            'sites' => $sites,
        ];
    }

    protected function getFooterWidgets(): array
    {
        if (!$this->sitesAvailable || $this->needsToSelect) {
            return [];
        }

        return [
            CpuLoadChart::make(['siteId' => $this->siteId]),
            MemoryLoadChart::make(['siteId' => $this->siteId]),
            DiskSpaceChart::make(['siteId' => $this->siteId]),
        ];
    }
}
