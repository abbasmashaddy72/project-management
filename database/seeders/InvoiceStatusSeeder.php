<?php

namespace Database\Seeders;

use App\Models\InvoiceStatus;
use Illuminate\Database\Seeder;

class InvoiceStatusSeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Draft',
            'description' => 'Invoice is in draft status and not yet finalized.',
            'is_default' => true
        ],
        [
            'name' => 'Sent',
            'description' => 'Invoice has been sent to the client but not yet paid.',
            'is_default' => false
        ],
        [
            'name' => 'Paid',
            'description' => 'Invoice has been paid by the client.',
            'is_default' => false
        ],
        [
            'name' => 'Overdue',
            'description' => 'Invoice is past the due date and payment is overdue.',
            'is_default' => false
        ],
        [
            'name' => 'Void',
            'description' => 'Invoice has been voided and is no longer valid.',
            'is_default' => false
        ],
        [
            'name' => 'Pending Approval',
            'description' => 'Invoice is pending approval before being sent.',
            'is_default' => false
        ],
        [
            'name' => 'Processing',
            'description' => 'Payment for the invoice is currently being processed.',
            'is_default' => false
        ],
        [
            'name' => 'Refunded',
            'description' => 'Payment for the invoice has been refunded.',
            'is_default' => false
        ],
        [
            'name' => 'Partially Paid',
            'description' => 'Only a partial payment has been received for the invoice.',
            'is_default' => false
        ],
        [
            'name' => 'Disputed',
            'description' => 'Invoice payment is under dispute.',
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
            InvoiceStatus::firstOrCreate($item);
        }
    }
}
