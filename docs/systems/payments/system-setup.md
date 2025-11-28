# Payment System Setup & Implementation

## Environment Configuration

### Xendit Payment Gateway Setup

#### 1. Environment Variables (.env files)

**Development Mode (.env.dev, .env.local, .env.example):**
```env
# Payment Configuration - Development/Test Mode
PAYMENT_MODE=test
XENDIT_API_KEY=xnd_development_xxxxxxxxxxxxx
XENDIT_WEBHOOK_TOKEN=whsec_xxxxxxxxxxxxx
ENABLE_CASH_PAYMENT=true
```

**Production Mode (.env.prod):**
```env
# Payment Configuration - Production Mode
PAYMENT_MODE=production
XENDIT_API_KEY=xnd_live_xxxxxxxxxxxxx  # From Xendit Dashboard
XENDIT_WEBHOOK_TOKEN=whsec_live_xxxxxxxxxxxxx  # From Xendit Dashboard
ENABLE_CASH_PAYMENT=true  # Optional: disable if not needed
```

#### 2. Config File (config/payment.php)

```php
'mode' => env('PAYMENT_MODE', 'test'),  // 'test' or 'production'
'pay_first' => env('PAY_FIRST_ENABLED', true),  // Default: require payment first
'cash_enabled' => env('CASH_PAYMENT_ENABLED', true),  // Enable/disable cash payments
'xendit' => [
    'api_key' => env('XENDIT_API_KEY'),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
],
```

---

## Payment Flow Implementation

### 1. Pay-First Orders (Service has pay_first = true)

**Flow Diagram:**
```
1. Create Order
   ↓
2. User clicks "Proceed to Checkout"
   ↓
3. Payment Methods Page (Xendit or Cash)
   ├─→ [Xendit Selected]
   │   ↓
   │   4a. Redirect to Xendit Invoice
   │   ↓
   │   5a. User completes payment
   │   ↓
   │   6a. Success → Payment Status = "paid"
   │   ↓
   │   7a. Order Active → Go to Order Page
   │
   └─→ [Cash Selected]
       ↓
       4b. Cash Handshake Page
       ↓
       5b. Buyer clicks "I have paid"
       ↓
       6b. Seller receives notification
       ↓
       7b. Seller confirms/disputes
       ↓
       8b. If confirmed → Payment Status = "paid"
           Order Active → Go to Order Page
```

**Implementation:**
```php
// OrderController::store()
if ($service->pay_first) {
    // Create order in pending state
    $order = $this->orderService->createOrderFromService($service, Auth::user());
    // Redirect to payment checkout
    return redirect()->route('payments.checkout', $order);
}
```

---

### 2. Non-Pay-First Orders (Service has pay_first = false)

**Flow Diagram:**
```
1. Create Order
   ↓
2. Order Created Successfully
   ↓
3. Redirect to Order Show Page
   ↓
4. Display Payment Reminder Banner
   "Please complete payment to begin work"
   ↓
5. Optional: Buyer clicks "Pay Now"
   ↓
6. Same payment flow as Pay-First
```

**Implementation:**
```php
// OrderController::store()
if (!$service->pay_first) {
    // Create order and redirect to show
    $order = $this->orderService->createOrderFromService($service, Auth::user());
    return redirect()->route('orders.show', $order)
        ->with('info', 'Order created! Please proceed with payment to start work.');
}
```

---

## Payment Methods

### A. Xendit Online Payment

**Supported Methods:**
- Credit/Debit Card
- e-Wallet (OVO, GCash, DANA, LinkAja, etc.)
- Bank Transfer
- QR Code (QRIS)

**Features:**
- Instant payment confirmation
- Automatic order status update
- Webhook integration for real-time updates
- Invoice tracking

**Routes:**
- `POST /payments/pay/{order}` - Initiate Xendit payment
- `GET /payments/success` - Success callback
- `GET /payments/failed` - Failure callback

**Controller Methods:**
```php
PaymentController::pay()  // Creates Xendit invoice
PaymentController::success()  // Handles successful payment
PaymentController::failed()  // Handles failed payment
```

---

### B. Cash Payment (Local/Manual)

**Features:**
- Three-step handshake process (no database required)
- In-memory cache-based (TTL: 1 hour)
- Real-time status tracking
- Dispute mechanism

**Flow:**

#### Step 1: Initiation
```
Order → Buyer selects "Cash Payment"
→ Handshake initiated with cached data
→ Redirect to handshake page
```

#### Step 2: Buyer Confirms Payment
```
Buyer clicks "I have paid this amount"
→ CashPaymentService::buyerClaimedPayment()
→ Status: "pending" → "buyer_claimed"
→ Redirect to "Waiting for Seller" page
→ Seller receives notification
```

#### Step 3: Seller Confirms/Disputes
**Option A: Seller Confirms**
```
Seller clicks "I received the payment"
→ CashPaymentService::sellerConfirmedPayment()
→ Order payment_status = "paid"
→ Order payment_method = "cash"
→ Order becomes active
→ Redirect to order success page
```

**Option B: Seller Disputes**
```
Seller clicks "I didn't receive payment"
→ CashPaymentService::sellerRejectedPayment()
→ Order reverts to payment_status = "pending"
→ Buyer can retry
→ Dispute recorded (cache: handshake_data['rejection_reason'])
```

**Routes:**
```php
GET  /payments/cash/handshake         // Display handshake form
POST /payments/cash/buyer-claimed     // Buyer confirms payment
POST /payments/cash/seller-confirmed  // Seller confirms receipt
POST /payments/cash/seller-rejected   // Seller disputes
GET  /payments/cash/waiting           // Waiting for seller confirmation
GET  /payments/cash/disputed          // Payment disputed
```

**Cached Data Structure:**
```php
[
    'order_id' => 123,
    'buyer_id' => 45,
    'seller_id' => 67,
    'amount' => 550.00,
    'status' => 'pending|buyer_claimed|seller_confirmed|seller_rejected',
    'buyer_claimed_at' => '2025-11-25 22:30:00',
    'seller_response_at' => '2025-11-25 22:32:00',
    'rejection_reason' => 'optional reason if rejected',
    'initiated_at' => '2025-11-25 22:25:00',
]
```

---

## Database Changes

### New Migration: add_payment_method_to_orders_table

```php
// Adds to orders table:
$table->string('payment_method')->default('xendit');  // 'xendit' or 'cash'
$table->timestamp('paid_at')->nullable();  // When payment was completed
```

### Order Model Updates

**New Fillable Fields:**
- `payment_method` - Payment method used
- `paid_at` - Payment completion timestamp

---

## Service Classes

### PaymentService (Xendit)

**Development Mode:**
- Simulates Xendit invoice
- Auto-marks as paid
- Returns success URL

**Production Mode:**
- Creates real Xendit invoice
- Captures provider reference
- Logs all transactions

**Methods:**
```php
createInvoice(Order $order): string  // Returns invoice URL
```

### CashPaymentService

**Methods:**
```php
initiateHandshake(Order $order): string
buyerClaimedPayment(string $handshakeId): bool
sellerConfirmedPayment(string $handshakeId, int $orderId): bool
sellerRejectedPayment(string $handshakeId, int $orderId, string $reason): bool
getHandshakeStatus(string $handshakeId): array|null
cancelHandshake(string $handshakeId): bool
```

---

## Order Status Flow

```
Order Created
    ↓
    ├─→ [If pay_first = true]
    │   Payment Pending
    │   ↓
    │   Payment Complete? → Yes → Payment Status = "paid" → Order Active
    │   ↓ No
    │   Waiting for Payment
    │
    └─→ [If pay_first = false]
        Order Active (Payment Optional)
        ↓
        Payment Pending (if initiated)
        ↓
        Payment Complete? → Yes → Payment Status = "paid"
```

---

## Testing Payment Setup

### Test Xendit in Development Mode

1. **Verify Environment:**
   ```bash
   echo $PAYMENT_MODE  # Should output: test
   ```

2. **Test Order Creation:**
   - Create an order from a service with `pay_first = true`
   - Should redirect to payment checkout

3. **Test Payment Initiation:**
   - Click "Pay with Xendit"
   - Should simulate payment and redirect to success page

### Test Cash Payment

1. **Enable Cash Payment:**
   - Ensure `ENABLE_CASH_PAYMENT=true` in .env

2. **Test Handshake:**
   - Create an order
   - Select "Cash Payment"
   - Buyer claims payment
   - Seller confirms/disputes

---

## Configuration Checklist

- [ ] Set `PAYMENT_MODE=test` for development
- [ ] Set `PAYMENT_MODE=production` for production
- [ ] Add Xendit API key from dashboard
- [ ] Add Xendit Webhook token from dashboard
- [ ] Set `ENABLE_CASH_PAYMENT=true/false` based on preference
- [ ] Run migration: `php artisan migrate`
- [ ] Update service `pay_first` field as needed
- [ ] Test payment flow end-to-end

---

## Important Notes

1. **Cache TTL:** Cash payment handshakes expire after 1 hour
2. **No Database:** Cash payments use in-memory cache only
3. **Pay-First Logic:** Enforced at OrderController level
4. **Testing Mode:** Auto-approves payments in development
5. **Error Handling:** All payment operations logged comprehensively
6. **Authorization:** Payment confirmations require proper user authorization

---

## Quick Reference

| Scenario | Config | Behavior |
|----------|--------|----------|
| Pay First + Xendit | pay_first=true, cash_enabled=false | Force payment before work |
| Pay Optional + Xendit | pay_first=false, cash_enabled=false | Payment optional, online only |
| Pay First + Cash Only | pay_first=true, cash_enabled=true | Force payment via cash handshake |
| Pay Optional + Both | pay_first=false, cash_enabled=true | Multiple payment options |

