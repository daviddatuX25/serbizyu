# Messaging Integration Deep Plan - Orders & Bids

**Date:** November 26, 2025  
**Status:** Planning & Implementation Phase  
**Objective:** Integrate messaging system into Orders and Bids with full edge case handling

---

## 1. CURRENT SYSTEM ANALYSIS

### 1.1 Messaging Architecture (Existing)
```
MessageThread (parent_type, parent_id via morphs)
    ├── Message (sender_id, content, read_at)
    │   └── MessageAttachment (file_path, file_type)
    └── MessageController (API)
        ├── createThread()
        ├── sendMessage()
        ├── markAsRead()
        └── listMessages()
```

**Key Features:**
- ✅ Polymorphic threading (can attach to any parent_type)
- ✅ File attachments support
- ✅ Read status tracking
- ✅ Livewire ChatInterface component
- ⚠️ Single creator_id (may need multiple participants)

### 1.2 Order Model (Current)
- `buyer_id`, `seller_id`, `service_id`
- `status`, `payment_status`
- Relations: buyer, seller, service, workInstance, payment

### 1.3 OpenOfferBid Model (Current)
- `open_offer_id`, `bidder_id`, `service_id`
- `amount`, `message` (field for bid message)
- `status` (BidStatus enum)
- Relations: service, openOffer, bidder

---

## 2. MESSAGING FOR ORDERS

### 2.1 Requirements & Use Cases

#### Use Case 1: Order Communication
- **Participants:** Buyer + Seller (2 people)
- **When:** From order creation until completion/cancellation
- **Purpose:** Discussion about order details, revisions, questions
- **Actions:** Create thread on order creation, access from order view/detail

#### Use Case 2: Edge Cases
1. **User viewing their own orders**
   - Buyer viewing their own order ✓
   - Seller viewing their own order ✓
   - Buyer trying to view seller's order → 403 Forbidden
   - Seller trying to view buyer's order → 403 Forbidden

2. **Order State Changes**
   - What if order is cancelled? → Keep thread visible in archive
   - What if order is refunded? → Keep thread for dispute context
   - What if both users delete thread? → Soft delete & audit trail

3. **Multiple Orders with Same User**
   - Can have multiple threads (one per order)
   - Must clearly show which thread belongs to which order

4. **Concurrent Access**
   - Both users can chat simultaneously
   - Real-time updates via Livewire/Broadcasting
   - Read receipts to show activity

### 2.2 Implementation Plan

#### Step 1: Add MessageThread Relation to Order Model
```php
// In Order.php
public function messageThread()
{
    return $this->morphOne(MessageThread::class, 'parent');
}

// Or for multiple threads (if needed):
public function messageThreads()
{
    return $this->morphMany(MessageThread::class, 'parent');
}
```

#### Step 2: Auto-Create Thread on Order Creation
```php
// In OrderService or OrderController
public function createOrder(...) {
    $order = Order::create([...]);
    
    MessageThread::create([
        'creator_id' => $order->buyer_id,
        'title' => "Order #{$order->id} - Discussion",
        'parent_type' => Order::class,
        'parent_id' => $order->id,
    ]);
    
    return $order;
}
```

#### Step 3: Authorization Layer
```php
// OrderMessagePolicy
public function viewThread(User $user, Order $order): bool
{
    return $user->id === $order->buyer_id || $user->id === $order->seller_id;
}

public function sendMessage(User $user, Order $order): bool
{
    return ($user->id === $order->buyer_id || $user->id === $order->seller_id)
        && $order->status !== 'cancelled';  // Can't message cancelled orders
}
```

#### Step 4: UI Components
- Add "Chat" tab/button on Order detail page
- Show unread message count in orders list
- Integrate ChatInterface Livewire component
- Show order context in chat header (order #, amount, service)

#### Step 5: Notifications
- Notify when new message arrives
- Show in-app notifications
- Optional: Email notifications for unanswered messages

---

## 3. MESSAGING FOR BIDS

### 3.1 Requirements & Use Cases

#### Use Case 1: Bid Discussion
- **Participants:** OpenOffer creator + Bidder (2 people)
- **When:** After bid is placed, before acceptance/rejection
- **Purpose:** Negotiate terms, clarify requirements, discuss scope
- **Note:** Bid already has `message` field (one-way initial message)

#### Use Case 2: Edge Cases
1. **Bid Status Progression**
   - Pending: ✓ Both can chat
   - Accepted: ✓ Both can chat (escalates to Order)
   - Rejected: ❓ Can they still chat? (Design decision)
   - Withdrawn: ❓ Can they still chat? (Design decision)

2. **Multiple Bidders**
   - Each bidder has separate thread with offer creator
   - Bidders CANNOT see other bidders' threads
   - Offer creator can see all bidders' threads

3. **Bid to Order Conversion**
   - When bid is accepted → Auto-create Order message thread
   - Optionally copy bid discussion to order thread
   - Keep bid thread for reference

4. **Authorization**
   - Only offer creator can view bids they received
   - Only bidder can view their own bid
   - Third parties cannot access bid discussions

5. **Concurrent Actions**
   - User is chatting while offer creator accepts bid
   - Bid moves to "accepted" → thread should reflect this
   - Chain: Bid accepted → Order created → Messaging continues in Order thread

### 3.2 Implementation Plan

#### Step 1: Add MessageThread Relation to OpenOfferBid
```php
// In OpenOfferBid.php
public function messageThread()
{
    return $this->morphOne(MessageThread::class, 'parent');
}

// Get offer creator
public function offerCreator()
{
    return $this->openOffer->creator();  // via OpenOffer relation
}
```

#### Step 2: Auto-Create Thread on Bid Creation
```php
// In OpenOfferBidService::createBid()
public function createBid(User $bidder, OpenOffer $openOffer, array $data): OpenOfferBid
{
    $bid = OpenOfferBid::create([
        'open_offer_id' => $openOffer->id,
        'bidder_id' => $bidder->id,
        // ... other fields
    ]);
    
    // Create message thread
    MessageThread::create([
        'creator_id' => $bidder->id,
        'title' => "Bid Discussion - {$openOffer->title}",
        'parent_type' => OpenOfferBid::class,
        'parent_id' => $bid->id,
    ]);
    
    return $bid;
}
```

#### Step 3: Authorization Layer
```php
// OpenOfferBidMessagePolicy
public function viewThread(User $user, OpenOfferBid $bid): bool
{
    $offerCreator = $bid->openOffer->creator_id;
    $bidder = $bid->bidder_id;
    
    return $user->id === $offerCreator || $user->id === $bidder;
}

public function sendMessage(User $user, OpenOfferBid $bid): bool
{
    if ($bid->status === BidStatus::REJECTED) {
        return false;  // Can't message rejected bids
    }
    
    return $this->viewThread($user, $bid);
}
```

#### Step 4: Bid Acceptance → Order Conversion Flow
```php
// In OpenOfferBidService::acceptBid()
public function acceptBid(OpenOfferBid $bid): OpenOfferBid
{
    DB::transaction(function () use ($bid) {
        // Accept bid
        $bid->update(['status' => BidStatus::ACCEPTED]);
        
        // Create order
        $order = Order::create([
            'buyer_id' => $bid->openOffer->creator_id,
            'seller_id' => $bid->bidder_id,
            'service_id' => $bid->service_id,
            'open_offer_bid_id' => $bid->id,
            'price' => $bid->amount,
            // ... other fields
        ]);
        
        // Create new message thread for order
        MessageThread::create([
            'creator_id' => $bid->openOffer->creator_id,
            'title' => "Order #{$order->id} - Discussion",
            'parent_type' => Order::class,
            'parent_id' => $order->id,
        ]);
        
        // Optional: Copy bid discussion context to order thread
        // (or just keep as reference)
    });
}
```

#### Step 5: UI Components for Bids
- Add "Message Bidder" button on bid card/detail
- Bidder sees "Message Offer Creator" button
- Show message count on bid
- Display in bid detail page with offer context
- Integrate ChatInterface component

---

## 4. DATABASE SCHEMA ADJUSTMENTS

### 4.1 Current message_threads Structure
```sql
CREATE TABLE message_threads (
    id BIGINT PRIMARY KEY,
    creator_id BIGINT FOREIGN KEY users.id,
    title VARCHAR(255) NULLABLE,
    parent_type VARCHAR(255),  -- 'App\Domains\Orders\Models\Order'
    parent_id BIGINT,
    timestamps
);
```

### 4.2 Potential Issues & Solutions

**Issue 1: creator_id doesn't capture all participants**
- ✅ Current design is fine (polymorphic relation handles parent)
- Messages have sender_id, so we can query all participants

**Issue 2: No explicit participants table**
- ✅ Not needed for 1-to-1 (order/bid are 2 people)
- If needed later: create message_thread_participants table

**Issue 3: Soft deletes for privacy**
```sql
ALTER TABLE message_threads ADD COLUMN deleted_at TIMESTAMP NULLABLE;
ALTER TABLE messages ADD COLUMN deleted_at TIMESTAMP NULLABLE;
```

### 4.3 Migration Plan
```
1. No schema changes needed initially (already supports polymorphic)
2. Optional: Add soft deletes if users can delete conversations
3. Optional: Add message_thread_participants if expanding to group chats later
```

---

## 5. EDGE CASES & SOLUTIONS

### 5.1 Order-Related Edge Cases

| Edge Case | Scenario | Solution |
|-----------|----------|----------|
| **Cancelled Order** | User tries to message cancelled order | Show archived view, allow read-only or no messaging |
| **Refunded Order** | User tries to message refunded order | Keep thread for dispute context, allow messaging if disputed |
| **Order Deletion** | Admin deletes order | Soft delete order, keep thread with parent reference |
| **User Deletion** | User deletes their account | Anonymize messages from that user, keep conversation history |
| **Concurrent Messages** | Both users type simultaneously | Real-time sync via Livewire/Broadcasting |
| **Very Long Chat** | Order with 100+ messages | Pagination in UI, archive oldest messages |
| **Attachment Issues** | File upload fails mid-message | Validate file before sending, show error to user |
| **Blocked Users** | One user blocks the other | Hide threads from blocked user, show notification |

### 5.2 Bid-Related Edge Cases

| Edge Case | Scenario | Solution |
|-----------|----------|----------|
| **Bid Accepted** | User chatting while offer creator accepts | Auto-create order thread, notify user to continue in order |
| **Bid Rejected** | Can rejected bid still be messaged? | Option A: Block messaging on rejected. Option B: Allow for context |
| **Bid Withdrawn** | Bidder withdraws bid, can they still chat? | Similar to rejected - design decision needed |
| **Multiple Bids** | Same bidder bids multiple times on same offer | Each bid has own thread, clearly distinguish |
| **Offer Deleted** | Offer creator deletes offer before accepting bid | Soft delete offer, keep bid thread for reference |
| **Bid Status Sync** | UI shows old bid status while chatting | Refresh bid status when opening thread, listen to bid updates |
| **No Bids Yet** | Message thread created but no bids? | Shouldn't happen (thread created with bid), but add validation |

### 5.3 Authorization Edge Cases

| Edge Case | Scenario | Solution |
|-----------|----------|----------|
| **Admin Access** | Admin needs to view user conversations | Create admin-specific policy, log access |
| **Support Team** | Support agent needs to help with order dispute | Implement "join conversation" feature with audit log |
| **Impersonation** | User tries to fake sender_id | Enforce Auth::id() in controller, never trust request data |
| **Thread Hijacking** | User tries to access unrelated thread | Always verify user is participant before showing content |
| **Read Receipts Privacy** | Sender sees when user read message | Option A: Show read status. Option B: Privacy - don't show |

---

## 6. API ROUTES & CONTROLLERS

### 6.1 New/Modified Routes

```php
// Order Messaging Routes
Route::middleware(['auth'])->prefix('orders')->name('orders.')->group(function () {
    // Existing order routes...
    
    // Messaging for orders
    Route::prefix('{order}/messages')->name('messages.')->group(function () {
        Route::get('/', [OrderMessageController::class, 'index'])->name('index');
        Route::post('/', [OrderMessageController::class, 'store'])->name('store');
        Route::get('/thread', [OrderMessageController::class, 'getOrCreateThread'])->name('thread');
    });
});

// Bid Messaging Routes
Route::middleware(['auth'])->prefix('bids')->name('bids.')->group(function () {
    Route::prefix('{bid}/messages')->name('messages.')->group(function () {
        Route::get('/', [BidMessageController::class, 'index'])->name('index');
        Route::post('/', [BidMessageController::class, 'store'])->name('store');
        Route::get('/thread', [BidMessageController::class, 'getOrCreateThread'])->name('thread');
    });
});
```

### 6.2 Controller Methods

```php
// OrderMessageController
class OrderMessageController extends Controller {
    public function getOrCreateThread(Order $order)
    {
        $this->authorize('viewThread', $order);
        
        $thread = MessageThread::where('parent_type', Order::class)
            ->where('parent_id', $order->id)
            ->first();
        
        if (!$thread) {
            $thread = MessageThread::create([...]);
        }
        
        return response()->json($thread);
    }
}

// BidMessageController
class BidMessageController extends Controller {
    public function getOrCreateThread(OpenOfferBid $bid)
    {
        $this->authorize('viewThread', $bid);
        
        $thread = MessageThread::where('parent_type', OpenOfferBid::class)
            ->where('parent_id', $bid->id)
            ->first();
        
        if (!$thread) {
            $thread = MessageThread::create([...]);
        }
        
        return response()->json($thread);
    }
}
```

---

## 7. LIVEWIRE COMPONENTS

### 7.1 Existing Component
- `ChatInterface.php` - Generic chat component (reusable)

### 7.2 New Components (Optional)
- `OrderChat.php` - Wraps ChatInterface for orders
- `BidChat.php` - Wraps ChatInterface for bids
- `MessageBadge.php` - Shows unread count (already exists)

### 7.3 Integration Points
```php
// In Order detail blade
<livewire:chat-interface :thread="$order->messageThread" />

// In Bid detail blade
<livewire:chat-interface :thread="$bid->messageThread" />
```

---

## 8. NOTIFICATIONS & BROADCASTING

### 8.1 Events to Broadcast
```php
// MessageSent event
class MessageSent {
    public function broadcastOn(): Channel {
        return new PrivateChannel("message-thread.{$this->message->thread_id}");
    }
}

// OrderMessageAlert event
class OrderMessageAlert {
    public function broadcastOn(): Channel {
        return new PrivateChannel("user.{$this->order->seller_id}");  // For seller
    }
}

// BidMessageAlert event  
class BidMessageAlert {
    public function broadcastOn(): Channel {
        return new PrivateChannel("user.{$this->bid->openOffer->creator_id}");  // For offer creator
    }
}
```

### 8.2 Notification Channels
- ✅ In-app (database notification)
- 🔄 Real-time (broadcasting)
- 📧 Email (optional for unanswered messages after X hours)
- 📱 Push (future enhancement)

---

## 9. IMPLEMENTATION TIMELINE

### Phase 1: Foundation (Days 1-2)
- [ ] Add messageThread relations to Order & OpenOfferBid models
- [ ] Create OrderMessageController & BidMessageController
- [ ] Add routes for order/bid messaging
- [ ] Add authorization policies
- [ ] Write tests for edge cases

### Phase 2: Data Layer (Days 2-3)
- [ ] Modify OrderService to auto-create thread
- [ ] Modify OpenOfferBidService to auto-create thread
- [ ] Modify acceptBid flow to create order thread
- [ ] Add soft delete support to messages (if needed)
- [ ] Create migration for any schema changes

### Phase 3: UI Components (Days 3-4)
- [ ] Add chat tab to Order detail page
- [ ] Add chat button to Bid card/detail
- [ ] Integrate ChatInterface component
- [ ] Add message count badges
- [ ] Add order/bid context in chat header

### Phase 4: Advanced Features (Days 4-5)
- [ ] Implement real-time broadcasting
- [ ] Add notifications (in-app + email)
- [ ] Add read receipts
- [ ] Add typing indicators
- [ ] Add soft deletes for conversations

### Phase 5: Testing & Polish (Days 5-6)
- [ ] Manual testing of all edge cases
- [ ] Load testing (many messages)
- [ ] Security testing (authorization)
- [ ] User acceptance testing
- [ ] Documentation & deployment

---

## 10. TESTING CHECKLIST

### Unit Tests
- [ ] MessageThread creation on order placement
- [ ] MessageThread creation on bid placement
- [ ] Authorization checks (view, send, delete)
- [ ] Message storage with attachments
- [ ] Read status updates

### Integration Tests
- [ ] Full order flow with messaging
- [ ] Full bid→order flow with messaging
- [ ] Multiple orders with same user
- [ ] Concurrent message sending
- [ ] Order cancellation with active chat

### E2E Tests
- [ ] User A creates order, User B can chat
- [ ] User A bids, User B (offer creator) can respond
- [ ] Bid accepted → order created → chat continues
- [ ] Unread messages show correctly
- [ ] Attachments upload & download

### Edge Case Tests
- [ ] Deleted user shows as "Unknown User"
- [ ] Cancelled orders show archived chat
- [ ] Rejected bids can/cannot be messaged
- [ ] Admin can view user conversations
- [ ] Rate limiting on message sending

---

## 11. CONFIGURATION & CONSTANTS

### 11.1 Message Settings
```php
// config/messaging.php
return [
    'max_attachment_size' => 10 * 1024 * 1024,  // 10MB
    'allowed_mime_types' => ['image/*', 'application/pdf'],
    'message_char_limit' => 5000,
    'threads_per_page' => 20,
    'messages_per_page' => 50,
    'read_receipt_enabled' => true,
    'typing_indicator_enabled' => true,
    'soft_delete_messages' => true,
    'auto_delete_after_days' => 365,  // null for never
];
```

### 11.2 Edge Case Configuration
```php
'bid_messaging' => [
    'allow_after_rejection' => false,  // Can message rejected bids?
    'allow_after_withdrawal' => false,  // Can message withdrawn bids?
    'auto_close_on_acceptance' => false,  // Close bid thread when accepted?
],

'order_messaging' => [
    'allow_after_cancellation' => true,  // Can message cancelled orders?
    'allow_after_completion' => true,  // Can message completed orders?
    'allow_after_refund' => true,  // Can message refunded orders?
],
```

---

## 12. DEPLOYMENT CHECKLIST

- [ ] Database migrations applied
- [ ] Eloquent relations defined and tested
- [ ] Controllers & policies created
- [ ] Routes registered
- [ ] Livewire components mounted on views
- [ ] Broadcasting configured (if using real-time)
- [ ] File storage configured for attachments
- [ ] Queue configured for notifications (if async)
- [ ] Email templates created
- [ ] Documentation updated
- [ ] User communication (changelog/release notes)

---

## 13. FUTURE ENHANCEMENTS

1. **Group Messaging** - Allow multiple support staff to join order chat
2. **Message Search** - Search across all conversations
3. **Typing Indicators** - Show when other user is typing
4. **Message Reactions** - Emoji reactions to messages
5. **Message Pinning** - Pin important messages
6. **Conversation Archiving** - Archive old conversations
7. **Conversation Templates** - Quick reply templates for sellers
8. **Message Scheduling** - Schedule messages to send later
9. **Message Translation** - Auto-translate messages
10. **Sentiment Analysis** - Flag potentially problematic conversations

---

## SUMMARY

This deep plan addresses:
✅ Current system analysis
✅ Requirements for orders & bids
✅ Comprehensive edge case coverage
✅ Authorization & security
✅ Database & migration strategy
✅ API routes & controllers
✅ UI/UX integration
✅ Notifications & broadcasting
✅ Testing strategy
✅ Implementation timeline
✅ Deployment readiness

**Ready to begin implementation?**
