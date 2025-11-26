# Quick Reference: Integration Implementation Tasks

## 🎯 For the Developer - What Needs to Be Built

### Priority 1: Database & Models (Foundation)

#### 1. Create Service Reviews Migration
**File**: `database/migrations/xxxx_create_service_reviews_table.php`

```php
Schema::create('service_reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
    $table->foreignId('service_id')->constrained('services');
    $table->foreignId('reviewer_id')->constrained('users'); // Buyer
    $table->foreignId('reviewed_user_id')->constrained('users'); // Seller
    $table->tinyInteger('rating'); // 1-5
    $table->text('comment')->nullable();
    $table->enum('visibility', ['public', 'private'])->default('public');
    $table->integer('helpful_count')->default(0);
    $table->boolean('flagged')->default(false);
    $table->text('flag_reason')->nullable();
    $table->timestamps();
    
    // Prevent duplicate reviews
    $table->unique(['order_id', 'reviewer_id']);
});
```

#### 2. Update Orders Migration
Add to existing orders table:
```php
$table->timestamp('review_invite_sent_at')->nullable();
$table->boolean('is_reviewed')->default(false);
```

#### 3. Update Services Table
```php
$table->decimal('average_rating', 3, 2)->default(0);
$table->integer('review_count')->default(0);
```

#### 4. Update Users Table
```php
$table->decimal('seller_average_rating', 3, 2)->default(0);
$table->integer('seller_review_count')->default(0);
```

#### 5. Create ServiceReview Model
**File**: `app/Domains/Reviews/Models/ServiceReview.php`

```php
<?php
namespace App\Domains\Reviews\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Orders\Models\Order;
use App\Domains\Listings\Models\Service;
use App\Domains\Users\Models\User;

class ServiceReview extends Model
{
    protected $fillable = [
        'order_id',
        'service_id',
        'reviewer_id',
        'reviewed_user_id',
        'rating',
        'comment',
        'visibility',
    ];

    protected $casts = [
        'rating' => 'integer',
        'flagged' => 'boolean',
    ];

    // RELATIONSHIPS
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewedSeller()
    {
        return $this->belongsTo(User::class, 'reviewed_user_id');
    }

    // ACCESSORS / MUTATORS
    public function getRatingStarsAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    // SCOPES
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('reviewed_user_id', $sellerId);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }
}
```

#### 6. Update Order Model
Add to `app/Domains/Orders/Models/Order.php`:

```php
public function serviceReview()
{
    return $this->hasOne(\App\Domains\Reviews\Models\ServiceReview::class);
}
```

#### 7. Update Service Model
Add to `app/Domains/Listings/Models/Service.php`:

```php
public function reviews()
{
    return $this->hasMany(\App\Domains\Reviews\Models\ServiceReview::class);
}

public function publicReviews()
{
    return $this->reviews()->where('visibility', 'public');
}

public function getAverageRatingAttribute()
{
    return $this->reviews()->avg('rating') ?? 0;
}

public function getReviewCountAttribute()
{
    return $this->reviews()->count();
}
```

#### 8. Update User Model
Add to `app/Domains/Users\Models\User.php`:

```php
public function writtenReviews()
{
    return $this->hasMany(\App\Domains\Reviews\Models\ServiceReview::class, 'reviewer_id');
}

public function receivedReviews()
{
    return $this->hasMany(\App\Domains\Reviews\Models\ServiceReview::class, 'reviewed_user_id');
}

public function getSellerAverageRatingAttribute()
{
    return $this->receivedReviews()->avg('rating') ?? 0;
}

public function getSellerReviewCountAttribute()
{
    return $this->receivedReviews()->count();
}
```

---

### Priority 2: Complete Work Instance (Order Completion)

#### 1. Update WorkInstanceController.completeStep()
**File**: `app/Domains/Work/Http/Controllers/WorkInstanceController.php`

At the end of the method, add:

```php
// After setting workInstance to completed:
if ($allStepsCompleted) {
    $workInstance->status = 'completed';
    $workInstance->completed_at = now();
    $workInstance->save();
    
    // ✅ UPDATE ASSOCIATED ORDER
    $order = $workInstance->order;
    $order->status = 'completed'; // or use OrderStatus enum
    $order->is_reviewed = false; // Allow review now
    $order->save();
    
    // ✅ Send notification to buyer
    // $order->buyer->notify(new OrderCompletedNotification($order));
}
```

---

### Priority 3: Service Review Controller & Routes

#### 1. Create ServiceReviewController
**File**: `app/Domains/Reviews/Http/Controllers/ServiceReviewController.php`

```php
<?php
namespace App\Domains\Reviews\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Orders\Models\Order;
use App\Domains\Reviews\Models\ServiceReview;
use Illuminate\Http\Request;

class ServiceReviewController extends Controller
{
    // Show review form (if eligible)
    public function create(Order $order)
    {
        $this->authorize('create', [ServiceReview::class, $order]);
        
        return view('reviews.create', [
            'order' => $order,
            'service' => $order->service,
            'seller' => $order->seller,
        ]);
    }

    // Store new review
    public function store(Order $order, Request $request)
    {
        $this->authorize('create', [ServiceReview::class, $order]);
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = ServiceReview::create([
            'order_id' => $order->id,
            'service_id' => $order->service_id,
            'reviewer_id' => auth()->id(),
            'reviewed_user_id' => $order->seller_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        // Update order flag
        $order->update(['is_reviewed' => true]);

        // TODO: Dispatch ReviewCreated event to update ratings

        return redirect()->route('orders.show', $order)
            ->with('success', 'Thank you for your review!');
    }

    // Show single review
    public function show(ServiceReview $review)
    {
        // Check visibility
        if ($review->visibility === 'private' && 
            auth()->id() !== $review->reviewer_id && 
            auth()->id() !== $review->reviewed_user_id) {
            abort(403);
        }

        return view('reviews.show', compact('review'));
    }

    // Edit review form (buyer only)
    public function edit(ServiceReview $review)
    {
        $this->authorize('update', $review);
        return view('reviews.edit', compact('review'));
    }

    // Update review (buyer only)
    public function update(ServiceReview $review, Request $request)
    {
        $this->authorize('update', $review);
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return redirect()->route('reviews.show', $review)
            ->with('success', 'Review updated!');
    }

    // Delete review (buyer only)
    public function destroy(ServiceReview $review)
    {
        $this->authorize('delete', $review);
        
        $order = $review->order;
        $review->delete();
        $order->update(['is_reviewed' => false]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Review deleted!');
    }

    // List reviews for a service
    public function listByService(Service $service)
    {
        $reviews = $service->publicReviews()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reviews.by-service', [
            'service' => $service,
            'reviews' => $reviews,
            'averageRating' => $service->average_rating,
            'reviewCount' => $service->review_count,
        ]);
    }

    // List reviews received by seller
    public function listBySeller(User $seller)
    {
        $reviews = $seller->receivedReviews()
            ->where('visibility', 'public')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reviews.by-seller', [
            'seller' => $seller,
            'reviews' => $reviews,
            'averageRating' => $seller->seller_average_rating,
            'reviewCount' => $seller->seller_review_count,
        ]);
    }
}
```

#### 2. Create ServiceReviewPolicy
**File**: `app/Domains/Reviews/Policies/ServiceReviewPolicy.php`

```php
<?php
namespace App\Domains\Reviews\Policies;

use App\Domains\Users\Models\User;
use App\Domains\Reviews\Models\ServiceReview;
use App\Domains\Orders\Models\Order;

class ServiceReviewPolicy
{
    // Can create review (buyer of completed order, not already reviewed)
    public function create(User $user, ServiceReview $review, Order $order)
    {
        return $order->buyer_id === $user->id 
            && $order->status === 'completed'
            && !$order->serviceReview; // Not already reviewed
    }

    // Can view review
    public function view(User $user, ServiceReview $review)
    {
        // Public reviews anyone can view
        if ($review->visibility === 'public') {
            return true;
        }
        
        // Private: only reviewer and reviewed can see
        return $user->id === $review->reviewer_id 
            || $user->id === $review->reviewed_user_id;
    }

    // Can update (buyer only, within 30 days)
    public function update(User $user, ServiceReview $review)
    {
        return $user->id === $review->reviewer_id 
            && $review->created_at->addDays(30)->isFuture();
    }

    // Can delete (buyer only, within 7 days)
    public function delete(User $user, ServiceReview $review)
    {
        return $user->id === $review->reviewer_id 
            && $review->created_at->addDays(7)->isFuture();
    }
}
```

#### 3. Add Review Routes
**File**: `routes/web.php` - Add to orders group:

```php
Route::middleware(['auth'])->prefix('orders')->name('orders.')->group(function () {
    // ... existing order routes ...
    
    // Review routes (nested under order)
    Route::prefix('{order}/review')->name('review.')->group(function () {
        Route::get('create', [ServiceReviewController::class, 'create'])->name('create');
        Route::post('/', [ServiceReviewController::class, 'store'])->name('store');
        Route::get('/{review}', [ServiceReviewController::class, 'show'])->name('show');
        Route::get('/{review}/edit', [ServiceReviewController::class, 'edit'])->name('edit');
        Route::put('/{review}', [ServiceReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [ServiceReviewController::class, 'destroy'])->name('destroy');
    });
});

// Service reviews (public routes)
Route::get('/services/{service}/reviews', [ServiceReviewController::class, 'listByService'])->name('services.reviews');
Route::get('/users/{user}/reviews-received', [ServiceReviewController::class, 'listBySeller'])->name('users.reviews');
```

---

### Priority 4: Events & Listeners (Rating Updates)

#### 1. Create ReviewCreated Event
**File**: `app/Domains/Reviews/Events/ReviewCreated.php`

```php
<?php
namespace App\Domains\Reviews\Events;

use App\Domains\Reviews\Models\ServiceReview;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public ServiceReview $review)
    {
    }
}
```

#### 2. Create UpdateServiceRating Listener
**File**: `app/Domains/Reviews/Listeners/UpdateServiceRating.php`

```php
<?php
namespace App\Domains\Reviews\Listeners;

use App\Domains\Reviews\Events\ReviewCreated;

class UpdateServiceRating
{
    public function handle(ReviewCreated $event)
    {
        $service = $event->review->service;
        
        $service->update([
            'average_rating' => $service->reviews()->avg('rating'),
            'review_count' => $service->reviews()->count(),
        ]);
    }
}
```

#### 3. Create UpdateUserRating Listener
**File**: `app/Domains/Reviews/Listeners/UpdateUserRating.php`

```php
<?php
namespace App\Domains\Reviews\Listeners;

use App\Domains\Reviews\Events\ReviewCreated;

class UpdateUserRating
{
    public function handle(ReviewCreated $event)
    {
        $seller = $event->review->reviewedSeller;
        
        $seller->update([
            'seller_average_rating' => $seller->receivedReviews()->avg('rating'),
            'seller_review_count' => $seller->receivedReviews()->count(),
        ]);
    }
}
```

#### 4. Register Listeners in EventServiceProvider
**File**: `app/Providers/EventServiceProvider.php`

```php
protected $listen = [
    // ... existing events ...
    \App\Domains\Reviews\Events\ReviewCreated::class => [
        \App\Domains\Reviews\Listeners\UpdateServiceRating::class,
        \App\Domains\Reviews\Listeners\UpdateUserRating::class,
    ],
];
```

---

### Priority 5: Views (Frontend)

#### 1. Review Creation Form
**File**: `resources/views/reviews/create.blade.php`

```php
<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6">Leave a Review</h1>
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-2">{{ $service->title }}</h2>
            <p class="text-gray-600">Service by {{ $service->creator->name }}</p>
        </div>

        <form action="{{ route('orders.review.store', $order) }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf

            <!-- Rating -->
            <div class="mb-6">
                <label class="block text-lg font-semibold mb-3">Rating</label>
                <div class="flex gap-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <input type="radio" name="rating" value="{{ $i }}" id="rating-{{ $i }}" class="hidden peer" required>
                        <label for="rating-{{ $i }}" class="cursor-pointer text-4xl peer-checked:text-yellow-400 text-gray-300 transition">
                            ★
                        </label>
                    @endfor
                </div>
                @error('rating')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Comment -->
            <div class="mb-6">
                <label for="comment" class="block text-lg font-semibold mb-2">Your Review</label>
                <textarea name="comment" id="comment" rows="5" class="w-full border rounded-lg p-3" placeholder="Share your experience..."></textarea>
                @error('comment')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Submit Review
                </button>
                <a href="{{ route('orders.show', $order) }}" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
```

#### 2. Review Display Component
**File**: `resources/views/components/service-review.blade.php`

```php
@props(['review'])

<div class="border rounded-lg p-4 bg-gray-50">
    <div class="flex justify-between items-start mb-2">
        <div>
            <h4 class="font-semibold text-gray-800">{{ $review->reviewer->name }}</h4>
            <p class="text-sm text-gray-600">{{ $review->created_at->format('M d, Y') }}</p>
        </div>
        <div class="text-yellow-400">
            {{ $review->rating_stars }}
        </div>
    </div>
    
    <p class="text-gray-700">{{ $review->comment }}</p>
    
    @if (auth()->id() === $review->reviewer_id)
        <div class="mt-3 flex gap-2">
            <a href="{{ route('reviews.edit', $review) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
            <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline text-sm" onclick="confirm('Delete review?')">
                    Delete
                </button>
            </form>
        </div>
    @endif
</div>
```

---

### Priority 6: Route Refactoring (Phase 1)

Update `routes/web.php`:

```php
// Phase 1: Add new routes alongside old ones
Route::middleware(['auth'])->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    
    // New work routes
    Route::get('/{order}/work', [WorkInstanceController::class, 'show'])->name('work.show');
    Route::post('/{order}/work/steps/{workInstanceStep}/start', [WorkInstanceController::class, 'startStep'])->name('work.steps.start');
    Route::post('/{order}/work/steps/{workInstanceStep}/complete', [WorkInstanceController::class, 'completeStep'])->name('work.steps.complete');
    
    // Review routes
    Route::get('/{order}/review/create', [ServiceReviewController::class, 'create'])->name('review.create');
    Route::post('/{order}/review', [ServiceReviewController::class, 'store'])->name('review.store');
});

// OLD routes (still working for now - will be deprecated)
Route::middleware(['auth'])->prefix('work-instances')->name('work-instances.')->group(function () {
    Route::get('/{workInstance}', [WorkInstanceController::class, 'show'])->name('show');
    Route::post('/{workInstance}/steps/{workInstanceStep}/start', [WorkInstanceController::class, 'startStep'])->name('steps.start');
    Route::post('/{workInstance}/steps/{workInstanceStep}/complete', [WorkInstanceController::class, 'completeStep'])->name('steps.complete');
});
```

---

## 📋 Implementation Order

1. **Database** (Migrations & Models) - 30 min
2. **Review Controller & Policy** - 45 min
3. **Events & Listeners** - 20 min
4. **Update WorkInstanceController** - 15 min
5. **Routes** - 15 min
6. **Views** - 60 min
7. **Testing** - 30 min

**Total Estimated Time**: 3.5 hours

---

## 🧪 Testing Checklist

```php
// Test 1: Review creation prevents duplicates
$order->update(['status' => 'completed']);
$review1 = ServiceReview::create([...]);
$review2 = ServiceReview::create([...]); // Should fail (unique constraint)

// Test 2: Ratings update on new review
$initialRating = $service->average_rating;
ServiceReview::create(['service_id' => $service->id, 'rating' => 5]);
$finalRating = $service->fresh()->average_rating;
Assert $finalRating > $initialRating;

// Test 3: Order completion triggers status update
$workInstance->update(['status' => 'completed']);
Assert $order->fresh()->status === 'completed';

// Test 4: Authorization - buyer can review completed order
Sanctum::actingAs($buyer);
$this->post(route('orders.review.store', $order))->assertSuccessful();

// Test 5: Authorization - seller cannot review
Sanctum::actingAs($seller);
$this->post(route('orders.review.store', $order))->assertForbidden();
```

---

## 🚀 Ready for Implementation!

All models, controllers, views, and logic are designed. Developer can proceed with building according to this specification.

**Status**: ✅ Architecture Complete, Ready to Code
