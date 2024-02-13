<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Http\Client\Response;
use App\ValueObjects\Period;
use App\Contracts\SiteVigilanceSite;
use App\Events\UptimeCheckFailedEvent;
use App\Contracts\SiteVigilanceUptimeCheck;
use App\Events\UptimeCheckRecoveredEvent;
use App\Exceptions\InvalidPeriodException;
use App\Repositories\UptimeCheckRepository;
use App\Events\RequestTookLongerThanMaxDurationEvent;

class UptimeCheckService
{
    protected SiteVigilanceUptimeCheck $uptimeCheck;

    /**
     * @throws InvalidPeriodException
     */
    public function check(SiteVigilanceSite $site, Response|Exception $response): void
    {
        if (!$site->uptimeCheck) {
            $this->uptimeCheck = UptimeCheckRepository::resolveModel();
            $this->uptimeCheck->site_id = $site->id;
            $this->uptimeCheck->status_last_change_date = now();
        } else {
            $this->uptimeCheck = $site->uptimeCheck;
        }

        if ($response instanceof Response) {
            $this->handleResponse($response);

            return;
        }

        $this->handleException($response);
    }

    /**
     * @throws InvalidPeriodException
     */
    protected function handleResponse(Response $response): void
    {
        if (!$response->ok()) {

            $this->handleResponseError($response);
            return;
        }
        $this->uptimeCheck->saveSuccessfulCheck($response);

        if ($this->uptimeCheck->was_failing) {
            $this->notifyUptimeWasRecovered();
        }

        if ($this->uptimeCheck->requestTookTooLong()) {
            $this->notifyRequestTookLongerThanMaxRequestDuration();
        }

        return;
    }

    /**
     * @throws InvalidPeriodException
     */
    protected function handleResponseError(Response $response): void
    {
        $this->uptimeCheck->saveFailedCheck($response);

        if ($this->shouldNotifyAboutUptimeFailing()) {
            $this->updateFailedEventWasFiredAt(now());
            $this->notifyAboutDowntime();
        }
    }

    /**
     * @throws InvalidPeriodException
     */
    protected function handleException(Exception $exception): void
    {
        $this->uptimeCheck->saveFailedCheck($exception);

        if ($this->shouldNotifyAboutUptimeFailing()) {
            $this->updateFailedEventWasFiredAt(now());
            $this->notifyAboutDowntime();
        }
    }

    /**
     * @param Carbon|null $date
     *
     * @return void
     */
    protected function updateFailedEventWasFiredAt(?Carbon $date): void
    {
        $this->uptimeCheck->check_failed_event_fired_on_date = $date;
        $this->uptimeCheck->save();
        $this->uptimeCheck->refresh();
    }

    /**
     * @throws InvalidPeriodException
     *
     * @return void
     */
    protected function notifyUptimeWasRecovered(): void
    {
        $lastStatusChangeDate = $this->uptimeCheck->status_last_change_date ? clone $this->uptimeCheck->status_last_change_date : null;
        $downtimePeriod = new Period($lastStatusChangeDate, now());

        event(new UptimeCheckRecoveredEvent($this->uptimeCheck, $downtimePeriod));

        $this->updateFailedEventWasFiredAt(null);
    }

    /**
     * @return void
     */
    protected function notifyRequestTookLongerThanMaxRequestDuration(): void
    {
        $maxRequestDuration = $this->uptimeCheck->site->max_request_duration_ms;
        event(new RequestTookLongerThanMaxDurationEvent($this->uptimeCheck, $maxRequestDuration));
    }

    /**
     * @throws InvalidPeriodException
     *
     * @return void
     */
    protected function notifyAboutDowntime(): void
    {
        $downtimePeriod = new Period($this->uptimeCheck->status_last_change_date, $this->uptimeCheck->last_check_date);
        event(new UptimeCheckFailedEvent($this->uptimeCheck, $downtimePeriod));
    }

    protected function shouldNotifyAboutUptimeFailing(): bool
    {
        if ($this->uptimeCheck->check_times_failed_in_a_row === config('sitevigilance.uptime_check.notify_failed_check_after_consecutive_failures')) {
            return true;
        }

        if (!$this->uptimeCheck->was_failing) {
            return false;
        }

        if ($this->uptimeCheck->check_failed_event_fired_on_date->diffInMinutes() >= config('sitevigilance.uptime_check.resend_uptime_check_failed_notification_every_minutes')) {
            return true;
        }

        return false;
    }
}
