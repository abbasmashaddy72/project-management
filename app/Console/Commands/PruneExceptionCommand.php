<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\ExceptionLogGroupRepository;

class PruneExceptionCommand extends Command
{
    protected $signature = 'exception:delete';

    protected $description = 'Prune old exceptions';

    public function handle()
    {
        if (!$this->isEnabled()) {
            $this->info('Exception prune is disabled. If you want to enable it, check the sitevigilance config file.');

            return;
        }

        $this->info('Starting deletion of old exceptions...');

        $time = $this->getExceptionAge();

        $this->info('Old exceptions deleted successfully!');

        $this->deleteOldExceptions($time);
    }

    public function isEnabled(): bool
    {
        return config('sitevigilance.prune_exception.enabled');
    }

    public static function getExceptionAge(): int
    {
        return config('sitevigilance.prune_exception.prune_exceptions_older_than_days');
    }

    public static function deleteOldExceptions(int $time): void
    {
        $exceptions = ExceptionLogGroupRepository::query()
            ->where('first_seen', '<', now()->subDays($time));

        $exceptions->delete();
    }
}
