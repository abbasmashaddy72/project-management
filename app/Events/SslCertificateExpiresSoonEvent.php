<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Contracts\SiteVigilanceSslCertificateCheck;

class SslCertificateExpiresSoonEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public SiteVigilanceSslCertificateCheck $sslCertificateCheck)
    {
    }
}
