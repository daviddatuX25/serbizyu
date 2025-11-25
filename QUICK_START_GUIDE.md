# ⚡ QUICK REFERENCE - MESSAGING + PAYMENT MVP

## 🎯 WHAT'S READY NOW

✅ **Messaging** - Send/receive messages, unread counts  
✅ **Payment Config** - Environment-driven payment flow  
✅ **Cash Handshake** - No-database buyer/seller verification  
✅ **Pay-First Logic** - Routes based on config  

---

## 📨 MESSAGING USAGE

### API Endpoints:

```bash
# Get conversations
GET /api/messages/conversations

# Get chat history with user
GET /api/messages/{userId}/history

# Send message
POST /api/messages/{userId}
Body: { "content": "Hello!" }

# Mark message as read
PUT /api/messages/{messageId}/read

# Get unread count
GET /api/messages/unread/count
```

### Web Routes:

```
GET  /messages               → View all conversations
GET  /messages/{user}        → View chat with user
POST /messages/{thread}      → Send message
```

---

## 💳 PAYMENT FLOW

### Configuration (.env):

```env
PAY_FIRST_ENABLED=true      # true=pay before order | false=pay after
CASH_PAYMENT_ENABLED=true   # true=enable cash option
PAYMENT_MODE=test           # test or live
```

### Decision Tree:

```
Bid Accepted
    ↓
if (PAY_FIRST_ENABLED) {
    Show payment options:
    - Card / GCash / Bank Transfer
    - Cash (if CASH_PAYMENT_ENABLED)
} else {
    Create order directly
    Show "Payment due" reminder
}
```

### Cash Handshake Routes:

```
GET  /payments/cash/{order}               → Show handshake page
POST /payments/cash/{order}/buyer-confirm → Buyer: "I paid"
POST /payments/cash/{order}/seller-verify → Seller: Accept/Reject
```

---

## 🔧 CHECK PAYMENT CONFIG

```php
// In controller/view
if (config('payment.pay_first')) {
    // Require payment before order
}

if (config('payment.cash_enabled')) {
    // Show cash option
}
```

---

## 📱 CASH HANDSHAKE FLOW

**Buyer Page:**
```
"Amount: ₱5,000"
[✓ I Have Paid the Seller] ← Click to confirm
```

**Seller Page (after buyer confirms):**
```
"Buyer claims payment received"
[✓ Payment Received] [✗ Not Received]
```

**Result:**
- If accepted → Order proceeds
- If rejected → Payment failed, try again

---

## 🚀 TESTING

### Test Messaging:

```bash
# Send message (needs auth token)
curl -X POST http://127.0.0.1:8000/api/messages/2 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"content": "Test message"}'
```

### Test Payment Flow:

```php
// In tinker or route
use App\Domains\Payments\Helpers\PaymentFlowDecision;

PaymentFlowDecision::getFlowAfterBidAcceptance();
// Returns: 'pay_first' or 'order_page'

PaymentFlowDecision::availablePaymentMethods();
// Returns: ['card', 'gcash', 'bank_transfer', 'cash']
```

---

## 📂 KEY FILES

**Messaging:**
- `app/Domains/Messaging/Services/MessageService.php`
- `app/Domains/Messaging/Http/Controllers/MessageController.php`
- `routes/api.php` (messaging endpoints)
- `routes/web.php` (messaging routes)

**Payment:**
- `app/Domains/Payments/Services/PaymentHandler.php`
- `app/Domains/Payments/Helpers/PaymentFlowDecision.php`
- `app/Domains/Payments/Http/Controllers/CashPaymentController.php`
- `resources/views/payments/cash-handshake.blade.php`
- `config/payment.php`
- `.env` (payment config)

---

## 🔗 INTEGRATION EXAMPLE

```php
// When bid is accepted
use App\Domains\Payments\Helpers\PaymentFlowDecision;

$flow = PaymentFlowDecision::getFlowAfterBidAcceptance();

if ($flow === 'pay_first') {
    // Show payment options
    $methods = PaymentFlowDecision::availablePaymentMethods();
    return view('checkout', compact('methods'));
} else {
    // Create order and redirect
    $order = Order::create([...]);
    return redirect()->route('orders.show', $order);
}
```

---

## ⚡ QUICK TOGGLES

Change behavior by updating `.env`:

```env
# Enable/disable pay-first
PAY_FIRST_ENABLED=true    # ← Change this

# Enable/disable cash option
CASH_PAYMENT_ENABLED=true # ← Change this

# Switch between test/live
PAYMENT_MODE=test         # ← Change this
```

No code changes needed! Config-driven 🎉

---

## 🎯 NEXT STEPS

1. **Test Messaging:** Send messages between users via API
2. **Configure Xendit:** Add API keys to `.env`
3. **Test Payment Flow:** Try different env configs
4. **Integrate Checkout:** Connect to Xendit SDK for card payments
5. **Add Broadcasting:** Soketi + Echo for real-time messages (optional)

---

**Everything is ready. Start integrating!** 🚀
