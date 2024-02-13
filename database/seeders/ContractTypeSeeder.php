<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private array $data = [
        [
            'name' => 'Hourly',
            'description' => 'Developer gets paid for hours worked, suitable for flexible project scopes.',
            'is_default' => true
        ],
        [
            'name' => 'Fixed',
            'description' => 'Developer agrees on a set price for the entire project with clear requirements.',
            'is_default' => false
        ],
        [
            'name' => 'Retainer',
            'description' => 'Client pays a regular fee for reserved developer hours in an ongoing relationship',
            'is_default' => false
        ],
        [
            'name' => 'Equity-based',
            'description' => 'Developer receives ownership shares in exchange for services, common in startups',
            'is_default' => false
        ],
        [
            'name' => 'Build-Operate-Transfer (BOT)',
            'description' => 'Developer builds and operates a project, transferring ownership to the client later',
            'is_default' => false
        ],
        [
            'name' => 'Cost-Plus',
            'description' => 'Client reimburses developer for costs plus an additional fee, good for transparency',
            'is_default' => false
        ],
        [
            'name' => 'Milestone-based',
            'description' => 'Payments tied to achieving project milestones, useful for larger projects',
            'is_default' => false
        ],
        [
            'name' => 'Subcontracting or Outsourcing',
            'description' => 'Developer outsources part of the project to another party with specialized skills',
            'is_default' => false
        ],
        [
            'name' => 'Non-Disclosure Agreement (NDA)',
            'description' => 'Legal contract ensuring confidentiality of project details',
            'is_default' => false
        ],
        [
            'name' => 'Joint Venture Agreement',
            'description' => 'Developers and clients form a partnership to share responsibilities and risks',
            'is_default' => false
        ],
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
            ContractType::firstOrCreate($item);
        }
    }
}
