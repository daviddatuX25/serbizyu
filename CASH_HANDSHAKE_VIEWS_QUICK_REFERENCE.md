# 🎯 Cash Handshake Views - Quick Reference

## What Changed?

### Before ❌
- **Single View**: `cash-handshake.blade.php` 
- Mixed buyer/seller logic with `@if ($isBuyer)` conditional
- Confusing for users (unclear what role they have)
- Hard to maintain (code duplication)

### After ✅
- **Two Separate Views**:
  - `cash-payment-request.blade.php` (Buyer: Request to Pay)
  - `cash-payment-release.blade.php` (Seller: Verify & Release)
- Clear, focused interface for each role
- Better UX and maintainability
- Dedicated styling and interaction for each flow

---

## File Locations

```
VIEWS (NEW):
├── resources/views/payments/cash-payment-request.blade.php
└── resources/views/payments/cash-payment-release.blade.php

DOCUMENTATION (NEW):
├── CASH_HANDSHAKE_SEPARATED_VIEWS.md (Full implementation guide)
└── CASH_HANDSHAKE_VIEWS_QUICK_REFERENCE.md (This file)

CONTROLLERS (UPDATED):
└── app/Domains/Payments/Http/Controllers/PaymentController.php
    └── cashHandshake() method routes to correct view

SERVICES (UNCHANGED):
└── app/Domains/Payments/Services/CashPaymentService.php

ROUTES (UNCHANGED):
└── /payments/cash/handshake
└── /payments/cash/buyer-claimed
└── /payments/cash/seller-confirmed
└── /payments/cash/seller-rejected
```

---

## How Controller Routes Works

```php
// In PaymentController::cashHandshake()

$isBuyer = $currentUserId === $order->buyer_id;
$isSeller = $currentUserId === $order->seller_id;

if ($isBuyer) {
    // → Shows cash-payment-request.blade.php
    // Feature: One big button "Confirm Payment Sent"
    return view('payments.cash-payment-request', ...);
} else {
    // → Shows cash-payment-release.blade.php
    // Feature: Two buttons "Received" / "Not Received"
    return view('payments.cash-payment-release', ...);
}
```

---

## Buyer View Features

### Screen 1: Initial State
```
┌─────────────────────────────────┐
│ 👤 Your Role: BUYER            │
├─────────────────────────────────┤
│ REQUEST PAYMENT                 │
│                                 │
│ 💵 Amount Due: ₱5,000.00       │
│ 📋 Order ID: #123              │
│                                 │
│ PAYMENT STATUS:                 │
│ ✓ Payment Sent (Your action)   │
│ 🟡 Request Confirmation        │
│ ⏳ Seller Verification         │
│                                 │
│ [CONFIRM PAYMENT SENT] (Blue)  │
│                                 │
│ 💡 Tips:                        │
│ • Confirm only after transfer   │
│ • Seller has 1 hour to verify   │
│ • Contact seller if rejected    │
└─────────────────────────────────┘
```

### Screen 2: After Clicking Button
```
┌─────────────────────────────────┐
│ ⏳ WAITING FOR SELLER           │
│                                 │
│ Button now says: "Payment       │
│ Requested" (Disabled/Gray)      │
│                                 │
│ Status shows:                   │
│ ✓ Payment Sent                  │
│ ✓ Request Confirmation          │
│ ⏳ Seller Verification (Waiting)│
│                                 │
│ Message: "Seller is verifying   │
│ your payment. Please wait..."   │
└─────────────────────────────────┘
```

### Screen 3: Success
```
┌─────────────────────────────────┐
│ ✓ PAYMENT CONFIRMED!            │
│                                 │
│ ✓ Payment Sent                  │
│ ✓ Request Confirmation          │
│ ✓ Seller Verification           │
│                                 │
│ "Seller confirmed receipt.      │
│  Order will proceed..."         │
│                                 │
│ (Auto-redirects to order page)  │
└─────────────────────────────────┘
```

### Screen 4: Rejected
```
┌─────────────────────────────────┐
│ ✗ PAYMENT NOT RECEIVED          │
│                                 │
│ "Seller hasn't received         │
│ payment yet."                   │
│                                 │
│ Seller's Note: "Transfer not    │
│ received in my account"         │
│                                 │
│ "Please contact seller to fix"  │
└─────────────────────────────────┘
```

---

## Seller View Features

### Screen 1: Waiting for Buyer
```
┌─────────────────────────────────┐
│ 👤 Your Role: SELLER            │
├─────────────────────────────────┤
│ VERIFY & RELEASE PAYMENT        │
│                                 │
│ 💵 Expected Amount: ₱5,000.00  │
│ 📋 Order ID: #123              │
│ 👥 Buyer: John Doe             │
│                                 │
│ PAYMENT STATUS:                 │
│ ⏳ Buyer Claims Payment         │
│ 🔲 Verify Payment Receipt       │
│ 🔲 Order Proceeds              │
│                                 │
│ ⏳ WAITING FOR BUYER...         │
│                                 │
│ "Buyer hasn't confirmed yet."   │
│ "Usually takes just a moment."  │
└─────────────────────────────────┘
```

### Screen 2: Buyer Confirmed (Action Required)
```
┌─────────────────────────────────┐
│ DID YOU RECEIVE ₱5,000.00?      │
│                                 │
│ "Buyer has confirmed they sent  │
│ payment. Verify if you          │
│ received it:"                   │
│                                 │
│ [✓ RECEIVED] [✗ NOT RECEIVED]  │
│  (Green)      (Red)             │
│                                 │
│ Status shows:                   │
│ ✓ Buyer Claims Payment          │
│ 🟡 Verify Payment Receipt       │
│ ⏳ Order Proceeds               │
└─────────────────────────────────┘
```

### Screen 3: Confirmed
```
┌─────────────────────────────────┐
│ ✓ PAYMENT CONFIRMED & RELEASED! │
│                                 │
│ ✓ Buyer Claims Payment          │
│ ✓ Verify Payment Receipt        │
│ ✓ Order Proceeds                │
│                                 │
│ "Payment verified successfully. │
│ Order is active."               │
│                                 │
│ Released at: Nov 26, 10:32 AM  │
│                                 │
│ [VIEW ORDER →]                  │
│                                 │
│ (Auto-redirects to order page)  │
└─────────────────────────────────┘
```

### Screen 4: Rejected
```
┌─────────────────────────────────┐
│ ✗ PAYMENT NOT RECEIVED          │
│                                 │
│ "You indicated payment not      │
│ received. Buyer will be asked   │
│ to retry or contact you."       │
│                                 │
│ Marked at: Nov 26, 10:32 AM    │
│                                 │
│ [START OVER] [MESSAGE BUYER]    │
└─────────────────────────────────┘
```

---

## JavaScript Components

### Buyer (`buyerPaymentRequest`)
```javascript
{
    data,              // Current handshake status
    loading: false,    // Loading state during submit
    pollInterval,      // Polling interval ID
    
    init()             // Start polling on page load
    requestPayment()   // Submit buyer claim via AJAX
    destroy()          // Cleanup on unload
}
```

### Seller (`sellerPaymentRelease`)
```javascript
{
    data,              // Current handshake status
    loading: false,    // Loading state during submit
    actionType,        // 'confirm' or 'reject'
    pollInterval,      // Polling interval ID
    
    init()             // Start polling on page load
    releasePayment()   // Submit response (confirm/reject)
    destroy()          // Cleanup on unload
}
```

---

## Real-Time Updates

Both views poll the same endpoint every **2 seconds**:

```
GET /payments/cash/handshake/status?handshakeId=cash_123_abc123def
```

**Response:**
```json
{
    "handshakeData": {
        "status": "buyer_claimed",
        "buyer_claimed_at": "2025-11-26T10:30:45",
        "seller_response_at": null,
        "rejection_reason": null
    }
}
```

**Update triggers:**
- Buyer sees seller's response immediately
- Seller sees buyer's confirmation immediately
- Auto-redirect on completion

---

## Status Flow

```
INITIAL: "pending"
    ↓ (Buyer clicks button)
    ↓
BUYER_CLAIMED: "buyer_claimed"
    ↓ (Seller clicks button)
    ├─ CONFIRMED: "seller_confirmed" → Order active → Redirect
    └─ REJECTED: "seller_rejected" → Order pending → Show rejection
```

---

## Testing Steps

### Test as Buyer
1. Create order with cash payment
2. You should see **cash-payment-request** view
3. Blue banner: "👤 Your Role: BUYER"
4. Click "✓ Confirm Payment Sent"
5. Button disables, shows spinner
6. See "⏳ Waiting for Seller" message
7. Wait for seller to confirm
8. Auto-redirect to order on confirm

### Test as Seller
1. Open same order in different browser/account
2. You should see **cash-payment-release** view
3. Green banner: "👤 Your Role: SELLER"
4. See "⏳ WAITING FOR BUYER" initially
5. Wait for buyer to confirm
6. Action buttons appear: "✓ Received" | "✗ Not Received"
7. Click "✓ Payment Received"
8. See confirmation message
9. Auto-redirect to order

### Test Rejection
1. Follow seller flow above
2. Click "✗ Not Received" instead
3. See "✗ Payment Not Received" message
4. Buyer gets same notification
5. Buyer can retry

---

## Common Issues & Fixes

| Issue | Cause | Fix |
|-------|-------|-----|
| "View not found" error | Views not created in correct location | Ensure files in `resources/views/payments/` |
| Always shows wrong view | Wrong user ID check | Verify `Auth::id() === $order->buyer_id` |
| Button doesn't work | CSRF token missing | Check `meta[name="csrf-token"]` in layout |
| Status not updating | Polling not working | Check `/payments/cash/handshake/status` route |
| Auto-redirect fails | Wrong redirect URL | Check route: `'/orders/' . $order->id` |

---

## Configuration

No new config needed! Uses existing settings:

```php
// config/payment.php
return [
    'pay_first' => env('PAY_FIRST_ENABLED', true),
    'cash_enabled' => env('CASH_PAYMENT_ENABLED', true),
    // ... other settings
];
```

---

## Routes Used

```
GET  /payments/cash/handshake
     ↳ PaymentController::cashHandshake()
     ↳ Routes to correct view based on role

POST /payments/cash/buyer-claimed
     ↳ PaymentController::buyerClaimedPayment()
     ↳ Called from buyer view

POST /payments/cash/seller-confirmed
     ↳ PaymentController::sellerConfirmedPayment()
     ↳ Called from seller view

POST /payments/cash/seller-rejected
     ↳ PaymentController::sellerRejectedPayment()
     ↳ Called from seller view

GET  /payments/cash/handshake/status
     ↳ PaymentController::getHandshakeStatus()
     ↳ Used for real-time polling
```

---

## Next Steps

1. ✅ Review both views
2. ✅ Test buyer flow
3. ✅ Test seller flow
4. ✅ Test rejection flow
5. ✅ Monitor logs
6. ✅ Deploy to staging
7. ✅ Deploy to production

---

## Support

Need help? Check:
- Full docs: `CASH_HANDSHAKE_SEPARATED_VIEWS.md`
- Logs: `storage/logs/laravel.log`
- DB: `Cache::get($handshakeId)`
- Routes: `php artisan route:list | grep cash`

