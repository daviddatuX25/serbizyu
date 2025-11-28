<?php

namespace App\Console\Commands;

use App\Domains\Common\Models\Address;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Users\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Plank\Mediable\MediaUploader;

class SeedCategoryImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:category-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed services and open offers from organized random_services categories with media attachments';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('🚀 Starting category image seeding...');

        $categoriesJsonPath = base_path('categories.json');

        if (! file_exists($categoriesJsonPath)) {
            $this->error('❌ categories.json not found. Run organize-images.php first.');

            return;
        }

        $categories = json_decode(file_get_contents($categoriesJsonPath), true);

        if (! $categories) {
            $this->error('❌ Failed to parse categories.json');

            return;
        }

        // Get a user to act as creator - use first user or create one
        $creator = User::first();
        if (! $creator) {
            $this->error('❌ No users found in database. Run migrations and seeders first.');

            return;
        }

        // Get an address for services/offers
        $address = Address::first();
        if (! $address) {
            $this->error('❌ No addresses found in database. Run migrations and seeders first.');

            return;
        }

        // Get a default category or create one
        $category = Category::first();
        if (! $category) {
            $category = Category::create(['name' => 'General Services']);
        }

        // Get a workflow template
        $workflow = WorkflowTemplate::first();
        if (! $workflow) {
            $this->error('❌ No workflow templates found in database. Run migrations and seeders first.');

            return;
        }

        $this->info('📂 Found '.count($categories).' categories');
        $this->info("👤 Using user: {$creator->firstname} {$creator->lastname} (ID: {$creator->id})");
        $this->info("📍 Using address: {$address->street_address} (ID: {$address->id})");
        $this->info("🏷️  Using category: {$category->name} (ID: {$category->id})");
        $this->info("📋 Using workflow: {$workflow->name} (ID: {$workflow->id})");

        $serviceCount = 0;
        $openOfferCount = 0;
        $totalImages = 0;

        foreach ($categories as $categoryName => $categoryData) {
            $this->line("\n📦 Processing category: <fg=cyan>$categoryName</>");

            $servicesDir = base_path("random_services/$categoryName/services");
            $openOffersDir = base_path("random_services/$categoryName/openoffers");

            // Process services
            if (is_dir($servicesDir)) {
                $servicesImages = $this->getImageFiles($servicesDir);
                if (! empty($servicesImages)) {
                    $this->line('   └─ Services: '.count($servicesImages).' images');
                    $serviceCount += $this->seedServices($categoryName, $servicesImages, $creator, $address, $category, $workflow);
                    $totalImages += count($servicesImages);
                }
            }

            // Process open offers
            if (is_dir($openOffersDir)) {
                $openOffersImages = $this->getImageFiles($openOffersDir);
                if (! empty($openOffersImages)) {
                    $this->line('   └─ Open Offers: '.count($openOffersImages).' images');
                    $openOfferCount += $this->seedOpenOffers($categoryName, $openOffersImages, $creator, $address, $category, $workflow);
                    $totalImages += count($openOffersImages);
                }
            }
        }

        $this->newLine();
        $this->info('✅ Seeding complete!');
        $this->line("   📊 Services created: <fg=green>$serviceCount</>");
        $this->line("   📊 Open Offers created: <fg=green>$openOfferCount</>");
        $this->line("   📊 Total images processed: <fg=green>$totalImages</>");
    }

    /**
     * Get all image files from a directory
     */
    private function getImageFiles(string $directory): array
    {
        if (! is_dir($directory)) {
            return [];
        }

        $images = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic'];

        foreach (scandir($directory) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $directory.DIRECTORY_SEPARATOR.$file;

            if (is_file($filePath)) {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExtensions)) {
                    $images[] = $filePath;
                }
            }
        }

        return $images;
    }

    /**
     * Seed services from images
     */
    private function seedServices(string $categoryName, array $imagePaths, User $creator, Address $address, Category $category, WorkflowTemplate $workflow): int
    {
        $created = 0;

        foreach ($imagePaths as $imagePath) {
            try {
                $fileName = basename($imagePath);
                $title = $this->generateServiceTitle($categoryName, $fileName);

                $service = Service::create([
                    'title' => $title,
                    'description' => "Professional {$categoryName} service with image verification",
                    'price' => rand(25, 500),
                    'creator_id' => $creator->id,
                    'address_id' => $address->id,
                    'category_id' => $category->id,
                    'workflow_template_id' => $workflow->id,
                    'pay_first' => false,
                ]);

                $this->attachImageToService($service, $imagePath);
                $created++;

                $this->line("      ✓ Service created: $title");
            } catch (\Exception $e) {
                $this->warn("      ⚠ Failed to seed service from $fileName: ".$e->getMessage());
            }
        }

        return $created;
    }

    /**
     * Seed open offers from images
     */
    private function seedOpenOffers(string $categoryName, array $imagePaths, User $creator, Address $address, Category $category, WorkflowTemplate $workflow): int
    {
        $created = 0;

        foreach ($imagePaths as $imagePath) {
            try {
                $fileName = basename($imagePath);
                $title = $this->generateOpenOfferTitle($categoryName, $fileName);

                $openOffer = OpenOffer::create([
                    'title' => $title,
                    'description' => "Limited offer in {$categoryName} category",
                    'budget' => rand(50, 1000),
                    'creator_id' => $creator->id,
                    'address_id' => $address->id,
                    'category_id' => $category->id,
                    'workflow_template_id' => $workflow->id,
                    'pay_first' => false,
                ]);

                $this->attachImageToOpenOffer($openOffer, $imagePath);
                $created++;

                $this->line("      ✓ Open Offer created: $title");
            } catch (\Exception $e) {
                $this->warn("      ⚠ Failed to seed open offer from $fileName: ".$e->getMessage());
            }
        }

        return $created;
    }

    /**
     * Attach image to Service using Mediable
     * Mimics ServiceService::handleUploadedFiles pattern
     */
    private function attachImageToService(Service $service, string $imagePath): void
    {
        try {
            $uploader = app(\Plank\Mediable\MediaUploader::class);

            // Upload using MediaUploader (matching ServiceService pattern)
            $media = $uploader->fromSource($imagePath)
                ->toDestination('public', 'services/images')
                ->upload();

            // Attach with gallery tag (matching ServiceService pattern)
            $service->attachMedia($media, 'gallery');
        } catch (\Exception $e) {
            throw new \Exception('Failed to attach image to service: '.$e->getMessage());
        }
    }

    /**
     * Attach image to OpenOffer using Mediable
     * Mimics OpenOfferService::handleUploadedFiles pattern
     */
    private function attachImageToOpenOffer(OpenOffer $openOffer, string $imagePath): void
    {
        try {
            $uploader = app(\Plank\Mediable\MediaUploader::class);

            // Upload using MediaUploader (matching OpenOfferService pattern)
            $media = $uploader->fromSource($imagePath)
                ->toDirectory('open-offers')
                ->upload();

            // Attach with images tag (matching OpenOfferService pattern)
            $openOffer->attachMedia($media, 'images');
        } catch (\Exception $e) {
            throw new \Exception('Failed to attach image to open offer: '.$e->getMessage());
        }
    }

    /**
     * Generate a service title
     */
    private function generateServiceTitle(string $categoryName, string $fileName): string
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $title = Str::title(str_replace('_', ' ', $baseName));

        if (strlen($title) > 80) {
            $title = substr($title, 0, 80).'...';
        }

        return trim($title) ?: $categoryName.' Service';
    }

    /**
     * Generate an open offer title
     */
    private function generateOpenOfferTitle(string $categoryName, string $fileName): string
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $title = 'Special: '.Str::title(str_replace('_', ' ', $baseName));

        if (strlen($title) > 80) {
            $title = substr($title, 0, 80).'...';
        }

        return trim($title) ?: 'Special '.$categoryName.' Offer';
    }
}
