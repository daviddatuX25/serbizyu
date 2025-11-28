# How to Use the Messaging System - Complete User Guide

**Status:** ✅ FULLY INTEGRATED AND OPERATIONAL

Your system has a **complete, fully functional messaging system** integrated into both Orders and Bids. Here's exactly how to use it.

---

## 1. MESSAGING IN ORDERS

### How It Works

Every order automatically gets a message thread created. The buyer and seller can chat directly about the order.

### How to Start Chatting in an Order

#### Step 1: Navigate to an Order
1. Go to **Orders** from your dashboard
2. Click on any order to view details
3. You'll see a **"Messages"** tab next to the **"Details"** tab

```
┌─────────────────────────────┐
│ Order Details     Messages   │  ← Click on "Messages"
└─────────────────────────────┘
```

#### Step 2: Open the Chat
1. Click the **"Messages"** tab
2. The chat interface appears (if a message thread exists)
3. You'll see the name of the person you're chatting with:
   - If you're the **buyer**: "Chat with [Seller Name]"
   - If you're the **seller**: "Chat with [Buyer Name]"

#### Step 3: Send a Message
1. Type your message in the **text input** at the bottom
2. (Optional) **Attach files** by clicking the file upload button
3. Click **"Send"** button
4. The message appears instantly in the chat

### Message Features

**Your Messages:**
- Appear in **blue** on the right side
- Show your name and timestamp
- Can include file attachments (up to 10MB per file)

**Other Person's Messages:**
- Appear in **gray** on the left side
- Show their name and timestamp
- May include file attachments

**Read Status:**
- Messages automatically marked as read when you open the chat
- You know the other person has read your messages when they send a reply

### Unread Message Badge

When you have **unread messages** in an order:
1. A **red badge** appears on the "Messages" tab
2. The badge shows the **number of unread messages**
3. The badge disappears when you open the chat

```
┌─────────────────────────────┐
│ Order Details  Messages  [3] │  ← Red badge shows 3 unread
└─────────────────────────────┘
```

### When Can You Message?

✅ **You can message when:**
- The order exists (even if work hasn't started)
- You're either the buyer or seller
- The order is not cancelled

❌ **You cannot message when:**
- The order is cancelled
- You try to view messages from a different order you're not part of

---

## 2. MESSAGING IN BIDS

### How It Works

When a **bidder** submits a bid on an open offer, a message thread is created. The bidder and offer creator can chat about the bid.

### How to Start Chatting in a Bid

#### For Bidders (Who Submitted a Bid)

##### Step 1: Navigate to Your Bids
1. Go to **Creator Dashboard** → **Open Offers** → **Bids** (or your bids list)
2. Click on a bid to view it
3. You'll see a **"Messages"** tab

##### Step 2: Open the Chat
1. Click the **"Messages"** tab
2. The chat interface appears
3. You're chatting with the **offer creator**

##### Step 3: Send a Message
1. Type your message in the text input
2. (Optional) Attach files
3. Click **"Send"**

#### For Offer Creators (Who Received a Bid)

##### Step 1: Navigate to Received Bids
1. Go to **Creator Dashboard** → **Open Offers**
2. Click on an open offer to see all bids
3. Click on a specific bid to view details
4. You'll see a **"Messages"** tab

##### Step 2: Open the Chat
1. Click the **"Messages"** tab
2. The chat interface appears
3. You're chatting with the **bidder**

##### Step 3: Send a Message
1. Type your message in the text input
2. (Optional) Attach files
3. Click **"Send"**

### Bid Messaging Features

**Same as Order Messaging:**
- Messages are timestamped
- Automatic read status tracking
- File attachment support (up to 10MB)
- Color-coded: your messages blue, others gray
- Unread count badge

### When Can You Message in Bids?

✅ **You can message when:**
- You're the bidder or offer creator
- The bid is **not rejected**
- The bid exists

❌ **You cannot message when:**
- The bid is **rejected**
- You try to access a bid you didn't participate in

---

## 3. MESSAGE NOTIFICATIONS

### Email Notifications

**When you receive a message, you get:**
1. **Instant email notification** (if configured)
   - Shows sender's name
   - Shows message preview (first 150 characters)
   - Has a button to "View Full Conversation"

**Email includes:**
```
From: Serbizyu Support <noreply@serbizyu.com>
Subject: New message in Order #123

Hi [Your Name],

[Sender] sent you a message in Order #123.

Message Preview: [First 150 chars of message...]

[View Full Conversation Button]
```

### Database Notifications

**When you receive a message:**
1. A notification is stored in your account
2. You can view all notifications in your **notification center**
3. Notifications include:
   - Who sent it
   - Which order/bid it's about
   - Quick link to the conversation

### When Do You Get Notified?

✅ **Notifications sent when:**
- Someone sends you a message in an order
- Someone sends you a message in a bid
- You're the recipient (opposite party)

❌ **No notifications when:**
- You send your own messages
- The chat partner is viewing the same chat

---

## 4. FILE ATTACHMENTS IN MESSAGES

### How to Attach Files

1. In the chat, click the **file upload input**
2. **Select files** from your computer
3. You can select **multiple files** at once
4. Each file can be up to **10MB**
5. Click **"Send"** to send the message with files

### Supported File Types

All file types are supported, including:
- **Images:** `.jpg`, `.png`, `.gif`, `.webp`
- **Documents:** `.pdf`, `.doc`, `.docx`, `.xls`, `.xlsx`
- **Archives:** `.zip`, `.rar`, `.7z`
- **Videos:** `.mp4`, `.mov`, `.avi`
- **Audio:** `.mp3`, `.wav`, `.m4a`

### Downloading Attachments

1. In the message, you'll see "Attachments:" section
2. Click on the attachment link to **download**
3. Opens in a new browser tab/window

---

## 5. FILE STRUCTURE & ROUTING

### API Endpoints (For Developers)

**Order Messaging:**
```
GET    /orders/{order}/messages              - List all messages
POST   /orders/{order}/messages              - Send a message
GET    /orders/{order}/messages/thread       - Get/create thread
POST   /orders/{order}/messages/read         - Mark as read
```

**Bid Messaging:**
```
GET    /bids/{bid}/messages                  - List all messages
POST   /bids/{bid}/messages                  - Send a message
GET    /bids/{bid}/messages/thread           - Get/create thread
POST   /bids/{bid}/messages/read             - Mark as read
```

### View Files

**Components:**
- `app/Livewire/ChatInterface.php` - Main chat component
- `app/Livewire/OrderChat.php` - Order chat wrapper
- `app/Livewire/BidChat.php` - Bid chat wrapper
- `app/Livewire/OrderMessagesCount.php` - Unread badge for orders
- `app/Livewire/BidMessagesCount.php` - Unread badge for bids

**Views:**
- `resources/views/livewire/chat-interface.blade.php` - Chat UI
- `resources/views/livewire/order-chat.blade.php` - Order chat layout
- `resources/views/livewire/bid-chat.blade.php` - Bid chat layout

**Controllers:**
- `app/Domains/Orders/Http/Controllers/OrderMessageController.php`
- `app/Domains/Listings/Http/Controllers/BidMessageController.php`

### Database Tables

```sql
message_threads        -- Conversation container
├── id
├── creator_id         -- Who created the thread
├── parent_type        -- "Order" or "OpenOfferBid"
├── parent_id          -- ID of the order/bid
├── title
├── created_at

messages               -- Individual messages
├── id
├── message_thread_id
├── sender_id          -- Who sent the message
├── content            -- Message text
├── read_at            -- When read (or null if unread)
├── created_at

message_attachments    -- File attachments
├── id
├── message_id
├── file_path          -- Path to uploaded file
├── file_type          -- MIME type

notifications          -- Email/DB notification records
├── id
├── notifiable_type    -- User
├── notifiable_id
├── type               -- OrderMessageNotification, BidMessageNotification
├── data               -- JSON data
├── read_at
├── created_at
```

---

## 6. REALTIME FEATURES (When Configured)

### Broadcasting Setup

When **broadcasting is enabled** (Pusher/Redis), you get:

**Realtime Message Delivery:**
- Messages appear instantly without page refresh
- Uses WebSocket connection for live updates

**Typing Indicators:**
- See when the other person is typing
- Shows "[User] is typing..." indicator
- Automatically clears after 3 seconds

**How to Enable:**
1. Set up Pusher account (https://pusher.com)
2. Add Pusher credentials to `.env`
3. Install frontend dependencies: `npm install laravel-echo pusher-js`
4. Configure broadcasting driver: `BROADCAST_DRIVER=pusher`

See `MESSAGING_SYSTEM_CONFIGURATION_GUIDE.md` for detailed setup.

---

## 7. COMMON QUESTIONS

### Q: Can I message someone I haven't done business with?

**A:** No. You can only message:
- In an **order** you're part of (as buyer or seller)
- In a **bid** you participated in (as bidder or offer creator)

The system uses authorization to prevent unauthorized access.

### Q: What happens if the order gets cancelled?

**A:** You can no longer send new messages in a cancelled order, but:
- You can still **view** past messages
- The conversation history is preserved
- You might see a "Cannot message a cancelled order" message if you try to send

### Q: What if the bid gets rejected?

**A:** You can no longer send new messages in a rejected bid, but:
- You can still **view** past messages
- The conversation is archived
- You'll see "Cannot message a rejected bid" if you try to send

### Q: How long are messages stored?

**A:** Indefinitely! Messages are:
- Stored permanently in the database
- Accessible even after order completion
- Preserved with soft deletes (can be restored if needed)

### Q: Can I delete messages?

**A:** Currently: No direct delete button in UI
- Messages are permanent for legal/audit purposes
- Contact support if you need message removal

Future feature: Message deletion may be added with audit trail.

### Q: Are my messages private?

**A:** Yes! Messages are:
- Only visible to the participants (buyer/seller, bidder/creator)
- Transmitted securely via HTTPS
- Broadcast via private channels (not public)
- Protected by authorization policies

### Q: What if I don't see the Messages tab?

**Possible causes:**
1. **Order hasn't been created yet** - Message thread is created automatically when order is created
2. **You're not part of this order** - You must be buyer or seller
3. **Not logged in** - Login required to access messaging

**Solution:**
1. Make sure the order exists
2. Check you're the buyer or seller
3. Refresh the page

### Q: Messages aren't appearing in real-time?

**This is normal if broadcasting isn't configured yet.**

Messages will:
- ✅ Save to database immediately
- ✅ Appear after page refresh
- ✅ Trigger email notifications (if configured)
- ⏳ Appear realtime once broadcasting is set up

To enable realtime:
1. Follow configuration guide in `MESSAGING_SYSTEM_CONFIGURATION_GUIDE.md`
2. Set `BROADCAST_DRIVER=pusher`
3. Add Pusher credentials

---

## 8. QUICK START CHECKLIST

### For Orders
- [ ] Create or view an order
- [ ] Click "Messages" tab
- [ ] See the other participant's name
- [ ] Type a message
- [ ] Click "Send"
- [ ] See message appear in chat

### For Bids
- [ ] Create a bid or view one you received
- [ ] Click "Messages" tab
- [ ] See the other participant's name
- [ ] Type a message
- [ ] Click "Send"
- [ ] See message appear in chat

### Get Notifications
- [ ] Configure mail service (see configuration guide)
- [ ] Have someone send you a message
- [ ] Check your email for notification
- [ ] Click "View Full Conversation"

---

## 9. ADVANCED: REALTIME SETUP (Optional)

### Why Set This Up?

Currently messages work great, but require page refresh. With realtime:
- ✅ Messages appear instantly
- ✅ See typing indicators
- ✅ No page refresh needed
- ✅ Better user experience

### Quick Setup (5 minutes)

```bash
# 1. Add to .env
BROADCAST_DRIVER=pusher
QUEUE_CONNECTION=database
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

# 2. Install npm dependencies
npm install laravel-echo pusher-js

# 3. Start queue worker (in new terminal)
php artisan queue:work

# 4. Done!
```

Full guide: See `MESSAGING_SYSTEM_CONFIGURATION_GUIDE.md`

---

## 10. SUPPORT & TROUBLESHOOTING

### System is Working - Verified ✅

- ✅ Message creation and storage
- ✅ Auto-thread creation for orders/bids
- ✅ Authorization enforcement
- ✅ File attachment support
- ✅ Read status tracking
- ✅ UI components integrated
- ✅ Routes configured
- ✅ Database tables created

### What's Optional

- ⏳ **Broadcasting** (Pusher) - For realtime updates
- ⏳ **Email notifications** - Requires mail service
- ⏳ **Typing indicators** - Requires broadcasting

### Troubleshooting

**Messages tab shows "No conversation yet"?**
- This is normal for brand new orders/bids
- Message thread exists, just no messages yet
- Start typing to create first message

**Can't see other person's messages?**
- Refresh the page
- Make sure you're viewing the same order/bid
- Check you're logged in as correct user

**Getting authorization errors?**
- Make sure you're part of the order/bid
- Can't message cancelled orders
- Can't message rejected bids

**Email notifications not arriving?**
- Mail service not configured yet
- See `MESSAGING_SYSTEM_CONFIGURATION_GUIDE.md`
- Test with: `php artisan tinker` → test mail command

---

## Summary

Your messaging system is **fully integrated and ready to use right now**:

1. **Orders** ✅ Chat with buyer/seller in every order
2. **Bids** ✅ Chat with bidder/creator in every bid
3. **Files** ✅ Attach documents, images, anything up to 10MB
4. **Notifications** ✅ Get alerts when someone messages you
5. **Realtime** ⏳ Optional - set up Pusher for instant updates

**Start using it now** by opening any order or bid and clicking the "Messages" tab!

---

*Last Updated: November 26, 2025*
