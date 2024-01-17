<?php

namespace App\Repositories;

use App\Contracts\SiteVigilanceSslCertificateCheck;

class SslCertificateCheckRepository extends ModelRepository
{
    protected static string $contract = SiteVigilanceSslCertificateCheck::class;

    protected static string $modelClassConfigKey = 'sitevigilance.ssl_certificate_check.model';

    public static function isEnabled(): bool
    {
        return config('sitevigilance.ssl_certificate_check.enabled');
    }

    public static function resolveModel(): SiteVigilanceSslCertificateCheck
    {
        $modelClass = static::resolveModelClass();

        return new $modelClass();
    }
}
