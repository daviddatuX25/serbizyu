# 📱 MESSAGING SYSTEM - QUICK REFERENCE

## Three-Tier Architecture at a Glance

```
TIER 1: DIRECT MESSAGING          TIER 2: CONTEXTUAL THREADS        TIER 3: ACTIVITY THREADS
User ↔ User (Private)             Entity + Group Chat                Work Step Discussions
────────────────────────          ──────────────────────             ────────────────────
┌─────────────────────┐            ┌──────────────────────┐           ┌────────────────────┐
│ Direct Messages     │            │ Message Threads      │           │ Activity Threads   │
├─────────────────────┤            ├──────────────────────┤           ├────────────────────┤
│ sender_id           │            │ threadable_type      │           │ work_instance_id   │
│ receiver_id         │            │ threadable_id        │           │ creator_id         │
│ content             │            │ subject              │           │ content            │
│ read_at             │            │ has many             │           │ type (enum)        │
└─────────────────────┘            │   - participants     │           │ has many           │
                                   │   - messages         │           │   - media          │
Broadcasting:                      └──────────────────────┘           │   - messages       │
Echo.private('users.{id}')                                            └────────────────────┘
                                   Broadcasting:
                                   Echo.join('threads.{id}')          Broadcasting:
                                                                       Echo.join('work-instances.{id}')
```

---

## Workflow Integration Map

```
BIDDING FLOW
┌────────────────────────────────────────────────────────────┐
│ User places BID on OpenOffer                               │
└───────────────┬──────────────────────────────────────────┘
                │
                ├─→ OpenOfferBid created
                ├─→ MessageThread auto-created
                │   ├─ threadable_type: 'OpenOfferBid'
                │   ├─ subject: "Discussion: {offer_title}"
                │   └─ participants auto-added:
                │       ├─ Bidder
                │       └─ Offer Creator
                │
                └─→ BidPlaced event → broadcast

                Users can now:
                ✓ Negotiate price in thread
                ✓ Ask clarifying questions
                ✓ Share requirements


ORDER FLOW
┌────────────────────────────────────────────────────────────┐
│ Bid accepted → Order created                               │
└───────────────┬──────────────────────────────────────────┘
                │
                ├─→ Order created
                ├─→ MessageThread auto-created
                │   ├─ threadable_type: 'Order'
                │   └─ participants: Buyer + Seller
                │
                ├─→ WorkInstances created (per workflow steps)
                │   └─→ ActivityThreads ready for step-level discussions
                │
                └─→ Workflow starts

                Users can now:
                ✓ Discuss order requirements (thread)
                ✓ Post evidence on work steps (activity threads)
                ✓ Ask questions within steps (activity prompts)


QUICK DEAL FLOW
┌────────────────────────────────────────────────────────────┐
│ Quick Deal session started                                  │
└───────────────┬──────────────────────────────────────────┘
                │
                ├─→ QuickDeal created
                ├─→ MessageThread auto-created
                │   └─ participants: Creator + Proposers
                │
                ├─→ Service proposals come in (real-time)
                │
                └─→ Deal accepted → Order created

                Users can now:
                ✓ Discuss proposals in thread
                ✓ Chat live in session
                ✓ Negotiate terms


SERVICE LISTING
┌────────────────────────────────────────────────────────────┐
│ Service page viewed by potential customers                 │
└───────────────┬──────────────────────────────────────────┘
                │
                ├─→ Service detail shown
                ├─→ Q&A Thread section available
                │   └─ Potential buyers can ask questions
                │
                └─→ Service owner can respond

                Users can now:
                ✓ Ask pre-purchase questions
                ✓ Get quick answers
                ✓ Build confidence before ordering
```

---

## Database Relationships

```
User (1) ──→ (Many) DirectMessage (as sender or receiver)
User (Many) ──→ (Many) MessageThread (through thread_participants)
User (1) ──→ (Many) ActivityThread (as creator)
User (1) ──→ (Many) ThreadMessage (as sender)

MessageThread (1) ──→ (Polymorphic) Threadable
  ├─ Service
  ├─ OpenOfferBid
  ├─ OpenOffer
  ├─ Order
  └─ QuickDeal

MessageThread (1) ──→ (Many) ThreadParticipant
MessageThread (1) ──→ (Many) ThreadMessage

WorkInstance (1) ──→ (Many) ActivityThread
ActivityThread (1) ──→ (Many) ActivityThreadMedia
ActivityThread (1) ──→ (Many) ThreadMessage (activity thread replies)
```

---

## API Quick Map

### Direct Messaging
```
GET    /api/messages/conversations              List all conversations
GET    /api/messages/{userId}/history           Get message history
POST   /api/messages/{userId}                    Send message
PUT    /api/messages/{messageId}/read            Mark as read
GET    /api/messages/unread/count                Get unread badge count
```

### Contextual Threads
```
GET    /api/threads/{threadable_type}/{id}     Get or create thread
GET    /api/threads/{threadId}/messages        Get messages (paginated)
POST   /api/threads/{threadId}/messages        Send message
POST   /api/threads/{threadId}/participants    Add participant
PUT    /api/threads/{threadId}/read             Mark thread as read
GET    /api/threads/{threadId}/participants    List participants
```

### Activity Threads
```
GET    /api/work-instances/{instanceId}/threads           List threads
POST   /api/work-instances/{instanceId}/threads           Create thread (with media)
GET    /api/activity-threads/{threadId}/messages          Get replies
POST   /api/activity-threads/{threadId}/messages          Add reply
POST   /api/activity-threads/{threadId}/media             Upload media
DELETE /api/activity-threads/{threadId}/messages/{msgId}  Delete reply (own)
```

---

## Broadcasting Channels

```
PRIVATE CHANNELS (Authorization Required)
├─ users.{userId}              → Direct message notifications
└─ orders.{orderId}            → Order updates

PRESENCE CHANNELS (Who's Online)
├─ threads.{threadId}          → Thread participants (show who's viewing)
└─ work-instances.{instanceId} → Buyer & seller online status

EVENTS BROADCAST
├─ DirectMessageSent           → New DM received
├─ ThreadMessageSent           → New message in thread
├─ ActivityThreadCreated       → New work activity
├─ ActivityThreadMessageAdded  → Reply to activity
└─ UserOnline/UserOffline      → Presence updates
```

---

## Livewire Components Needed

```
Messaging Domain:
├─ DirectMessageList              (Conversation sidebar list)
├─ DirectMessageThread            (Active conversation)
├─ MessageNotificationBadge       (Unread count icon)
├─ ThreadMessages                 (Contextual thread display)
└─ ThreadParticipants             (Who's in the thread)

Work Domain:
├─ ActivityThreadList             (List threads for work step)
├─ ActivityThreadForm             (Create new thread)
└─ ActivityThreadReply            (Reply to thread)

Shared:
└─ ToastNotification              (Real-time alerts)
```

---

## Real-Time Flow Example

```
User A sends message to User B:

1. User A types in DirectMessageThread component
2. Presses Send
3. Livewire calls API: POST /api/messages/B
4. Backend creates DirectMessage record
5. Backend fires DirectMessageSent event
6. Event broadcasts to users.{B} channel
7. User B receives in real-time via Echo listener
8. Livewire auto-updates conversation list & thread
9. Toast notification shown "New message from A"
10. Unread count badge updates
11. Message appears instantly (no page refresh)

Time: ~50-200ms depending on network
```

---

## Security Checkpoints

✓ Authorization Policy checks:
  - Can only view own direct messages
  - Can only view threads you're a participant of
  - Can only send to threads you belong to

✓ Input Validation:
  - Max 5000 characters per message
  - File size limits (50MB total)
  - MIME type validation for media

✓ Broadcast Channels:
  - Private channels require authentication
  - Presence channels verify participant status
  - No public broadcasting of sensitive data

✓ XSS Prevention:
  - All user content escaped in Blade templates
  - Never use {!! for user messages

---

## Implementation Phases Summary

| Phase | Focus | Files | Time |
|-------|-------|-------|------|
| **1** | Direct Messaging | Controller + Service + 2 Livewire | 3-4 days |
| **2** | Contextual Threads | Polymorphic setup + integration | 4-5 days |
| **3** | Activity Threads | Thread replies + media handling | 3-4 days |
| **Polish** | Tests + UI refinement | Full test suite | 2-3 days |

---

## Success Checklist

Before marking complete:

```
Backend
☐ All API endpoints working
☐ Authorization policies enforced
☐ Real-time events broadcasting
☐ Unread tracking accurate
☐ N+1 queries fixed
☐ Full test coverage

Frontend
☐ All components rendering
☐ Real-time updates working
☐ Unread badges showing
☐ Mobile responsive
☐ No console errors
☐ Notifications working

Integration
☐ Bids create threads ✓
☐ Orders create threads ✓
☐ Services have Q&A ✓
☐ Quick deals threaded ✓
☐ Activity threads ready ✓

Deployment
☐ Broadcast channels secure
☐ No production secrets in code
☐ Database backups configured
☐ Monitoring set up
```

---

## Quick Troubleshooting

| Issue | Cause | Fix |
|-------|-------|-----|
| Messages not appearing | Broadcasting not running | Start queue worker + Soketi |
| Unread count wrong | Last_read_at not updated | Check markAsRead endpoint |
| Thread not created | Service not hooked | Verify event listeners |
| Media not uploading | Storage link missing | Run `php artisan storage:link` |
| No real-time updates | Echo not initialized | Check bootstrap.js |
| Authorization errors | Policy not checked | Add `$this->authorize()` |

---

## Resources

- Master Plan: `master_plan.md` (Full DB schema)
- Implementation Details: `MESSAGING_IMPLEMENTATION_PLAN.md`
- API Testing: Use Postman collection (to create)
- Broadcasting: `config/broadcasting.php`
- Channels: `routes/channels.php`

---

**This system is modular** — each tier can be developed independently. Start with Tier 1 (Direct Messaging) for quick wins, then expand!
