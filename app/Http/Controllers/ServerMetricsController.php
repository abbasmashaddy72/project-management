<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Site;
use App\Models\ServerMetric;
use App\Contracts\SiteVigilanceSite;
use App\Repositories\SiteRepository;
use App\Events\ServerMetricAlertEvent;

class ServerMetricsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var Site */
        $site = SiteRepository::query()
            ->where('api_token', $request->input('api_token'))
            ->first();

        abort_if(!$site, 403);

        $serverMetric = ServerMetric::create([
            'cpu_load' => $request->input('cpuLoad'),
            'memory_usage' => $request->input('memory'),
            'disk_usage' => json_encode($request->input('disk')),
            'site_id' => $site->id,
        ]);

        $cpuLoad = $request->input('cpuLoad');
        $memory = $request->input('memory');
        $diskUsagePercentage = $serverMetric->disk_usage['percentage'];

        $this->checkLimits($site, $cpuLoad, $memory, $diskUsagePercentage);

        return response()->json([
            'success' => true,
        ]);
    }

    private function checkLimits(SiteVigilanceSite $site, int $cpuLoad, int $memory, float $diskUsagePercentage)
    {
        $cpuLimit = $site->cpu_limit;
        $ramLimit = $site->ram_limit;
        $diskLimit = $site->disk_limit;

        if ($cpuLoad >= $cpuLimit) {
            $event = new ServerMetricAlertEvent($site, 'cpu', $cpuLoad, $site->server_monitoring_notification_enabled);
            event($event);
        }

        if ($memory >= $ramLimit) {
            $event = new ServerMetricAlertEvent($site, 'ram', $memory, $site->server_monitoring_notification_enabled);
            event($event);
        }

        if ($diskUsagePercentage >= $diskLimit) {
            $event = new ServerMetricAlertEvent($site, 'disk', $diskUsagePercentage, $site->server_monitoring_notification_enabled);
            event($event);
        }
    }
}
