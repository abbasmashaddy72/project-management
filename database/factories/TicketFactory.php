<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name',
            'content',
            'owner_id',
            'responsible_id',
            'status_id',
            'project_id',
            'code',
            'order',
            'type_id',
            'priority_id',
            'estimation',
            'epic_id',
            'sprint_id'
        ];
    }
}
