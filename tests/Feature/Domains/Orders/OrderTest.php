<?php

namespace Tests\Feature\Domains\Orders;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Listings\Models\Service;
use App\Domains\Orders\Events\OrderCreated;
use App\Domains\Orders\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Domains\Users\Models\User; // Corrected namespace
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Service $service;
    protected OpenOffer $openOffer;
    protected OpenOfferBid $acceptedBid;
    protected OpenOfferBid $unacceptedBid;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create();
        $this->seller = User::factory()->create();
        $this->service = Service::factory()->for($this->seller, 'creator')->create();
        $this->openOffer = OpenOffer::factory()->for($this->buyer, 'creator')->create();

        $this->acceptedBid = OpenOfferBid::factory()->for($this->openOffer)->for($this->service)->create([
            'bidder_id' => $this->buyer->id,
            'open_offer_id' => $this->openOffer->id,
            'service_id' => $this->service->id,
            'is_accepted' => true,
            'price' => 100.00,
        ]);

        $this->unacceptedBid = OpenOfferBid::factory()->for($this->openOffer)->for($this->service)->create([
            'bidder_id' => $this->buyer->id,
            'open_offer_id' => $this->openOffer->id,
            'service_id' => $this->service->id,
            'is_accepted' => false,
            'price' => 120.00,
        ]);
    }

    /** @test */
    public function guest_cannot_access_order_pages(): void
    {
        $this->get(route('orders.index'))->assertRedirect(route('auth.signin'));
        $this->get(route('orders.create', ['open_offer_bid_id' => $this->acceptedBid->id]))->assertRedirect(route('auth.signin'));
        $this->post(route('orders.store'))->assertRedirect(route('auth.signin'));
        $this->get(route('orders.show', Order::factory()->create()))->assertRedirect(route('auth.signin'));
    }

    /** @test */
    public function authenticated_user_can_view_their_orders_index(): void
    {
        $this->actingAs($this->buyer)
             ->get(route('orders.index'))
             ->assertOk()
             ->assertViewIs('orders.index')
             ->assertSee('My Orders');
    }

    /** @test */
    public function authenticated_user_can_view_their_order_details(): void
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'open_offer_id' => $this->openOffer->id,
            'open_offer_bid_id' => $this->acceptedBid->id,
        ]);

        $this->actingAs($this->buyer)
             ->get(route('orders.show', $order))
             ->assertOk()
             ->assertViewIs('orders.show')
             ->assertSee('Order Details')
             ->assertSee($order->id);

        $this->actingAs($this->seller)
             ->get(route('orders.show', $order))
             ->assertOk()
             ->assertViewIs('orders.show')
             ->assertSee('Order Details')
             ->assertSee($order->id);
    }

    /** @test */
    public function authenticated_user_cannot_view_other_users_orders(): void
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'open_offer_id' => $this->openOffer->id,
            'open_offer_bid_id' => $this->acceptedBid->id,
        ]);

        $this->actingAs($otherUser)
             ->get(route('orders.show', $order))
             ->assertForbidden();
    }

    /** @test */
    public function buyer_can_access_order_create_page_with_accepted_bid(): void
    {
        $this->actingAs($this->buyer)
             ->get(route('orders.create', ['open_offer_bid_id' => $this->acceptedBid->id]))
             ->assertOk()
             ->assertViewIs('orders.create')
             ->assertSee('Review and Confirm Your Order')
             ->assertSee($this->acceptedBid->price);
    }

    /** @test */
    public function buyer_cannot_access_order_create_page_with_unaccepted_bid(): void
    {
        $this->actingAs($this->buyer)
             ->get(route('orders.create', ['open_offer_bid_id' => $this->unacceptedBid->id]))
             ->assertForbidden();
    }

    /** @test */
    public function buyer_cannot_access_order_create_page_without_bid_id(): void
    {
        $this->actingAs($this->buyer)
             ->get(route('orders.create'))
             ->assertRedirect(route('dashboard'))
             ->assertSessionHas('error', 'No bid specified for order creation.');
    }

    /** @test */
    public function buyer_can_create_order_from_accepted_bid(): void
    {
        Event::fake();

        $platformFeePercentage = config('fees.platform_percentage', 5);
        $expectedPlatformFee = ($this->acceptedBid->price * $platformFeePercentage) / 100;
        $expectedTotalAmount = $this->acceptedBid->price + $expectedPlatformFee;

        $this->actingAs($this->buyer)
             ->post(route('orders.store'), ['open_offer_bid_id' => $this->acceptedBid->id])
             ->assertRedirect(route('orders.show', Order::first()))
             ->assertSessionHas('success', 'Order created successfully!');

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'open_offer_id' => $this->openOffer->id,
            'open_offer_bid_id' => $this->acceptedBid->id,
            'price' => $this->acceptedBid->price,
            'platform_fee' => $expectedPlatformFee,
            'total_amount' => $expectedTotalAmount,
            'status' => OrderStatus::Pending->value,
            'payment_status' => PaymentStatus::Unpaid->value,
        ]);

        Event::assertDispatched(OrderCreated::class, function ($event) {
            return $event->order->buyer_id === $this->buyer->id;
        });
    }

    /** @test */
    public function buyer_cannot_create_order_from_unaccepted_bid(): void
    {
        Event::fake();

        $this->actingAs($this->buyer)
             ->post(route('orders.store'), ['open_offer_bid_id' => $this->unacceptedBid->id])
             ->assertForbidden();

        $this->assertDatabaseMissing('orders', [
            'open_offer_bid_id' => $this->unacceptedBid->id,
        ]);

        Event::assertNotDispatched(OrderCreated::class);
    }

    /** @test */
    public function buyer_cannot_create_order_for_bid_they_did_not_make(): void
    {
        Event::fake();

        $otherUser = User::factory()->create();
        $bidByOtherUser = OpenOfferBid::factory()->for($this->openOffer)->for($this->service)->create([
            'bidder_id' => $otherUser->id,
            'open_offer_id' => $this->openOffer->id,
            'service_id' => $this->service->id,
            'is_accepted' => true,
            'price' => 150.00,
        ]);

        $this->actingAs($this->buyer)
             ->post(route('orders.store'), ['open_offer_bid_id' => $bidByOtherUser->id])
             ->assertForbidden();

        $this->assertDatabaseMissing('orders', [
            'open_offer_bid_id' => $bidByOtherUser->id,
        ]);

        Event::assertNotDispatched(OrderCreated::class);
    }

    /** @test */
    public function buyer_can_cancel_their_pending_order(): void
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'open_offer_id' => $this->openOffer->id,
            'open_offer_bid_id' => $this->acceptedBid->id,
            'status' => OrderStatus::Pending,
        ]);

        $this->actingAs($this->buyer)
             ->delete(route('orders.destroy', $order))
             ->assertRedirect(route('orders.show', $order)) // Assuming redirect back to show page
             ->assertSessionHas('success', 'Order cancelled successfully!');

        $order->refresh();
        $this->assertEquals(OrderStatus::Cancelled, $order->status);
        $this->assertNotNull($order->cancelled_at);
    }

    /** @test */
    public function buyer_cannot_cancel_non_pending_order(): void
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'open_offer_id' => $this->openOffer->id,
            'open_offer_bid_id' => $this->acceptedBid->id,
            'status' => OrderStatus::Completed, // Not pending
        ]);

        $this->actingAs($this->buyer)
             ->delete(route('orders.destroy', $order))
             ->assertForbidden(); // Policy should prevent this

        $order->refresh();
        $this->assertEquals(OrderStatus::Completed, $order->status); // Status should not change
    }

    /** @test */
    public function seller_cannot_cancel_order(): void
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'open_offer_id' => $this->openOffer->id,
            'open_offer_bid_id' => $this->acceptedBid->id,
            'status' => OrderStatus::Pending,
        ]);

        $this->actingAs($this->seller)
             ->delete(route('orders.destroy', $order))
             ->assertForbidden(); // Policy should prevent this

        $order->refresh();
        $this->assertEquals(OrderStatus::Pending, $order->status); // Status should not change
    }
}
