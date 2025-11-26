<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Create roles first
            RolesSeeder::class,
            
            // 2. Create users (they need roles)
            UserSeeder::class,
            
            // 3. Create addresses and link to users
            AddressSeeder::class,
            
            // 4. Create categories
            CategorySeeder::class,
            
            // 5. Create work catalogs (building blocks for workflows)
            WorkCatalogSeeder::class,
            
            // 6. Create workflow templates and work templates
            WorkflowAndWorkTemplateSeeder::class,
            
            // 7. Create services and open offers (need all previous data)
            ListingsSeeder::class,
            
            // 8. Optional: Create some bids on open offers
            // OpenOfferBidSeeder::class,
            
            // 9. Create reviews for users and services
            UserReviewSeeder::class,
            ServiceReviewSeeder::class,
        ]);
    }
}
