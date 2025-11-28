# Work Visibility - Quick Reference Guide

## What Changed?

✅ **Buyers can now see work progress** while sellers fulfill orders
✅ **Both can message each other** about work steps
✅ **Only sellers can complete steps** (buyer sees helpful message instead)
✅ **Clear role indicators** throughout the UI

---

## Buyer Experience

### What Buyers See
- ✅ Work progress timeline with all steps
- ✅ Real-time completion status updates
- ✅ Service details and pricing
- ✅ Seller information and contact
- ✅ Step-by-step discussions
- ✅ General order chat

### What Buyers CANNOT Do
- ❌ Start work steps
- ❌ Mark steps as complete
- ❌ Change work status
- ❌ Cancel work (only order level)

### How Buyers Message
1. Click on a step to see discussion thread
2. "Step Discussion" section shows all messages
3. Reply or post new messages
4. Seller gets notification immediately
5. Messages are permanent record

---

## Seller Experience

### What Sellers See
- ✅ Assigned work instances
- ✅ All steps they need to complete
- ✅ Buyer information
- ✅ Order and payment details
- ✅ Step discussions with buyer

### What Sellers Can Do
- ✅ Start a work step
- ✅ Complete a work step
- ✅ Post messages in step discussions
- ✅ Post messages in order chat
- ✅ See buyer's messages and reactions

### How Sellers Fulfill
1. Open work instance
2. Click "Start Step" button
3. Work on the step
4. Click "Complete Step" when done
5. Progress bar updates automatically
6. Buyer notified of completion

---

## Key UI Elements

### Role Badge (Header)
```
Seller: "Your Service to Deliver" (Blue)
Buyer:  "Your Purchase" (Green)
```

### Context Banner
```
Sellers:
  ⚙️ Work Fulfillment - You're the Seller
  Description: Work through each step...

Buyers:
  📋 Work Progress - You're the Buyer
  Description: Track the seller's progress...
```

### Participant Cards
```
Seller Card:
  - Highlighted in BLUE if you're the seller
  - Shows (You) label
  - Dark background and text

Buyer Card:
  - Highlighted in GREEN if you're the buyer
  - Shows (You) label
  - Dark background and text
```

### Step Actions
```
Seller View:
  [Start Step] or [Complete Step] buttons

Buyer View:
  💬 Only the seller can complete steps. 
     You can message about this step below.
```

---

## Access Control

### Can View Work?
- Seller: YES (their own work)
- Buyer: YES (their purchase)
- Admin: YES (with superuser)

### Can Start Steps?
- Seller: YES (only seller)
- Buyer: NO (grayed out with message)

### Can Complete Steps?
- Seller: YES (only seller)
- Buyer: NO (grayed out with message)

### Can Message?
- Seller: YES (any step, order chat)
- Buyer: YES (any step, order chat)

### Can See Messages?
- Seller: YES (everything)
- Buyer: YES (everything)

---

## Messaging Types

### Activity Thread (Per Step)
- Discussion about specific work step
- Shows in "Step Discussion" section
- Both parties can post
- Visible to both parties

### Work Chat (Whole Order)
- General questions/discussions
- Shows in "Work Chat" section at bottom
- Both parties can post
- Visible to both parties
- May be popup or inline based on view

---

## How to Test

### Test as Buyer
1. Login with buyer account
2. Go to Orders
3. Click on completed order
4. View work instance
5. Verify:
   - ✅ Can see all steps
   - ✅ Cannot see Start/Complete buttons
   - ✅ Can post messages
   - ✅ See "You're the Buyer" banner

### Test as Seller
1. Login with seller account
2. Go to Work Dashboard
3. Click on work instance
4. Verify:
   - ✅ Can see Start Step button
   - ✅ Can see Complete Step button (when in progress)
   - ✅ Can post messages
   - ✅ See "You're the Seller" banner

### Test Messaging
1. Buyer sends message in step discussion
2. Seller receives notification
3. Seller replies
4. Buyer receives notification
5. Both see full conversation thread

---

## Database Tables

### OrderStable
- `buyer_id` - User who purchased
- `seller_id` - User who will fulfill
- Connects to WorkInstance

### WorkInstance
- `order_id` - Parent order
- `status` - pending, in_progress, completed
- `progress` - % complete
- Links to WorkInstanceSteps

### WorkInstanceStep
- `work_instance_id` - Parent work
- `status` - pending, in_progress, completed
- `started_at`, `completed_at` - Timestamps
- Linked to ActivityThread

### ActivityThread
- `work_instance_step_id` - Which step
- Contains ActivityMessages

### ActivityMessage
- `activity_thread_id` - Which thread
- `user_id` - Who posted
- `content` - Message text
- Both buyer/seller can post

---

## Key Files

| File | Purpose | Changes |
|------|---------|---------|
| `WorkInstanceController.php` | Views and step management | Updated index() |
| `WorkInstancePolicy.php` | Authorization rules | Added comments |
| `work.show.blade.php` | Main work display | Added role banners & guards |
| `WorkInstancePolicy.php` | Who can do what | Documents buyer/seller split |
| `ActivityController.php` | Message management | No changes - already open |

---

## Troubleshooting

### Buyer Can't See Work
- Check authorization in policy
- Verify buyer_id in orders table
- Check WorkInstancePolicy.view()

### Seller Can't Mark Complete
- Verify seller_id in orders table
- Check WorkInstancePolicy.completeStep()
- Ensure work is not already completed

### Messages Not Showing
- Check ActivityThread exists
- Verify ActivityMessage records created
- Check messaging policy in WorkInstancePolicy

### Missing Buttons for Seller
- Verify user is seller (auth()->id() === order->seller_id)
- Check $isSeller variable in view
- Inspect HTML for conditional comments

---

## Future Roadmap

- [ ] Real-time updates without refresh
- [ ] Buyer approval gate before completion
- [ ] Revision request system
- [ ] Payment holds/releases per milestone
- [ ] Activity audit trail
- [ ] File attachments per step
- [ ] Ratings/reviews after completion
