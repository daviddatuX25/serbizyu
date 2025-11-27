<?php

namespace Tests\Feature\Domains\Payments;

use App\Domains\Listings\Models\Service;
use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSettlementWorkCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected $buyer;

    protected $seller;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create();
        $this->seller = User::factory()->create();
        $this->service = Service::factory()->for($this->seller, 'creator')->create();
    }

    /**
     * Test: Order is not eligible for review if payment not settled
     */
    public function test_order_not_eligible_for_review_if_payment_not_settled(): void
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::IN_PROGRESS->value,
            'payment_status' => 'unpaid',
        ]);

        $this->assertFalse($order->isEligibleForReview());
    }

    /**
     * Test: Order is eligible for review when payment settled AND work complete
     */
    public function test_order_eligible_for_review_when_payment_settled_and_work_complete(): void
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::COMPLETED->value,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->assertTrue($order->isEligibleForReview());
    }

    /**
     * Test: Order with paid payment but in_progress status not eligible
     */
    public function test_order_not_eligible_if_status_not_completed(): void
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::IN_PROGRESS->value,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->assertFalse($order->isEligibleForReview());
    }

    /**
     * Test: Payment status field behavior
     */
    public function test_payment_status_tracking(): void
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::IN_PROGRESS->value,
            'payment_status' => 'unpaid',
        ]);

        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertNull($order->paid_at);

        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->assertEquals('paid', $order->payment_status);
        $this->assertNotNull($order->paid_at);
    }

    /**
     * Test: Pay-first service requires payment before work
     */
    public function test_pay_first_service_requires_payment_before_work(): void
    {
        $payFirstService = Service::factory()->for($this->seller, 'creator')->create([
            'pay_first' => true,
        ]);

        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $payFirstService->id,
            'status' => OrderStatus::IN_PROGRESS->value,
            'payment_status' => 'unpaid',
        ]);

        $this->assertTrue($payFirstService->pay_first);
        $this->assertFalse($order->isEligibleForReview());

        $order->update([
            'status' => OrderStatus::COMPLETED->value,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->assertTrue($order->isEligibleForReview());
    }

    /**
     * Test: Order status transitions
     */
    public function test_order_status_transitions(): void
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::IN_PROGRESS->value,
            'payment_status' => 'unpaid',
        ]);

        $this->assertEquals(OrderStatus::IN_PROGRESS->value, $order->status);

        $order->update([
            'status' => OrderStatus::COMPLETED->value,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $order->refresh();
        $this->assertEquals(OrderStatus::COMPLETED->value, $order->status);
        $this->assertEquals('paid', $order->payment_status);
    }
}
