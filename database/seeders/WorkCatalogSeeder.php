<?php

namespace Database\Seeders;

use App\Domains\Listings\Models\WorkCatalog;
use Illuminate\Database\Seeder;

class WorkCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Generic, reusable work catalog items (category_id is nullable - can be used across categories)
        $workCatalogs = [
            [
                'name' => 'Initial Consultation',
                'description' => 'Meet with client to discuss requirements and expectations',
                'category_id' => null,
            ],
            [
                'name' => 'Site Inspection',
                'description' => 'Visit and assess the work location',
                'category_id' => null,
            ],
            [
                'name' => 'Material/Resource Preparation',
                'description' => 'Acquire and prepare necessary materials and supplies',
                'category_id' => null,
            ],
            [
                'name' => 'Work Execution',
                'description' => 'Perform the actual service work',
                'category_id' => null,
            ],
            [
                'name' => 'Quality Check',
                'description' => 'Review completed work for quality assurance',
                'category_id' => null,
            ],
            [
                'name' => 'Client Approval',
                'description' => 'Present finished work to client for approval',
                'category_id' => null,
            ],
            [
                'name' => 'Documentation',
                'description' => 'Take photos and document completed work',
                'category_id' => null,
            ],
            [
                'name' => 'Payment Processing',
                'description' => 'Collect payment from client',
                'category_id' => null,
            ],
        ];

        foreach ($workCatalogs as $catalog) {
            WorkCatalog::firstOrCreate(
                ['name' => $catalog['name']],
                $catalog
            );
        }
    }
}
