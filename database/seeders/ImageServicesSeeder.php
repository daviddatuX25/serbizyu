<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\Category;
use App\Domains\Users\Models\User;
use App\Domains\Common\Models\Address;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Support\Facades\Storage;

class ImageServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates Service records from images in storage/app/public/images/{category}/
     */
    public function run(): void
    {
        // Get creator user
        $creator = User::role('admin')->first();
        
        if (!$creator) {
            $this->command->error('No admin user found. Please create an admin user first.');
            return;
        }

        // Get or create default address and workflow template
        $address = Address::first() ?? Address::create([
            'street' => 'Default',
            'city' => 'Default',
            'state' => 'Default',
            'postal_code' => '00000',
            'country' => 'Default',
        ]);

        $workflowTemplate = WorkflowTemplate::first() ?? WorkflowTemplate::create([
            'name' => 'Default Workflow',
            'description' => 'Default workflow template',
            'steps' => [],
        ]);

        // Scan image directories
        $imagesPath = storage_path('app/public/images');
        $categoryFolders = array_filter(
            scandir($imagesPath),
            fn($folder) => is_dir("$imagesPath/$folder") && !str_starts_with($folder, '.')
        );

        $serviceCount = 0;

        foreach ($categoryFolders as $categoryFolder) {
            $categoryPath = "$imagesPath/$categoryFolder";
            
            // Get images
            $images = array_filter(
                scandir($categoryPath),
                fn($file) => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), 
                    ['jpg', 'jpeg', 'png', 'gif', 'webp'])
                    && is_file("$categoryPath/$file")
            );

            if (empty($images)) {
                continue;
            }

            // Get or create category
            $category = Category::firstOrCreate(
                ['name' => str_replace('_', ' ', $categoryFolder)]
            );

            // Create service for each image
            foreach ($images as $image) {
                $imageName = pathinfo($image, PATHINFO_FILENAME);
                $title = ucwords(str_replace(['_', '-'], ' ', $imageName));
                
                $service = Service::create([
                    'title' => $title,
                    'description' => "Professional {$category->name} service - {$imageName}",
                    'price' => 0,
                    'pay_first' => true,
                    'category_id' => $category->id,
                    'creator_id' => $creator->id,
                    'address_id' => $address->id,
                    'workflow_template_id' => $workflowTemplate->id,
                ]);

                // Skip image attachment for now - Mediable needs proper setup
                // Images can be attached manually or through the service update interface
                $this->command->info("✅ {$service->title}");
                
                $serviceCount++;
            }
        }

        $this->command->info("\n✨ Created $serviceCount services from images!");
    }
}
