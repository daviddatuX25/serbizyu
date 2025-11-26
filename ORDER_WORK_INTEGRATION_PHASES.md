# 🔄 ORDER-WORK INTEGRATION - IMPLEMENTATION PHASES

**Status**: Phase 1 ✅ COMPLETE | Phases 2-4 IN PROGRESS  
**Date**: November 27, 2025

---

## ✅ PHASE 1: Work Completion Sync (COMPLETE)

### What Was Done
- ✅ Updated `WorkInstanceController::completeStep()` 
- ✅ Added `OrderStatus::COMPLETED` import
- ✅ When all work steps complete → Order status automatically updates to COMPLETED
- ✅ Added clear success message for UX

### Code Changed
**File**: `app/Domains/Work/Http/Controllers/WorkInstanceController.php`

```php
// BEFORE: No order status update
if ($allStepsCompleted) {
    $workInstance->status = 'completed';
    $workInstance->completed_at = now();
}
$workInstance->save();

// AFTER: Order status synced ✅
if ($allStepsCompleted) {
    $workInstance->status = 'completed';
    $workInstance->completed_at = now();
    $workInstance->save();

    // ✅ SYNC ORDER STATUS - Critical for review system
    $order = $workInstance->order;
    $order->status = OrderStatus::COMPLETED;
    $order->save();
    
    return back()->with('success', 'All work steps completed! Order is now complete. Buyer can now leave a review.');
}
```

### Impact
- ✅ Order and Work systems now synchronized
- ✅ Review system can now check `Order.status === COMPLETED`
- ✅ Enables downstream features (reviews, ratings, disbursement)

---

## 🔄 PHASE 2: Route Refactoring (IN PROGRESS)

### Current State (FLAT)
```
/work-instances/{workInstance}
/work-instances/{workInstance}/steps/{step}/start
/work-instances/{workInstance}/steps/{step}/complete
/work-instances/{workInstance}/steps/{step}/activities
/creator/work-dashboard                          ← Standalone dashboard
```

### Target State (HIERARCHICAL)
```
/orders/{order}                                   ← Main order view
/orders/{order}/work                              ← Nested work progress
/orders/{order}/work/steps/{step}/start           ← Start step under order
/orders/{order}/work/steps/{step}/complete        ← Complete step under order
/orders/{order}/work/activities                   ← Activity threads
```

### Implementation Strategy

#### Step 1: Add New Nested Routes (Keep Old for Now)

**File**: `routes/web.php`

Find this section:
```php
// Work Instance Management
Route::middleware(['auth'])->prefix('work-instances')->name('work-instances.')->group(function () {
    Route::get('/{workInstance}', [WorkInstanceController::class, 'show'])->name('show');
    Route::post('/{workInstance}/steps/{workInstanceStep}/start', [WorkInstanceController::class, 'startStep'])->name('steps.start');
    Route::post('/{workInstance}/steps/{workInstanceStep}/complete', [WorkInstanceController::class, 'completeStep'])->name('steps.complete');
    Route::resource('/{workInstance}/steps/{workInstanceStep}/activities', ActivityController::class);
});
```

**Replace with** (add BOTH old and new):
```php
// Work Instance Management - NEW HIERARCHICAL ROUTES (Phase 2.1)
Route::middleware(['auth'])->prefix('orders/{order}/work')->name('orders.work.')->group(function () {
    // These map to the same controllers but with order context
    Route::get('/', [WorkInstanceController::class, 'show'])->name('show');
    Route::post('/steps/{workInstanceStep}/start', [WorkInstanceController::class, 'startStep'])->name('steps.start');
    Route::post('/steps/{workInstanceStep}/complete', [WorkInstanceController::class, 'completeStep'])->name('steps.complete');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::post('/steps/{workInstanceStep}/activities', [ActivityController::class, 'store'])->name('activities.store');
});

// Work Instance Management - OLD ROUTES (DEPRECATED - kept for backward compatibility)
Route::middleware(['auth'])->prefix('work-instances')->name('work-instances.')->group(function () {
    Route::get('/{workInstance}', [WorkInstanceController::class, 'show'])->name('show');
    Route::post('/{workInstance}/steps/{workInstanceStep}/start', [WorkInstanceController::class, 'startStep'])->name('steps.start');
    Route::post('/{workInstance}/steps/{workInstanceStep}/complete', [WorkInstanceController::class, 'completeStep'])->name('steps.complete');
    Route::resource('/{workInstance}/steps/{workInstanceStep}/activities', ActivityController::class);
});
```

#### Step 2: Update Controllers to Handle Both Old and New Routes

The controllers already get `WorkInstance` and `Order` via route model binding, so they should work with minimal changes.

**Potential Issue**: The new routes pass `{order}` but the old code expects `{workInstance}`. 

**Solution**: Update controller methods to accept both:

```php
// In WorkInstanceController
public function show($order = null, WorkInstance $workInstance = null)
{
    // Handle new route: /orders/{order}/work
    if ($order !== null) {
        $workInstance = Order::findOrFail($order)->workInstance;
    }
    
    $this->authorize('view', $workInstance);
    $workInstance->load('workInstanceSteps.activityThread.messages');
    return view('work.show', compact('workInstance'));
}
```

#### Step 3: Update Views and Links

Find all references to old routes and update them:

**Search for**:
- `route('work-instances.show', ...)`
- `route('work-instances.steps.start', ...)`
- `route('work-instances.steps.complete', ...)`
- `/work-instances/`

**Replace with**:
- `route('orders.work.show', [$order])`
- `route('orders.work.steps.start', [$order, $step])`
- `route('orders.work.steps.complete', [$order, $step])`
- `/orders/{$order}/work`

---

## 🗑️ PHASE 3: Remove Standalone Work Dashboard

### Current State
```
/creator/work-dashboard          ← Standalone dashboard
↓ calls WorkInstanceController::index()
↓ renders work-dashboard view
```

### Target State
```
/creator/dashboard               ← Already exists, show work here
/orders/{order}                  ← Show embedded work in order view
```

### Implementation

#### Step 1: Remove Dashboard Route
**File**: `routes/web.php`

Find and remove:
```php
// Seller Work Dashboard
Route::get('/work-dashboard', [WorkInstanceController::class, 'index'])->name('creator.work-dashboard');
```

#### Step 2: Add Work Listing to Creator Dashboard
**File**: `app/Domains/Users/Http/Controllers/CreatorDashboardController.php`

Update the `index()` method to include work instances:

```php
public function index()
{
    $user = Auth::user();
    $workInstances = WorkInstance::whereHas('order', function ($query) use ($user) {
        $query->where(function ($q) use ($user) {
            $q->where('seller_id', $user->id)
              ->orWhere('buyer_id', $user->id);
        });
    })->with('order')->latest()->paginate(10);
    
    // ... return view with $workInstances
}
```

#### Step 3: Update Creator Dashboard View
**File**: `resources/views/creator/dashboard.blade.php`

Add section showing active work:
```php
<div class="card mt-4">
    <div class="card-header">
        <h5>Active Work</h5>
    </div>
    <div class="card-body">
        @forelse($workInstances as $work)
            <div class="work-item">
                <a href="{{ route('orders.work.show', $work->order) }}">
                    Order #{{ $work->order->id }}
                </a>
                <span class="badge">{{ $work->status }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width: {{ $work->getProgressPercentage() }}%">
                        {{ $work->getProgressPercentage() }}%
                    </div>
                </div>
            </div>
        @empty
            <p>No active work</p>
        @endforelse
    </div>
</div>
```

---

## 📋 PHASE 4: Review System Integration

### Current State
- ServiceReview model exists ✓
- Review controller exists ✓
- But review system doesn't know order is complete

### Target State
```
Order Complete (Phase 1 ✅)
    ↓
Order.status = 'completed'
    ↓
Buyer sees "Leave Review" button
    ↓
Buyer clicks "Leave Review"
    ↓
Review form appears
    ↓
ServiceReview created
    ↓
Service/Seller ratings auto-update
```

### Implementation

#### Step 1: Add Review Eligibility Check
**File**: `app/Domains/Orders/Models/Order.php`

Add method to check if order can be reviewed:

```php
public function isEligibleForReview(): bool
{
    return $this->status === OrderStatus::COMPLETED 
        && !$this->reviews()->exists();
}

public function reviews()
{
    return $this->hasMany(ServiceReview::class);
}
```

#### Step 2: Update Order Show View
**File**: `resources/views/orders/show.blade.php`

Add review section:

```php
@if($order->isEligibleForReview())
    <div class="alert alert-info">
        <h5>Work Complete!</h5>
        <p>Please leave a review to help {{ $order->seller->name }} improve.</p>
        <a href="{{ route('reviews.create', ['order' => $order]) }}" class="btn btn-primary">
            Leave Review
        </a>
    </div>
@elseif($order->reviews()->exists())
    <div class="alert alert-success">
        <h5>Your Review</h5>
        {{-- Show review details --}}
    </div>
@endif
```

#### Step 3: Create Review Route
**File**: `routes/web.php`

```php
Route::middleware(['auth'])->prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/create', [ReviewController::class, 'create'])->name('create');
    Route::post('/', [ReviewController::class, 'store'])->name('store');
    Route::put('/{review}', [ReviewController::class, 'update'])->name('update');
});
```

#### Step 4: Review Controller Validation
**File**: Check authorization:

```php
public function store(Request $request)
{
    $order = Order::findOrFail($request->order_id);
    
    // Verify buyer and order is complete
    $this->authorize('create', [ServiceReview::class, $order]);
    
    // Create review...
}
```

---

## 🧪 TESTING CHECKLIST

### Phase 1 Tests (Sync)
- [ ] Create order
- [ ] Start work via `/orders/{order}/work/steps/{step}/start`
- [ ] Complete all steps
- [ ] Check database: `orders.status = 'completed'` ✓
- [ ] Check notifications sent ✓

### Phase 2 Tests (Routes)
- [ ] Old route `/work-instances/{id}` still works
- [ ] New route `/orders/{order}/work` works
- [ ] Start step from new route
- [ ] Complete step from new route
- [ ] Old and new routes show same view

### Phase 3 Tests (Dashboard)
- [ ] `/creator/work-dashboard` redirects or 404s
- [ ] Creator dashboard shows active work
- [ ] Clicking work opens `/orders/{order}/work`

### Phase 4 Tests (Review)
- [ ] After order complete, review button appears
- [ ] Clicking review opens form
- [ ] Submitting review creates ServiceReview
- [ ] Service ratings update ✓
- [ ] Can't review incomplete order
- [ ] Can't review twice

---

## 🎯 FINAL VERIFICATION

After completing all phases:

```
BEFORE Integration:
├─ Order System
│   └─ Order model
├─ Work System (SEPARATE)
│   └─ WorkInstance model
│   └─ Work Routes (FLAT)
│   └─ Work Dashboard (STANDALONE)
└─ Review System
    └─ Disconnected

AFTER Integration:
└─ Order System (UNIFIED)
    ├─ Order model (master)
    ├─ WorkInstance (nested under order)
    │   └─ Routes: /orders/{order}/work
    ├─ ServiceReview (nested under order)
    │   └─ Triggered when order.status = COMPLETED
    └─ Dashboard: One unified order+work view
```

---

## 📝 FILES TO MODIFY SUMMARY

| Phase | File | Change | Status |
|-------|------|--------|--------|
| 1 | `WorkInstanceController.php` | Add order status sync | ✅ DONE |
| 2 | `routes/web.php` | Add nested routes | ⏳ TODO |
| 2 | `OrderController.php` | Handle order param | ⏳ TODO |
| 2 | Views | Update route references | ⏳ TODO |
| 3 | `routes/web.php` | Remove dashboard route | ⏳ TODO |
| 3 | `CreatorDashboardController.php` | Add work section | ⏳ TODO |
| 3 | Dashboard view | Show embedded work | ⏳ TODO |
| 4 | `Order.php` | Add review methods | ⏳ TODO |
| 4 | Order view | Show review button | ⏳ TODO |
| 4 | `ReviewController` | Validate order.status | ⏳ TODO |

---

## 🚀 NEXT STEPS

1. **Phase 2**: Start route refactoring
   - Add new nested routes
   - Keep old routes for backward compat
   - Test both work

2. **Phase 3**: Integrate dashboard
   - Remove standalone work dashboard
   - Add work to creator dashboard

3. **Phase 4**: Connect review system
   - Add review button to order view
   - Trigger on order completion

---

**Last Updated**: November 27, 2025 | Phase 1 Complete ✅
