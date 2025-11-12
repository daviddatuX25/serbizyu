<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Listings\Models\WorkCatalog;

class WorkCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $workCatalogs = [
            [
                'name' => 'Initial Consultation',
                'description' => 'Meet with client to discuss requirements and expectations',
                'config' => [
                    'estimated_duration' => '1-2 hours',
                    'location' => 'client_location',
                    'requirements' => ['notepad', 'measuring_tools']
                ]
            ],
            [
                'name' => 'Site Inspection',
                'description' => 'Visit and assess the work location',
                'config' => [
                    'estimated_duration' => '30 minutes - 1 hour',
                    'requirements' => ['camera', 'measuring_tape']
                ]
            ],
            [
                'name' => 'Material Purchase',
                'description' => 'Acquire necessary materials and supplies',
                'config' => [
                    'estimated_duration' => '2-4 hours',
                    'payment_type' => 'client_advance'
                ]
            ],
            [
                'name' => 'On-site Work',
                'description' => 'Perform the actual service work',
                'config' => [
                    'estimated_duration' => 'varies',
                    'location' => 'client_location'
                ]
            ],
            [
                'name' => 'Quality Check',
                'description' => 'Review completed work for quality assurance',
                'config' => [
                    'estimated_duration' => '30 minutes',
                    'requires_client' => true
                ]
            ],
            [
                'name' => 'Final Cleanup',
                'description' => 'Clean up work area and remove debris',
                'config' => [
                    'estimated_duration' => '30 minutes - 1 hour',
                ]
            ],
            [
                'name' => 'Client Walkthrough',
                'description' => 'Present finished work to client for approval',
                'config' => [
                    'estimated_duration' => '15-30 minutes',
                    'requires_client' => true
                ]
            ],
            [
                'name' => 'Payment Collection',
                'description' => 'Collect payment from client',
                'config' => [
                    'payment_methods' => ['cash', 'gcash', 'bank_transfer']
                ]
            ],
            [
                'name' => 'Documentation',
                'description' => 'Take photos and document completed work',
                'config' => [
                    'estimated_duration' => '15 minutes',
                    'requirements' => ['camera', 'smartphone']
                ]
            ],
            [
                'name' => 'Follow-up Visit',
                'description' => 'Return to check on completed work',
                'config' => [
                    'estimated_duration' => '30 minutes',
                    'typical_timeframe' => '1-2 weeks after completion'
                ]
            ],
        ];

        foreach ($workCatalogs as $catalog) {
            $catalog['config'] = json_encode($catalog['config']);
            WorkCatalog::create($catalog);
        }
    }
}