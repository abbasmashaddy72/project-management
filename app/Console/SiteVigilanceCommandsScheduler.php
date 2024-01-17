<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CheckUptimeCommand;
use App\Console\Commands\DeleteOldExceptionCommand;
use App\Console\Commands\CheckSslCertificateCommand;

class SiteVigilanceCommandsScheduler
{
    public static function scheduleSiteVigilanceCommands(Schedule $schedule, string $uptimeCheckCron, string $sslCertificateCheckCron, ?string $deleteOldExceptionCron = null)
    {
        /** @var bool $uptimeCheckIsEnabled */
        $uptimeCheckIsEnabled = config('sitevigilance.uptime_check.enabled');
        /** @var bool $sslCheckIsEnabled */
        $sslCheckIsEnabled = config('sitevigilance.ssl_certificate_check.enabled');
        /** @var bool $deleteOldExceptionIsEnabled */
        $deleteOldExceptionIsEnabled = config('sitevigilance.exception_deletion.enabled');

        if ($uptimeCheckIsEnabled) {
            $schedule->command(CheckUptimeCommand::class)
                ->cron($uptimeCheckCron);
        }

        if ($sslCheckIsEnabled) {
            $schedule->command(CheckSslCertificateCommand::class)
                ->cron($sslCertificateCheckCron);
        }

        if ($deleteOldExceptionIsEnabled && $deleteOldExceptionCron) {
            $schedule->command(DeleteOldExceptionCommand::class)
                ->cron($deleteOldExceptionCron);
        }
    }
}
