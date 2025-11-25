<?php

namespace Tests\Feature;

use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\Service;
use App\Domains\Users\Models\User;
use App\Domains\Orders\Models\Order;
use App\Domains\Work\Models\WorkInstance;
use App\Domains\Work\Models\WorkInstanceStep;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_an_order_from_a_service_page(): void
    {
        // 1. Arrange
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $category = Category::factory()->create();
        $workflowTemplate = WorkflowTemplate::factory()->create();
        $service = Service::factory()->create([
            'category_id' => $category->id,
            'creator_id' => $seller->id,
            'workflow_template_id' => $workflowTemplate->id,
        ]);

        // 2. Act
        $response = $this->actingAs($buyer)->post(route('orders.store'), [
            'service_id' => $service->id,
        ]);

        // 3. Assert
        $this->assertDatabaseHas('orders', [
            'service_id' => $service->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $order = Order::where('service_id', $service->id)->where('buyer_id', $buyer->id)->first();

        $response->assertRedirect(route('orders.show', $order));

        $this->assertDatabaseHas('work_instances', [
            'order_id' => $order->id,
            'workflow_template_id' => $workflowTemplate->id,
        ]);

        $workInstance = WorkInstance::where('order_id', $order->id)->first();

        $this->assertDatabaseHas('work_instance_steps', [
            'work_instance_id' => $workInstance->id,
        ]);
    }
}
