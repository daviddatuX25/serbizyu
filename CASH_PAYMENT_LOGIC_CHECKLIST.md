# CASH PAYMENT HANDSHAKE - LOGIC EDGE CASES CHECKLIST

**Status:** Processing all edge cases  
**Date:** November 26, 2025

---

## PHASE 1: BUYER VIEW LOGIC - All Edge Cases

### 1.1 Initial State (No Action Yet)
- [ ] Display: "Payment Notification" heading
- [ ] Button text: "✓ Payment Sent" (NOT "Confirm Payment Sent")
- [ ] Button state: ENABLED (clickable, blue)
- [ ] Step 2 label: "Notify Seller" (NOT "Request Confirmation")
- [ ] Step 3 label: "Seller Confirms Receipt"
- [ ] Status indicator: Shows "Pending"
- [ ] Help text: "Notify the seller that you've sent the payment"
- [ ] Cache state: buyer_claimed_at = null
- [ ] Polling: Active (checks every 2 seconds)

### 1.2 After Buyer Clicks "Payment Sent"
- [ ] Button text changes to: "✓ Payment Notified"
- [ ] Button state: DISABLED (grayed out, not clickable)
- [ ] Status indicator: Changes from "Pending" to "Requested"
- [ ] Message appears: "⏳ Waiting for Seller Confirmation..."
- [ ] Cache state: buyer_claimed_at = timestamp
- [ ] Message color: Yellow background (waiting state)
- [ ] Timestamp shown: "Payment claimed at: [time]"
- [ ] Polling: Continues every 2 seconds
- [ ] Button click prevented: Multiple clicks should not cause duplicate entries

### 1.3 While Waiting for Seller (Buyer Perspective)
- [ ] Shows yellow waiting box
- [ ] Message: "The seller will now confirm receipt of your payment..."
- [ ] Spinner icon visible
- [ ] No additional buyer actions possible
- [ ] Buyer can refresh page and resume waiting state
- [ ] Polling still active for seller response
- [ ] Auto-redirect on success (auto-navigates when seller confirms)

### 1.4 When Seller Confirms (Success Path)
- [ ] Yellow waiting box disappears
- [ ] Green success box appears
- [ ] Message: "✓ Payment Confirmed!"
- [ ] Shows: "The seller has confirmed receipt of your payment"
- [ ] Shows: "Your order will now proceed"
- [ ] Shows confirmation timestamp
- [ ] Auto-redirect to /orders/{id} after 2 seconds
- [ ] Cache state: seller_response_at is set, status='seller_confirmed'

### 1.5 When Seller Rejects (Failure Path)
- [ ] Green box disappears
- [ ] Red error box appears
- [ ] Message: "✗ Payment Not Received"
- [ ] Shows: "The seller has not received your payment yet"
- [ ] Shows seller's rejection reason (if provided)
- [ ] Buyer can try again
- [ ] Shows rejection timestamp
- [ ] Button becomes re-enabled (buyer can click again)
- [ ] Cache state: seller_response_at is set, status='seller_rejected'

### 1.6 Network Error / Timeout
- [ ] Polling errors don't crash the page
- [ ] Console shows error but page continues
- [ ] Buyer can manually refresh to check status
- [ ] Retry capability maintained

---

## PHASE 2: SELLER VIEW LOGIC - All Edge Cases

### 2.1 Initial State (Waiting for Buyer)
- [ ] Display: "Verify & Release Payment" heading
- [ ] Section shown: YELLOW "Waiting for Buyer" box
- [ ] Message: "⏳ WAITING FOR BUYER..."
- [ ] Spinner visible with animation
- [ ] Help text: "The buyer hasn't yet confirmed they've sent the payment"
- [ ] Additional text: "This step usually takes just a few moments"
- [ ] NO action buttons visible yet
- [ ] Step 1 status: Yellow (⏳ Buyer Claims Payment - Waiting)
- [ ] Step 2 status: Gray (🔲 Verify Payment Receipt - Not started)
- [ ] Step 3 status: Gray (🔲 Order Proceeds)
- [ ] Blue section: HIDDEN (x-show="data.buyer_claimed_at && !data.seller_response_at")
- [ ] Amber section: HIDDEN (x-show="!data.buyer_claimed_at && !data.seller_response_at")
- [ ] Polling: Active every 2 seconds checking for buyer_claimed_at
- [ ] Cache state: buyer_claimed_at = null

### 2.2 Buyer Clicks "Payment Sent" (Seller Detects via Polling)
- [ ] Polling detects: buyer_claimed_at is now set
- [ ] Yellow "Waiting for Buyer" section DISAPPEARS
- [ ] BLUE section APPEARS: "✓ Buyer Confirmed Payment Sent"
- [ ] Blue background applied to action section
- [ ] Message: "Have you received ₱X.XX from the buyer?"
- [ ] Primary button appears: "✓ Confirm Payment Received" (GREEN - prominent)
- [ ] Secondary button appears: "Not Received - Ask Buyer to Retry" (border style - less prominent)
- [ ] Step 1 status: Changes to Green (✓ Buyer Claims Payment)
- [ ] Timestamp shown: "Buyer confirmed at: [time]"
- [ ] Seller gets notification (visual change on page)
- [ ] AMBER section: REMAINS HIDDEN (buyer already claimed)

### 2.3 Seller Never Gets Buyer Click (Timeout / Buyer Forgot)
- [ ] After extended wait, AMBER section can be shown if needed
- [ ] AMBER "Record Payment" section visible
- [ ] Background: Amber border (indicates fallback/manual record)
- [ ] Heading: "📝 Record Payment"
- [ ] Message: "If you have already received but the buyer hasn't notified..."
- [ ] Button: "📝 Record Payment Received" (AMBER - manual action)
- [ ] This allows seller to manually record if buyer forgot to click
- [ ] Both sections CANNOT be visible at same time
- [ ] Logic: If buyer_claimed_at exists → show BLUE, else show AMBER (if !seller_response_at)

### 2.4 Seller Clicks "Confirm Payment Received" (From Blue Section)
- [ ] Button state: DISABLED immediately
- [ ] Loading spinner appears on button
- [ ] Button text: "Processing..."
- [ ] API call: POST /payments/cash/seller-confirmed
- [ ] Cache updates: seller_response_at = timestamp, status='seller_confirmed'
- [ ] Green success box appears: "✓ Payment Confirmed & Released!"
- [ ] Message: "You have confirmed receipt of ₱X.XX"
- [ ] Message: "The order is now active and ready to proceed"
- [ ] Timestamp shown: "Released at: [time]"
- [ ] "View Order →" link appears
- [ ] Order payment_status updated to 'paid'
- [ ] Order payment_method set to 'cash'
- [ ] Auto-redirect to /orders/{id} after 2 seconds
- [ ] Buyer sees same confirmation on their side (via polling)

### 2.5 Seller Clicks "Not Received - Ask Buyer to Retry" (From Blue Section - Fallback)
- [ ] Button state: DISABLED immediately
- [ ] Loading spinner appears
- [ ] API call: POST /payments/cash/seller-rejected
- [ ] Cache updates: seller_response_at = timestamp, status='seller_rejected'
- [ ] Red error box appears: "✗ Payment Not Received"
- [ ] Message: "You have indicated payment not received"
- [ ] Message: "The buyer will be asked to resend or contact you"
- [ ] Shows timestamp when marked
- [ ] Options appear: "Start Over" button, "Message Buyer" link
- [ ] Order payment_status reverts to 'pending'
- [ ] Buyer sees rejection on their side (via polling)
- [ ] Seller can click "Start Over" to reset (page refresh)
- [ ] Seller can "Message Buyer" to communicate issue

### 2.6 Seller Clicks "Record Payment Received" (From Amber Section - Fallback)
- [ ] This path only shows if buyer HASN'T clicked yet
- [ ] Button state: DISABLED immediately
- [ ] Loading spinner appears
- [ ] API call: POST /payments/cash/seller-confirmed (same endpoint)
- [ ] Effect: SAME as confirming from blue section
- [ ] Order becomes active
- [ ] Green success appears
- [ ] Auto-redirect happens
- [ ] Difference: Buyer never clicked, but seller manually recorded
- [ ] Result: Order still active, payment accepted

### 2.7 Seller Walks Away (Never Responds)
- [ ] Cache TTL: 1 hour expiration
- [ ] After 1 hour: Handshake expires
- [ ] Buyer sees error on next poll
- [ ] Seller sees handshake expired if they return
- [ ] Both redirected to error/expired page
- [ ] Order remains in pending state if not confirmed

### 2.8 Network Error / Polling Fails
- [ ] Seller manually refreshes page
- [ ] State restored from cache
- [ ] Polling resumes
- [ ] No data loss

---

## PHASE 3: INTERACTION MATRIX - All Scenarios

### Scenario A: Happy Path (Normal Flow)
```
Buyer: Clicks "Payment Sent"
  └─ buyer_claimed_at = now
Seller: Polls, sees buyer_claimed_at
  └─ Shows BLUE section: "Confirm Payment Received"
Seller: Clicks "Confirm Payment Received"
  └─ seller_confirmed_payment()
  └─ order.payment_status = 'paid'
  └─ Auto-redirect both users
Result: ✅ ORDER ACTIVE
```
- [ ] Buyer action: 1 click
- [ ] Seller action: 1 click
- [ ] Duration: ~2-5 seconds
- [ ] Success: ✓ Order moves to active

### Scenario B: Seller Records Manually (Fallback Path)
```
Buyer: Never clicks "Payment Sent" (forgot/network issue)
Seller: Polls, buyer_claimed_at = null
  └─ Shows AMBER section: "Record Payment"
Seller: Clicks "Record Payment Received"
  └─ seller_confirmed_payment() (same endpoint)
  └─ order.payment_status = 'paid'
  └─ Auto-redirect
Result: ✅ ORDER ACTIVE (manual record)
```
- [ ] Buyer action: 0 clicks
- [ ] Seller action: 1 click
- [ ] Duration: Determined by seller
- [ ] Success: ✓ Order moves to active via fallback

### Scenario C: Seller Rejects Payment (Dispute)
```
Buyer: Clicks "Payment Sent"
  └─ buyer_claimed_at = now
Seller: Polls, sees buyer_claimed_at
  └─ Shows BLUE section
Seller: Clicks "Not Received - Ask Buyer to Retry"
  └─ seller_rejected_payment()
  └─ order.payment_status = 'pending' (reverted)
  └─ Buyer sees rejection (polling)
Result: ❌ ORDER PENDING (buyer can retry)
```
- [ ] Buyer sees rejection message
- [ ] Buyer can try payment again
- [ ] Seller can message buyer
- [ ] Order stays pending until resolved

### Scenario D: Buyer Retries After Rejection
```
Previous: Seller rejected
Current: Buyer clicks "Payment Sent" again
  └─ New buyer_claimed_at = now
  └─ Cache is fresh (new timestamp)
Seller: Polls, sees new buyer_claimed_at
  └─ Shows BLUE section again
Seller: Reviews and clicks "Confirm Payment Received"
  └─ Order confirmed
Result: ✅ ORDER ACTIVE (on retry)
```
- [ ] Cache doesn't retain rejection
- [ ] Fresh state for retry
- [ ] Seller can confirm on second attempt

### Scenario E: Multiple Browser Tabs (Same User)
```
Buyer: Opens 2 tabs with same handshake
  └─ Tab 1: Clicks "Payment Sent"
  └─ Tab 2: Also shows "Payment Sent" (enabled)
  └─ Only Tab 1's click registers (first POST wins)
  └─ Tab 2's click: Would also send, but cache already updated
Result: ✓ Both tabs eventually show same state (via polling)
```
- [ ] Prevents duplicate submissions (first click wins)
- [ ] Both tabs sync via polling
- [ ] No state corruption

### Scenario F: Seller in 2 Tabs (Same User)
```
Seller: Opens 2 tabs with same handshake
  └─ Tab 1: Polls, sees buyer_claimed_at
  └─ Tab 2: Also polls, sees same buyer_claimed_at
  └─ Both show "Confirm Payment Received"
  └─ Tab 1: Clicks button → Order confirmed
  └─ Tab 2: Clicks button → Already confirmed (error handled)
Result: ✓ Only one confirmation, error on second is graceful
```
- [ ] First click processes payment
- [ ] Second click returns error
- [ ] Authorization check catches: Only seller can confirm once

### Scenario G: Authorization Check (Unauthorized User)
```
User C: Tries to access /payments/cash/handshake for Order A
  └─ Order A: buyer=User A, seller=User B
  └─ User C ≠ User A AND User C ≠ User B
  └─ Controller checks: if (!$isBuyer && !$isSeller) abort(403)
Result: ❌ 403 Unauthorized
```
- [ ] User C cannot access handshake page
- [ ] Only buyer or seller can access
- [ ] Logged in audit log

### Scenario H: API Request Without Proper User
```
Attacker: POST /payments/cash/buyer-claimed with order_id=1
  └─ Checks in controller: Auth::id() !== order.buyer_id
  └─ Returns 403 with message
Result: ❌ 403 Only buyer can claim payment
```
- [ ] Only actual buyer can claim payment
- [ ] Only actual seller can confirm payment
- [ ] Authorization per endpoint

### Scenario I: Cache Expiration (1 Hour)
```
Time 0:00: Handshake created
  └─ Cache TTL = 3600 seconds (1 hour)
Time 1:00: Cache key expires
  └─ getHandshakeStatus($handshakeId) returns null
  └─ View shows 404 or "Handshake expired"
  └─ Both users redirected
Result: ✓ Automatic cleanup after 1 hour
```
- [ ] No database cleanup needed
- [ ] Cache auto-expires
- [ ] Order remains in pending if not confirmed
- [ ] User redirected with message

### Scenario J: Concurrent Request Race Condition
```
Buyer & Seller: Both click simultaneously
  └─ Buyer: POST /payments/cash/buyer-claimed
  └─ Seller: POST /payments/cash/seller-confirmed (at same time)
  └─ Buyer click: Updates cache, buyer_claimed_at set
  └─ Seller click: Checks state, finds buyer_claimed_at exists
  └─ Both succeed (seller can confirm even if just claimed)
Result: ✓ No race condition, seller can confirm on same cycle
```
- [ ] No locking mechanism needed (cache is atomic)
- [ ] Both operations idempotent
- [ ] Final state: Order paid

---

## PHASE 4: DATA STATE TRANSITIONS

### 4.1 Cache State Lifecycle
```
INITIAL STATE:
{
    status: 'pending',
    buyer_claimed_at: null,
    seller_response_at: null,
    rejection_reason: null
}

AFTER BUYER CLICKS:
{
    status: 'buyer_claimed',
    buyer_claimed_at: '2025-11-26T10:30:45',
    seller_response_at: null,
    rejection_reason: null
}

AFTER SELLER CONFIRMS:
{
    status: 'seller_confirmed',
    buyer_claimed_at: '2025-11-26T10:30:45',
    seller_response_at: '2025-11-26T10:31:00',
    rejection_reason: null
}

AFTER SELLER REJECTS:
{
    status: 'seller_rejected',
    buyer_claimed_at: '2025-11-26T10:30:45',
    seller_response_at: '2025-11-26T10:31:00',
    rejection_reason: 'Transfer not found in account'
}
```
- [ ] All states properly transition
- [ ] No invalid state combinations
- [ ] Timestamps always present when action taken
- [ ] Rejection reason only on rejection

### 4.2 Order Status Updates
```
INITIAL:
order.payment_status = 'pending'
order.payment_method = 'cash'

ON BUYER CLAIM:
(No update - just cache change)

ON SELLER CONFIRM:
order.payment_status = 'paid'
order.paid_at = now()
order.payment_method = 'cash'

ON SELLER REJECT:
order.payment_status = 'pending'
order.paid_at = null (reverted)
```
- [ ] Payment status properly updated
- [ ] Paid timestamp set on confirmation
- [ ] Reverted on rejection
- [ ] Database reflects truth

### 4.3 Browser State Management
```
BUYER SIDE:
Initial: data = {status: 'pending', ...}
Click: data = {status: 'buyer_claimed', buyer_claimed_at: now}
Poll: data refreshes from GET /payments/cash/handshake/status
Confirm: data = {status: 'seller_confirmed', seller_response_at: now}
Auto-redirect: window.location.href = '/orders/{id}'

SELLER SIDE:
Initial: data = {status: 'pending', buyer_claimed_at: null}
Poll 1: data unchanged (buyer hasn't clicked yet)
Poll 2: data = {status: 'buyer_claimed', buyer_claimed_at: now}
Click: data = {status: 'seller_confirmed', seller_response_at: now}
Auto-redirect: window.location.href = '/orders/{id}'
```
- [ ] Alpine.js data binding reactive
- [ ] Polling updates data reactively
- [ ] UI re-renders on data change
- [ ] Auto-redirect on completion

---

## PHASE 5: UI STATE VISIBILITY LOGIC

### 5.1 Buyer View - Element Visibility
```
BUTTON STATE:
x-show="!data.buyer_claimed_at && !loading"
  └─ Shows: "✓ Payment Sent" (Initial state)

x-show="data.buyer_claimed_at"
  └─ Shows: "✓ Payment Notified" (After click, disabled)

:disabled="data.buyer_claimed_at"
  └─ Disabled: After buyer clicks

WAITING MESSAGE:
x-show="data.buyer_claimed_at && !data.seller_response_at"
  └─ Shows: Yellow waiting box (After buyer clicks, before seller responds)

SUCCESS MESSAGE:
x-show="data.seller_response_at && data.status === 'seller_confirmed'"
  └─ Shows: Green success box (After seller confirms)

ERROR MESSAGE:
x-show="data.seller_response_at && data.status === 'seller_rejected'"
  └─ Shows: Red error box (After seller rejects)
```
- [ ] All visibility conditions correct
- [ ] Mutually exclusive states
- [ ] No multiple sections visible at once
- [ ] Proper data binding

### 5.2 Seller View - Element Visibility
```
WAITING SECTION:
x-show="!data.buyer_claimed_at"
  └─ Shows: Yellow "Waiting for Buyer..." box (Initial)
  └─ Hides: Once buyer clicks

BLUE SECTION (Primary):
x-show="data.buyer_claimed_at && !data.seller_response_at"
  └─ Shows: When buyer has claimed AND seller hasn't responded
  └─ Contains: "Confirm Payment Received" button (Green)
  └─ Contains: "Not Received - Ask Retry" button (Border)

AMBER SECTION (Fallback):
x-show="!data.buyer_claimed_at && !data.seller_response_at"
  └─ Shows: When buyer hasn't claimed AND seller hasn't responded
  └─ Contains: "Record Payment Received" button (Amber)

SUCCESS SECTION:
x-show="data.seller_response_at && data.status === 'seller_confirmed'"
  └─ Shows: Green "Payment Confirmed & Released!" box

ERROR SECTION:
x-show="data.seller_response_at && data.status === 'seller_rejected'"
  └─ Shows: Red "Payment Not Received" box
```
- [ ] Blue and Amber sections are mutually exclusive
- [ ] No sections appear when seller has already responded
- [ ] Proper state gating

### 5.3 Button Enabled/Disabled States
```
BUYER BUTTON:
:disabled="data.buyer_claimed_at"
  └─ Enabled: Until buyer clicks
  └─ Disabled: After buyer clicks (cannot spam)

SELLER CONFIRM BUTTON (Blue):
:disabled="data.seller_response_at"
  └─ Enabled: After buyer claims, before seller responds
  └─ Disabled: After seller has responded

SELLER NOT RECEIVED BUTTON (Blue):
:disabled="data.seller_response_at"
  └─ Enabled: After buyer claims, before seller responds
  └─ Disabled: After seller has responded

SELLER RECORD BUTTON (Amber):
:disabled="data.seller_response_at"
  └─ Enabled: Initially (if buyer hasn't claimed)
  └─ Disabled: After seller has responded
```
- [ ] No double-click possibility
- [ ] Buttons locked after action taken
- [ ] Loading spinner shows during submission

---

## PHASE 6: POLLING & REAL-TIME SYNC

### 6.1 Buyer Polling
```
Interval: Every 2 seconds
Endpoint: GET /payments/cash/handshake/status?handshakeId=...
Checks for: seller_response_at
On Update:
  - If seller_confirmed: Show green success, redirect in 2 sec
  - If seller_rejected: Show red error, allow retry
  - If still pending: Keep waiting
```
- [ ] Polling starts on page load
- [ ] Polling stops on page unload
- [ ] Polling stops on successful redirect
- [ ] Error handling prevents crash

### 6.2 Seller Polling
```
Interval: Every 2 seconds
Endpoint: GET /payments/cash/handshake/status?handshakeId=...
Checks for: buyer_claimed_at
On Update:
  - If buyer_claimed_at appears: Show BLUE section
  - If still null: Keep showing AMBER/waiting
  - Update timestamp in status display
```
- [ ] Polling starts on page load
- [ ] Polling stops on page unload
- [ ] Polling stops on successful redirect
- [ ] Real-time sync of buyer status

### 6.3 Poll Cleanup
```
On Page Unload:
  clearInterval(this.pollInterval)
  this.pollInterval = null

On Redirect:
  this.destroy()
  window.location.href = '/orders/{id}'

On Error:
  Log error, continue polling
  User can refresh if stuck
```
- [ ] No memory leaks from polling
- [ ] Cleanup on page exit
- [ ] Graceful error handling

---

## PHASE 7: ERROR HANDLING & EDGE CASES

### 7.1 Network Errors
- [ ] POST request fails → Show error toast
- [ ] GET polling fails → Log but continue
- [ ] Timeout → Show retry option
- [ ] User can manually refresh to sync

### 7.2 Invalid States
- [ ] Handshake ID invalid → 404 "Handshake not found"
- [ ] Order ID invalid → 404 Order not found
- [ ] User not buyer/seller → 403 Unauthorized
- [ ] Cache expired → Redirect to error page

### 7.3 Double-Submit Prevention
- [ ] Button disabled after first click
- [ ] Loading spinner visible during request
- [ ] Multiple form submissions prevented
- [ ] API idempotent (doesn't double-charge)

### 7.4 State Inconsistency
- [ ] Cache vs Database mismatch handled
- [ ] Polling syncs UI to current state
- [ ] Refresh recovers correct state
- [ ] TTL prevents stale data

### 7.5 Concurrent Actions
- [ ] Same user in 2 tabs: First wins, second gets error
- [ ] Different users (buyer/seller): Both succeed in sequence
- [ ] Attacker tries to confirm others' payment: 403 error
- [ ] Cache prevents duplicate state

---

## PHASE 8: COMPLETION VERIFICATION

### 8.1 Buyer Flow Complete When:
- [ ] Buyer can see "Payment Notification" heading ✓
- [ ] Buyer can click "✓ Payment Sent" button ✓
- [ ] Button disables and shows "Payment Notified" ✓
- [ ] Buyer sees "Waiting for Seller Confirmation..." ✓
- [ ] Polling active every 2 seconds ✓
- [ ] On seller confirm: Green success box appears ✓
- [ ] Auto-redirect to /orders/{id} ✓

### 8.2 Seller Flow Complete When:
- [ ] Seller sees "Waiting for Buyer..." initially ✓
- [ ] After buyer clicks: "Confirm Payment Received" button appears (BLUE) ✓
- [ ] OR "Record Payment" button shows if buyer forgot (AMBER) ✓
- [ ] Seller can click primary "Confirm Payment Received" ✓
- [ ] OR Seller can click fallback "Record Payment Received" ✓
- [ ] Green success appears after confirmation ✓
- [ ] Auto-redirect to /orders/{id} ✓

### 8.3 Rejection Flow Complete When:
- [ ] Seller can click "Not Received - Ask Retry" ✓
- [ ] Red error box appears ✓
- [ ] Buyer sees rejection on their side ✓
- [ ] Buyer can retry by clicking "Payment Sent" again ✓
- [ ] Order reverts to pending status ✓

### 8.4 Fallback Flow Complete When:
- [ ] Seller sees "Record Payment" option ✓
- [ ] Seller can record without buyer clicking ✓
- [ ] Same end result: Order active ✓
- [ ] No broken state ✓

### 8.5 Authorization Complete When:
- [ ] Only buyer can see buyer view ✓
- [ ] Only seller can see seller view ✓
- [ ] Third party gets 403 ✓
- [ ] Only buyer can claim payment ✓
- [ ] Only seller can confirm payment ✓

---

## PHASE 9: IMPLEMENTATION TASKS

### 9.1 Buyer View (cash-payment-request.blade.php)
- [x] Title: "Payment Notification" ✓
- [x] Subtitle: "Notify the seller that you've sent the payment" ✓
- [x] Button text: "✓ Payment Sent" ✓
- [x] Step 2: "Notify Seller" ✓
- [x] Step 3: "Seller Confirms Receipt" ✓
- [x] Waiting message: Updated ✓
- [x] All messaging aligned to logic ✓

### 9.2 Seller View (cash-payment-release.blade.php)
- [x] Two action sections created ✓
- [x] BLUE section: "Buyer Confirmed Payment Sent" ✓
- [x] BLUE section: "Confirm Payment Received" button (GREEN) ✓
- [x] BLUE section: "Not Received" button (Border) ✓
- [x] AMBER section: "Record Payment" button (AMBER) ✓
- [x] AMBER section: "Only shows if buyer forgot" ✓
- [x] Mutual exclusivity: Blue OR Amber, not both ✓
- [x] Proper x-show conditions applied ✓

### 9.3 Backend (PaymentController.php)
- [x] No changes needed (endpoints work same way) ✓
- [x] Authorization checks present ✓
- [x] buyerClaimedPayment() endpoint functional ✓
- [x] sellerConfirmedPayment() endpoint functional ✓
- [x] sellerRejectedPayment() endpoint functional ✓

### 9.4 Services (CashPaymentService.php)
- [x] No changes needed ✓
- [x] Cache logic unchanged ✓
- [x] State transitions correct ✓

---

## ✅ CHECKLIST COMPLETE

All logic edge cases defined and ready for:
1. **Buyer Flow Verification** - Section 8.1
2. **Seller Flow Verification** - Section 8.2
3. **Rejection Flow Verification** - Section 8.3
4. **Fallback Flow Verification** - Section 8.4
5. **Authorization Verification** - Section 8.5
6. **Error Handling Verification** - Section 7
7. **State Transitions Verification** - Section 4

---

**Next Step:** Go through each section and verify implementation handles all cases

