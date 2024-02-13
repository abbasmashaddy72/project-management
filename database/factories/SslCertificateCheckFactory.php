<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\SslCertificateStatus;
use App\Models\SslCertificateCheck;

class SslCertificateCheckFactory extends Factory
{
    protected $model = SslCertificateCheck::class;

    public function definition(): array
    {
        return [
            'status' => SslCertificateStatus::NOT_YET_CHECKED,
            'issuer' => $this->faker->word(),
            'expiration_date' => now()->addDays(15),
            'check_failure_reason' => null,
            'site_id' => Site::pluck('id')->random(),
        ];
    }
}
