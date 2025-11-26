# CASH PAYMENT LOGIC - STEP-BY-STEP EXECUTION GUIDE

**Objective:** Verify all logic edge cases are properly implemented in views  
**Focus:** NO TESTING - Logic verification only  
**Format:** Reference the checklist and verify each section

---

## HOW TO USE THIS GUIDE

1. Open `CASH_PAYMENT_LOGIC_CHECKLIST.md`
2. Start with **PHASE 1: BUYER VIEW LOGIC**
3. Go through each section (1.1, 1.2, 1.3, etc.)
4. Verify each checkbox item is implemented
5. Move to next PHASE
6. **Do NOT test** - just verify code implements logic

---

## QUICK REFERENCE: What Each Phase Covers

| Phase | Section | Focus |
|-------|---------|-------|
| **1** | Buyer View Logic | Initial state, after click, waiting, success, error |
| **2** | Seller View Logic | Waiting, buyer detected, confirm path, fallback, timeout |
| **3** | Interaction Matrix | 10 different user scenarios |
| **4** | Data State Transitions | Cache lifecycle, order updates, browser state |
| **5** | UI Visibility Logic | Element show/hide conditions |
| **6** | Polling & Real-time | Polling behavior, cleanup, sync |
| **7** | Error Handling | Network errors, invalid states, double-submit |
| **8** | Completion Verification | When flow is "done" |
| **9** | Implementation Tasks | What was already coded |

---

## PHASE 1 CHECK: BUYER VIEW

**File to Verify:** `resources/views/payments/cash-payment-request.blade.php`

### 1.1 Initial State - Check These:
```
HEADING: "Payment Notification" (not "Request Payment")
SUBTITLE: "Notify the seller that you've sent the payment"
BUTTON: Text says "✓ Payment Sent" (not "Confirm Payment Sent")
BUTTON: Class includes "bg-blue-600" (enabled state)
STEP 2: Label says "Notify Seller" (not "Request Confirmation")
STEP 3: Label says "Seller Confirms Receipt" (not "Seller Verification")
STATUS: Shows "Pending"
HELP: Text updated to match "notify" not "request"
```
**Verification:**
- [ ] All text matches corrected logic
- [ ] Button is blue and enabled initially
- [ ] No reference to "Request Confirmation"

### 1.2 After Button Click - Check These:
```
BUTTON: Text shows "✓ Payment Notified" (after click)
BUTTON: :disabled="data.buyer_claimed_at" applied
BUTTON: Class shows "bg-gray-400" when disabled
STATUS: Changes from "Pending" to "Requested"
MESSAGE: Shows "Waiting for Seller Confirmation..."
MESSAGE: Background color = yellow (waiting)
SPINNER: Shows animated spinner icon
```
**Verification:**
- [ ] Button text changes to "Notified"
- [ ] Button becomes disabled (gray)
- [ ] Yellow waiting message appears
- [ ] `:disabled` binding correct

### 1.3 Waiting for Seller - Check These:
```
YELLOW BOX: x-show="data.buyer_claimed_at && !data.seller_response_at"
MESSAGE: "The seller will now confirm receipt of your payment..."
SPINNER: Animated spinner visible
TIMESTAMP: Shows "Requested at: [time]"
POLLING: setInterval every 2 seconds checking for seller_response_at
```
**Verification:**
- [ ] Yellow box only shows when buyer_claimed_at exists AND seller hasn't responded
- [ ] Correct conditional logic
- [ ] Polling code checks for seller_response_at
- [ ] Interval = 2000ms

### 1.4 Success Path - Check These:
```
GREEN BOX: x-show="data.seller_response_at && data.status === 'seller_confirmed'"
MESSAGE: "✓ Payment Confirmed!"
DETAIL: "The seller has confirmed receipt of your payment"
DETAIL: "Your order will now proceed"
TIMESTAMP: Shows "Confirmed at: [time]"
REDIRECT: setTimeout(..., 2000) → '/orders/{id}'
```
**Verification:**
- [ ] Green box shows only on seller_confirmed status
- [ ] Auto-redirect code present
- [ ] Redirect waits 2 seconds
- [ ] Correct order ID in redirect

### 1.5 Rejection Path - Check These:
```
RED BOX: x-show="data.seller_response_at && data.status === 'seller_rejected'"
MESSAGE: "✗ Payment Not Received"
DETAIL: "The seller has not received your payment yet"
REASON: x-show="data.rejection_reason" displays seller's reason
HELP: "Please contact the seller to clarify or resend"
BUTTON: Re-enabled so buyer can try again
```
**Verification:**
- [ ] Red box shows only on seller_rejected status
- [ ] Rejection reason displays if provided
- [ ] Button becomes re-enabled for retry

### 1.6 Polling & Cleanup - Check These:
```
INIT: setupPolling() called in @load="init()"
CLEANUP: destroy() called in @beforeunload="destroy()"
INTERVAL: setInterval(..., 2000)
ENDPOINT: /payments/cash/handshake/status?handshakeId=...
SUCCESS REDIRECT: this.destroy() before window.location.href
```
**Verification:**
- [ ] Polling starts on load
- [ ] Polling stops on unload
- [ ] Polling stops on redirect
- [ ] No memory leaks

**PHASE 1 RESULT:** ✅ or ❌ (Mark when complete)

---

## PHASE 2 CHECK: SELLER VIEW - PRIMARY PATH

**File to Verify:** `resources/views/payments/cash-payment-release.blade.php`

### 2.1 Initial State (Waiting) - Check These:
```
YELLOW SECTION: x-show="!data.buyer_claimed_at"
HEADING: "Waiting for Buyer"
MESSAGE: "The buyer hasn't yet confirmed they've sent the payment"
SPINNER: Animated spinner visible
STEP 1: Shows ⏳ (waiting icon, yellow)
BLUE SECTION: HIDDEN (x-show should be false)
AMBER SECTION: HIDDEN (x-show should be false)
POLLING: GET /payments/cash/handshake/status every 2 seconds
```
**Verification:**
- [ ] Yellow section shows initially
- [ ] Blue and Amber sections hidden
- [ ] Correct x-show conditional: `!data.buyer_claimed_at`
- [ ] Only yellow section visible on page load

### 2.2 Buyer Clicked (Polling Detects) - Check These:
```
YELLOW SECTION: DISAPPEARS (data.buyer_claimed_at now exists)
BLUE SECTION: APPEARS with x-show="data.buyer_claimed_at && !data.seller_response_at"
HEADING: "✓ Buyer Confirmed Payment Sent"
MESSAGE: "Have you received ₱X.XX from the buyer?"
BACKGROUND: Blue border/background applied
BUTTON 1: "✓ Confirm Payment Received" (GREEN, prominent)
BUTTON 2: "Not Received - Ask Buyer to Retry" (Border, less prominent)
TIMESTAMP: Shows "Buyer confirmed at: [time]"
STEP 1: Now shows ✓ (green, completed)
```
**Verification:**
- [ ] Blue section has correct x-show condition
- [ ] Both buttons visible in blue section
- [ ] Green button is primary (larger, more prominent)
- [ ] Border button is secondary (smaller, less prominent)
- [ ] Buyer timestamp displayed
- [ ] Background styling (blue) applied

### 2.3 Seller Clicks "Confirm Payment" - Check These:
```
BUTTON: @click="releasePayment(true)"
LOADING: Shows spinner, text "Processing..."
DISABLED: :disabled="data.seller_response_at"
API CALL: POST /payments/cash/seller-confirmed
BODY: { handshake_id, order_id }
ON SUCCESS:
  - this.data.status = 'seller_confirmed'
  - this.data.seller_response_at = new Date().toISOString()
  - this.destroy() (stop polling)
  - setTimeout(redirect, 2000)
  - window.location.href = '/orders/{id}'
```
**Verification:**
- [ ] Button click handler `releasePayment(true)` present
- [ ] Loading state managed
- [ ] API endpoint correct
- [ ] Response handled
- [ ] Auto-redirect logic present

### 2.4 After Confirmation - Check These:
```
BLUE SECTION: HIDDEN
AMBER SECTION: HIDDEN
GREEN BOX: x-show="data.seller_response_at && data.status === 'seller_confirmed'"
MESSAGE: "✓ Payment Confirmed & Released!"
DETAIL: "You have confirmed receipt of ₱X.XX"
DETAIL: "The order is now active and ready to proceed"
TIMESTAMP: "Released at: [time]"
LINK: "View Order →" button to /orders/{id}
REDIRECT: Auto-redirect in 2 seconds
```
**Verification:**
- [ ] Green success box shown
- [ ] Confirmation message clear
- [ ] Timestamp displayed
- [ ] Auto-redirect happens
- [ ] Order page link available

**PHASE 2 RESULT:** ✅ or ❌ (Mark when complete)

---

## PHASE 3 CHECK: SELLER VIEW - FALLBACK PATH

**File to Verify:** `resources/views/payments/cash-payment-release.blade.php`

### 3.1 Fallback Section Visibility - Check These:
```
AMBER SECTION: x-show="!data.buyer_claimed_at && !data.seller_response_at"
HEADING: "📝 Record Payment"
MESSAGE: "If you have already received ₱X.XX but the buyer hasn't notified you yet"
MESSAGE: "you can record it directly:"
BACKGROUND: Amber border (different from blue)
BUTTON: "📝 Record Payment Received" (AMBER color, not green)
HELP: "Use this if buyer forgot to click 'Payment Sent'"
```
**Verification:**
- [ ] Amber section has correct x-show condition
- [ ] Only shows when buyer HASN'T claimed AND seller HASN'T responded
- [ ] Background styling (amber) applied
- [ ] Button text and color correct
- [ ] Help text explains when to use

### 3.2 Buyer Never Clicks (Timing) - Check These:
```
TIME 0: Handshake created
  └─ buyer_claimed_at = null
  └─ Yellow "Waiting" section shows
  └─ Amber section hidden (seller_response_at exists!)

Wait 5 minutes: Buyer still hasn't clicked
  └─ buyer_claimed_at still null
  └─ Amber section should show
  └─ Seller can manually record

Seller clicks "Record Payment":
  └─ Same as blue section confirm
  └─ Order becomes active
  └─ Order payment_status = 'paid'
```
**Verification:**
- [ ] Logic correctly handles missing buyer click
- [ ] Fallback available if needed
- [ ] Same backend endpoint called (idempotent)

### 3.3 Mutual Exclusivity - Check These:
```
NEVER BOTH AT SAME TIME:
❌ Blue section (data.buyer_claimed_at=true) + Amber section (data.buyer_claimed_at=false)
  
RULE:
IF buyer_claimed_at exists AND seller hasn't responded:
  └─ SHOW blue section (primary path)
  
IF buyer_claimed_at doesn't exist AND seller hasn't responded:
  └─ SHOW amber section (fallback path)
  
IF seller has responded:
  └─ HIDE both, show success/error box
```
**Verification:**
- [ ] x-show conditions prevent overlap
- [ ] Tested logic: `buyer_claimed_at && !seller_response_at` vs `!buyer_claimed_at && !seller_response_at`
- [ ] No scenario where both visible

**PHASE 3 RESULT:** ✅ or ❌ (Mark when complete)

---

## PHASE 4 CHECK: ERROR HANDLING - REJECTION

**File to Verify:** Both views' rejection handling

### 4.1 Buyer Rejects in Seller View - Check These:
```
BLUE SECTION: Secondary button "Not Received - Ask Buyer to Retry"
BUTTON: @click="releasePayment(false)"
LOADING: Shows spinner
API CALL: POST /payments/cash/seller-rejected
BODY: { handshake_id, order_id, reason: '' }
ON SUCCESS:
  - this.data.status = 'seller_rejected'
  - this.data.seller_response_at = now
```
**Verification:**
- [ ] Button click handler present
- [ ] Correct endpoint
- [ ] Rejection recorded

### 4.2 Buyer Sees Rejection - Check These:
```
BUYER SIDE (via polling):
  GET /payments/cash/handshake/status
  └─ Returns: status='seller_rejected'
  
RED BOX: x-show="data.seller_response_at && data.status === 'seller_rejected'"
MESSAGE: "✗ Payment Not Received"
DETAIL: "The seller has not received your payment yet"
HELP: "Please contact the seller"
TIMESTAMP: Shows when seller rejected
BUTTON: Re-enabled (buyer can try again)
```
**Verification:**
- [ ] Buyer sees rejection in real-time (via polling)
- [ ] Red box shown correctly
- [ ] Button enabled for retry
- [ ] Clear messaging

### 4.3 Buyer Can Retry - Check These:
```
INITIAL: buyer_claimed_at = null
RETRY ATTEMPT: Buyer clicks "Payment Sent" again
NEW STATE: buyer_claimed_at = new timestamp
SELLER SIDE: Polls, sees new buyer_claimed_at
SELLER VIEW: Blue section appears again
SELLER: Can confirm again
RESULT: Order becomes active on second attempt
```
**Verification:**
- [ ] Retry logic works
- [ ] New state recorded
- [ ] Fresh opportunity to confirm

**PHASE 4 RESULT:** ✅ or ❌ (Mark when complete)

---

## PHASE 5 CHECK: AUTHORIZATION

**File to Verify:** `PaymentController.php`

### 5.1 Buyer View Access - Check These:
```
CONTROLLER: if ($isBuyer) return view('payments.cash-payment-request', ...)
CHECK: $isBuyer = $currentUserId === $order->buyer_id
UNAUTHORIZED: if (!$isBuyer && !$isSeller) abort(403)
RESULT: Only buyer or seller can view
```
**Verification:**
- [ ] Only buyer can see buyer view
- [ ] Only seller can see seller view
- [ ] Third party gets 403

### 5.2 API Authorization - Check These:
```
ENDPOINT: POST /payments/cash/buyer-claimed
CHECK: if (Auth::id() !== $order->buyer_id) return 403
RESULT: Only actual buyer can claim

ENDPOINT: POST /payments/cash/seller-confirmed
CHECK: if (Auth::id() !== $order->seller_id) return 403
RESULT: Only actual seller can confirm

ENDPOINT: POST /payments/cash/seller-rejected
CHECK: if (Auth::id() !== $order->seller_id) return 403
RESULT: Only actual seller can reject
```
**Verification:**
- [ ] All three endpoints have auth checks
- [ ] Correct error returned (403)
- [ ] Logged in audit

**PHASE 5 RESULT:** ✅ or ❌ (Mark when complete)

---

## PHASE 6 CHECK: STATE TRANSITIONS

**File to Verify:** `CashPaymentService.php` + Controller

### 6.1 Cache State Progression - Check These:
```
START: {status: 'pending', buyer_claimed_at: null, seller_response_at: null}

BUYER CLICKS:
  buyerClaimedPayment()
  └─ data['status'] = 'buyer_claimed'
  └─ data['buyer_claimed_at'] = now()
  └─ Result: {status: 'buyer_claimed', buyer_claimed_at: now, seller_response_at: null}

SELLER CONFIRMS:
  sellerConfirmedPayment()
  └─ data['status'] = 'seller_confirmed'
  └─ data['seller_response_at'] = now()
  └─ Result: {status: 'seller_confirmed', buyer_claimed_at: X, seller_response_at: now}

DATABASE UPDATE:
  order.payment_status = 'paid'
  order.paid_at = now()
```
**Verification:**
- [ ] Cache state transitions correctly
- [ ] Database updates on confirm
- [ ] Order marked paid

### 6.2 Order Status - Check These:
```
INITIAL: payment_status='pending'
ON BUYER CLAIM: (no DB update, just cache)
ON SELLER CONFIRM: 
  order->update([
    'payment_status' => 'paid',
    'paid_at' => now(),
    'payment_method' => 'cash'
  ])
ON SELLER REJECT:
  order->update(['payment_status' => 'pending'])
```
**Verification:**
- [ ] Order properly marked paid on confirm
- [ ] Order reverted to pending on reject
- [ ] paid_at timestamp set

**PHASE 6 RESULT:** ✅ or ❌ (Mark when complete)

---

## PHASE 7 CHECK: UI VISIBILITY CONDITIONS

**File to Verify:** Both blade files

### 7.1 Buyer View Visibility - Check These:
```
BUTTON INITIAL:
  x-show="!data.buyer_claimed_at && !loading"
  └─ Shows "✓ Payment Sent"

BUTTON AFTER CLICK:
  x-show="data.buyer_claimed_at"
  └─ Shows "✓ Payment Notified"

YELLOW WAITING:
  x-show="data.buyer_claimed_at && !data.seller_response_at"

GREEN SUCCESS:
  x-show="data.seller_response_at && data.status === 'seller_confirmed'"

RED ERROR:
  x-show="data.seller_response_at && data.status === 'seller_rejected'"
```
**Verification:**
- [ ] All x-show conditions are mutually exclusive
- [ ] Correct binding to data properties
- [ ] No two sections visible at once

### 7.2 Seller View Visibility - Check These:
```
YELLOW WAITING:
  x-show="!data.buyer_claimed_at"

BLUE PRIMARY:
  x-show="data.buyer_claimed_at && !data.seller_response_at"

AMBER FALLBACK:
  x-show="!data.buyer_claimed_at && !data.seller_response_at"

GREEN SUCCESS:
  x-show="data.seller_response_at && data.status === 'seller_confirmed'"

RED ERROR:
  x-show="data.seller_response_at && data.status === 'seller_rejected'"
```
**Verification:**
- [ ] Blue and Amber never both visible
- [ ] Correct conditions prevent overlap
- [ ] Only one section visible at a time

**PHASE 7 RESULT:** ✅ or ❌ (Mark when complete)

---

## PHASE 8 CHECK: POLLING & REAL-TIME

**File to Verify:** Both blade files JavaScript

### 8.1 Buyer Polling - Check These:
```
setInterval:
  GET /payments/cash/handshake/status?handshakeId=...
  Interval: 2000ms (every 2 seconds)
  Check: result.handshakeData.seller_response_at
  On Success: Update this.data = result.handshakeData
  
If seller_confirmed:
  this.destroy()
  setTimeout(redirect, 1500)
  window.location.href = '/orders/{id}'
```
**Verification:**
- [ ] Polling interval correct (2000ms)
- [ ] Endpoint called correctly
- [ ] Response processed
- [ ] Auto-redirect on confirm

### 8.2 Seller Polling - Check These:
```
setInterval:
  GET /payments/cash/handshake/status?handshakeId=...
  Interval: 2000ms
  Check: result.handshakeData.buyer_claimed_at
  On Update: this.data = result.handshakeData
  
UI Re-renders:
  Blue section appears when buyer_claimed_at is set
  Amber section hidden
```
**Verification:**
- [ ] Polling active
- [ ] Detects buyer_claimed_at
- [ ] UI updates reactively

### 8.3 Cleanup - Check These:
```
Page Load: @load="init()" → setupPolling()
Page Unload: @beforeunload="destroy()" → clearInterval()
After Redirect: this.destroy() before window.location.href

Cleanup Code:
  if (this.pollInterval) {
    clearInterval(this.pollInterval);
    this.pollInterval = null;
  }
```
**Verification:**
- [ ] Polling starts on load
- [ ] Polling stops on unload
- [ ] No memory leaks
- [ ] Cleanup function called

**PHASE 8 RESULT:** ✅ or ❌ (Mark when complete)

---

## PHASE 9 CHECK: EDGE CASES

### 9.1 Double-Click Prevention
```
MECHANISM: :disabled="data.buyer_claimed_at"
RESULT: Button disabled after first click
VERIFICATION: Cannot submit twice in same session
```
- [ ] Implemented

### 9.2 Network Error
```
BUYER: Poll fails → console log but continue
SELLER: Poll fails → continue polling
User can refresh to sync
```
- [ ] Error handling present

### 9.3 Cache Expiration
```
TTL: 3600 seconds (1 hour)
After 1 hour: Cache key expires
Effect: getHandshakeStatus() returns null
User redirected to 404
```
- [ ] TTL set correctly

### 9.4 Race Condition
```
Scenario: Buyer & Seller click at same time
Result: Both succeed (cache is atomic)
Final state: Order active
```
- [ ] Handled by atomic cache ops

### 9.5 Same User in 2 Tabs
```
Tab 1: Clicks "Payment Sent"
Tab 2: Also has same button
First click wins, second gets state update via polling
Both tabs sync
```
- [ ] Polling keeps tabs in sync

**PHASE 9 RESULT:** ✅ or ❌ (Mark when complete)

---

## PHASE 10: FINAL VERIFICATION

### All Phases Complete?
- [ ] Phase 1: Buyer View ✅
- [ ] Phase 2: Seller Primary ✅
- [ ] Phase 3: Seller Fallback ✅
- [ ] Phase 4: Error Handling ✅
- [ ] Phase 5: Authorization ✅
- [ ] Phase 6: State Transitions ✅
- [ ] Phase 7: UI Visibility ✅
- [ ] Phase 8: Polling ✅
- [ ] Phase 9: Edge Cases ✅

### Logic Fully Implemented?
- [ ] Buyer can notify seller
- [ ] Seller sees primary "Confirm" button
- [ ] Seller sees fallback "Record" button
- [ ] Both lead to same end result
- [ ] Rejections handled
- [ ] Retries work
- [ ] Authorization enforced
- [ ] Polling syncs realtime
- [ ] Edge cases covered

---

## STATUS: READY FOR NEXT STEP

When all phases marked complete ✅, implementation is LOGIC-COMPLETE.

