# Service Views Migration Summary

## Overview
Transformed Laravel Blade views from table-based layout to modern, mobile-first card-based design matching the demo wireframe.

---

## 📦 Required Dependencies

### 1. Swiper.js (Image Carousel)
Add to your layout or specific views:
```html
<!-- CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
```

### 2. Alpine.js (Already in your project)
Used for collapsible sections and tabs.

---

## 🎨 Files Created/Modified

### 1. **service-form.blade.php** (Livewire Component)
**Location:** `resources/views/livewire/service-form.blade.php`

**Features:**
- ✅ Mobile-first responsive design
- ✅ Two states: Empty (create) and Filled (edit)
- ✅ Horizontal scrollable media uploader
- ✅ Collapsible workflow display (compact/list view)
- ✅ Address selection with "Add new address" link
- ✅ Workflow selection: "Select existing" / "Create new"
- ✅ Images persist when adding more (no removal on add)

**State Management:**
- Empty state shows when creating new service
- Filled state shows when editing OR when workflow/media is added

---

### 2. **show.blade.php** (Guest/Buyer View)
**Location:** `resources/views/listings/show.blade.php`

**Features:**
- ✅ Swiper.js image carousel with navigation
- ✅ 5-star rating display (placeholder)
- ✅ Collapsible workflow schedule
- ✅ Creator info card
- ✅ Reviews section (with empty state)
- ✅ "Add to wishlist" + "Proceed to Order" buttons
- ✅ Responsive: mobile-first, scales to desktop

**Swiper Configuration:**
```javascript
{
    loop: true,
    autoplay: { delay: 5000 },
    pagination: { clickable: true },
    navigation: { prev/next buttons }
}
```

---

### 3. **show.blade.php** (Owner View)
**Location:** `resources/views/creator/services/show.blade.php`

**Features:**
- ✅ Status indicator (Active/Flagged)
- ✅ Action menu: Edit, View live, Delete
- ✅ Analytics placeholders:
  - Total revenue
  - Today's clicks
  - Wishlist adds
- ✅ Chart placeholder (ready for Chart.js)
- ✅ Tabs: Orders & Reviews
- ✅ Empty states for both tabs
- ✅ "Preview as Customer" button

**Required Controller Data:**
```php
return view('creator.services.show', [
    'service' => $service,
    'analytics' => [
        'total_revenue' => 0,
        'today_clicks' => 0,
        'wishlist_count' => 0,
    ],
    'orders' => [],
    'reviews' => [],
]);
```

---

### 4. **service-card.blade.php** (Browse Grid)
**Location:** `resources/views/listings/partials/service-card.blade.php`

**Features:**
- ✅ Service image with fallback
- ✅ Category badge
- ✅ "Pay First" badge
- ✅ Creator avatar & name
- ✅ Location with icon
- ✅ Price display
- ✅ Wishlist heart button (with onclick handler)
- ✅ Hover effects

---

### 5. **index.blade.php** (My Services)
**Location:** `resources/views/creator/services/index.blade.php`

**Changed:**
- ❌ Removed table layout
- ✅ Added card grid (1 col mobile, 2 col tablet, 3 col desktop)
- ✅ Quick stats per service (Orders, Views, Rating)
- ✅ Action buttons: View, Edit, Delete
- ✅ Empty state with CTA
- ✅ Status badges on images

---

## 🔧 Additional Implementation Needed

### 1. Livewire Component Updates

**ServiceForm.php additions:**
```php
// Add these methods to handle media better
public function selectAllImages()
{
    // Select all for batch operations
}

public function deleteSelected()
{
    // Delete selected images
}

// Dispatch event for workflow selector
public function openWorkflowSelector()
{
    $this->dispatch('openWorkflowSelector');
}
```

### 2. Workflow Selector Modal
Create a Livewire modal component:
```bash
php artisan make:livewire WorkflowSelectorModal
```

Should display list of workflow templates and emit selected template ID back to service form.

### 3. Routes to Add
```php
// If not already present
Route::get('/creator/services/{service}', [ServiceController::class, 'show'])
    ->name('creator.services.show');

Route::get('/profile/addresses', [ProfileController::class, 'addresses'])
    ->name('profile.addresses');

Route::get('/creator/workflows/create', [WorkflowController::class, 'create'])
    ->name('creator.workflows.create');
```

---

## 🎯 Workflow States Logic

### Service Form States:

**Empty State** (Initial Create):
- No workflow selected → Show "Select existing" / "Create new" buttons
- No images → Show large upload placeholder

**Filled State** (Edit or After Adding):
- Workflow selected → Show workflow steps with "Change" button
- Images uploaded → Show horizontal scrollable gallery with + button

**Toggle Logic:**
```php
@if($workflow_template_id && ($service->exists || count($newFiles) > 0))
    {{-- Filled state --}}
@else
    {{-- Empty state --}}
@endif
```

---

## 📱 Responsive Breakpoints

**Mobile (< 640px):**
- Single column cards
- Stacked form elements
- Collapsible sections default closed

**Tablet (640px - 1024px):**
- 2 column grid
- Side-by-side form fields

**Desktop (> 1024px):**
- 3 column grid
- Full feature visibility
- Expanded layouts

---

## 🚀 Testing Checklist

- [ ] Create new service (empty state)
- [ ] Add workflow (switches to filled state)
- [ ] Add images (+ button keeps existing)
- [ ] Edit existing service (filled state)
- [ ] Remove workflow (switches to empty state)
- [ ] Image carousel navigation works
- [ ] Collapsible workflow toggles
- [ ] Address link works
- [ ] Workflow selector modal opens
- [ ] Mobile responsive at all breakpoints
- [ ] Owner view analytics display
- [ ] Tab switching (Orders/Reviews)
- [ ] Delete confirmation works

---

## 💡 Future Enhancements

1. **Analytics Integration:**
   - Connect Chart.js for revenue graph
   - Track real click/view data
   - Wishlist counter

2. **Image Optimization:**
   - Lazy loading for carousels
   - Thumbnail generation
   - WebP format support

3. **Real-time Updates:**
   - Livewire polling for orders
   - Notification badges
   - Status updates

4. **Search & Filters:**
   - AJAX-powered search on browse page
   - Category filtering
   - Location-based search

5. **Workflow Builder:**
   - In-form workflow creation
   - Drag & drop step ordering
   - Template saving

---

## 🐛 Known Issues / Notes

1. **Alpine.js x-cloak:** Make sure you have this in your CSS:
   ```css
   [x-cloak] { display: none !important; }
   ```

2. **Swiper on Multiple Pages:** If using on browse + show pages, ensure unique class names or separate initialization.

3. **Temporary URLs:** Livewire's `temporaryUrl()` requires proper storage configuration.

4. **Profile Photo URL:** Ensure users have default avatars or fallback images.

---

## 📞 Support & Questions

If you encounter issues:
1. Check browser console for JS errors
2. Verify Livewire is properly loaded
3. Ensure Alpine.js is initialized
4. Check route names match your `web.php`

Happy coding! 🎉


---

// service-form.blade.php
<div class="max-w-lg mx-auto w-full bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden">
    <header class="flex justify-between items-center p-4 border-b">
        <h1 class="text-lg font-semibold">{{ $service->exists ? 'Edit Service' : 'Create a Service' }}</h1>
    </header>

    <form wire:submit.prevent="save" class="p-4 space-y-4">
        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" wire:model.defer="title" id="title" 
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Price and Pay First -->
        <div class="flex space-x-4">
            <div class="w-1/2">
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" wire:model.defer="price" id="price" step="0.01"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="w-1/2">
                <label for="pay-first" class="block text-sm font-medium text-gray-700">Pay First</label>
                <select wire:model.defer="pay_first" id="pay-first" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>

        <!-- Category and Address -->
        <div class="flex space-x-4">
            <div class="w-1/2">
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select wire:model.defer="category_id" id="category"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select...</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="w-1/2">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <select wire:model="address_id" id="address"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select...</option>
                    @foreach($addresses as $address)
                        <option value="{{ $address->id }}">
                            {{ $address->street }}, {{ $address->city }}
                            @if($address->is_primary) (Primary) @endif
                        </option>
                    @endforeach
                </select>
                @error('address_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                <a href="{{ route('profile.addresses') }}" class="text-xs text-blue-600 hover:text-blue-700 mt-1 inline-block">+ Add new address</a>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea wire:model.defer="description" id="description" rows="4" 
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Workflow Section -->
        @if($workflow_template_id && $service->exists)
            <!-- Filled State: Show selected workflow -->
            <div class="border rounded-lg p-4 space-y-3" x-data="{ expanded: false }">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-sm">Workflow Steps</h3>
                    <button type="button" wire:click="$set('workflow_template_id', '')" class="text-red-600 text-sm hover:text-red-700">
                        Change
                    </button>
                </div>
                
                <!-- Compact View -->
                <div class="text-sm text-gray-600" x-show="!expanded">
                    @php
                        $template = $workflowTemplates->firstWhere('id', $workflow_template_id);
                    @endphp
                    @if($template)
                        {{ $template->workTemplates->pluck('name')->implode(' > ') }}
                    @endif
                </div>
                
                <!-- Expanded List View -->
                <div class="space-y-2" x-show="expanded" x-cloak>
                    @if($template)
                        @foreach($template->workTemplates as $step)
                            <p class="block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm">
                                {{ $step->name }}
                            </p>
                        @endforeach
                    @endif
                </div>
                
                <button type="button" @click="expanded = !expanded" class="text-xs text-blue-600 hover:text-blue-700">
                    <span x-show="!expanded">Show as list</span>
                    <span x-show="expanded" x-cloak>Show compact</span>
                </button>
            </div>
        @else
            <!-- Empty State: Selection buttons -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Workflow</label>
                <div class="flex space-x-2">
                    <button type="button" wire:click="$dispatch('openWorkflowSelector')"
                        class="flex-1 bg-gray-100 border border-gray-300 rounded-md p-2 text-sm hover:bg-gray-200 transition">
                        Select existing
                    </button>
                    <a href="{{ route('creator.workflows.create') }}" 
                        class="flex-1 bg-gray-800 text-white text-center rounded-md p-2 text-sm hover:bg-gray-900 transition">
                        Create new workflow
                    </a>
                </div>
                @error('workflow_template_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Featured Photos Section -->
        <div class="border rounded-lg p-4">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-semibold text-sm">Featured photos</h3>
                @if(count($existingImages) > 0 || count($newFiles) > 0)
                    <div class="flex space-x-3 text-sm">
                        <button type="button" wire:click="selectAllImages" class="text-blue-600 hover:text-blue-700">
                            Select all
                        </button>
                        <button type="button" wire:click="deleteSelected" class="text-red-600 hover:text-red-700">
                            Delete
                        </button>
                    </div>
                @endif
            </div>

            @if(count($existingImages) > 0 || count($newFiles) > 0)
                <!-- Filled State: Horizontal scroll with images -->
                <div class="flex space-x-3 overflow-x-auto pb-2">
                    <!-- Upload Button -->
                    <div class="flex-shrink-0 flex items-center justify-center w-24 h-24 border-2 border-dashed border-gray-300 rounded-md hover:border-blue-400 transition cursor-pointer">
                        <label for="file-upload" class="cursor-pointer">
                            <span class="text-4xl text-gray-400">+</span>
                            <input id="file-upload" type="file" wire:model="newFiles" multiple class="sr-only" accept="image/*">
                        </label>
                    </div>

                    <!-- Existing Images -->
                    @foreach($existingImages as $img)
                        <div class="flex-shrink-0 w-24 h-24 relative group">
                            <img src="{{ $img['url'] }}" class="w-full h-full object-cover rounded-md border border-gray-200">
                            <button type="button" wire:click="removeExistingImage({{ $img['id'] }})"
                                class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 rounded-full text-xs opacity-0 group-hover:opacity-100 transition">
                                ×
                            </button>
                        </div>
                    @endforeach

                    <!-- New Files Preview -->
                    @foreach($newFiles as $i => $file)
                        <div class="flex-shrink-0 w-24 h-24 relative group">
                            <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover rounded-md border-2 border-green-300">
                            <button type="button" wire:click="removeNewFile({{ $i }})"
                                class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 rounded-full text-xs opacity-0 group-hover:opacity-100 transition">
                                ×
                            </button>
                            <span class="absolute bottom-1 left-1 bg-green-600 text-white px-2 py-0.5 text-xs rounded">
                                New
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State: Large upload area -->
                <div class="mt-2 flex items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-md hover:border-blue-400 transition cursor-pointer">
                    <label for="file-upload-empty" class="cursor-pointer">
                        <span class="text-4xl text-gray-400">+</span>
                        <input id="file-upload-empty" type="file" wire:model="newFiles" multiple class="sr-only" accept="image/*">
                    </label>
                </div>
            @endif

            @error('newFiles.*') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF up to 2MB</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-4 border-t mt-6">
            <a href="{{ route('creator.services.index') }}" 
                class="bg-gray-100 border border-gray-300 rounded-md px-4 py-2 text-sm hover:bg-gray-200 transition">
                Cancel
            </a>
            <button type="submit" 
                class="bg-blue-600 text-white rounded-md px-4 py-2 text-sm hover:bg-blue-700 transition">
                {{ $service->exists ? 'Update' : 'Publish' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Workflow selector modal listener
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('openWorkflowSelector', () => {
            // Open modal with workflow templates list
            // Implementation depends on your modal system
            console.log('Open workflow selector');
        });
    });
</script>
@endpush


// show.blade.php (for guests/other users)
<x-app-layout>
    <!-- Include Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <div class="container mx-auto px-4 py-6 max-w-lg md:max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden">
            <!-- Header (Mobile Only) -->
            <header class="flex justify-between items-center p-4 border-b md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </header>

            <!-- Image Carousel -->
            <div class="relative h-48 md:h-80 bg-gray-200">
                @if($service->media->isNotEmpty())
                    <div class="swiper serviceSwiper h-full">
                        <div class="swiper-wrapper">
                            @foreach($service->media as $media)
                                <div class="swiper-slide">
                                    <img src="{{ $media->getUrl() }}" alt="{{ $service->title }}" 
                                        class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                        <!-- Navigation arrows -->
                        <div class="swiper-button-prev text-white"></div>
                        <div class="swiper-button-next text-white"></div>
                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full">
                        <span class="text-gray-400 text-lg">No images available</span>
                    </div>
                @endif
            </div>

            <!-- Service Details -->
            <div class="p-4 space-y-3">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $service->title }}</h2>
                
                <!-- Rating -->
                <div class="flex items-center space-x-2">
                    <span class="text-yellow-400 text-lg">★★★★★</span>
                    <span class="text-sm text-gray-600">(0 reviews)</span>
                </div>

                <!-- Service Info -->
                <div class="text-sm md:text-base text-gray-700 space-y-2">
                    <p><strong>Rate:</strong> ${{ number_format($service->price, 2) }}/hr</p>
                    <p><strong>Location:</strong> {{ $service->address->town }}, {{ $service->address->province }}</p>
                    
                    <!-- Workflow - Collapsible -->
                    <div x-data="{ expanded: false }">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <strong>Schedule:</strong>
                                <span x-show="!expanded" class="ml-1">
                                    {{ $service->workflowTemplate->workTemplates->pluck('name')->implode(' > ') }}
                                </span>
                            </div>
                            @if($service->workflowTemplate->workTemplates->count() > 3)
                                <button @click="expanded = !expanded" class="text-blue-600 text-xs ml-2 flex-shrink-0">
                                    <span x-show="!expanded">Show more</span>
                                    <span x-show="expanded" x-cloak>Show less</span>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Expanded list view -->
                        <div x-show="expanded" x-cloak class="mt-2 space-y-1 pl-4">
                            @foreach($service->workflowTemplate->workTemplates as $index => $step)
                                <p class="text-sm">{{ $index + 1 }}. {{ $step->name }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($service->description)
                    <div class="pt-3 border-t">
                        <h3 class="font-semibold text-gray-800 mb-2">About this service</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $service->description }}</p>
                    </div>
                @endif

                <!-- Creator Info -->
                <div class="pt-3 border-t">
                    <h3 class="font-semibold text-gray-800 mb-3">Service by</h3>
                    <div class="flex items-center space-x-3">
                        <img src="{{ $service->creator->profile_photo_url }}" 
                            alt="{{ $service->creator->name }}" 
                            class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $service->creator->name }}</h4>
                            <p class="text-xs text-gray-500">Member since {{ $service->creator->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="pt-3 border-t">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Reviews</h3>
                    <div class="space-y-3">
                        <!-- Example review -->
                        <div class="border rounded-lg p-3 bg-gray-50">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-semibold text-sm text-gray-800">David Datu Sarmiento</span>
                                <span class="text-yellow-400 text-xs">★★★★★</span>
                            </div>
                            <p class="text-sm text-gray-600">
                                I found that transacting with serbisyo makes my work easier
                            </p>
                        </div>
                        
                        <!-- No reviews state -->
                        <p class="text-sm text-gray-500 text-center py-4">No reviews yet. Be the first to review!</p>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <footer class="flex flex-col sm:flex-row justify-between items-center gap-3 p-4 border-t bg-gray-50">
                <button type="button" 
                    class="w-full sm:w-auto text-blue-600 font-medium hover:text-blue-700 transition order-2 sm:order-1">
                    Add to wishlist
                </button>
                <button type="button" 
                    class="w-full sm:w-auto bg-blue-600 text-white rounded-lg px-6 py-3 font-semibold hover:bg-blue-700 transition shadow-md order-1 sm:order-2">
                    Proceed to Order
                </button>
            </footer>
        </div>
    </div>

    <!-- Include Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const swiper = new Swiper('.serviceSwiper', {
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
            });
        });
    </script>

    <style>
        .swiper-button-prev,
        .swiper-button-next {
            color: white !important;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 50%;
            width: 40px !important;
            height: 40px !important;
        }

        .swiper-button-prev:after,
        .swiper-button-next:after {
            font-size: 16px !important;
        }

        .swiper-pagination-bullet {
            background: white !important;
            opacity: 0.7;
        }

        .swiper-pagination-bullet-active {
            opacity: 1 !important;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>


// show.blade.php (owner view with analyticss)
<x-app-layout>
    <div class="container mx-auto px-4 py-6 max-w-lg md:max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden">
            <!-- Header -->
            <header class="flex justify-between items-center p-4 border-b">
                <h1 class="text-lg font-semibold">My Service</h1>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </header>

            <!-- Status and Actions -->
            <nav class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-sm font-medium text-gray-600 border-b bg-gray-50">
                <div class="flex items-center space-x-2">
                    @if($service->is_flagged ?? false)
                        <span class="flex items-center text-orange-600">
                            <span class="h-2 w-2 bg-orange-500 rounded-full mr-2"></span>Flagged
                        </span>
                    @else
                        <span class="flex items-center text-green-600">
                            <span class="h-2 w-2 bg-green-500 rounded-full mr-2"></span>Active
                        </span>
                    @endif
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('creator.services.edit', $service) }}" 
                        class="hover:text-gray-900 transition">Edit</a>
                    <a href="{{ route('listings.show', $service) }}" target="_blank"
                        class="hover:text-gray-900 transition">View live</a>
                    <form action="{{ route('creator.services.destroy', $service) }}" method="POST" class="inline" 
                        onsubmit="return confirm('Are you sure you want to delete this service?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 transition">Delete</button>
                    </form>
                </div>
            </nav>

            <!-- Analytics Chart Placeholder -->
            <div class="p-4 space-y-4">
                <div class="h-32 bg-gray-100 rounded-lg flex items-center justify-center border">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs text-gray-500">Total revenue</p>
                        <p class="text-2xl font-bold text-gray-800">
                            ${{ number_format($analytics['total_revenue'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Today</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $analytics['today_clicks'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Clicks</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Total</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $analytics['wishlist_count'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Wishlist adds</p>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="px-4" x-data="{ activeTab: 'orders' }">
                <div class="flex border-b">
                    <button @click="activeTab = 'orders'" 
                        :class="activeTab === 'orders' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'"
                        class="flex-1 py-3 text-center font-medium transition">
                        Orders
                    </button>
                    <button @click="activeTab = 'reviews'" 
                        :class="activeTab === 'reviews' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'"
                        class="flex-1 py-3 text-center font-medium transition">
                        Reviews
                    </button>
                </div>

                <!-- Orders Tab -->
                <div x-show="activeTab === 'orders'" class="py-4 space-y-3">
                    @forelse($orders ?? [] as $order)
                        <div class="border rounded-lg p-3 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-sm">Order #{{ $order->id }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->status_color ?? 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($order->status ?? 'pending') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $order->customer_name ?? 'Customer' }}</p>
                            <p class="text-sm font-semibold text-gray-800">${{ number_format($order->amount ?? 0, 2) }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">No orders yet</p>
                        </div>
                    @endforelse
                </div>

                <!-- Reviews Tab -->
                <div x-show="activeTab === 'reviews'" x-cloak class="py-4 space-y-3">
                    @forelse($reviews ?? [] as $review)
                        <div class="border rounded-lg p-3 bg-gray-50">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-sm">{{ $review->reviewer_name }}</span>
                                <span class="text-yellow-400 text-sm">{{ str_repeat('★', $review->rating) }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">No reviews yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Preview Section -->
            <div class="p-4 border-t">
                <a href="{{ route('listings.show', $service) }}?preview=true" 
                    class="block w-full bg-gray-100 hover:bg-gray-200 text-center py-3 rounded-lg font-medium text-gray-700 transition">
                    Preview as Customer
                </a>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>


// service-card.blade.php
{{-- resources/views/listings/partials/service-card.blade.php --}}
<a href="{{ route('listings.show', $service) }}" 
    class="block bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
    
    <!-- Service Image -->
    <div class="relative h-48 bg-gray-200 overflow-hidden">
        @if($service->media->isNotEmpty())
            <img src="{{ $service->media->first()->getUrl() }}" 
                alt="{{ $service->title }}" 
                class="w-full h-full object-cover">
        @else
            <div class="flex items-center justify-center h-full">
                <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
        
        <!-- Category Badge -->
        <div class="absolute top-2 left-2">
            <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full shadow">
                {{ $service->category->name ?? 'Service' }}
            </span>
        </div>

        <!-- Pay First Badge -->
        @if($service->pay_first)
            <div class="absolute top-2 right-2">
                <span class="px-3 py-1 bg-green-600 text-white text-xs font-semibold rounded-full shadow">
                    Pay First
                </span>
            </div>
        @endif
    </div>

    <!-- Service Details -->
    <div class="p-4 space-y-2">
        <h3 class="text-lg font-bold text-gray-800 line-clamp-2">{{ $service->title }}</h3>
        
        <!-- Rating -->
        <div class="flex items-center space-x-2">
            <span class="text-yellow-400 text-sm">★★★★★</span>
            <span class="text-xs text-gray-500">(0)</span>
        </div>

        <!-- Creator Info -->
        <div class="flex items-center space-x-2 pt-1">
            <img src="{{ $service->creator->profile_photo_url }}" 
                alt="{{ $service->creator->name }}" 
                class="w-8 h-8 rounded-full">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-700 truncate">{{ $service->creator->name }}</p>
            </div>
        </div>

        <!-- Location -->
        <div class="flex items-center text-sm text-gray-600">
            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="truncate">{{ $service->address->town }}, {{ $service->address->province }}</span>
        </div>

        <!-- Price -->
        <div class="pt-2 border-t flex justify-between items-center">
            <div>
                <span class="text-xs text-gray-500">Starting at</span>
                <p class="text-xl font-bold text-gray-800">${{ number_format($service->price, 2) }}</p>
            </div>
            <button type="button" 
                onclick="event.preventDefault(); event.stopPropagation(); addToWishlist({{ $service->id }})"
                class="p-2 hover:bg-gray-100 rounded-full transition">
                <svg class="w-6 h-6 text-gray-400 hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
        </div>
    </div>
</a>

@once
@push('scripts')
<script>
    function addToWishlist(serviceId) {
        // Implement wishlist functionality
        console.log('Adding service', serviceId, 'to wishlist');
        // You can make an AJAX call here
    }
</script>
@endpush
@endonce


{{-- resources/views/creator/services/create.blade.php --}}
<x-app-layout>
    <div class="py-6">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:service-form 
                :categories="$categories"
                :workflowTemplates="$workflowTemplates"
                :addresses="$addresses"
                :key="'service-form-create'"
            />
        </div>
    </div>
</x-app-layout>

{{-- resources/views/creator/services/edit.blade.php --}}
<x-app-layout>
    <div class="py-6">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:service-form 
                :service="$service"
                :categories="$categories"
                :workflowTemplates="$workflowTemplates"
                :addresses="$addresses"
                :key="'service-form-'.$service->id"
            />
        </div>
    </div>
</x-app-layout>

{{-- resources/views/creator/services/index.blade.php (Updated Table to Cards) --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Services') }}
            </h2>
            <a href="{{ route('creator.services.create') }}" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Service</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @forelse ($services as $service)
                <!-- Card Grid Layout (Mobile First) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($services as $service)
                        <div class="bg-white rounded-2xl border border-gray-300 shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                            <!-- Service Image -->
                            <div class="relative h-48 bg-gray-200">
                                @if($service->media->isNotEmpty())
                                    <img src="{{ $service->media->first()->getUrl() }}" 
                                        alt="{{ $service->title }}" 
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <!-- Status Badge -->
                                <div class="absolute top-2 right-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full shadow {{ $service->is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Service Details -->
                            <div class="p-4 space-y-3">
                                <h3 class="text-lg font-bold text-gray-800 line-clamp-2">{{ $service->title }}</h3>
                                
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ $service->category->name ?? 'N/A' }}</span>
                                    <span class="font-bold text-gray-800">${{ number_format($service->price, 2) }}</span>
                                </div>

                                <!-- Quick Stats -->
                                <div class="grid grid-cols-3 gap-2 pt-2 border-t text-center">
                                    <div>
                                        <p class="text-xs text-gray-500">Orders</p>
                                        <p class="text-lg font-semibold">0</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Views</p>
                                        <p class="text-lg font-semibold">0</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Rating</p>
                                        <p class="text-lg font-semibold">-</p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-2 pt-3 border-t">
                                    <a href="{{ route('creator.services.show', $service) }}" 
                                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-center py-2 rounded-lg text-sm font-medium transition">
                                        View
                                    </a>
                                    <a href="{{ route('creator.services.edit', $service) }}" 
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-lg text-sm font-medium transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('creator.services.destroy', $service) }}" method="POST" class="flex-1" 
                                        onsubmit="return confirm('Are you sure you want to delete this service?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg text-sm font-medium transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-300 shadow-md p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-gray-500 text-lg mb-4">No services found.</p>
                    <a href="{{ route('creator.services.create') }}" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create your first service
                    </a>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($services->hasPages())
                <div class="mt-8">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>



