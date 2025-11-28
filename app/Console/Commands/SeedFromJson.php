<?php

namespace App\Console\Commands;

use App\Domains\Common\Models\Address;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\WorkCatalog;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkTemplate;
use App\Domains\Users\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Plank\Mediable\MediaUploader;

class SeedFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:from-json {--file=docs/seeding/seeder.json} {--dry-run} {--resume}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed listings and images from seeder.json configuration file';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $fileName = $this->option('file');
        $dryRun = $this->option('dry-run');
        $resume = $this->option('resume');

        $this->info('🚀 Processing '.$fileName.'...');

        // Load seeder.json - resolve path relative to project root if not absolute
        $filePath = $fileName;
        if (! file_exists($filePath)) {
            $filePath = base_path($fileName);
        }

        if (! file_exists($filePath)) {
            $this->error("❌ File not found: $fileName (checked: {$filePath})");

            return;
        }

        $seederData = json_decode(file_get_contents($filePath), true);
        if (! $seederData) {
            $this->error("❌ Invalid JSON in $filePath");

            return;
        }

        // Validate required structure - support both old and new formats
        $hasImages = isset($seederData['images_and_categories']);
        $hasDirPath = isset($seederData['dir_path']);
        $hasNewCatalogs = isset($seederData['category_catalogs']) || isset($seederData['global_catalogs']);
        $hasOldCatalogs = isset($seederData['work_catalogs']);

        if (! $hasImages || (! $hasDirPath && ! $hasNewCatalogs && ! $hasOldCatalogs)) {
            $this->error('❌ Invalid seeder.json structure. Missing required fields.');

            return;
        }

        $dirPath = $seederData['dir_path'] ?? base_path();
        $settings = $seederData['settings'] ?? [];
        $autoCreateCategories = $settings['auto_create_categories'] ?? true;
        $skipDuplicates = $settings['skip_duplicates'] ?? true;

        // PHASE 1: Create WorkCatalogs first
        // Support both old format (work_catalogs with category_name) and new format (category_catalogs + global_catalogs)

        // NEW FORMAT: category_catalogs (scoped to categories)
        if (isset($seederData['category_catalogs']) && is_array($seederData['category_catalogs'])) {
            $this->info("\n📋 Creating category-scoped work catalogs...");
            foreach ($seederData['category_catalogs'] as $categoryName => $catalogs) {
                // Get or create the category first
                $category = $this->getOrCreateCategory($categoryName, $autoCreateCategories);
                if (! $category) {
                    $this->warn("   ⚠ Category not found or could not be created: {$categoryName}");

                    continue;
                }

                foreach ($catalogs as $catalogData) {
                    try {
                        if (isset($catalogData['name'])) {
                            WorkCatalog::firstOrCreate(
                                ['name' => $catalogData['name']],
                                [
                                    'description' => $catalogData['description'] ?? '',
                                    'category_id' => $category->id,
                                ]
                            );
                            $this->line("   ✓ WorkCatalog: {$catalogData['name']} → Category: {$categoryName}");
                        }
                    } catch (\Exception $e) {
                        $this->warn("   ⚠ Failed to create WorkCatalog: {$e->getMessage()}");
                    }
                }
            }
        }

        // NEW FORMAT: global_catalogs (no category relationship)
        if (isset($seederData['global_catalogs']) && is_array($seederData['global_catalogs'])) {
            $this->info("\n📋 Creating global work catalogs...");
            foreach ($seederData['global_catalogs'] as $catalogData) {
                try {
                    if (isset($catalogData['name'])) {
                        WorkCatalog::firstOrCreate(
                            ['name' => $catalogData['name']],
                            [
                                'description' => $catalogData['description'] ?? '',
                                'category_id' => null,
                            ]
                        );
                        $this->line("   ✓ WorkCatalog: {$catalogData['name']} (generic)");
                    }
                } catch (\Exception $e) {
                    $this->warn("   ⚠ Failed to create WorkCatalog: {$e->getMessage()}");
                }
            }
        }

        // OLD FORMAT: work_catalogs with category_name field (for backwards compatibility)
        if (isset($seederData['work_catalogs']) && is_array($seederData['work_catalogs'])) {
            $this->info("\n📋 Creating work catalogs...");
            foreach ($seederData['work_catalogs'] as $catalogData) {
                try {
                    if (isset($catalogData['name'])) {
                        // Resolve category_id if category name is provided
                        $categoryId = null;
                        if (isset($catalogData['category_name'])) {
                            $category = Category::where('name', $catalogData['category_name'])->first();
                            $categoryId = $category?->id;
                        }

                        WorkCatalog::firstOrCreate(
                            ['name' => $catalogData['name']],
                            [
                                'description' => $catalogData['description'] ?? '',
                                'category_id' => $categoryId,
                            ]
                        );
                        $catalogLabel = $categoryId
                            ? "{$catalogData['name']} (Category ID: {$categoryId})"
                            : "{$catalogData['name']} (generic)";
                        $this->line("   ✓ WorkCatalog: {$catalogLabel}");
                    }
                } catch (\Exception $e) {
                    $this->warn("   ⚠ Failed to create WorkCatalog: {$e->getMessage()}");
                }
            }
        }

        // PHASE 2: Create Workflows and their WorkTemplates (steps)
        // Collect all workflows from listings
        $workflowsToCreate = [];
        $categoriesData = $seederData['images_and_categories'];

        foreach ($categoriesData as $categoryListings) {
            foreach ($categoryListings as $listing) {
                if (isset($listing['workflow_name'])) {
                    $workflowName = $listing['workflow_name'];
                    if (! isset($workflowsToCreate[$workflowName])) {
                        $workflowsToCreate[$workflowName] = $listing;
                    }
                }
            }
        }

        $this->info("\n📋 Creating workflows and work templates...");
        foreach ($workflowsToCreate as $workflowName => $listingData) {
            try {
                $workflow = $this->getWorkflow($workflowName);

                // Create WorkTemplates (steps) for this workflow
                // Steps are objects with: name (required), duration_minutes (optional), work_catalog_ref (optional)
                if (isset($listingData['workflow_steps']) && is_array($listingData['workflow_steps'])) {
                    foreach ($listingData['workflow_steps'] as $stepIndex => $stepData) {
                        try {
                            // Extract step details (support both string and object formats)
                            $stepName = is_array($stepData) ? ($stepData['name'] ?? $stepData) : $stepData;
                            $durationMinutes = is_array($stepData) ? ($stepData['duration_minutes'] ?? null) : null;
                            $workCatalogRef = is_array($stepData) ? ($stepData['work_catalog_ref'] ?? null) : null;

                            // Create or update WorkTemplate
                            $workTemplate = WorkTemplate::firstOrCreate(
                                [
                                    'workflow_template_id' => $workflow->id,
                                    'name' => $stepName,
                                ],
                                [
                                    'description' => $stepName,
                                    'order' => $stepIndex + 1,
                                    'duration_minutes' => $durationMinutes,
                                    'work_catalog_id' => null, // Will be set below if ref exists
                                ]
                            );

                            // Update duration if it was provided and not yet set
                            if ($durationMinutes && ! $workTemplate->duration_minutes) {
                                $workTemplate->update(['duration_minutes' => $durationMinutes]);
                            }

                            // Link to WorkCatalog if work_catalog_ref is specified
                            if ($workCatalogRef) {
                                $workCatalog = WorkCatalog::where('name', $workCatalogRef)->first();
                                if ($workCatalog) {
                                    if (! $workTemplate->work_catalog_id) {
                                        $workTemplate->update(['work_catalog_id' => $workCatalog->id]);
                                    }
                                    $durationLabel = $durationMinutes ? " ({$durationMinutes}min)" : '';
                                    $this->line("      ✓ Step: {$stepName}{$durationLabel} → catalog: {$workCatalogRef}");
                                } else {
                                    $this->warn("      ⚠ Work catalog not found: {$workCatalogRef}");
                                }
                            } else {
                                $durationLabel = $durationMinutes ? " ({$durationMinutes}min)" : '';
                                $this->line("      ✓ Step: {$stepName}{$durationLabel} (custom, no catalog)");
                            }
                        } catch (\Exception $e) {
                            $this->warn("   ⚠ Failed to create WorkTemplate step '{$stepName}': {$e->getMessage()}");
                        }
                    }
                }

                $this->line("   ✓ Workflow: <fg=green>{$workflowName}</> with ".count($listingData['workflow_steps'] ?? []).' steps');
            } catch (\Exception $e) {
                $this->warn("   ⚠ Failed to create Workflow '{$workflowName}': {$e->getMessage()}");
            }
        }

        // Count total listings
        $categoriesData = $seederData['images_and_categories'];
        $totalListings = 0;
        foreach ($categoriesData as $categoryListings) {
            $totalListings += count($categoryListings);
        }

        $this->info('✅ Found '.count($categoriesData).' categories');
        $this->info("📸 Total listings: $totalListings");

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $serviceCount = 0;
        $offerCount = 0;
        $imageCount = 0;
        $errors = [];

        // Process each category
        foreach ($categoriesData as $categoryName => $listings) {
            $this->line("\n📦 Processing category: <fg=cyan>$categoryName</>");

            foreach ($listings as $listing) {
                try {
                    // Validate required fields
                    $validation = $this->validateListing($listing);
                    if (! $validation['valid']) {
                        $errors[] = "Listing skipped: {$validation['error']}";
                        $this->warn("   ⚠ {$validation['error']}");

                        continue;
                    }

                    // Check for duplicates
                    if ($skipDuplicates) {
                        $existingCount = Service::where('title', $listing['listing_name'])->count() +
                                        OpenOffer::where('title', $listing['listing_name'])->count();
                        if ($existingCount > 0) {
                            $this->warn("   ⏭ Skipping duplicate: {$listing['listing_name']}");

                            continue;
                        }
                    }

                    // Get or create category (use category name from outer key)
                    $category = $this->getOrCreateCategory($categoryName, $autoCreateCategories);
                    if (! $category) {
                        throw new \Exception("Category not found: {$categoryName}");
                    }

                    // Get workflow
                    $workflow = $this->getWorkflow($listing['workflow_name'] ?? null);
                    if (! $workflow) {
                        throw new \Exception("Could not create or find workflow: {$listing['workflow_name']}");
                    }
                    // Get random user with 'user' role
                    $creator = $this->getRandomUser();

                    // Get random address for geographic diversity
                    $address = Address::inRandomOrder()->first();
                    if (! $address) {
                        throw new \Exception('No addresses found in database');
                    }

                    // Handle images - support both old format (single 'relative_path') and new format (array of 'images')
                    $imagesToProcess = [];
                    if (isset($listing['images']) && is_array($listing['images'])) {
                        // New format: array of images
                        $imagesToProcess = $listing['images'];
                    } elseif (isset($listing['relative_path'])) {
                        // Old format: single image
                        $imagesToProcess = [['filename' => basename($listing['relative_path']), 'relative_path' => $listing['relative_path']]];
                    }

                    if (empty($imagesToProcess)) {
                        throw new \Exception("No images found for: {$listing['listing_name']}");
                    }

                    // Get first image path for listing creation
                    $firstImage = $imagesToProcess[0];
                    $imagePath = $firstImage['relative_path'] ?? null;
                    if (! file_exists($imagePath)) {
                        // Try from listing_seeder directory
                        $imagePath = base_path('listing_seeder'.DIRECTORY_SEPARATOR.$firstImage['relative_path']);
                    }
                    if (! file_exists($imagePath)) {
                        throw new \Exception("Image not found: {$firstImage['relative_path']}");
                    }

                    // Create listing with first image
                    if ($listing['listing_type'] === 'service') {
                        if (! $dryRun) {
                            $service = $this->createService(
                                $listing,
                                $creator,
                                $category,
                                $workflow,
                                $address,
                                $imagePath
                            );

                            // Attach remaining images to the service
                            foreach (array_slice($imagesToProcess, 1) as $additionalImage) {
                                $additionalImagePath = $additionalImage['relative_path'] ?? null;
                                if (! file_exists($additionalImagePath)) {
                                    $additionalImagePath = base_path('listing_seeder'.DIRECTORY_SEPARATOR.$additionalImage['relative_path']);
                                }
                                if (file_exists($additionalImagePath)) {
                                    try {
                                        $this->attachImage($service, $additionalImagePath, 'gallery');
                                    } catch (\Exception $e) {
                                        $this->warn("      ⚠ Could not attach image {$additionalImage['filename']}: {$e->getMessage()}");
                                    }
                                }
                            }
                        }
                        $serviceCount++;
                        $imageCount += count($imagesToProcess);
                        $imageCountForService = count($imagesToProcess);
                        $this->line("   ✓ Service: {$listing['listing_name']} ($imageCountForService images)");
                    } else {
                        if (! $dryRun) {
                            $offer = $this->createOpenOffer(
                                $listing,
                                $creator,
                                $category,
                                $workflow,
                                $address,
                                $imagePath
                            );

                            // Attach remaining images to the offer
                            foreach (array_slice($imagesToProcess, 1) as $additionalImage) {
                                $additionalImagePath = $additionalImage['relative_path'] ?? null;
                                if (! file_exists($additionalImagePath)) {
                                    $additionalImagePath = base_path('listing_seeder'.DIRECTORY_SEPARATOR.$additionalImage['relative_path']);
                                }
                                if (file_exists($additionalImagePath)) {
                                    try {
                                        $this->attachImage($offer, $additionalImagePath, 'gallery');
                                    } catch (\Exception $e) {
                                        $this->warn("      ⚠ Could not attach image {$additionalImage['filename']}: {$e->getMessage()}");
                                    }
                                }
                            }
                        }
                        $offerCount++;
                        $imageCount += count($imagesToProcess);
                        $imageCountForOffer = count($imagesToProcess);
                        $this->line("   ✓ Offer: {$listing['listing_name']} ($imageCountForOffer images)");
                    }
                } catch (\Exception $e) {
                    $error = "Failed {$listing['listing_type']}: {$listing['listing_name']} - {$e->getMessage()}";
                    $errors[] = $error;
                    $this->warn("   ❌ $error");
                    Log::error('Seeding error: '.$error, [
                        'trace' => $e->getTraceAsString(),
                        'listing_name' => $listing['listing_name'] ?? 'unknown',
                        'images_count' => isset($listing['images']) ? count($listing['images']) : 'N/A',
                    ]);
                }
            }
        }

        // Summary
        $this->newLine();
        $this->info('✅ Seeding complete!');
        if ($dryRun) {
            $this->warn('(DRY RUN - no changes made)');
        }
        $this->line("   📊 Services created: <fg=green>$serviceCount</>");
        $this->line("   📊 Offers created: <fg=green>$offerCount</>");
        $this->line("   📸 Images uploaded: <fg=green>$imageCount</>");

        if (! empty($errors)) {
            $this->warn('   ⚠ Errors: '.count($errors));
            foreach ($errors as $error) {
                $this->warn("      - $error");
            }
        }
    }

    /**
     * Validate listing entry has all required fields
     */
    private function validateListing(array $listing): array
    {
        // Support both old format (relative_path) and new format (images array)
        $required = ['listing_name', 'listing_description', 'workflow_name', 'workflow_steps', 'listing_type', 'listing_price_or_budget'];

        foreach ($required as $field) {
            if (! isset($listing[$field]) || (is_string($listing[$field]) && empty($listing[$field]))) {
                return [
                    'valid' => false,
                    'error' => "Missing required field: $field",
                ];
            }
        }

        // Check for either 'images' or 'relative_path'
        if (! isset($listing['images']) && ! isset($listing['relative_path'])) {
            return [
                'valid' => false,
                'error' => "Missing either 'images' array or 'relative_path'",
            ];
        }

        if (! in_array($listing['listing_type'], ['service', 'offer'])) {
            return ['valid' => false, 'error' => 'listing_type must be service or offer'];
        }

        return ['valid' => true];
    }

    /**
     * Get or create category
     */
    private function getOrCreateCategory(string $categoryName, bool $autoCreate): ?Category
    {
        $category = Category::where('name', $categoryName)->first();

        if (! $category && $autoCreate) {
            $category = Category::create(['name' => $categoryName]);
        }

        return $category;
    }

    /**
     * Get or create workflow by name
     */
    private function getWorkflow(?string $workflowName): ?WorkflowTemplate
    {
        if (! $workflowName) {
            return WorkflowTemplate::first();
        }

        // Try to find existing workflow
        $workflow = WorkflowTemplate::where('name', $workflowName)->first();

        if ($workflow) {
            return $workflow;
        }

        // Create new workflow if it doesn't exist
        $this->line("   📋 Creating new workflow: <fg=yellow>$workflowName</>");

        try {
            // Get a user with 'user' role to set as creator
            $creator = $this->getRandomUser();

            $workflow = WorkflowTemplate::create([
                'name' => $workflowName,
                'description' => "Auto-generated workflow from seeder.json: $workflowName",
                'creator_id' => $creator->id,
                'is_public' => true,
            ]);

            $this->line("   ✓ Workflow created: $workflowName (ID: {$workflow->id})");

            return $workflow;
        } catch (\Exception $e) {
            $this->warn("   ❌ Failed to create workflow: {$e->getMessage()}");

            return WorkflowTemplate::first();
        }
    }

    /**
     * Get random user with 'user' role (not admin/moderator)
     */
    private function getRandomUser(): User
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->inRandomOrder()->first();

        if (! $user) {
            // Fallback: create a user if none exist
            $nameParts = ['seeded', time()];
            $user = User::create([
                'firstname' => $nameParts[0],
                'lastname' => 'User',
                'email' => 'seeded-'.time().'@localhost',
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('user');
            $this->line("   Created new user: {$user->email}");
        }

        return $user;
    }

    /**
     * Get or create user by email (legacy, for backward compatibility)
     */
    private function getOrCreateUser(string $email): User
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            $nameParts = explode('@', $email);
            $name = ucfirst($nameParts[0]);
            $user = User::create([
                'firstname' => $name,
                'lastname' => 'User',
                'email' => $email,
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('user');
        }

        return $user;
    }

    /**
     * Create service with media
     */
    private function createService(
        array $listing,
        User $creator,
        Category $category,
        WorkflowTemplate $workflow,
        Address $address,
        string $imagePath
    ): Service {
        // Only extract the fields we need - avoid passing arrays that might cause issues
        $serviceData = [
            'title' => $listing['listing_name'] ?? '',
            'description' => $listing['listing_description'] ?? '',
            'price' => (int) ($listing['listing_price_or_budget'] ?? 0),
            'creator_id' => $creator->id,
            'category_id' => $category->id,
            'workflow_template_id' => $workflow->id,
            'address_id' => $address->id,
            'pay_first' => false,
        ];

        $service = Service::create($serviceData);

        $this->attachImage($service, $imagePath, 'gallery');

        return $service;
    }

    /**
     * Create open offer with media
     */
    private function createOpenOffer(
        array $listing,
        User $creator,
        Category $category,
        WorkflowTemplate $workflow,
        Address $address,
        string $imagePath
    ): OpenOffer {
        // Only extract the fields we need - avoid passing arrays that might cause issues
        $offerData = [
            'title' => $listing['listing_name'] ?? '',
            'description' => $listing['listing_description'] ?? '',
            'budget' => (int) ($listing['listing_price_or_budget'] ?? 0),
            'creator_id' => $creator->id,
            'category_id' => $category->id,
            'workflow_template_id' => $workflow->id,
            'address_id' => $address->id,
            'pay_first' => false,
        ];

        $openOffer = OpenOffer::create($offerData);

        $this->attachImage($openOffer, $imagePath, 'gallery');

        return $openOffer;
    }

    /**
     * Attach image to model using Mediable
     */
    private function attachImage($model, string $imagePath, string $tag): void
    {
        try {
            $uploader = app(MediaUploader::class);

            $media = $uploader->fromSource($imagePath)
                ->toDestination('public', ($model instanceof Service ? 'services/images' : 'open-offers/images'))
                ->upload();

            $model->attachMedia($media, $tag);
        } catch (\Exception $e) {
            Log::error("Failed to attach image for {$model->title}: ".$e->getMessage());
            throw $e;
        }
    }
}
