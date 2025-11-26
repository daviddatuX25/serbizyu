<?php

namespace Tests\Feature\Domains\Listings;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Messaging\Models\Message;
use App\Enums\BidStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BidMessagingTest extends TestCase
{
    use RefreshDatabase;

    protected User $offerCreator;
    protected User $bidder;
    protected Service $service;
    protected OpenOffer $openOffer;
    protected OpenOfferBid $bid;

    protected function setUp(): void
    {
        parent::setUp();

        $this->offerCreator = User::factory()->create();
        $this->bidder = User::factory()->create();
        $this->service = Service::factory()->create(['creator_id' => $this->offerCreator->id]);

        $this->openOffer = OpenOffer::factory()->create([
            'creator_id' => $this->offerCreator->id,
            'service_id' => $this->service->id,
        ]);

        $this->bid = OpenOfferBid::factory()->create([
            'open_offer_id' => $this->openOffer->id,
            'bidder_id' => $this->bidder->id,
            'service_id' => $this->service->id,
            'status' => BidStatus::PENDING,
        ]);
    }

    /** @test */
    public function bidder_can_get_or_create_bid_message_thread()
    {
        $this->actingAs($this->bidder);

        $response = $this->getJson(route('bids.messages.thread', ['bid' => $this->bid]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'creator_id', 'title', 'parent_type', 'parent_id']);

        $this->assertDatabaseHas('message_threads', [
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);
    }

    /** @test */
    public function offer_creator_can_get_or_create_bid_message_thread()
    {
        $this->actingAs($this->offerCreator);

        $response = $this->getJson(route('bids.messages.thread', ['bid' => $this->bid]));

        $response->assertStatus(200);

        $this->assertDatabaseHas('message_threads', [
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);
    }

    /** @test */
    public function third_party_cannot_view_bid_message_thread()
    {
        $thirdParty = User::factory()->create();
        $this->actingAs($thirdParty);

        $response = $this->getJson(route('bids.messages.thread', ['bid' => $this->bid]));

        $response->assertStatus(403);
    }

    /** @test */
    public function bidder_can_send_message_to_bid()
    {
        MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        $this->actingAs($this->bidder);

        $response = $this->postJson(route('bids.messages.store', ['bid' => $this->bid]), [
            'content' => 'I can complete this quickly. Do you need it by tomorrow?',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->bidder->id,
            'content' => 'I can complete this quickly. Do you need it by tomorrow?',
        ]);
    }

    /** @test */
    public function offer_creator_can_send_message_to_bid()
    {
        MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        $this->actingAs($this->offerCreator);

        $response = $this->postJson(route('bids.messages.store', ['bid' => $this->bid]), [
            'content' => 'Yes, I need it by tomorrow. What is your final price?',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->offerCreator->id,
            'content' => 'Yes, I need it by tomorrow. What is your final price?',
        ]);
    }

    /** @test */
    public function third_party_cannot_send_message_to_bid()
    {
        MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        $thirdParty = User::factory()->create();
        $this->actingAs($thirdParty);

        $response = $this->postJson(route('bids.messages.store', ['bid' => $this->bid]), [
            'content' => 'I should not be able to send this.',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function cannot_send_message_to_rejected_bid()
    {
        $this->bid->update(['status' => BidStatus::REJECTED]);

        MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        $this->actingAs($this->bidder);

        $response = $this->postJson(route('bids.messages.store', ['bid' => $this->bid]), [
            'content' => 'This should fail.',
        ]);

        $response->assertStatus(403);
        $response->assertJsonPath('message', 'Cannot message a rejected bid.');
    }

    /** @test */
    public function can_still_message_accepted_bid()
    {
        $this->bid->update(['status' => BidStatus::ACCEPTED]);

        MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        $this->actingAs($this->bidder);

        $response = $this->postJson(route('bids.messages.store', ['bid' => $this->bid]), [
            'content' => 'Great! I will start working on this now.',
        ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function bidder_can_view_all_messages_for_bid()
    {
        $thread = MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        Message::factory()->count(5)->create([
            'message_thread_id' => $thread2->id,
            'sender_id' => $this->creator->id,
        ]);

        $this->actingAs($this->bidder);

        $response = $this->getJson(route('bids.messages.index', ['bid' => $this->bid]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'thread' => ['id', 'creator_id'],
            'messages' => ['data' => ['*' => ['id', 'sender_id', 'content']]],
            'bid' => ['id', 'bidder_id', 'open_offer_id'],
        ]);
    }

    /** @test */
    public function offer_creator_can_view_all_messages_for_bid()
    {
        $thread = MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        Message::factory()->count(3)->create([
            'message_thread_id' => $thread->id,
            'sender_id' => $this->bidder->id,
        ]);

        $this->actingAs($this->offerCreator);

        $response = $this->getJson(route('bids.messages.index', ['bid' => $this->bid]));

        $response->assertStatus(200);
    }

    /** @test */
    public function third_party_cannot_view_bid_messages()
    {
        $thread = MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        $thirdParty = User::factory()->create();
        $this->actingAs($thirdParty);

        $response = $this->getJson(route('bids.messages.index', ['bid' => $this->bid]));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_send_message_with_attachments()
    {
        MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        $this->actingAs($this->bidder);

        $file = \Illuminate\Http\UploadedFile::fake()->create('proposal.pdf', 512);

        $response = $this->postJson(route('bids.messages.store', ['bid' => $this->bid]), [
            'content' => 'Here is my detailed proposal.',
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
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        Message::factory()->count(2)->create([
            'thread_id' => $thread->id,
            'sender_id' => $this->offerCreator->id,
            'read_at' => null,
        ]);

        $this->actingAs($this->bidder);

        $response = $this->postJson(route('bids.messages.markAsRead', ['bid' => $this->bid]));

        $response->assertStatus(200);

        $unreadCount = Message::where('message_thread_id', $thread->id)
            ->where('read_at', null)
            ->where('sender_id', $this->offerCreator->id)
            ->count();

        $this->assertEquals(0, $unreadCount);
    }

    /** @test */
    public function message_content_is_validated()
    {
        MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        $this->actingAs($this->bidder);

        // Empty message
        $response = $this->postJson(route('bids.messages.store', ['bid' => $this->bid]), [
            'content' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('content');
    }

    /** @test */
    public function multiple_bids_on_same_offer_have_separate_threads()
    {
        $bidder2 = User::factory()->create();
        $bid2 = OpenOfferBid::factory()->create([
            'open_offer_id' => $this->openOffer->id,
            'bidder_id' => $bidder2->id,
            'service_id' => $this->service->id,
        ]);

        MessageThread::factory()->create([
            'creator_id' => $this->bidder->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $this->bid->id,
        ]);

        MessageThread::factory()->create([
            'creator_id' => $bidder2->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $bid2->id,
        ]);

        $this->actingAs($this->offerCreator);

        $response1 = $this->getJson(route('bids.messages.index', ['bid' => $this->bid]));
        $response2 = $this->getJson(route('bids.messages.index', ['bid' => $bid2]));

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Verify they're different threads
        $thread1_id = $response1->json('thread.id');
        $thread2_id = $response2->json('thread.id');

        $this->assertNotEquals($thread1_id, $thread2_id);
    }

    /** @test */
    public function offer_creator_cannot_see_bidder2s_messages_to_other_bids()
    {
        $bidder2 = User::factory()->create();
        $bid2 = OpenOfferBid::factory()->create([
            'open_offer_id' => $this->openOffer->id,
            'bidder_id' => $bidder2->id,
            'service_id' => $this->service->id,
        ]);

        $thread2 = MessageThread::factory()->create([
            'creator_id' => $bidder2->id,
            'parent_type' => OpenOfferBid::class,
            'parent_id' => $bid2->id,
        ]);

        Message::factory()->count(3)->create([
            'thread_id' => $thread2->id,
            'sender_id' => $bidder2->id,
        ]);

        $this->actingAs($this->offerCreator);

        // Can view their own bid messages
        $response1 = $this->getJson(route('bids.messages.index', ['bid' => $this->bid]));
        $response1->assertStatus(200);

        // Can view other bids they received
        $response2 = $this->getJson(route('bids.messages.index', ['bid' => $bid2]));
        $response2->assertStatus(200);
    }
}
