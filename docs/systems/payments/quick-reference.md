# Quick Payment Setup Reference

## 1. Environment Setup (5 minutes)

### .env File Additions
```env
# Test Mode (Development)
PAYMENT_MODE=test
XENDIT_API_KEY=xnd_development_test
XENDIT_WEBHOOK_TOKEN=whsec_test
ENABLE_CASH_PAYMENT=true

# Production (Replace with real keys)
# PAYMENT_MODE=production
# XENDIT_API_KEY=xnd_live_your_key_here
# XENDIT_WEBHOOK_TOKEN=whsec_live_your_token_here
```

## 2. Database Migration (2 minutes)
```bash
php artisan migrate
# This adds payment_method and paid_at columns to orders table
```

## 3. Service Configuration

### For Pay-First Required (Before Work Starts)
```php
// In services table or ServiceController
Service::create([
    'pay_first' => true,  // ← Payment required before order activation
    'price' => 500.00,
    // ... other fields
]);
```

**Behavior:**
- User creates order → Forced to checkout
- Must pay before order becomes active

### For Optional Payment (Work Can Start Immediately)
```php
// In services table or ServiceController
Service::create([
    'pay_first' => false,  // ← Payment optional
    'price' => 500.00,
    // ... other fields
]);
```

**Behavior:**
- User creates order → Shows order page
- Payment reminder displayed
- Can start work immediately (optional payment)

## 4. Payment Method Routing

### What User Sees at Checkout

```
Choose Payment Method:

1. 💳 Online Payment (Xendit)
   - Credit/Debit Card
   - e-Wallet (OVO, GCash, etc.)
   - Bank Transfer
   - QR Code (QRIS)
   
2. 💵 Cash Payment
   - Pay in person / via local method
   - Seller confirmation required
```

## 5. Cash Payment Flow (User Perspective)

### Buyer Side
```
1. Select "Cash Payment"
2. See handshake page with amount
3. Click "I have paid this amount"
4. See "Waiting for seller to confirm..."
5. Seller confirms → Order active ✓
   OR Seller disputes → Try again ↻
```

### Seller Side
```
1. Receive notification: "Buyer claims payment"
2. Verify payment received in person/bank
3. Click "I received the payment" → Order active ✓
   OR Click "I didn't receive it" → Buyer can retry
```

## 6. Order Creation Flow (Automatic)

```
OrderController::store()
    ↓
Check: service.pay_first?
    ├─ YES → redirect to payments.checkout
    └─ NO  → redirect to orders.show (with payment reminder)

PaymentController::checkout()
    ↓
Display payment method options
    ├─ Xendit selected
    │   ├─ In TEST → Auto-approve
    │   └─ In PROD → Real Xendit invoice
    │
    └─ Cash selected
        └─ Show handshake form
```

## 7. Code Changes Summary

### OrderController (1 method updated)
```php
store() {
    if ($service->pay_first) {
        // Force payment
        return redirect()->route('payments.checkout', $order);
    }
    // Optional payment
    return redirect()->route('orders.show', $order);
}
```

### PaymentController (7 new methods)
```php
checkout()                  // Select payment method
pay()                      // Route to xendit or cash
cashHandshake()            // Display handshake form
buyerClaimedPayment()      // Buyer: "I paid"
sellerConfirmedPayment()   // Seller: "I got it"
sellerRejectedPayment()    // Seller: "I didn't get it"
waitingForSeller()         // Waiting page
paymentDisputed()          // Dispute page
```

### New Service: CashPaymentService
```php
initiateHandshake()        // Start cache-based handshake
buyerClaimedPayment()      // Update cache: buyer claimed
sellerConfirmedPayment()   // Update order: mark paid
sellerRejectedPayment()    // Reset order: payment_status=pending
getHandshakeStatus()       // Check current state
```

## 8. Testing Checklist

### Test Pay-First Service
- [ ] Create service with pay_first=true
- [ ] Create order from service
- [ ] Should redirect to /payments/checkout
- [ ] Select payment method
- [ ] Complete payment
- [ ] Order should be marked paid

### Test Optional Payment Service
- [ ] Create service with pay_first=false
- [ ] Create order from service
- [ ] Should redirect to /orders/{id}
- [ ] Should show payment reminder
- [ ] Click "Pay Now" (optional)
- [ ] Complete payment
- [ ] Order should update

### Test Xendit (Dev Mode)
- [ ] Select "Xendit" payment
- [ ] Should auto-approve in test mode
- [ ] Should redirect to success page
- [ ] Order payment_status should be "paid"

### Test Cash Payment
- [ ] Select "Cash Payment"
- [ ] Show handshake form
- [ ] Buyer clicks "I have paid"
- [ ] Redirect to waiting page
- [ ] Seller confirms
- [ ] Order payment_status should be "paid"
- [ ] Seller disputes
- [ ] Order stays pending
- [ ] Buyer can retry

## 9. Database Fields Added

```
orders table:
├─ payment_method VARCHAR  (default: 'xendit')  // NEW
└─ paid_at TIMESTAMP NULL  (nullable)            // NEW
```

## 10. Routes Added

```
Payments Routes:
├─ GET  /payments/checkout/{order}
├─ POST /payments/pay/{order}
├─ GET  /payments/success
├─ GET  /payments/failed
├─ GET  /payments/cash/handshake              // NEW
├─ POST /payments/cash/buyer-claimed          // NEW
├─ POST /payments/cash/seller-confirmed       // NEW
├─ POST /payments/cash/seller-rejected        // NEW
├─ GET  /payments/cash/waiting                // NEW
└─ GET  /payments/cash/disputed               // NEW
```

## 11. Configuration Matrix

| Scenario | pay_first | cash_enabled | Result |
|----------|-----------|--------------|--------|
| Online pay required | true | false | Xendit only, forced |
| Optional online | false | false | Xendit, optional |
| Cash required | true | true | Cash only, forced |
| Both available | false | true | Both methods available |

## 12. Important Environment Variables

```env
# Mode: 'test' for dev, 'production' for live
PAYMENT_MODE=test

# Xendit API credentials (required for online payments)
XENDIT_API_KEY=xnd_development_xxxxx
XENDIT_WEBHOOK_TOKEN=whsec_xxxxx

# Enable/disable cash payments
ENABLE_CASH_PAYMENT=true
```

## 13. State Management

### Order Payment States
```
pending          → Order created, waiting for payment
paid             → Payment confirmed (either method)
refunded         → Payment refunded
```

### Order Status (Separate from Payment)
```
pending          → Order in progress
active           → Work in progress (requires paid status)
completed        → Work completed
cancelled        → Order cancelled
```

## 14. Cash Payment Cache Structure

```php
[
    'order_id' => 123,
    'buyer_id' => 45,
    'seller_id' => 67,
    'amount' => 550.00,
    'status' => 'pending|buyer_claimed|seller_confirmed|seller_rejected',
    'buyer_claimed_at' => '2025-11-25 22:30:00',
    'seller_response_at' => '2025-11-25 22:32:00',
    'rejection_reason' => 'optional if rejected',
    'initiated_at' => '2025-11-25 22:25:00',
]
```
**TTL:** 1 hour (expires and is removed from cache)
**Storage:** In-memory (no database)
**Persistence:** Cash payment only updates DB on seller confirmation

## 15. Deployment Steps

```bash
# 1. Update .env with Xendit keys
XENDIT_API_KEY=xnd_live_xxxx
XENDIT_WEBHOOK_TOKEN=whsec_live_xxxx
PAYMENT_MODE=production

# 2. Run migrations
php artisan migrate

# 3. Configure services (set pay_first field)
# Update via admin or database directly

# 4. Test complete flow
# Create test orders with both pay_first values

# 5. Enable production Xendit webhooks
# Configure in Xendit dashboard
```

---

## Summary

✅ **Pay-First:** Enforces payment before order activation  
✅ **Optional Payment:** Allows order creation without payment  
✅ **Xendit:** Real online payment processing  
✅ **Cash:** In-memory handshake (no DB, 1-hour TTL)  
✅ **Dev Mode:** Auto-approves for testing  
✅ **Prod Mode:** Real payment processing  

**All ready to deploy!**

