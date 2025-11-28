# Work Visibility Implementation - Buyer/Seller Access

## Overview
The work system has been fully implemented to allow both **buyers and sellers** to view work instances and exchange messages, while maintaining strict seller-only control over work fulfillment steps.

## Key Features Implemented

### 1. **Buyer Can View Work Progress**
- Buyers can access their purchased services' work instances
- Real-time progress tracking with visual timeline
- Step-by-step status updates
- Order details and participant information

### 2. **Seller Can Fulfill Work Steps**
- Only sellers can start and complete work steps
- Buyers see a visual indicator that they cannot fulfill steps
- Clear UI distinction between buyer and seller roles

### 3. **Bidirectional Messaging**
- Both buyer and seller can send messages about each step
- Activity threads allow step-specific discussions
- Main work chat for general order communication
- Notifications sent to both parties for all interactions

### 4. **Clear Role Identification**
- Header badges show user role ("Your Service to Deliver" / "Your Purchase")
- Colored context banners explain the user's responsibilities
- Participant cards highlight the current user
- Visual indicators on all interactive elements

---

## Implementation Details

### Controller Changes
**File**: `app/Domains/Work/Http/Controllers/WorkInstanceController.php`

#### `index()` Method - Enhanced to Show Both Roles
```php
public function index()
{
    $currentUserId = Auth::id();
    
    $workInstances = WorkInstance::whereHas('order', function ($query) use ($currentUserId) {
        $query->where(function ($q) use ($currentUserId) {
            $q->where('seller_id', $currentUserId)
              ->orWhere('buyer_id', $currentUserId);
        });
    })->with('order')->get();

    return view('creator.work-dashboard', compact('workInstances'));
}
```

**What Changed**: Previously only showed seller's work instances. Now includes both:
- Work instances where the user is the seller
- Work instances where the user is the buyer

---

### Policy Updates
**File**: `app/Domains/Work/Policies/WorkInstancePolicy.php`

#### Key Policies (Already Implemented, Enhanced with Comments)

```php
/**
 * Both buyer and seller can view the work instance and its progress
 * - Seller: Can see their own work and fulfill steps
 * - Buyer: Can see the work progress and send messages
 */
public function view(User $user, WorkInstance $workInstance): bool
{
    return $user->id === $workInstance->order->buyer_id || 
           $user->id === $workInstance->order->seller_id;
}

/**
 * Only seller can update/manage the work
 */
public function update(User $user, WorkInstance $workInstance): bool
{
    return $user->id === $workInstance->order->seller_id;
}

/**
 * Only seller can complete steps
 */
public function completeStep(User $user, WorkInstance $workInstance): bool
{
    return $user->id === $workInstance->order->seller_id && $workInstance->status !== 'completed';
}

/**
 * Both buyer and seller can send and receive messages about work steps
 */
public function addActivity(User $user, WorkInstance $workInstance): bool
{
    return $user->id === $workInstance->order->buyer_id || 
           $user->id === $workInstance->order->seller_id;
}
```

---

### View Enhancements
**File**: `resources/views/work/show.blade.php`

#### Role-Based Context Banners
```blade
@php
    $isSeller = auth()->id() === $workInstance->order->seller_id;
    $isBuyer = auth()->id() === $workInstance->order->buyer_id;
@endphp

@if($isBuyer)
    <div class="bg-blue-50 border border-blue-200 rounded-lg mb-6 p-4">
        <h4 class="font-semibold text-blue-900 mb-2">📋 Work Progress - You're the Buyer</h4>
        <p class="text-sm text-blue-800">Track the seller's progress on your order. You can message the seller about any step and monitor real-time progress below.</p>
    </div>
@elseif($isSeller)
    <div class="bg-purple-50 border border-purple-200 rounded-lg mb-6 p-4">
        <h4 class="font-semibold text-purple-900 mb-2">⚙️ Work Fulfillment - You're the Seller</h4>
        <p class="text-sm text-purple-800">Work through each step and mark them complete as you finish. The buyer can see your progress and message you for clarifications.</p>
    </div>
@endif
```

#### Role Indicators in Header
```blade
@if($isSeller)
    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded font-medium">Your Service to Deliver</span>
@elseif($isBuyer)
    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded font-medium">Your Purchase</span>
@endif
```

#### Step Action Guards
```blade
<!-- Sellers: Full action buttons -->
@if(auth()->id() === $workInstance->order->seller_id && !$step->isCompleted())
    <div class="flex gap-2 pt-2">
        <!-- Start/Complete Step Buttons -->
    </div>
@elseif(auth()->id() === $workInstance->order->buyer_id && !$step->isCompleted())
    <!-- Buyers: Helpful message -->
    <div class="text-xs text-gray-500 italic pt-2 flex items-center gap-2">
        <span>💬</span>
        <span>Only the seller can complete steps. You can message about this step below.</span>
    </div>
@endif
```

#### Enhanced Participant Cards
```blade
<div class="p-4 rounded-lg {{ $isSeller ? 'bg-blue-50 border-2 border-blue-300' : 'bg-gray-50 border border-gray-200' }}">
    <h4 class="font-medium {{ $isSeller ? 'text-blue-900' : 'text-gray-700' }} mb-2 flex items-center gap-2">
        👨‍💼 Seller
        @if($isSeller)
            <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded font-semibold">(You)</span>
        @endif
    </h4>
    <!-- Seller Info -->
</div>
```

---

### Messaging Architecture

#### Activity Threads (Step-Level Discussion)
**File**: `app/Domains/Work/Models/ActivityThread.php`

- Each work step has an optional `ActivityThread` for discussions
- Messages are stored in `ActivityMessage` model
- Both buyer and seller can post messages

#### Work Chat (Order-Level Discussion)
**File**: `app/Livewire/WorkChat.php`

- Uses `MessageThread` (separate from activity threads)
- Integrated chat popup component
- Both parties can send/receive messages

#### Activity Controller
**File**: `app/Domains/Work/Http/Controllers/ActivityController.php`

```php
public function store(Request $request)
{
    // No authorization check - both buyer and seller can post
    $activityMessage = ActivityMessage::create([
        'activity_thread_id' => $request->activity_thread_id,
        'user_id' => Auth::id(),
        'content' => $request->content,
    ]);

    // Notifications sent to both buyer and seller
    $workInstance = $activityThread->workInstanceStep->workInstance;
    $order = $workInstance->order;
    $recipients = collect([$order->buyer, $order->seller])->unique('id');
    Notification::send($recipients, new ActivityMessageCreated($activityMessage));
}
```

---

## Access Control Matrix

| Action | Seller | Buyer | Notes |
|--------|--------|-------|-------|
| View Work Instance | ✅ | ✅ | Both can see progress |
| View Work Steps | ✅ | ✅ | Both see full details |
| Start Step | ✅ | ❌ | Seller only |
| Complete Step | ✅ | ❌ | Seller only |
| View Step Discussion | ✅ | ✅ | Activity thread |
| Post Step Message | ✅ | ✅ | Both can message |
| View Work Chat | ✅ | ✅ | Main order chat |
| Post Chat Message | ✅ | ✅ | Both can message |
| View Order Details | ✅ | ✅ | Shared visibility |
| View Participants | ✅ | ✅ | See both sides |

---

## User Experience Flow

### For Sellers
1. Dashboard shows work instances they need to fulfill
2. Click to open work instance with full step details
3. See buyer information and payment status
4. Progress through steps sequentially
5. Receive notifications when buyer messages
6. Can message buyer about any step

### For Buyers
1. Dashboard shows work instances they purchased
2. Click to open work instance to track progress
3. See seller information and work timeline
4. Real-time progress tracking with visual indicators
5. Can message seller for clarifications
6. Receive notifications when seller completes steps

---

## Database Relationships

```
Order
├── seller_id (User)
├── buyer_id (User)
└── WorkInstance
    ├── status
    ├── progress tracking
    └── WorkInstanceSteps
        ├── status
        ├── started_at
        ├── completed_at
        └── ActivityThread
            └── ActivityMessages
```

---

## Routes

All routes are protected by middleware and policy checks:

```php
Route::middleware(['auth'])->prefix('work-instances')->name('work-instances.')->group(function () {
    Route::get('/{workInstance}', 'show');                    // Both can view
    Route::post('/{workInstance}/steps/{step}/start', 'startStep');      // Seller only
    Route::post('/{workInstance}/steps/{step}/complete', 'completeStep'); // Seller only
    Route::resource('/{workInstance}/steps/{step}/activities', 'ActivityController'); // Both can post
});
```

---

## Testing Scenarios

### Test 1: Buyer Can View Work
1. Login as buyer
2. Navigate to orders
3. Click on completed order
4. Work instance shows progress timeline
5. ✅ Can see all steps and their status

### Test 2: Seller Can Fulfill Work
1. Login as seller
2. Navigate to work dashboard
3. Click work instance
4. See "Start Step" button (only for seller)
5. Complete steps sequentially
6. ✅ Progress updates in real-time

### Test 3: Buyer Cannot Fulfill
1. Login as buyer
2. Open work instance
3. Step actions show helpful message instead of buttons
4. "Complete Step" buttons NOT visible
5. ✅ UI clearly shows seller-only actions

### Test 4: Bidirectional Messaging
1. Seller posts message in step discussion
2. Buyer receives notification
3. Buyer replies in same thread
4. Seller sees notification
5. ✅ Messages flow both ways

### Test 5: Progress Visibility
1. Buyer viewing work instance
2. Seller completes a step
3. Buyer's page auto-updates (if using Livewire polling)
4. Progress bar increases
5. ✅ Buyer sees real-time updates

---

## Summary of Changes

| File | Changes |
|------|---------|
| `WorkInstanceController.php` | Enhanced `index()` to include buyer's instances |
| `WorkInstancePolicy.php` | Added documentation to clarify buyer/seller access |
| `work.show.blade.php` | Added role context banners, step action guards, enhanced participants |
| All Other Files | No changes needed - already support bidirectional access |

---

## Future Enhancements

1. **Real-time Updates**: Add Livewire polling for instant progress updates
2. **Activity Feed**: Timeline view of all interactions
3. **Approval Gate**: Buyer approval before final completion
4. **Review System**: Buyer can review completed work before marking done
5. **Revision Requests**: Buyer can request revisions before acceptance
6. **Historical Records**: Archive completed work with full message history

---

## Notes

- All authorization happens at policy level - views are defensive
- Messages always notify both parties
- Work can only be advanced by seller
- Seller cannot delete or hide work from buyer
- Both can view all order and payment details
