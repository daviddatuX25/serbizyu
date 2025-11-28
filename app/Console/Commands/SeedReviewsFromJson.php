<?php

namespace App\Console\Commands;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\ServiceReview;
use App\Domains\Users\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SeedReviewsFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-reviews-from-json {--reset : Delete existing reviews before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed service reviews from the seeder.json file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Reset reviews if requested
        if ($this->option('reset')) {
            $this->info('Deleting existing reviews...');
            DB::table('service_reviews')->delete();
        }

        // Load seeder.json
        $seederPath = base_path('docs/seeding/seeder.json');
        if (! File::exists($seederPath)) {
            $this->error("seeder.json not found at {$seederPath}");

            return self::FAILURE;
        }

        $seederData = json_decode(File::get($seederPath), true);
        if (! isset($seederData['images_and_categories'])) {
            $this->error('Invalid seeder.json structure');

            return self::FAILURE;
        }

        $reviewsCreated = 0;
        $users = User::whereNotNull('id')->get();

        if ($users->isEmpty()) {
            $this->warn('No users found in database. Skipping review seeding.');

            return self::SUCCESS;
        }

        // Process each category and listing
        foreach ($seederData['images_and_categories'] as $category => $listings) {
            foreach ($listings as $listing) {
                // Skip if no reviews
                if (empty($listing['reviews'])) {
                    continue;
                }

                // Find the service by name
                $service = Service::where('title', $listing['listing_name'] ?? null)->first();
                if (! $service) {
                    $this->warn("Service not found: {$listing['listing_name']}");

                    continue;
                }

                // Create reviews for this service
                foreach ($listing['reviews'] as $reviewData) {
                    // Get random user as reviewer
                    $reviewer = $users->random();

                    try {
                        ServiceReview::create([
                            'reviewer_id' => $reviewer->id,
                            'service_id' => $service->id,
                            'order_id' => null, // Can set to null or link to actual order if available
                            'rating' => (int) ($reviewData['rating'] ?? 5),
                            'title' => $reviewData['title'] ?? 'Good Service',
                            'comment' => $reviewData['comment'] ?? '',
                            'tags' => $reviewData['tags'] ?? [],
                            'helpful_count' => random_int(0, 15),
                            'is_verified_purchase' => (bool) ($reviewData['is_verified_purchase'] ?? true),
                        ]);
                        $reviewsCreated++;
                    } catch (\Exception $e) {
                        $this->warn("Failed to create review for {$listing['listing_name']}: {$e->getMessage()}");
                    }
                }
            }
        }

        $this->info("✅ Successfully seeded {$reviewsCreated} service reviews!");

        return self::SUCCESS;
    }
}
