<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Users\Models\User;
use App\Domains\Orders\Models\Order;
use App\Domains\Work\Models\WorkInstance;

class CreatorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_dashboard_displays_orders_and_work_stats(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        // Create orders with addresses to satisfy service factory constraints
        $order1 = Order::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'pending'
        ]);
        $order2 = Order::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'completed'
        ]);

        // Create a work instance for the first order
        WorkInstance::create([
            'order_id' => $order1->id,
            'workflow_template_id' => null,
            'current_step_index' => 0,
            'status' => 'in_progress'
        ]);

        $response = $this->actingAs($seller)->get(route('creator.dashboard'));

        $response->assertStatus(200)
            ->assertViewHas('orders')
            ->assertViewHas('orderStats')
            ->assertViewHas('workInstances')
            ->assertViewHas('workStats');
    }
}
