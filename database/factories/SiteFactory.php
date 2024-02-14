<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\Team;
use Illuminate\Support\Str;
use App\ValueObjects\RequestDuration;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        $realWebsites = [
            ['name' => 'Google', 'url' => 'http://www.google.com'],
            ['name' => 'GitHub', 'url' => 'https://www.github.com'],
            ['name' => 'Wikipedia', 'url' => 'https://www.wikipedia.org'],
            ['name' => 'Reddit', 'url' => 'https://www.reddit.com'],
            ['name' => 'Stack Overflow', 'url' => 'https://www.stackoverflow.com'],
            ['name' => 'Microsoft', 'url' => 'https://www.microsoft.com'],
            ['name' => 'Apple', 'url' => 'https://www.apple.com'],
            ['name' => 'Twitter', 'url' => 'https://www.twitter.com'],
            ['name' => 'LinkedIn', 'url' => 'https://www.linkedin.com'],
            ['name' => 'Instagram', 'url' => 'https://www.instagram.com'],
            ['name' => 'YouTube', 'url' => 'https://www.youtube.com'],
        ];

        $website = $this->faker->unique()->randomElement($realWebsites);

        return [
            'url' => $website['url'],
            'name' => $website['name'],
            'max_request_duration_ms' => RequestDuration::from(1000),
            'uptime_check_enabled' => true,
            'ssl_certificate_check_enabled' => true,
            'api_token' => Str::random(60),
            'cpu_limit' => $this->faker->randomNumber(),
            'ram_limit' => $this->faker->randomNumber(),
            'disk_limit' => $this->faker->randomNumber(),
            'team_id' => Team::first()->id,
        ];
    }
}
