<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CheckUptimeCommand;
use App\Console\Commands\PruneExceptionCommand;
use App\Console\Commands\PruneServerMetricCommand;
use App\Console\Commands\CheckSslCertificateCommand;

class SiteVigilanceCommandsScheduler
{
    public static function scheduleSiteVigilanceCommands(Schedule $schedule, string $uptimeCheckCron, string $sslCertificateCheckCron, ?string $pruneOldExceptionCron = null, ?string $pruneOldServerMetricsCron = null)
    {
        /** @var bool $uptimeCheckIsEnabled */
        $uptimeCheckIsEnabled = config('sitevigilance.uptime_check.enabled');
        /** @var bool $sslCheckIsEnabled */
        $sslCheckIsEnabled = config('sitevigilance.ssl_certificate_check.enabled');
        /** @var bool $pruneOldExceptionIsEnabled */
        $pruneOldExceptionIsEnabled = config('sitevigilance.prune_exception.enabled');
        /** @var bool $pruneOldServerMetricsIsEnabled */
        $pruneOldServerMetricsIsEnabled = config('sitevigilance.prune_server_monitoring.enabled');

        if ($uptimeCheckIsEnabled) {
            $schedule->command(CheckUptimeCommand::class)
                ->cron($uptimeCheckCron);
        }

        if ($sslCheckIsEnabled) {
            $schedule->command(CheckSslCertificateCommand::class)
                ->cron($sslCertificateCheckCron);
        }

        if ($pruneOldExceptionIsEnabled && $pruneOldExceptionCron) {
            $schedule->command(PruneExceptionCommand::class)
                ->cron($pruneOldExceptionCron);
        }

        if ($pruneOldServerMetricsIsEnabled && $pruneOldServerMetricsCron) {
            $schedule->command(PruneServerMetricCommand::class)
                ->cron($pruneOldServerMetricsCron);
        }
    }
}
