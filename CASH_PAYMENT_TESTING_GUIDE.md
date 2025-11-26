# 🧪 Cash Handshake Payment Testing Guide

## Implementation Summary

The cash payment handshake system now includes **complete role-based differentiation** between buyer and seller views. Here's what was implemented:

### ✅ Changes Made

#### 1. **Enhanced PaymentController** (`app/Domains/Payments/Http/Controllers/PaymentController.php`)
- Added `Log::debug()` logging on cash handshake access with full role/user details
- Added authorization check: Only the buyer can call `buyerClaimedPayment()`
- Added authorization check: Only the seller can call `sellerConfirmedPayment()`
- Added authorization check: Only the seller can call `sellerRejectedPayment()`
- All endpoint return 403 with clear error message if wrong user attempts action
- Enhanced logging tracks unauthorized access attempts

#### 2. **Completely Redesigned Cash Handshake View** (`resources/views/payments/cash-handshake.blade.php`)

**Role Indicator Banner (Always Visible):**
- 🔵 Blue banner for BUYER with clear role label
- 🟢 Green banner for SELLER with clear role label
- Shows what each user is doing in this payment

**BUYER VIEW (Only shows when `$isBuyer = true`):**
- "Payment Confirmation" heading
- Order details with expected amount
- Status tracker: 
  - ✓ Payment Claimed by You (green when complete)
  - ⏳ Waiting for Seller Response (yellow/green/red based on status)
- Single action: "I Have Sent Payment to Seller" button
  - Only enabled when status = 'pending'
  - Shows timestamp when clicked
- Rejection notice: Shows if seller rejects payment
- Clear messaging throughout

**SELLER VIEW (Only shows when `$isSeller = true`):**
- "Payment Verification" heading  
- Order details with expected amount to receive
- Status tracker:
  - ⏳ Waiting for Buyer (yellow if not confirmed)
  - ✓ Buyer Confirmed (green when they claim payment)
  - ✓ Your Response Pending (gray if waiting)
  - ✓ You Confirmed Receipt (green) or ✗ You Rejected (red)
- Two-step process:
  1. Wait for buyer to confirm
  2. Choose: "Yes, Payment Received" or "No, Payment Not Received"
- Completion message when confirmed
- Different UI for rejection

**Key Differences:**
- Buyer sees STEP 1 (claim payment)
- Seller sees STEP 2 (verify receipt)
- Different colored cards, messaging, and buttons
- Different completion flow

#### 3. **View Cache Cleared**
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

#### 4. **Temporary Debug Scripts Removed**
- Cleaned up: `fix_payment_controller.php`, `add_imports.php`, `remove_duplicates.php`, `fix_view.php`, `debug_orders.php`

---

## 🧪 How to Test

### Prerequisites
1. Two separate user accounts (preferably in different browser windows/incognito)
2. A service or open offer to create an order
3. Cash payment enabled in config

### Test Scenario: Complete Cash Payment Flow

#### Step 1: Create Order
1. **User A (BUYER)** logs in, browses services, selects one with cash payment
2. **User B (SELLER)** is the service creator
3. Order created with:
   - `buyer_id = User A's ID`
   - `seller_id = User B's ID`

#### Step 2: Both Access Payment Page
1. Both users access: `/payments/cash/handshake?handshakeId=XXX&orderId=YYY`

**Expected Results:**

📍 **User A (Buyer) Sees:**
- ✅ Blue "Your Role: BUYER" banner at top
- ✅ "Payment Confirmation" heading
- ✅ "STEP 1: Confirm Payment Sent" section
- ✅ Single button: "I Have Sent Payment to Seller"
- ✅ Status shows "Waiting for Seller Response..."
- ❌ NO seller response buttons
- ❌ NO "STEP 2" section

📍 **User B (Seller) Sees:**
- ✅ Green "Your Role: SELLER" banner at top
- ✅ "Payment Verification" heading
- ✅ "Waiting for Buyer..." message initially
- ✅ Once buyer confirms, shows:
  - Two buttons: "Yes, Payment Received" / "No, Payment Not Received"
- ✅ Status updated in real-time (every 3 seconds)
- ❌ NO buyer payment button
- ❌ NO "STEP 1" section

#### Step 3: Buyer Claims Payment
1. **User A** clicks "I Have Sent Payment to Seller"
2. **Expected:** Button shows "Payment Claimed - Waiting for Seller"
3. **Expected:** Timestamp shows when claimed

#### Step 4: Seller Receives Update (Polling)
1. **User B's page updates within 3 seconds** (polling interval)
2. **Expected:** 
   - Buyer status changes to "✓ Buyer Has Confirmed Payment Sent"
   - Two action buttons appear
   - Can now respond to payment

#### Step 5: Seller Confirms Payment
1. **User B** clicks "Yes, Payment Received"
2. **Expected:** Buttons become disabled
3. **Expected:** Status shows "✓ You Confirmed Receipt"
4. **Expected:** Both users redirected to order page in ~1.5 seconds

#### Step 6: Seller Rejects Payment (Alternative Flow)
1. Repeat from Step 3 with different session
2. **User B** clicks "No, Payment Not Received"
3. **Expected:** 
   - Buttons become disabled
   - Status shows "✗ You Rejected Payment"
   - Buyer sees "Payment Rejected" message
   - Buyer can try again

---

## 🔍 Additional Test Cases

### Test Case: Authorization Checks

#### Test: Wrong User Tries to Claim Payment
```
Payload: 
  POST /payments/cash/buyer-claimed
  {handshake_id: X, order_id: Y}
  
As: User C (not buyer or seller)

Expected:
  HTTP 403 Forbidden
  Message: "Only the buyer can claim payment"
```

#### Test: Seller Tries to Claim Payment
```
Expected:
  HTTP 403 Forbidden
  Message: "Only the buyer can claim payment"
```

#### Test: Buyer Tries to Confirm Seller Payment
```
Expected:
  HTTP 403 Forbidden
  Message: "Only the seller can confirm payment"
```

### Test Case: Debug Logging
1. Set `APP_DEBUG=true` in `.env`
2. Enable log monitoring: `tail -f storage/logs/laravel.log`
3. Perform cash payment flow
4. Check logs for entries:
   - `Cash Handshake Access` (successful)
   - `Unauthorized cash handshake access attempt` (failed auth)
   - `Unauthorized buyer payment claim` (wrong user)
   - Etc.

---

## 📋 Debugging Checklist

If roles are **still showing the same**:

### 1. Verify Authentication
```
Check browser:
- User A logged in? → Check user ID in storage
- User B logged in? → Check user ID in different browser/incognito
```

### 2. Verify Order Data
```bash
# In your Laravel installation, check order:
php artisan tinker
Order::find(ORDER_ID)->only(['id', 'buyer_id', 'seller_id'])
```

### 3. Verify URL Parameters
```
Check browser URL:
/payments/cash/handshake?handshakeId=cash_XXX_YYY&orderId=ZZZ
- Is handshakeId present? ✓
- Is orderId present? ✓
- Is orderId correct? ✓
```

### 4. Check Browser Console
```
- Any JavaScript errors?
- Check Network tab → GET /payments/cash/handshake/status
- Should see polling requests every 3 seconds
```

### 5. Review Logs
```bash
tail -f storage/logs/laravel.log
```

Look for:
- `Cash Handshake Access` entries with correct user/buyer/seller IDs
- Any authorization errors
- 403 responses

### 6. Force Refresh
```
HARD REFRESH on cash handshake page:
  Windows/Linux: Ctrl+Shift+R
  Mac: Cmd+Shift+R
```

---

## 🎯 Expected Outcomes

After implementation, you should see:

✅ **BUYER VIEW:**
- Blue header "Your Role: BUYER"
- Single action button to claim payment
- Waits for seller response
- Shows rejection if seller rejects

✅ **SELLER VIEW:**
- Green header "Your Role: SELLER"
- Waits for buyer initially
- Two action buttons after buyer confirms
- Clear indication of payment received/rejected status

✅ **AUTHORIZATION:**
- Only buyer can claim payment (403 if wrong user)
- Only seller can confirm/reject (403 if wrong user)
- Logs all access and authorization attempts

✅ **REAL-TIME UPDATES:**
- Polling updates every 3 seconds
- Both users see fresh data
- Auto-redirect after payment confirmed

---

## 🚀 Next Steps

If everything works:
1. Deploy to staging/production
2. Test with real users
3. Monitor logs for any issues
4. Consider adding:
   - Email notifications to buyer/seller
   - Timeout handling (if payment not confirmed after X minutes)
   - Payment dispute resolution UI
   - Admin dashboard to view pending payments

