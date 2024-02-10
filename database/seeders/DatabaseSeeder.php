<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

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
                // TicketTypeSeeder::class,
                // TicketPrioritySeeder::class,
                // TicketStatusSeeder::class,
                // ActivitySeeder::class,
                // ProjectStatusSeeder::class,
            ]);
        }
    }
}
