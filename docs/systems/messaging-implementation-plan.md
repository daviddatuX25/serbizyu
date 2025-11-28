# 🎯 DEEP PLAN: COMPREHENSIVE MESSAGING SYSTEM
## Phase 3: Real-Time Messaging Implementation
**Version:** 1.0 | **Date:** November 2025 | **Status:** Planning

---

## EXECUTIVE SUMMARY

This plan implements a **three-tier messaging system** for Serbizyu:
1. **Direct User-to-User Messaging** (DMs via private channels)
2. **Contextual Thread Messaging** (polymorphic: on bids, services, offers, quick deals, and orders)
3. **Activity Thread Conversations** (inline discussions within work execution steps)

By end of implementation, users can collaborate seamlessly across all business contexts with real-time updates via Soketi/Echo.

---

## PART I: SYSTEM ARCHITECTURE

### A. Three-Tier Messaging Model

```
┌─────────────────────────────────────────────────────────────────┐
│                    MESSAGING SYSTEM                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  TIER 1: DIRECT MESSAGES                                        │
│  ├─ direct_messages (user_id → user_id)                        │
│  ├─ Real-time private channels                                 │
│  ├─ Unread count tracking                                      │
│  └─ Example: @seller asks @buyer about service details         │
│                                                                  │
│  TIER 2: CONTEXTUAL THREADS (POLYMORPHIC)                      │
│  ├─ message_threads (threadable: Bid/Service/Offer/Order)     │
│  ├─ message_participants (for group awareness)                 │
│  ├─ thread_messages (individual messages in thread)            │
│  ├─ Example: Bid #5 → Thread for negotiating price            │
│  └─ Example: Service #12 → Q&A from potential buyers          │
│                                                                  │
│  TIER 3: ACTIVITY THREADS (WORK EXECUTION)                     │
│  ├─ activity_threads (tied to work_instances)                  │
│  ├─ thread_messages (replies within activity thread)           │
│  ├─ activity_thread_media (evidence/proofs)                    │
│  └─ Example: Work Step 2 → Seller posts proof + buyers comment│
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### B. Key Design Decisions

| Aspect | Decision | Rationale |
|--------|----------|-----------|
| **Polymorphic Threads** | Multiple threadable types | Flexible for bids, services, offers, quick deals, orders |
| **Real-Time** | Soketi + Echo + Broadcasting | Instant updates without polling |
| **Unread Tracking** | Timestamp-based per user | Lightweight, queryable |
| **Privacy** | Private channels + presence | Authorized users only |
| **Media** | Attached to activity threads | Work proof/evidence documentation |
| **Notifications** | Events + DB notifications | Multi-channel (in-app, email later) |

---

## PART II: DATABASE SCHEMA (Already Exists)

### Existing Tables
✅ `direct_messages`  
✅ `message_threads`  
✅ `thread_participants`  
✅ `thread_messages`  
✅ `activity_threads` (tied to work)  
✅ `activity_thread_media`  

All schemas are in master_plan.md — no new migrations needed.

---

## PART III: IMPLEMENTATION ROADMAP

### PHASE 1: DIRECT MESSAGING (3-4 days)

#### 1.1 Backend - Direct Messages

**Files to Create:**
```
app/Domains/Messaging/
├── Http/Controllers/DirectMessageController.php
├── Http/Requests/StoreDirectMessageRequest.php
└── Services/DirectMessageService.php
```

**API Endpoints:**
```php
DirectMessageController
├── GET  /api/messages/conversations        (List conversations)
├── GET  /api/messages/{userId}/history     (Get history, paginated)
├── POST /api/messages/{userId}              (Send message)
├── PUT  /api/messages/{id}/read             (Mark as read)
└── GET  /api/messages/unread/count         (Unread badge count)
```

**Broadcasting:**
```javascript
// Private channel per user
Echo.private(`users.{{ auth()->id() }}`)
    .listen('DirectMessageSent', (e) => {
        // Update conversation list + notify
    })
```

#### 1.2 Frontend - Direct Messages (Livewire)

**Components to Create:**
```
app/Livewire/Messages/
├── DirectMessageList.php        (Conversation sidebar)
├── DirectMessageThread.php       (Active conversation)
└── MessageNotificationBadge.php  (Unread count)
```

**Integration Points:**
```blade
<!-- Top-right of navbar -->
<div class="relative">
    @livewire('message-notification-badge')
    
    <!-- On click, show dropdown with conversations -->
    @livewire('messages.direct-message-list')
</div>

<!-- Full page at /messages -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="md:col-span-1">
        @livewire('messages.direct-message-list')
    </div>
    <div class="md:col-span-3">
        @livewire('messages.direct-message-thread', ['userId' => $selectedUser->id ?? null])
    </div>
</div>
```

#### 1.3 Routes

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/messages', MessageController::class.'@index')->name('messages.index');
    Route::get('/messages/{user}', MessageController::class.'@show')->name('messages.show');
});

// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('messages')->group(function () {
        Route::get('conversations', DirectMessageController::class.'@conversations');
        Route::get('{user}/history', DirectMessageController::class.'@history');
        Route::post('{user}', DirectMessageController::class.'@store');
        Route::put('{message}/read', DirectMessageController::class.'@markAsRead');
        Route::get('unread/count', DirectMessageController::class.'@unreadCount');
    });
});
```

#### 1.4 Tests
- `SendDirectMessageTest`
- `DirectMessageControllerTest`
- `DirectMessageServiceTest`

---

### PHASE 2: CONTEXTUAL THREAD MESSAGING (4-5 days)

#### 2.1 Backend - Message Threads (Polymorphic)

**Files to Create:**
```
app/Domains/Messaging/
├── Http/Controllers/MessageThreadController.php
├── Http/Requests/StoreThreadMessageRequest.php
└── Services/MessageThreadService.php
```

**Key Design:**
- `MessageThread` is polymorphic (Bid, Service, Offer, Order, QuickDeal)
- Auto-create thread when entity is created
- Auto-add relevant participants
- Broadcasting to presence channel

#### 2.2 Model Relationships

**Update These Models:**
```php
// Service.php, OpenOffer.php, OpenOfferBid.php, Order.php, QuickDeal.php
public function messageThread()
{
    return $this->morphOne(MessageThread::class, 'threadable');
}

// User.php (already has this)
public function messageThreads()
{
    return $this->belongsToMany(
        MessageThread::class,
        'thread_participants'
    )->withPivot('last_read_at')->withTimestamps();
}
```

#### 2.3 Frontend - Thread Messaging

**Components to Create:**
```
app/Livewire/Messages/
├── ThreadMessages.php        (Main thread display)
└── ThreadParticipants.php    (Show who's in thread)
```

**Integration Points:**
```blade
<!-- On Bid show: resources/views/bids/show.blade.php -->
<x-section title="Discussion">
    @livewire('messages.thread-messages', [
        'threadableType' => 'OpenOfferBid',
        'threadableId' => $bid->id
    ])
</x-section>

<!-- On Service show -->
<!-- On Order show -->
<!-- etc. -->
```

#### 2.4 Auto-Thread Creation Hook

```php
// app/Domains/Listings/Services/BidService.php
public function placeBid(array $data): OpenOfferBid
{
    $bid = OpenOfferBid::create($data);
    
    // Auto-create thread
    $thread = MessageThread::firstOrCreate(
        ['threadable_type' => OpenOfferBid::class, 'threadable_id' => $bid->id],
        ['subject' => "Discussion: {$bid->openOffer->title}"]
    );
    
    // Auto-add participants
    MessageThreadService::addParticipant($thread, $bid->bidder_id);
    MessageThreadService::addParticipant($thread, $bid->openOffer->creator_id);
    
    return $bid;
}
```

#### 2.5 Tests
- `ThreadMessagingTest`
- `PolymorphicThreadTest`

---

### PHASE 3: ACTIVITY THREAD CONVERSATIONS (3-4 days)

#### 3.1 Backend - Activity Thread Replies

**Files to Create:**
```
app/Domains/Work/
├── Http/Controllers/ActivityThreadController.php
└── Services/ActivityThreadService.php (extend existing)
```

**API Endpoints:**
```php
ActivityThreadController
├── GET  /api/work-instances/{instance}/threads
├── POST /api/work-instances/{instance}/threads    (Create with media)
├── POST /api/activity-threads/{thread}/messages   (Reply)
├── DELETE /api/activity-threads/{thread}/messages/{msg}
└── POST /api/activity-threads/{thread}/media      (Add media)
```

#### 3.2 Frontend - Activity Thread Comments

**Integration:**
```blade
<!-- In work instance view: resources/views/work/show.blade.php -->

<!-- Loop through activity threads -->
@foreach ($workInstance->threads as $thread)
    <div class="border rounded p-4 mb-4">
        <!-- Thread header -->
        <div class="flex justify-between mb-3">
            <div>
                <h4 class="font-semibold">{{ $thread->title }}</h4>
                <p class="text-sm text-gray-600">{{ $thread->creator->name }}</p>
            </div>
            <span class="badge" :class="typeClasses($thread->type)">
                {{ ucfirst($thread->type) }}
            </span>
        </div>

        <!-- Thread content -->
        <p class="mb-3">{{ $thread->content }}</p>

        <!-- Media grid -->
        @if ($thread->media->count() > 0)
            <div class="grid grid-cols-3 gap-2 mb-3">
                @foreach ($thread->media as $media)
                    <img src="{{ Storage::url($media->path) }}" 
                         class="rounded cursor-pointer hover:opacity-75">
                @endforeach
            </div>
        @endif

        <!-- Replies section -->
        <div class="bg-gray-50 rounded p-3 max-h-40 overflow-y-auto">
            @foreach ($thread->messages as $msg)
                <div class="text-sm mb-1">
                    <strong>{{ $msg->sender->name }}:</strong> {{ $msg->content }}
                </div>
            @endforeach
        </div>

        <!-- Reply input -->
        <form class="mt-2" @submit.prevent="addReply({{ $thread->id }})">
            <input type="text" placeholder="Reply..." class="w-full border rounded px-2 py-1">
        </form>
    </div>
@endforeach
```

#### 3.3 Media Handling

```php
// Validation in StoreActivityThreadRequest
'media.*' => ['file', 'max:50000'],  // 50MB

// Storage structure
// storage/app/public/activity-media/{year}/{month}/{thread_id}/{filename}

// Thumbnail generation (intervention/image)
// - Images: 200x200 webp
// - Videos: Extract first frame
// - Docs: Icon placeholder
```

#### 3.4 Tests
- `ActivityThreadTest`
- `ActivityThreadMediaTest`

---

## PART IV: INTEGRATION WITH EXISTING SYSTEMS

### Bidding System
When bid placed → Create thread & auto-add participants

### Service Pages  
Q&A thread on service detail page

### Order Execution
- Order-level thread (general discussion)
- Activity threads within work steps

### Quick Deals
Proposals + discussion thread in session room

---

## PART V: REAL-TIME BROADCASTING

### Channel Authorization (routes/channels.php)
```php
// Direct messages
Broadcast::channel('users.{id}', fn($user, $id) => (int)$user->id === (int)$id);

// Message threads
Broadcast::channel('threads.{threadId}', function ($user, $threadId) {
    $thread = MessageThread::find($threadId);
    return $thread && $thread->participants()->where('user_id', $user->id)->exists();
});

// Work instances
Broadcast::channel('work-instances.{instanceId}', function ($user, $instanceId) {
    $instance = WorkInstance::find($instanceId);
    $order = $instance->order;
    return $order->buyer_id === $user->id || $order->seller_id === $user->id;
});
```

---

## PART VI: NOTIFICATIONS

### In-App Notifications
```php
// app/Notifications/NewDirectMessage.php
class NewDirectMessage extends Notification
{
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }
    
    public function toDatabase($notifiable): array
    {
        return [
            'from_user' => $this->message->sender->name,
            'preview' => Str::limit($this->message->content, 100),
            'url' => route('messages.show', $this->message->sender_id),
        ];
    }
}

// app/Notifications/NewThreadMessage.php
// app/Notifications/NewActivityThread.php
```

### Email (Phase 8 - Future)
Add to `via()` after user preferences implemented.

---

## PART VII: SECURITY

### Authorization Policies
```php
// app/Policies/MessagePolicy.php
class MessagePolicy
{
    public function viewThread(User $user, MessageThread $thread): bool
    {
        return $thread->participants()->where('user_id', $user->id)->exists();
    }
    
    public function sendMessage(User $user, MessageThread $thread): bool
    {
        return $this->viewThread($user, $thread);
    }
    
    public function sendDirectMessage(User $user, User $recipient): bool
    {
        return $user->id !== $recipient->id;
    }
}
```

### Input Validation
- Max message length: 5000 chars
- XSS: Auto-escaped in Blade
- File uploads: Validate MIME types + size

---

## PART VIII: TESTING STRATEGY

### Unit Tests
- DirectMessageServiceTest
- MessageThreadServiceTest
- ActivityThreadServiceTest

### Feature Tests
- SendDirectMessageTest
- ThreadMessagingTest  
- ActivityThreadTest
- BroadcastingTest

### Livewire Component Tests
- DirectMessageThreadTest
- ThreadMessagesTest
- ActivityThreadListTest

---

## PART IX: TIMELINE

| Phase | Duration | Key Deliverables |
|-------|----------|------------------|
| **Phase 1: Direct Messaging** | 3-4 days | DM API + Livewire components + real-time |
| **Phase 2: Contextual Threads** | 4-5 days | Polymorphic threads + auto-creation + integration |
| **Phase 3: Activity Threads** | 3-4 days | Thread replies + media + work execution integration |
| **Polish & Testing** | 2-3 days | End-to-end tests + UI refinements |
| **TOTAL** | **~12-16 days** | **Full Messaging System Live** |

---

## PART X: READY FOR ORDER EXECUTION

✅ Activity threads prepared for work steps  
✅ Thread conversations ready for buyer/seller collab  
✅ Media attachments ready for proof submission  

**When workflow is implemented:**
- Hook `WorkInstanceProgressed` event to notify
- No changes needed — seamless integration

---

## PART XI: DELIVERABLES CHECKLIST

### Backend
- [ ] DirectMessageController + Service
- [ ] MessageThreadController + Service (polymorphic)
- [ ] ActivityThreadController (extend existing)
- [ ] Events (DirectMessageSent, ThreadMessageSent, etc.)
- [ ] Broadcasting channels configured
- [ ] Policies + Authorization
- [ ] Validation rules
- [ ] Full test suite

### Frontend
- [ ] DirectMessageList Livewire
- [ ] DirectMessageThread Livewire
- [ ] ThreadMessages Livewire
- [ ] ActivityThreadList Livewire
- [ ] All Blade templates
- [ ] Echo real-time listeners
- [ ] Toast notifications
- [ ] Unread indicators

### Integration
- [ ] Bidding → Auto-create threads
- [ ] Services → Q&A threads
- [ ] Orders → Discussion threads
- [ ] Quick Deals → Proposal threads
- [ ] Work Execution → Activity threads ready

---

## SUCCESS METRICS

✅ Real-time delivery <500ms  
✅ Zero unread count bugs  
✅ Mobile responsive  
✅ 100+ messages without lag  
✅ All entity types threaded  
✅ No N+1 queries  
✅ Broadcasting secure  
✅ All tests passing  

---

**Ready to start Phase 1?** Proceed with Direct Messaging for immediate user value.
