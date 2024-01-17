<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\SiteRepository;
use App\Repositories\UptimeCheckRepository;

class CheckUptimeCommand extends Command
{
    protected $signature = 'check:uptime';

    protected $description = 'Check uptime for all registered sites';

    public function handle()
    {
        if (!UptimeCheckRepository::isEnabled()) {
            $this->info('[Uptime] This check is disabled. If you want to enable it, check the moonguard config file.');

            return;
        }

        $this->info('[Uptime] Starting check...');

        SiteRepository::query()
            ->whereUptimeCheckEnabled()
            ->whereIsNotOnMaintenance()
            ->with('uptimeCheck')
            ->get()
            ->checkUptime();

        $this->info('[Uptime] Uptime checked');
    }
}
