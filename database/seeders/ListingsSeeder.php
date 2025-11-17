<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Common\Models\Address;
use App\Domains\Users\Models\User;

class ListingsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::role('user')->get();
        $categories = Category::all();
        $workflows = WorkflowTemplate::where('is_public', true)->get();
        $addresses = Address::all();

        // Create Services
        $services = [
            [
                'title' => 'Arnel\'s Professional Plumbing',
                'description' => 'Expert plumbing services for all your needs. Leak repairs, pipe installations, and drain cleaning.',
                'price' => 200.00,
                'pay_first' => false,
                'category' => 'Home Repair & Maintenance',
                'workflow' => 'Basic Plumbing Service',
            ],
            [
                'title' => 'Quality House Painting',
                'description' => 'Professional interior and exterior painting. Color consultation included.',
                'price' => 150.00,
                'pay_first' => false,
                'category' => 'Home Repair & Maintenance',
                'workflow' => 'House Painting Service',
            ],
            [
                'title' => 'Maria\'s Catering Services',
                'description' => 'Delicious Filipino cuisine for all occasions. Birthday parties, weddings, and corporate events.',
                'price' => 250.00,
                'pay_first' => true,
                'category' => 'Catering Service',
                'workflow' => 'Event Catering Service',
            ],
            [
                'title' => 'Elite Event Decorations',
                'description' => 'Beautiful event setups for weddings, birthdays, and special occasions.',
                'price' => 3500.00,
                'pay_first' => true,
                'category' => 'Event Decorating Service',
                'workflow' => 'Event Decoration Service',
            ],
            [
                'title' => 'Quick Fix Electrical Services',
                'description' => 'Licensed electrician for repairs, installations, and troubleshooting.',
                'price' => 180.00,
                'pay_first' => false,
                'category' => 'Home Repair & Maintenance',
                'workflow' => 'Electrical Repair Service',
            ],
            [
                'title' => 'Reliable Construction Services',
                'description' => 'Small to medium construction projects. Renovations, extensions, and repairs.',
                'price' => 500.00,
                'pay_first' => true,
                'category' => 'Construction Service',
                'workflow' => 'Small Construction Project',
            ],
            [
                'title' => 'Budget-Friendly Catering',
                'description' => 'Affordable catering for small gatherings. Minimum 20 guests.',
                'price' => 150.00,
                'pay_first' => true,
                'category' => 'Catering Service',
                'workflow' => 'Event Catering Service',
            ],
            [
                'title' => 'Expert Tile Installation',
                'description' => 'Professional tile work for bathrooms, kitchens, and floors.',
                'price' => 220.00,
                'pay_first' => false,
                'category' => 'Construction Service',
                'workflow' => 'Small Construction Project',
            ],
        ];

        foreach ($services as $serviceData) {
            $category = $categories->firstWhere('name', $serviceData['category']);
            $workflow = $workflows->firstWhere('name', $serviceData['workflow']);
            $user = $users->random();
            $address = $addresses->random();

            if ($workflow) {
                Service::create([
                    'title' => $serviceData['title'],
                    'description' => $serviceData['description'],
                    'price' => $serviceData['price'],
                    'pay_first' => $serviceData['pay_first'],
                    'category_id' => $category->id,
                    'creator_id' => $user->id,
                    'workflow_template_id' => $workflow->id,
                    'address_id' => $address->id,
                ]);
            }
        }

        // Create Open Offers
        $openOffers = [
            [
                'title' => 'Looking for Catering Service',
                'description' => 'Need catering for 50 guests at a birthday party. Filipino cuisine preferred.',
                'budget' => 5000.00,
                'category' => 'Catering Service',
                'workflow' => 'Event Catering Service',
                'fulfilled' => false,
            ],
            [
                'title' => 'Need House Painting',
                'description' => '2-story house needs exterior painting. Labor only, we will provide materials.',
                'budget' => 12000.00,
                'category' => 'Home Repair & Maintenance',
                'workflow' => 'House Painting Service',
                'fulfilled' => false,
            ],
            [
                'title' => 'Event Decoration Needed',
                'description' => 'Wedding decoration for outdoor venue. Rustic theme, 100 guests.',
                'budget' => 15000.00,
                'category' => 'Event Decorating Service',
                'workflow' => 'Event Decoration Service',
                'fulfilled' => false,
            ],
            [
                'title' => 'Plumbing Repair Urgent',
                'description' => 'Major leak in bathroom. Need immediate repair.',
                'budget' => 3000.00,
                'category' => 'Home Repair & Maintenance',
                'workflow' => 'Basic Plumbing Service',
                'fulfilled' => false,
            ],
            [
                'title' => 'Small Bathroom Renovation',
                'description' => 'Looking for contractor to renovate small bathroom. Tiles and fixtures replacement.',
                'budget' => 25000.00,
                'category' => 'Construction Service',
                'workflow' => 'Small Construction Project',
                'fulfilled' => false,
            ],
            [
                'title' => 'Electrical Wiring Installation',
                'description' => 'New room needs complete electrical wiring. Licensed electrician required.',
                'budget' => 8000.00,
                'category' => 'Home Repair & Maintenance',
                'workflow' => 'Electrical Repair Service',
                'fulfilled' => false,
            ],
            [
                'title' => 'Birthday Party Catering',
                'description' => 'Simple menu for 30 people. Kids birthday party.',
                'budget' => 4000.00,
                'category' => 'Catering Service',
                'workflow' => 'Event Catering Service',
                'fulfilled' => true,
            ],
        ];

        foreach ($openOffers as $offerData) {
            $category = $categories->firstWhere('name', $offerData['category']);
            $workflow = $workflows->firstWhere('name', $offerData['workflow']);
            $user = $users->random();
            $address = $addresses->random();

            if ($workflow) {
                OpenOffer::create([
                    'title' => $offerData['title'],
                    'description' => $offerData['description'],
                    'budget' => $offerData['budget'],
                    'pay_first' => true,
                    'fulfilled' => $offerData['fulfilled'],
                    'category_id' => $category->id,
                    'creator_id' => $user->id,
                    'workflow_template_id' => $workflow->id,
                    'address_id' => $address->id,
                ]);
            }
        }
    }
}