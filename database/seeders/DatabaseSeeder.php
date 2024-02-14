<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Site;
use App\Models\UptimeCheck;
use App\Models\ExceptionLog;
use App\Models\ServerMetric;
use Illuminate\Database\Seeder;
use App\Models\ExceptionLogGroup;
use App\Models\SslCertificateCheck;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('app.env') == 'production') {
            $this->call([
                ShieldSeeder::class,
                UserSeeder::class,
                TicketTypeSeeder::class,
                TicketPrioritySeeder::class,
                TicketStatusSeeder::class,
                ActivitySeeder::class,
                ProjectStatusSeeder::class,
            ]);
        } else {
            $this->call([
                TeamSeeder::class,
                ShieldSeeder::class,
                UserSeeder::class,
                TicketTypeSeeder::class,
                TicketPrioritySeeder::class,
                TicketStatusSeeder::class,
                ActivitySeeder::class,
                ProjectStatusSeeder::class,
                ContractTypeSeeder::class,
                InvoiceStatusSeeder::class,
            ]);
            Site::factory(10)->create();
            UptimeCheck::factory(10)->create();
            SslCertificateCheck::factory(10)->create();
            ExceptionLogGroup::factory(10)->create();
            ExceptionLog::factory(10)->create();
            ServerMetric::factory(10)->create();
        }
    }
}
