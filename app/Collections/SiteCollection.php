<?php

namespace App\Collections;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\SiteVigilanceSite;
use App\Services\UptimeCheckService;
use App\Exceptions\InvalidPeriodException;
use App\Services\SslCertificateCheckService;

class SiteCollection extends Collection
{
    public function checkUptime(): void
    {
        /** @var array<string, Response> $responses */
        $responses = Http::pool(fn (Pool $pool) => $this->map(
            fn (SiteVigilanceSite $site) => $pool->as($site->url)->get($site->url)
        ));

        /** @var UptimeCheckService $uptimeCheckService */
        $uptimeCheckService = app(UptimeCheckService::class);

        $this->each(
            /**
             * @throws InvalidPeriodException
             */
            fn (SiteVigilanceSite $site) => $uptimeCheckService->check($site, $responses[$site->url->__toString()])
        );
    }

    public function checkSslCertificate(): void
    {
        /** @var SslCertificateCheckService $sslCertificateCheckService */
        $sslCertificateCheckService = app(SslCertificateCheckService::class);

        $this->each(fn (SiteVigilanceSite $site) => $sslCertificateCheckService->check($site));
    }
}
