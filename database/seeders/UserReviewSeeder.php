<?php

namespace Database\Seeders;

use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserReview;
use Illuminate\Database\Seeder;

class UserReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('id', '!=', 1)->take(10)->get();

        if ($users->count() < 2) {
            return;
        }

        $ratings = [5, 5, 4, 4, 3, 2, 1];
        $comments = [
            'Great seller, very reliable and professional!',
            'Excellent service and fast communication.',
            'Good quality work, will hire again.',
            'Decent service, met expectations.',
            'Average experience, could be better.',
            'Had some issues with the service.',
            'Not satisfied with the outcome.',
            'Highly professional and talented.',
            'Exceeded my expectations!',
            'Very responsive and helpful.',
        ];

        $tags = [
            ['professional', 'reliable'],
            ['fast', 'quality'],
            ['excellent', 'responsive'],
            ['good', 'friendly'],
            ['average'],
            ['slow'],
            ['poor'],
            ['amazing', 'skilled'],
            ['outstanding'],
            ['professional', 'friendly'],
        ];

        foreach ($users as $reviewee) {
            for ($i = 0; $i < rand(3, 8); $i++) {
                $reviewer = $users->where('id', '!=', $reviewee->id)->random();

                UserReview::create([
                    'reviewer_id' => $reviewer->id,
                    'reviewee_id' => $reviewee->id,
                    'rating' => $ratings[array_rand($ratings)],
                    'title' => 'User Review',
                    'comment' => $comments[array_rand($comments)],
                    'tags' => $tags[array_rand($tags)],
                    'helpful_count' => rand(0, 20),
                ]);
            }
        }
    }
}
