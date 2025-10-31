<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Listings\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Event Decorating Service',
            'Catering Service',
            'Home Repair & Maintenance',
            'Construction Service',
            'Programming & Tech',
            'Graphics & Design',
            'Digital Marketing',
            'Writing & Translation',
            'Video & Animation',
            'AI Services',
            'Music & Audio',
            'Business Consulting',
            'Photography',
            'Automotive Repair',
            'Landscaping & Gardening',
            'Cleaning Services',
            'Pet Care',
            'Tutoring & Education',
            'Health & Fitness',
            'Beauty Services',
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(['name' => $categoryName]);
        }
    }
}