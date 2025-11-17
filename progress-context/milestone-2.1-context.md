# Milestone 2.1: Order System Foundation - Detailed Plan (Revision 2)

This document outlines the detailed plan for implementing the Order System Foundation, following the project's iterative development process.

**Branch:** `feature/milestone-2.1-order-system`

## 1. Brainstorm and Scope Definition

*   **Domain:** `Orders`
*   **Entities:** `Order`
*   **Enums:** `OrderStatus`, `PaymentStatus`
*   **Events:** `OrderCreated`
*   **Invariants:**
    *   An order can only be created from an accepted bid.
    *   The order price is determined by the accepted bid.
    *   Platform fee is calculated based on a configurable percentage.
    *   Order status transitions are strictly controlled by the `OrderService`.
*   **Roles/Permissions (Spatie):**
    *   `buyer`: Can view their own orders, cancel orders (if status is `Pending`).
    *   `seller`: Can view orders placed on their services.
    *   `admin`: Can view all orders.

## 2. Logic and Flow Mapping

*   **Backend Flow:**
    1.  An `OpenOfferBid` is accepted by the seller.
    2.  The buyer navigates to a "Confirm Order" page, pre-filled with bid details.
    3.  The buyer submits the form, which hits the `OrderController@store` endpoint.
    4.  A `StoreOrderRequest` validates the input (e.g., `open_offer_bid_id`).
    5.  The `OrderPolicy` authorizes that the current user is the buyer.
    6.  `OrderService@createOrder` is called.
        *   It verifies the bid is accepted.
        *   It creates the `Order` record.
        *   It calculates the platform fee and total amount.
        *   It sets the initial `status` to `Pending` and `payment_status` to `Unpaid`.
    7.  The service dispatches an `OrderCreated` event with the new `Order` object.
    8.  The controller redirects the user to the `orders.show` page with a success message.
*   **Frontend Flow:**
    1.  After a seller accepts a bid, the buyer sees a "Complete Your Order" button on the offer page.
    2.  This button links to the `orders.create` route, passing the bid ID.
    3.  The `orders.create` page displays the service details, price, and a "Confirm & Proceed to Payment" button.
    4.  Upon confirmation, the user is redirected to the `orders.show` page for the new order.

## 3. Detailed Sub-Task Breakdown

### 3.1. Domain and Model Setup
*   **3.1.1: Create `Orders` Domain:** Create the directory structure for the new `Orders` domain (`app/Domains/Orders`).
*   **3.1.2: Create `OrderStatus` Enum:** Create `app/Enums/OrderStatus.php` with cases: `Pending`, `InProgress`, `Completed`, `Cancelled`, `Disputed`.
*   **3.1.3: Create `PaymentStatus` Enum:** Create `app/Enums/PaymentStatus.php` with cases: `Unpaid`, `Paid`, `Failed`, `Refunded`.
*   **3.1.4: Create `Order` Migration:** Generate and refine the migration for the `orders` table. Ensure all columns from the master plan are included and have the correct types (especially foreign keys and enums).
*   **3.1.5: Run Migration:** Run `php artisan migrate`.
*   **3.1.6: Create `Order` Model:** Create `app/Domains/Orders/Models/Order.php`.
    *   Add `$fillable` properties.
    *   Add casts for enums (`status` and `payment_status`).
    *   Define relationships: `buyer()` (`BelongsTo` User), `seller()` (`BelongsTo` User), `service()` (`BelongsTo` Service), `openOffer()` (`BelongsTo` OpenOffer), `bid()` (`BelongsTo` OpenOfferBid).

### 3.2. Service Layer and Business Logic
*   **3.2.1: Create `OrderService`:** Create the service class `app/Domains/Orders/Services/OrderService.php`.
*   **3.2.2: Implement `OrderService@createOrder`:**
    *   **Input:** `OpenOfferBid $bid`, `User $buyer`.
    *   **Validation:** Throw a `BusinessRuleException` if `!$bid->is_accepted`.
    *   **Logic:**
        *   Instantiate a new `Order`.
        *   Map properties from the `$bid` and related objects (`service_id`, `seller_id`, etc.).
        *   Calculate `platform_fee` (e.g., from a `config('fees.platform_percentage', 5)`)
        *   Calculate `total_amount`.
        *   Set `status` to `OrderStatus::Pending`.
        *   Set `payment_status` to `PaymentStatus::Unpaid`.
        *   Save the order.
    *   **Event:** Dispatch `new OrderCreated($order)`.
    *   **Return:** `Order $order`.
*   **3.2.3: Create `OrderCreated` Event:** Create the event class `app/Domains/Orders/Events/OrderCreated.php`. It should accept the `Order` model in its constructor.

### 3.3. Controller, Authorization, and Routing
*   **3.3.1: Create `OrderPolicy`:** Create `app/Domains/Orders/Policies/OrderPolicy.php`.
    *   `view(User $user, Order $order)`: Return `true` if `$user->id === $order->buyer_id || $user->id === $order->seller_id`.
    *   `create(User $user, OpenOfferBid $bid)`: Return `true` if `$user->id === $bid->user_id`.
    *   `cancel(User $user, Order $order)`: Return `true` if `$user->id === $order->buyer_id && $order->status === OrderStatus::Pending`.
*   **3.3.2: Register `OrderPolicy`:** Add `Order::class => OrderPolicy::class` to `AuthServiceProvider`.
*   **3.3.3: Create `StoreOrderRequest`:** Create `app/Domains/Orders/Http/Requests/StoreOrderRequest.php`.
    *   `authorize()`: Return `true`. Authorization will be in the controller.
    *   `rules()`: Return `['open_offer_bid_id' => 'required|exists:open_offer_bids,id']`.
*   **3.3.4: Create `OrderController`:** Create `app/Domains/Orders/Http/Controllers/OrderController.php`.
*   **3.3.5: Implement `OrderController@store`:**
    *   Load the `OpenOfferBid` from the validated request.
    *   Authorize the action: `$this->authorize('create', $bid)`.
    *   Call `OrderService@createOrder($bid, auth()->user())`.
    *   Add a success flash message to the session.
    *   Redirect to `route('orders.show', $order)`.
*   **3.3.6: Implement `OrderController@show` and `index`:**
    *   `show(Order $order)`: Authorize with `$this->authorize('view', $order)` and return the view.
    *   `index()`: Fetch orders where the user is either the buyer or the seller and return the view.
*   **3.3.7: Define Routes:** Add `Route::resource('orders', OrderController::class)` to `routes/web.php`, ensuring it's protected by the `auth` middleware.

### 3.4. Frontend Views
*   **3.4.1: Create `orders/index.blade.php`:**
    *   Loop through orders and display key info: Order ID, Service Title, Price, Status.
    *   Link each order to its `orders.show` page.
*   **3.4.2: Create `orders/show.blade.php`:**
    *   Display all order details in a structured way.
    *   Include buyer and seller information.
    *   Integrate the order status timeline component.
    *   Show a "Cancel Order" button if the policy passes (`@can('cancel', $order)`).
*   **3.4.3: Create `components/order-timeline.blade.php`:**
    *   A visual component that takes the order's status history (to be implemented later) or at least the current status and displays it nicely.
*   **3.4.4: Update Bid Acceptance Flow:** Modify the relevant view (`offers/show.blade.php` or a Livewire component) to provide a link/button to `route('orders.create', ['open_offer_bid_id' => $bid->id])` for the buyer once a bid is accepted.

### 3.5. Testing
*   **3.5.1: Create `OrderTest.php`:** Create `tests/Feature/Domains/Orders/OrderTest.php`.
*   **3.5.2: Write Authorization Tests:**
    *   Test that a guest cannot create or view orders.
    *   Test that a user cannot create an order for a bid they didn't make.
    *   Test that a user cannot view an order they are not involved in.
    *   Test that a user can only cancel their own order if the status is `Pending`.
*   **3.5.3: Write Order Creation Tests:**
    *   Test successful order creation from an accepted bid.
    *   Test that an order cannot be created from a non-accepted bid.
    *   Test that the platform fee and total amount are calculated correctly.
    *   Test that the `OrderCreated` event is dispatched upon creation.
*   **3.5.4: Write View Tests:**
    *   Test that the `orders.index` and `orders.show` routes load correctly for authorized users.

This detailed plan should provide a solid foundation and help avoid bugs. I will now proceed with the first step.
