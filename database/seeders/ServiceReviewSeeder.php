<?php

namespace Database\Seeders;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\ServiceReview;
use App\Domains\Users\Models\User;
use Illuminate\Database\Seeder;

class ServiceReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = Service::take(10)->get();
        $users = User::where('id', '!=', 1)->take(15)->get();

        if ($services->count() === 0 || $users->count() < 2) {
            return;
        }

        $ratings = [5, 5, 5, 4, 4, 4, 3, 3, 2, 1];
        $comments = [
            'Excellent service! Very satisfied with the work done.',
            'Professional and timely delivery. Highly recommended!',
            'Great quality and communication throughout.',
            'Good service, met all requirements.',
            'Decent work, but could have been better.',
            'Average service, nothing special.',
            'Had some issues with the final product.',
            'The service was okay but took longer than expected.',
            'Not very satisfied with the results.',
            'Outstanding work! Will definitely use again.',
            'Very experienced and knowledgeable service provider.',
            'Perfect! Exceeded expectations.',
            'Reliable and professional throughout the project.',
            'Good value for money.',
            'Fantastic experience from start to finish!',
        ];

        $tags = [
            ['professional', 'reliable', 'fast'],
            ['quality', 'excellent'],
            ['responsive', 'friendly'],
            ['skilled', 'professional'],
            ['good', 'decent'],
            ['average'],
            ['slow', 'complicated'],
            ['issues', 'delayed'],
            ['poor'],
            ['amazing', 'outstanding'],
        ];

        foreach ($services as $service) {
            for ($i = 0; $i < rand(5, 15); $i++) {
                $reviewer = $users->random();

                ServiceReview::create([
                    'reviewer_id' => $reviewer->id,
                    'service_id' => $service->id,
                    'order_id' => null,
                    'rating' => $ratings[array_rand($ratings)],
                    'title' => 'Service Review',
                    'comment' => $comments[array_rand($comments)],
                    'tags' => $tags[array_rand($tags)],
                    'helpful_count' => rand(0, 50),
                    'is_verified_purchase' => rand(0, 1),
                ]);
            }
        }
    }
}
