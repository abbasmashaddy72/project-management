<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    private array $data = [
        ['name' => 'Programming', 'description' => 'Coding, debugging, and software development.'],
        ['name' => 'Testing', 'description' => 'Executing test cases and ensuring software quality.'],
        ['name' => 'Learning', 'description' => 'Continuous education and skill development.'],
        ['name' => 'Research', 'description' => 'Gathering information to inform project decisions.'],
        ['name' => 'Migration', 'description' => 'Transferring data and applications to new environments.'],
        ['name' => 'Upgrade', 'description' => 'Improving existing systems with the latest technologies.'],
        ['name' => 'Backups', 'description' => 'Creating and managing data backups to prevent loss.'],
        ['name' => 'Enhancements', 'description' => 'Implementing additional features or improvements.'],
        ['name' => 'Patching', 'description' => 'Applying updates to address vulnerabilities or improve performance.'],
        ['name' => 'Security', 'description' => 'Implementing measures to protect systems and data.'],
        ['name' => 'Documentation', 'description' => 'Creating records and manuals detailing project processes.'],
        ['name' => 'Knowledge Transfer', 'description' => 'Sharing expertise and insights with team members.'],
        ['name' => 'User Feedback', 'description' => 'Collecting and incorporating user suggestions for system improvement.'],
        ['name' => 'Customization', 'description' => 'Adapting systems to meet specific user requirements.'],
        ['name' => 'Legal Guidance', 'description' => 'Seeking legal advice to ensure compliance and mitigate risks.'],
        ['name' => 'Other', 'description' => 'Miscellaneous activities not covered by specified categories.'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $item) {
            $item['team_id'] = 1;
            Activity::firstOrCreate($item);
        }
    }
}
