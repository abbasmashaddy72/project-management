<?php

namespace App\Filament\Resources\ServerMonitoringResource\Widgets;

use Flowframe\Trend\Trend;
use Filament\Support\RawJs;
use Livewire\Attributes\On;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use App\Models\ServerMetric;

class CpuLoadChart extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';

    public string | int | null $siteId = null;

    protected static ?string $maxHeight = '300px';

    public ?string $filter = 'hour';

    #[On('selected-site-changed')]
    public function updateSiteId($siteId): void
    {
        $this->siteId = $siteId;
        $this->getData();
    }

    public function getDescription(): ?string
    {
        return 'Time in chart displayed in UTC.';
    }

    protected function getData(): array
    {
        if ($this->siteId) {
            $filter = $this->filter;
            $query = ServerMetric::where('site_id', $this->siteId);

            match ($filter) {
                'hour' => $data = Trend::query($query)
                    ->between(start: now()->subHour(), end: now())
                    ->perMinute()
                    ->average('cpu_load'),

                'day' => $data = Trend::query($query)
                    ->between(start: now()->subDay(), end: now())
                    ->perHour()
                    ->average('cpu_load'),

                'week' => $data = Trend::query($query)
                    ->between(start: now()->subWeek(), end: now())
                    ->perDay()
                    ->average('cpu_load')
            };

            $chartData = [
                'datasets' => [
                    [
                        'label' => 'CPU Load',
                        'data' => $data->map(fn (TrendValue $value) => $value->aggregate == 0 ? null : $value->aggregate),
                        'spanGaps' => true,
                        'borderColor' => '#9BD0F5',
                        'fill' => true,
                    ],
                ],
                'labels' => $data->map(function (TrendValue $value) use ($filter) {
                    if ($filter === 'hour') {
                        [$date, $time] = explode(' ', $value->date);

                        return substr($time, 0, 5);
                    } elseif ($filter === 'day') {
                        [$date, $time] = explode(' ', $value->date);

                        return $time;
                    } else {
                        return $value->date;
                    }
                }),
            ];

            return $chartData;
        }

        return [];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'hour' => 'Last Hour',
            'day' => 'Last Day',
            'week' => 'Last Week',
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            responsive: true,
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        callback: (value) => value + '%',
                    },
                },
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += Math.round(context.raw) + '%';
                            return label;
                        }
                    }
                }
            }
        }
        JS);
    }
}
