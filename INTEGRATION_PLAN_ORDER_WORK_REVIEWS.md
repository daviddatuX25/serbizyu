# Integration Plan: Order Completion → Work → Service Review System

**Date**: November 26, 2025  
**Status**: Planning Phase (Implementation by Developer)  
**Priority**: High - Core System Architecture

---

## 📋 Executive Summary

This document outlines the complete integration strategy for connecting three core systems:
1. **Order Completion** - Completion of an order triggers workflow finalization
2. **Work Instance** - Work belongs to and is tied to orders (not standalone)
3. **Service Review System** - After work completion, buyers can review services

### Key Problems to Solve:
- Work instances currently accessible via `/work-instances` but should be `/orders/{order}/work` 
- Order completion doesn't automatically trigger service review capability
- Service review system needs to be properly scaffolded
- Routes need refactoring to establish proper hierarchy

---

## 🎯 Current Architecture Analysis

### Order Model (`Order.php`)
```php
public function workInstance()
{
    return $this->hasOne(\App\Domains\Work\Models\WorkInstance::class);
}
```
✅ **Good**: One-to-One relationship established
⚠️ **Issue**: Routes don't reflect this hierarchical relationship

### WorkInstance Model (`WorkInstance.php`)
```php
public function order()
{
    return $this->belongsTo(Order::class);
}
```
✅ **Good**: Relationship is bidirectional
⚠️ **Issue**: Work is accessed independently, not nested under orders

### Current Routes Structure
```
/work-instances/{workInstance}                    # CURRENT (Flat)
/work-instances/{workInstance}/steps/{step}/...  # CURRENT (Flat)
```

**Problems**:
1. Doesn't reflect that work belongs to an order
2. No clear association path from UI perspective
3. Order completion doesn't have clear work-related actions
4. Service review is not connected to work completion

---

## 🔄 Proposed Integration Flow

### Phase 1: Order Completion → Work Completion Link

**Current Flow:**
```
Order Created
    ↓
WorkInstance Created (with order_id FK)
    ↓
Seller completes steps
    ↓
All steps done? YES → WorkInstance.status = 'completed'
    ↓
(No automatic order status update)
```

**Proposed Flow:**
```
Order Created
    ↓
WorkInstance Created (with order_id FK)
    ↓
Seller completes all steps
    ↓
WorkInstance.status = 'completed' AND completed_at = now()
    ↓
✅ Update Order.status = 'completed'
    ↓
✅ Trigger OrderCompleted event (enables review)
    ↓
✅ Notify buyer that work is complete → invite to review
```

### Phase 2: Route Restructuring

**Old Routes** (REMOVE)
```
GET    /work-instances/{workInstance}
POST   /work-instances/{workInstance}/steps/{step}/start
POST   /work-instances/{workInstance}/steps/{step}/complete
GET    /work-instances/{workInstance}/steps/{step}/activities
```

**New Routes** (ADD - Hierarchical under Orders)
```
GET    /orders/{order}/work                          # Show full work progress
POST   /orders/{order}/work/steps/{step}/start       # Start step
POST   /orders/{order}/work/steps/{step}/complete    # Complete step
GET    /orders/{order}/work/activities               # View all activities
```

**Keep for Reference** (Optional convenience routes)
```
GET    /work/{workInstance}                 # Redirect to /orders/{order}/work
```

### Phase 3: Service Review System Setup

**New Domain Structure**
```
app/Domains/Reviews/
├── Models/
│   ├── ServiceReview.php          # Review of a service (after work done)
│   └── ReviewerProfile.php        # Reviewer metadata (rating, count, etc)
├── Http/
│   └── Controllers/
│       └── ServiceReviewController.php
├── Services/
│   └── ReviewService.php          # Business logic
├── Policies/
│   └── ServiceReviewPolicy.php    # Authorization
├── Events/
│   └── ReviewCreated.php          # Event when review posted
└── Listeners/
    ├── UpdateServiceRating.php    # Update service avg rating
    └── UpdateUserRating.php       # Update seller avg rating
```

**Service Review Model Structure**
```php
class ServiceReview extends Model
{
    // Relationships
    - belongsTo(Order)
    - belongsTo(Service)
    - belongsTo(User as reviewer) // Buyer
    - belongsTo(User as reviewed_seller)
    
    // Attributes
    - order_id (unique with reviewer_id - prevent duplicates)
    - service_id
    - reviewer_id (buyer)
    - reviewed_user_id (seller)
    - rating (1-5, integer)
    - comment (text)
    - visibility (public/private) optional
    - helpful_count (upvotes)
    - created_at, updated_at
}
```

**Review Routes**
```
POST   /orders/{order}/review/create     # Show review form (GET)
POST   /orders/{order}/review            # Store review (POST)
GET    /orders/{order}/review/{review}   # Show review
PUT    /orders/{order}/review/{review}   # Update review (buyer only)
DELETE /orders/{order}/review/{review}   # Delete review (buyer only)

# Service Reviews Display
GET    /services/{service}/reviews       # List all reviews for service
GET    /services/{service}/rating        # Get rating summary
GET    /users/{user}/reviews-received    # All reviews received by seller
```

---

## 📊 Data Model Changes Required

### 1. Orders Table (Update)
```sql
-- Add review-related nullable columns
ALTER TABLE orders ADD COLUMN review_invite_sent_at TIMESTAMP NULLABLE;
ALTER TABLE orders ADD COLUMN is_reviewed BOOLEAN DEFAULT FALSE;
```

### 2. Create Service Reviews Table
```sql
CREATE TABLE service_reviews (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT NOT NULL UNIQUE, -- One review per order
    service_id BIGINT NOT NULL,
    reviewer_id BIGINT NOT NULL,     -- Buyer
    reviewed_user_id BIGINT NOT NULL, -- Seller
    rating TINYINT NOT NULL,         -- 1-5
    comment TEXT NULLABLE,
    visibility ENUM('public', 'private') DEFAULT 'public',
    helpful_count INT DEFAULT 0,
    flagged BOOLEAN DEFAULT FALSE,
    flag_reason TEXT NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (reviewer_id) REFERENCES users(id),
    FOREIGN KEY (reviewed_user_id) REFERENCES users(id),
    
    UNIQUE KEY unique_order_review (order_id, reviewer_id)
);

CREATE INDEX idx_reviews_service ON service_reviews(service_id);
CREATE INDEX idx_reviews_seller ON service_reviews(reviewed_user_id);
```

### 3. Services Table (Update)
```sql
-- Add rating summary columns
ALTER TABLE services ADD COLUMN average_rating DECIMAL(3,2) DEFAULT 0.00;
ALTER TABLE services ADD COLUMN review_count INT DEFAULT 0;
```

### 4. Users Table (Update)
```sql
-- Add seller performance metrics
ALTER TABLE users ADD COLUMN seller_average_rating DECIMAL(3,2) DEFAULT 0.00;
ALTER TABLE users ADD COLUMN seller_review_count INT DEFAULT 0;
```

---

## 🔗 Integration Points

### A. Order Completion Trigger

**In OrderService.php or WorkInstanceController.completeStep():**

```php
// When LAST step is completed:
if ($allStepsCompleted) {
    $workInstance->status = 'completed';
    $workInstance->completed_at = now();
    $workInstance->save();
    
    // ✅ Update associated order
    $order = $workInstance->order;
    $order->status = OrderStatus::COMPLETED->value;
    $order->save();
    
    // ✅ Fire event to trigger review invitation
    OrderCompleted::dispatch($order);
    
    // ✅ Notify buyer: "Work complete! Ready to review?"
    Notification::send($order->buyer, new OrderCompletedNotification($order));
}
```

### B. Work Navigation from Order

**In OrderController.show():**

```php
public function show(Order $order)
{
    $this->authorize('view', $order);
    
    $workInstance = $order->workInstance; // Eager load
    $workInstance->load('workInstanceSteps.activityThread.messages');
    
    return view('orders.show', [
        'order' => $order,
        'workInstance' => $workInstance,
        'isOrderComplete' => $order->status === OrderStatus::COMPLETED,
        'canLeaveReview' => $this->canLeaveReview($order),
    ]);
}

private function canLeaveReview(Order $order): bool
{
    return $order->status === OrderStatus::COMPLETED 
        && auth()->id() === $order->buyer_id
        && !$order->serviceReview; // Not already reviewed
}
```

### C. Review Creation After Work Completion

**New ServiceReviewController:**

```php
class ServiceReviewController extends Controller
{
    public function create(Order $order)
    {
        $this->authorize('create', [ServiceReview::class, $order]);
        
        return view('reviews.create', [
            'order' => $order,
            'service' => $order->service,
            'seller' => $order->seller,
        ]);
    }
    
    public function store(Order $order, StoreServiceReviewRequest $request)
    {
        $this->authorize('create', [ServiceReview::class, $order]);
        
        $review = ServiceReview::create([
            'order_id' => $order->id,
            'service_id' => $order->service_id,
            'reviewer_id' => auth()->id(),
            'reviewed_user_id' => $order->seller_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        // Update service and seller ratings
        ReviewCreated::dispatch($review);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Review posted successfully!');
    }
}
```

---

## 🛠️ Implementation Checklist

### Database Changes
- [ ] Create `service_reviews` table migration
- [ ] Add columns to `orders` table migration
- [ ] Add columns to `services` table migration
- [ ] Add columns to `users` table migration
- [ ] Run migrations

### Models
- [ ] Create `ServiceReview` model
- [ ] Add relationships to `Order` model
- [ ] Add relationships to `Service` model
- [ ] Add relationships to `User` model
- [ ] Create `ReviewService` service class

### Controllers & Routes
- [ ] Create `ServiceReviewController`
- [ ] Update `WorkInstanceController` to complete orders on final step
- [ ] Update `OrderController.show()` to load work instance
- [ ] Refactor routes: Move `/work-instances` → `/orders/{order}/work`
- [ ] Add review routes under orders

### Policies
- [ ] Create `ServiceReviewPolicy` (authorization rules)
- [ ] Create `ServiceReviewRequest` form request

### Events & Listeners
- [ ] Create `OrderCompleted` event
- [ ] Create `ReviewCreated` event
- [ ] Create `UpdateServiceRating` listener
- [ ] Create `UpdateUserRating` listener

### Views
- [ ] Create `orders/show.blade.php` (with embedded work view)
- [ ] Create `orders/work-section.blade.php` (embedded work progress)
- [ ] Create `reviews/create.blade.php` (review form)
- [ ] Create `reviews/show.blade.php` (display review)
- [ ] Update `services/show.blade.php` to display reviews
- [ ] Update `users/profile.blade.php` to show seller ratings

### Notifications
- [ ] Create `OrderCompletedNotification`
- [ ] Create `ReviewPostedNotification`

### Tests
- [ ] Test order completion triggers work update
- [ ] Test review creation prevents duplicates
- [ ] Test rating calculations
- [ ] Test authorization/policies

---

## 🚀 Proposed Route Structure After Refactoring

```
# ORDER ROUTES (Authenticated)
GET    /orders                           # List user's orders
GET    /orders/{order}                   # View order + embedded work
POST   /orders                           # Create order
POST   /orders/{order}/cancel            # Cancel order
POST   /orders/{order}/messages          # Messaging (existing)

# WORK ROUTES (Nested under Orders)
GET    /orders/{order}/work              # Show work progress (replaces /work-instances/{id})
POST   /orders/{order}/work/steps/{step}/start      # Start step
POST   /orders/{order}/work/steps/{step}/complete   # Complete step
GET    /orders/{order}/work/activities   # Get all activity threads
POST   /orders/{order}/work/activities/{activity}/messages  # Add to activity

# REVIEW ROUTES (Nested under Orders)
GET    /orders/{order}/review            # Show review form (if eligible)
POST   /orders/{order}/review            # Create/store review
PUT    /orders/{order}/review            # Update review
DELETE /orders/{order}/review            # Delete review

# SERVICE REVIEWS (Public)
GET    /services/{service}/reviews       # List reviews for service
GET    /services/{service}/rating        # Rating summary
GET    /users/{user}/reviews             # Seller's received reviews
```

---

## 📝 Relationships Summary

### Current State
```
Order 1:1 WorkInstance
Order 1:1 Service
Order 1:1 Payment
Order M:M Messages
```

### After Integration
```
Order 1:1 WorkInstance
Order 1:1 ServiceReview (nullable)
Order 1:1 Service
Order 1:1 Payment

Service 1:M ServiceReview
User 1:M ServiceReview (as seller - received_reviews)
User 1:M ServiceReview (as buyer - written_reviews)

WorkInstance 1:M WorkInstanceStep
WorkInstanceStep 1:M ActivityThread
ActivityThread 1:M ActivityMessage
```

---

## 🔒 Authorization Rules

### Work Instance/Steps
- **View**: Buyer OR Seller of the order
- **Edit/Complete Step**: Seller of the order only
- **Access**: Only if order exists and is IN_PROGRESS or COMPLETED

### Service Review
- **Create**: Buyer of COMPLETED order (not already reviewed)
- **View**: Public visibility users, or reviewer/seller
- **Update**: Reviewer (buyer) only, within 30 days
- **Delete**: Reviewer (buyer) only, within 7 days

---

## 📚 Key Events

```
OrderCompleted
  └─ Triggered when: Last work step is completed
  └─ Actions:
     - Update order status to COMPLETED
     - Send notification to buyer: "Ready to review?"
     - Enable review submission button

ReviewCreated
  └─ Triggered when: Review is posted
  └─ Actions:
     - Update service average_rating
     - Update service review_count
     - Update seller average_rating
     - Update seller review_count
     - Send notification to seller
     - Mark order as is_reviewed = true

StepCompleted
  └─ Triggered when: Any step is completed
  └─ Actions:
     - Update progress percentage
     - Send notification to both parties
     - (No order status change unless LAST step)
```

---

## 🎬 User Experience Flow

### For Buyer (After Ordering)
```
1. Browse service
2. Create order
3. Follow work progress in /orders/{id}
   - See embedded work timeline
   - Monitor seller's progress
   - Participate in activity threads
4. When work complete → Review prompt appears
5. Submit review → Contributes to service/seller ratings
```

### For Seller
```
1. Receive order
2. Navigate to work dashboard (/creator/work-dashboard)
3. Open order and see work steps
4. Complete steps one by one
5. When ALL steps complete → Order marked complete
6. Buyer can now review their work
7. See reviews on profile and service pages
```

---

## ⚠️ Migration Strategy (Backward Compatibility)

### Phase 1: Add New Routes (Keep Old)
```php
// NEW routes
Route::prefix('orders/{order}')->name('orders.')->group(function () {
    Route::get('/work', [WorkInstanceController::class, 'show']);
    // ... other routes
});

// OLD routes (for now) - will be deprecated
Route::prefix('work-instances')->name('work-instances.')->group(function () {
    // ... existing routes
});
```

### Phase 2: Redirect Old to New
```php
// In WorkInstanceController or middleware:
if (request()->is('work-instances/*')) {
    // Redirect to new route
    return redirect(/* new URL */);
}
```

### Phase 3: Remove Old Routes
- Remove old work-instances routes
- Remove references from views
- Update all links

---

## 🧪 Testing Considerations

### Unit Tests
- Order completion triggers status update
- Review prevents duplicates (unique constraint)
- Rating calculations are accurate
- Authorization policies work correctly

### Integration Tests
- Full flow: Create order → Complete work → Post review → Verify ratings updated
- Order completion notification sent
- Review creation triggers listener events
- Ratings reflected on service/seller pages

### Feature Tests
- UI buttons appear/disappear based on state
- Form validation on review creation
- Redirect logic on old routes

---

## 📖 Documentation Files to Update

- [ ] `MESSAGING_USER_GUIDE.md` - Add work and review sections
- [ ] `README.md` - Update architecture section
- [ ] Update `MILESTONE_2.2_COMPLETION.md` - Add review system roadmap
- [ ] Create new `REVIEW_SYSTEM_GUIDE.md` - User guide for reviews
- [ ] Create new `INTEGRATION_TECHNICAL_GUIDE.md` - Developer implementation details

---

## ✅ Success Criteria

1. **Order-Work Hierarchy**: Work is accessed exclusively through orders
2. **Automatic Completion**: When final work step completes, order status updates automatically
3. **Review System Ready**: Service review system is fully scaffolded and documented
4. **Routes Corrected**: All routes reflect proper hierarchy
5. **No Data Loss**: Migration doesn't affect existing orders/work
6. **Tests Passing**: All integration tests pass
7. **UI Consistent**: Buyer and seller flows are intuitive

---

## 📞 Questions for Developer

Before implementation, clarify:

1. Should old `/work-instances` routes continue to work or redirect?
2. For reviews: Allow editing after posting? If yes, until when?
3. Should reviews require moderation before public display?
4. Can sellers respond to reviews?
5. Should there be a minimum time before review can be deleted?
6. Do we need helpful/unhelpful voting on reviews?
7. Should reviews be searchable by rating/date/helpfulness?

---

**Document Version**: 1.0  
**Last Updated**: November 26, 2025  
**Ready for Implementation**: ✅ Yes
