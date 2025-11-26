# Payment Method Selection UI - Implementation Guide

**Status:** ✅ COMPLETE  
**Date:** November 26, 2025

## Overview

A modal-based UI has been implemented to allow users to select their preferred payment method (Online/Xendit or Cash) when:
1. **Accepting a bid** from an open offer (creator accepting a bid)
2. **Proceeding to order** from a service listing (buyer checkout)

---

## What's New

### 1. **Bid Acceptance Flow** (Service/Bid → Offer)
**File:** `resources/views/listings/partials/bid-list.blade.php`

When a creator clicks "Accept" on a bid:
- A modal appears with two payment method options:
  - ✅ **Online (Xendit)** - Credit card, e-wallet, bank transfer
  - ✅ **Cash payment** - In-person handshake with seller confirmation
- Creator selects preferred method and clicks "Proceed"
- Form submits to `creator.openoffers.bids.accept` with `payment_method` parameter

**Implementation:**
```html
<div x-data="{ open: false }" class="inline-block">
    <button @click="open = true" type="button">Accept</button>
    
    <!-- Modal with payment method selection -->
    <div x-show="open" x-cloak class="fixed inset-0 z-50 ...">
        <!-- Xendit / Cash radio buttons -->
        <form action="{{ route('creator.openoffers.bids.accept', [...]) }}" method="POST">
            <input type="radio" name="payment_method" value="online" checked>
            <input type="radio" name="payment_method" value="cash">
        </form>
    </div>
</div>
```

---

### 2. **Service Checkout Flow** (Service Page)
**File:** `resources/views/listings/services/show.blade.php`

When a buyer clicks "Proceed to Order" on a service page:
- A modal appears with two payment method options (same as above)
- Buyer selects preferred method and clicks "Proceed"
- Form submits to `services.checkout` with `payment_method` parameter

**Implementation:** Similar modal-based UI using Alpine.js `x-data` and `x-show` directives

---

## Backend Integration

### Controller: `OpenOfferBidController::accept()`
**File:** `app/Domains/Listings/Http/Controllers/OpenOfferBidController.php`

```php
public function accept(Request $request, OpenOffer $openoffer, OpenOfferBid $bid)
{
    $this->authorize('accept', $bid);
    
    try {
        $this->openOfferBidService->acceptBid($bid);
        $order = $this->orderService->createOrderFromBid($bid);
        
        $service = $order->service;
        $paymentMethod = $request->input('payment_method') ?? ($service->payment_method?->value ?? null);
        
        // Route based on payment method and pay_first setting
        if ($service->pay_first) {
            if ($paymentMethod === 'cash') {
                return redirect()->route('payments.checkout', ['order' => $order, 'payment_method' => 'cash']);
            }
            return redirect()->route('payments.checkout', ['order' => $order, 'payment_method' => 'online']);
        }
        
        return redirect()->route('orders.show', $order)->with('success', 'Bid accepted and order created successfully!');
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
```

**Key Changes:**
- ✅ Accepts `Request` object to capture `payment_method` form field
- ✅ Reads user's selected payment method from form
- ✅ Routes to `payments.checkout` with `payment_method` query parameter
- ✅ Respects `pay_first` configuration for automatic payment enforcement

### Controller: `ServiceController::checkout()`
**File:** `app/Domains/Listings/Http/Controllers/ServiceController.php`

```php
private function handleOrderPaymentFlow($order, $service, $paymentMethod)
{
    if ($service->pay_first) {
        if ($paymentMethod === 'cash') {
            return redirect()->route('payments.checkout', ['order' => $order, 'payment_method' => 'cash']);
        }
        return redirect()->route('payments.checkout', ['order' => $order, 'payment_method' => 'online']);
    }

    return redirect()->route('orders.show', $order)
        ->with('info', 'Order created! Please proceed with payment to start work.');
}
```

---

## Payment Checkout Page

**File:** `resources/views/payments/checkout.blade.php`

The checkout page now:
- ✅ Receives `payment_method` query parameter
- ✅ Displays selected payment method as a badge
- ✅ Passes method through hidden form field to `payments.pay`
- ✅ `PaymentController::pay()` routes to appropriate payment processor

**Frontend:**
```blade
<form action="{{ route('payments.pay', $order) }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="{{ request('payment_method', 'online') }}">
    
    <!-- Display selected method -->
    <span class="px-3 py-1 rounded-full bg-gray-100">
        {{ strtoupper(request('payment_method', 'online')) }}
    </span>
</form>
```

---

## Data Flow Diagram

```
User clicks "Accept Bid"
    ↓
Modal appears (Alpine.js)
    ↓ (User selects online/cash)
    ↓
Form submits payment_method → OpenOfferBidController::accept()
    ↓
Order created
    ↓
if (pay_first && method=cash)
    → redirect to payments.checkout?payment_method=cash
else if (pay_first && method=online)
    → redirect to payments.checkout?payment_method=online
else
    → redirect to orders.show
    ↓
PaymentController handles checkout/pay with selected method
```

---

## Route Changes

**New/Modified Routes:**
```php
// Accept bid with payment method selection
POST /creator/openoffers/{openoffer}/bids/{bid}/accept → payment_method form field

// Service checkout with payment method selection
POST /services/{service}/checkout → payment_method form field

// Checkout page receives payment_method query param
GET /payments/checkout/{order}?payment_method=online|cash

// Pay with selected method
POST /payments/pay/{order} → payment_method hidden field
```

---

## UI Components

### Alpine.js Modal
- **Uses:** `x-data="{ open: false }"`, `x-show="open"`, `x-cloak`
- **Benefits:**
  - ✅ No page reload
  - ✅ Smooth fade in/out
  - ✅ Already included in Vite bundle
  - ✅ No additional dependencies

### Radio Button Options
```html
<label class="flex items-center space-x-2">
    <input type="radio" name="payment_method" value="online" checked>
    <span>Online (Xendit)</span>
</label>

<label class="flex items-center space-x-2">
    <input type="radio" name="payment_method" value="cash">
    <span>Cash payment</span>
</label>
```

---

## Configuration

**Service Model:**
```php
protected $fillable = ['title', 'description', 'price', 'pay_first', 'payment_method', ...];

protected $casts = [
    'pay_first' => 'boolean',
    'payment_method' => PaymentMethod::class,
];
```

**PaymentMethod Enum:**
- `ONLINE` - Xendit online payment
- `CASH` - Cash handshake payment
- `ANY` - User choice (uses modal)

---

## Test Coverage

**Unit Tests Added:**
1. ✅ `service_checkout_with_online_payment_redirects_to_checkout()`
2. ✅ `service_checkout_with_cash_payment_redirects_to_checkout_with_cash_selected()`
3. ✅ `creator_accepting_bid_redirects_to_checkout_with_online_payment()`
4. ✅ `creator_accepting_bid_redirects_to_checkout_with_cash_payment()`

---

## Environment Variables

```env
# Payment method defaults (can be overridden by user modal)
PAYMENT_MODE=production
XENDIT_API_KEY=xnd_live_xxxxx
XENDIT_WEBHOOK_TOKEN=whsec_live_xxxxx
ENABLE_CASH_PAYMENT=true
```

---

## Backward Compatibility

✅ **Fully backward compatible:**
- Default to `online` if no payment_method selected
- Existing orders and services work unchanged
- No database migrations required for UI feature
- Falls back to service's `payment_method` setting if not provided in form

---

## Next Steps / Optional Enhancements

1. **Save user payment preference** → Remember last used method per user
2. **Analytics** → Track which payment methods are most popular
3. **Wallet integration** → Add stored payment methods
4. **Split payments** → Allow partial payment options
5. **Recurring payment** → Subscription-style orders

---

## Files Modified

1. ✅ `resources/views/listings/partials/bid-list.blade.php`
2. ✅ `resources/views/listings/services/show.blade.php`
3. ✅ `resources/views/payments/checkout.blade.php`
4. ✅ `app/Domains/Listings/Http/Controllers/OpenOfferBidController.php`
5. ✅ `app/Domains/Listings/Http/Controllers/ServiceController.php`
6. ✅ `tests/Feature/OrderCreationTest.php`
7. ✅ `tests/Feature/Domains/Orders/OrderTest.php`
8. ✅ `README_PAYMENT_SYSTEM.md`

---

## Summary

The payment method selection UI is now fully implemented across the platform. Users can:
- ✅ Choose between Online (Xendit) and Cash payment when accepting bids
- ✅ Choose between Online (Xendit) and Cash payment when checking out services
- ✅ Have their choice automatically routed to the appropriate payment processor
- ✅ Fallback to service defaults if not using the modal

The implementation is clean, modular, and maintains backward compatibility with existing flows.
