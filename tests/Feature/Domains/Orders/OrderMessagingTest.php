<?php

namespace Tests\Feature\Domains\Orders;

use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Messaging\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderMessagingTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Service $service;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create();
        $this->seller = User::factory()->create();
        $this->service = Service::factory()->create(['creator_id' => $this->seller->id]);

        $this->order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
        ]);
    }

    /** @test */
    public function buyer_can_get_or_create_order_message_thread()
    {
        $this->actingAs($this->buyer);

        $response = $this->getJson(route('orders.messages.thread', ['order' => $this->order]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'creator_id', 'title', 'parent_type', 'parent_id']);

        $this->assertDatabaseHas('message_threads', [
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);
    }

    /** @test */
    public function seller_can_get_or_create_order_message_thread()
    {
        $this->actingAs($this->seller);

        $response = $this->getJson(route('orders.messages.thread', ['order' => $this->order]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('message_threads', [
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);
    }

    /** @test */
    public function third_party_cannot_view_order_message_thread()
    {
        $thirdParty = User::factory()->create();
        $this->actingAs($thirdParty);

        $response = $this->getJson(route('orders.messages.thread', ['order' => $this->order]));

        $response->assertStatus(403);
    }

    /** @test */
    public function buyer_can_send_message_to_order()
    {
        // First create the thread
        MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        $this->actingAs($this->buyer);

        $response = $this->postJson(route('orders.messages.store', ['order' => $this->order]), [
            'content' => 'Can you expedite this order?',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'sender_id', 'content']);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->buyer->id,
            'content' => 'Can you expedite this order?',
        ]);
    }

    /** @test */
    public function seller_can_send_message_to_order()
    {
        // First create the thread
        MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        $this->actingAs($this->seller);

        $response = $this->postJson(route('orders.messages.store', ['order' => $this->order]), [
            'content' => 'Order is being processed.',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->seller->id,
            'content' => 'Order is being processed.',
        ]);
    }

    /** @test */
    public function third_party_cannot_send_message_to_order()
    {
        // First create the thread
        MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        $thirdParty = User::factory()->create();
        $this->actingAs($thirdParty);

        $response = $this->postJson(route('orders.messages.store', ['order' => $this->order]), [
            'content' => 'I should not be able to send this.',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function cannot_send_message_to_cancelled_order()
    {
        $this->order->update(['status' => 'cancelled']);

        // Create the thread
        MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        $this->actingAs($this->buyer);

        $response = $this->postJson(route('orders.messages.store', ['order' => $this->order]), [
            'content' => 'This should fail.',
        ]);

        $response->assertStatus(403);
        $response->assertJsonPath('message', 'Cannot message a cancelled order.');
    }

    /** @test */
    public function buyer_can_view_all_messages_for_order()
    {
        $thread = MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        Message::factory()->count(5)->create([
            'message_thread_id' => $thread->id,
            'sender_id' => $this->seller->id,
        ]);

        $this->actingAs($this->buyer);

        $response = $this->getJson(route('orders.messages.index', ['order' => $this->order]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'thread' => ['id', 'creator_id'],
            'messages' => [
                'data' => [
                    '*' => ['id', 'sender_id', 'content']
                ]
            ]
        ]);
    }

    /** @test */
    public function seller_can_view_all_messages_for_order()
    {
        $thread = MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        Message::factory()->count(3)->create([
            'message_thread_id' => $thread->id,
            'sender_id' => $this->buyer->id,
        ]);

        $this->actingAs($this->seller);

        $response = $this->getJson(route('orders.messages.index', ['order' => $this->order]));

        $response->assertStatus(200);
    }

    /** @test */
    public function third_party_cannot_view_order_messages()
    {
        $thread = MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        $thirdParty = User::factory()->create();
        $this->actingAs($thirdParty);

        $response = $this->getJson(route('orders.messages.index', ['order' => $this->order]));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_send_message_with_attachments()
    {
        $thread = MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        $this->actingAs($this->buyer);

        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 512);

        $response = $this->postJson(route('orders.messages.store', ['order' => $this->order]), [
            'content' => 'Here is my document.',
            'attachments' => [$file],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('message_attachments', [
            'file_type' => 'application/pdf',
        ]);
    }

    /** @test */
    public function can_mark_messages_as_read()
    {
        $thread = MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        // Create unread messages from seller
        Message::factory()->count(3)->create([
            'message_thread_id' => $thread->id,
            'sender_id' => $this->seller->id,
            'read_at' => null,
        ]);

        $this->actingAs($this->buyer);

        $response = $this->postJson(route('orders.messages.markAsRead', ['order' => $this->order]));

        $response->assertStatus(200);

        // Verify messages are marked as read
        $unreadCount = Message::where('message_thread_id', $thread->id)
            ->where('read_at', null)
            ->where('sender_id', $this->seller->id)
            ->count();

        $this->assertEquals(0, $unreadCount);
    }

    /** @test */
    public function message_content_is_validated()
    {
        $thread = MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        $this->actingAs($this->buyer);

        // Empty message
        $response = $this->postJson(route('orders.messages.store', ['order' => $this->order]), [
            'content' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('content');
    }

    /** @test */
    public function multiple_orders_with_same_users_have_separate_threads()
    {
        $order2 = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
        ]);

        MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $this->order->id,
        ]);

        MessageThread::factory()->create([
            'creator_id' => $this->buyer->id,
            'parent_type' => Order::class,
            'parent_id' => $order2->id,
        ]);

        $this->actingAs($this->buyer);

        $response1 = $this->getJson(route('orders.messages.index', ['order' => $this->order]));
        $response2 = $this->getJson(route('orders.messages.index', ['order' => $order2]));

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Verify they're different threads
        $thread1_id = $response1->json('thread.id');
        $thread2_id = $response2->json('thread.id');

        $this->assertNotEquals($thread1_id, $thread2_id);
    }
}
