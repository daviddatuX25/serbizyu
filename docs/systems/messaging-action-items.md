# 🚀 MESSAGING SYSTEM - ACTION ITEMS & PRIORITIES

**Status:** Ready to implement  
**Priority:** Phase 3 (after Phase 2.1 order system foundation)  
**Estimated Total Time:** 12-16 days  

---

## IMMEDIATE NEXT STEPS (This Week)

### Step 1: Verify Database (2 hours)
**Current Status Check:**
```bash
# Do these migrations exist?
☐ direct_messages table
☐ message_threads table  
☐ thread_participants table
☐ thread_messages table (IMPORTANT: check naming - avoid conflict with activity_threads)
☐ activity_threads table
☐ activity_thread_media table
```

**Action:**
```bash
# Check existing migrations
ls -la database/migrations/ | grep message
ls -la database/migrations/ | grep activity

# If missing, create them from master_plan.md schema
php artisan make:migration create_direct_messages_table
php artisan make:migration create_message_threads_table
# etc.

# Run migrations
php artisan migrate
```

---

### Step 2: Verify Models Exist (1 hour)
**Check These Files:**
```bash
app/Domains/Messaging/Models/
├─ ☐ DirectMessage.php
├─ ☐ MessageThread.php
├─ ☐ ThreadParticipant.php (many-to-many pivot model)
└─ ☐ ThreadMessage.php

app/Domains/Work/Models/
├─ ☐ ActivityThread.php (should already exist)
├─ ☐ ActivityThreadMedia.php (should already exist)
└─ ☐ ThreadMessage.php (check if it conflicts with messaging thread_message)
```

**Action:**
```bash
# If any models missing, create them
php artisan make:model Domains/Messaging/Models/DirectMessage
php artisan make:model Domains/Messaging/Models/MessageThread
php artisan make:model Domains/Messaging/Models/ThreadParticipant
php artisan make:model Domains/Messaging/Models/ThreadMessage

# NOTE: Watch out for naming conflicts!
# - Activity thread messages: activity_thread_id foreign key
# - Contextual thread messages: message_thread_id foreign key
# Can they share the same table? NO - different schemas. Keep separate or rename.
```

**Critical Note on `ThreadMessage` Naming:**
> The `thread_messages` table is used by BOTH:
> 1. Contextual threads (via `message_threads.id`)
> 2. Activity threads (via `activity_thread_id`)
>
> **Decision:** Rename to avoid confusion:
> - `thread_messages` → for contextual threads
> - `activity_thread_messages` → for work activity

---

### Step 3: Add Relationships to User Model (30 mins)
**File:** `app/Models/User.php`

**Add These Methods:**
```php
// Direct messaging
public function directMessagesAsSender()
{
    return $this->hasMany(DirectMessage::class, 'sender_id');
}

public function directMessagesAsReceiver()
{
    return $this->hasMany(DirectMessage::class, 'receiver_id');
}

// Contextual threads
public function messageThreads()
{
    return $this->belongsToMany(
        MessageThread::class,
        'thread_participants',
        'user_id',
        'message_thread_id'
    )->withPivot('last_read_at')->withTimestamps();
}

// Activity threads
public function activityThreads()
{
    return $this->hasMany(ActivityThread::class, 'creator_id');
}
```

---

## WEEK 1: PHASE 1 - DIRECT MESSAGING

### Day 1: Backend API Setup (8 hours)

**Create These Files:**

1. **Service Layer**
```bash
touch app/Domains/Messaging/Services/DirectMessageService.php
```

**Implement:**
```php
namespace App\Domains\Messaging\Services;

class DirectMessageService
{
    public function sendMessage(User $sender, User $receiver, string $content): DirectMessage
    {
        // Create record
        // Fire event
        // Return message
    }
    
    public function markAsRead(DirectMessage $message): void
    {
        // Update read_at
    }
    
    public function getConversations(User $user, int $limit = 10): Collection
    {
        // Get latest message from each conversation
    }
    
    public function getHistory(User $user1, User $user2, int $page = 1): LengthAwarePaginator
    {
        // Get paginated message history between two users
    }
    
    public function getUnreadCount(User $user): int
    {
        // Count unread messages
    }
}
```

2. **Controller**
```bash
touch app/Domains/Messaging/Http/Controllers/DirectMessageController.php
```

**Implement All Endpoints:**
```php
class DirectMessageController extends Controller
{
    public function conversations()
    {
        // GET /api/messages/conversations
        // Return list of conversations with latest message
    }
    
    public function history($userId)
    {
        // GET /api/messages/{userId}/history
        // Return paginated conversation history
    }
    
    public function store($userId)
    {
        // POST /api/messages/{userId}
        // Send message
    }
    
    public function markAsRead($messageId)
    {
        // PUT /api/messages/{messageId}/read
        // Mark as read
    }
    
    public function unreadCount()
    {
        // GET /api/messages/unread/count
        // Return unread count
    }
}
```

3. **Request Validation**
```bash
touch app/Domains/Messaging/Http/Requests/StoreDirectMessageRequest.php
```

4. **Events**
```bash
php artisan make:event DirectMessageSent
```

5. **Routes**
Add to `routes/api.php`:
```php
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

6. **Tests**
```bash
touch tests/Feature/DirectMessagingTest.php
touch tests/Unit/DirectMessageServiceTest.php
```

**Run Tests:**
```bash
php artisan test tests/Feature/DirectMessagingTest.php
```

---

### Day 2: Frontend Components (8 hours)

**Create Livewire Components:**

1. **Message List Component**
```bash
php artisan make:livewire Messages/DirectMessageList
```

**Functionality:**
- Display list of conversations
- Show latest message preview
- Unread indicator
- Search by user name
- Click to open thread

2. **Message Thread Component**
```bash
php artisan make:livewire Messages/DirectMessageThread
```

**Functionality:**
- Display messages in conversation
- Input field to send
- Auto-scroll to latest
- Real-time updates

3. **Badge Component**
```bash
php artisan make:livewire MessageNotificationBadge
```

**Create Views:**
```bash
touch resources/views/livewire/messages/direct-message-list.blade.php
touch resources/views/livewire/messages/direct-message-thread.blade.php
touch resources/views/messages/index.blade.php (layout)
```

**Add Web Routes:**
```php
Route::middleware('auth')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
});
```

---

### Day 3: Real-Time & Testing (8 hours)

**Setup Broadcasting:**

1. **Configure Soketi (if not already done)**
```bash
npm install -g @soketi/soketi
soketi start
# Or in Docker
docker run -p 6001:6001 soketi/soketi:latest
```

2. **Add Channel Authorization**
```bash
# Edit routes/channels.php
Broadcast::channel('users.{id}', fn($user, $id) => (int)$user->id === (int)$id);
```

3. **Create Event Listener**
```bash
php artisan make:listener BroadcastDirectMessageSent
```

4. **Add Echo Listeners in Components**
```javascript
// In DirectMessageThread Livewire component
Echo.private(`users.{{ auth()->id() }}`)
    .listen('DirectMessageSent', (e) => {
        // Update messages
    })
```

5. **Full Test Suite**
```bash
php artisan test tests/Feature/DirectMessagingTest.php
php artisan test tests/Unit/DirectMessageServiceTest.php
php artisan test tests/Feature/Livewire/DirectMessageThreadTest.php
```

---

## WEEK 2: PHASE 2 - CONTEXTUAL THREADS

### Day 4-5: Polymorphic Thread Setup (16 hours)

**Create Controller & Service:**
```bash
touch app/Domains/Messaging/Http/Controllers/MessageThreadController.php
touch app/Domains/Messaging/Services/MessageThreadService.php
touch tests/Feature/ThreadMessagingTest.php
```

**Add Polymorphic Relationships to Models:**
```php
// Service.php
public function messageThread() { 
    return $this->morphOne(MessageThread::class, 'threadable'); 
}

// OpenOfferBid.php, OpenOffer.php, Order.php, QuickDeal.php
// Same pattern
```

**Implement Auto-Thread Creation Hooks:**

In each service (BidService, ServiceService, etc.):
```php
// When bid is placed
MessageThreadService::createOrGetThread(
    threadable: $bid,
    subject: "Bid Discussion"
);
```

**Create Tests:**
```bash
php artisan test tests/Feature/ThreadMessagingTest.php
```

---

### Day 6-7: Thread Frontend & Integration (16 hours)

**Create Livewire Components:**
```bash
php artisan make:livewire Messages/ThreadMessages
php artisan make:livewire Messages/ThreadParticipants
```

**Create Views & Routes:**
```bash
touch resources/views/livewire/messages/thread-messages.blade.php
touch resources/views/livewire/messages/thread-participants.blade.php
```

**Add Routes:**
```php
Route::prefix('threads')->middleware('auth:sanctum')->group(function () {
    Route::get('{threadableType}/{threadableId}', 
        MessageThreadController::class.'@getOrCreate');
    Route::get('{thread}/messages', 
        MessageThreadController::class.'@messages');
    Route::post('{thread}/messages', 
        MessageThreadController::class.'@storeMessage');
    // etc.
});
```

**Integrate into Existing Views:**
```blade
<!-- resources/views/bids/show.blade.php -->
@livewire('messages.thread-messages', [
    'threadableType' => 'OpenOfferBid',
    'threadableId' => $bid->id
])

<!-- Similar for: services, offers, orders -->
```

---

## WEEK 2-3: PHASE 3 - ACTIVITY THREADS

### Day 8-9: Activity Thread Replies (16 hours)

**Extend Existing Services:**
```php
// app/Domains/Work/Services/ActivityThreadService.php
// Add methods for replies, media, etc.
```

**Create Controller:**
```bash
touch app/Domains/Work/Http/Controllers/ActivityThreadController.php
```

**Add API Endpoints:**
```php
Route::prefix('activity-threads')->middleware('auth:sanctum')->group(function () {
    Route::get('{thread}/messages', 
        ActivityThreadController::class.'@getMessages');
    Route::post('{thread}/messages', 
        ActivityThreadController::class.'@addMessage');
    Route::post('{thread}/media', 
        ActivityThreadController::class.'@uploadMedia');
    // etc.
});
```

---

### Day 10: Activity Thread Frontend (8 hours)

**Create Views:**
```blade
<!-- Integrate into work/show.blade.php -->
@foreach ($workInstance->threads as $thread)
    <!-- Display thread with replies form -->
@endforeach
```

**Test:**
```bash
php artisan test tests/Feature/ActivityThreadTest.php
```

---

## WEEK 3: POLISH & INTEGRATION

### Day 11: End-to-End Testing (8 hours)

**Test All Flows:**
```bash
# Complete bidding flow → thread created
# Complete order flow → threads + activity ready
# Direct messaging → real-time working
# Quick deals → threaded

php artisan test
```

---

### Day 12: UI Refinements & Deployment (8 hours)

**Checklist:**
- [ ] Mobile responsive
- [ ] All unread counts working
- [ ] No N+1 queries
- [ ] Notifications working
- [ ] Performance tested
- [ ] Security audit
- [ ] Documentation complete

---

## DEPENDENCIES TO CHECK NOW

```
✓ Are these models in place?
  ├─ DirectMessage
  ├─ MessageThread
  ├─ ThreadParticipant
  ├─ ThreadMessage
  ├─ ActivityThread
  └─ ActivityThreadMedia

✓ Are these migrations run?
  ├─ direct_messages
  ├─ message_threads
  ├─ thread_participants
  ├─ thread_messages
  ├─ activity_threads
  └─ activity_thread_media

✓ Is broadcasting configured?
  ├─ config/broadcasting.php
  ├─ routes/channels.php
  └─ npm packages installed

✓ Are these Domains set up?
  ├─ app/Domains/Messaging/
  └─ app/Domains/Work/ (already exists)
```

---

## BLOCKERS TO RESOLVE FIRST

❌ **Phase 2 Integration (Order System)**
- The order system foundation must exist first
- Thread creation hooks depend on Order model working
- Recommendation: Ensure Phase 2.1 is done first

❌ **Work Execution System**
- Activity threads depend on WorkInstance existing
- But system is designed to be forward-compatible
- Safe to implement now, just won't be used until Phase 2.2

---

## RISK MITIGATION

| Risk | Mitigation |
|------|-----------|
| Real-time latency | Use queue workers + profile performance early |
| DB query bloat | Add eager loading from day 1 |
| Broadcasting auth | Test channel access control thoroughly |
| Naming conflicts | Use clear naming: thread_messages vs activity_thread_messages |
| Scope creep | Stick to MVP (no typing indicators, no read receipts yet) |

---

## SUCCESS CRITERIA

By end of Phase 3, verify:

```
✓ Can send/receive direct messages in real-time
✓ Direct message unread count accurate
✓ Message threads auto-created for all entity types
✓ Can send message in bid thread
✓ Can send message in service Q&A
✓ Can send message in order discussion
✓ Can create activity thread on work step
✓ Can reply to activity thread with media
✓ All participants notified via broadcast
✓ Unread indicators work everywhere
✓ Mobile messaging UI responsive
✓ No console errors in browser
✓ All feature tests passing
✓ No N+1 queries (use Laravel Debugbar)
✓ Performance: page loads <2s
```

---

## NEXT IMMEDIATE ACTION

**This Week:**
1. Verify database schema & run migrations
2. Verify/create all models
3. Start Day 1 of Phase 1 implementation

**Ready?** Create a branch:
```bash
git checkout -b feature/messaging-system-phase-1
```

Then begin with `DirectMessageService` implementation.

---

**Questions Before Starting?**
- Is the Order System Phase 2.1 complete?
- Are all migrations from master_plan.md already run?
- Is Soketi/broadcast setup working?
- Any naming conflicts with `thread_messages` table?

Clarify these and we're good to go! 🚀
