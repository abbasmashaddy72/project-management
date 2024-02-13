<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        if (config('app.env') == 'production') {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@project.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('super_admin');
        } else {
            $users = User::factory()->count(1)->create();

            $superAdmin = $users->first();
            $superAdmin->update([
                'email' => 'superadmin@project.com',
            ]);
            $superAdmin->assignRole('super_admin');

            // Retrieve the existing team named 'Default'
            $team = Team::where('name', 'Default')->first();

            if ($team) {
                // Update the team's user_id with the first user's id
                $team->update(['user_id' => $superAdmin->id]);

                // Attach the first user to the existing team
                $team->members()->attach($superAdmin);
            }
        }
    }
}
