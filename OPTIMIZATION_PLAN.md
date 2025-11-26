# System Optimization Plan - Serbizyu

## Executive Summary
This document outlines a comprehensive optimization strategy to eliminate redundancies, improve code reusability, centralize validation, organize routes hierarchically, and fix media upload validation issues.

---

## 1. REDUNDANCIES & BOTTLENECKS IDENTIFIED

### 1.1 Media Validation Rules (CRITICAL - High Impact Bug)
**Problem:** Image upload validation rules are scattered across multiple files with inconsistent limits:
- `UserVerificationController.php`: `max:2048` (2MB)
- `StoreOpenOfferRequest.php`: `max:5000` (5MB) vs `max:5048` (5MB inconsistency)
- `WithMedia.php` Livewire trait: `max:2048`
- Multiple blade files show conflicting text ("Max 2MB" vs "Max 5MB")
- **Error Mismatch**: When users upload files over limit, validation rules don't match displayed limits in UI

**Impact:** 
- Users confused about size limits
- Validation errors mismatch displayed constraints
- Hard to maintain - changes required in 5+ files

**Solution:** Centralize in `MediaConfig` class with proper validation rules

### 1.2 File Upload Blade Partials (Code Duplication)
**Problem:** Multiple blade files duplicating upload UI:
- `media-uploader.blade.php`
- `media-upload.blade.php`
- Scattered across creator services, open offers, user verification

**Solutions:** Create reusable components

### 1.3 Form Request Validation (Consistency Issue)
**Problem:** Each domain has its own validation format and messages
- Some use array rules: `['image', 'max:2048']`
- Some use string rules: `'image|max:2048'`
- Inconsistent message formatting across requests

**Solution:** Create base FormRequest classes per domain

### 1.4 Route Structure (Organization Issue)
**Current State:**
- Work routes nested under `orders/{order}/work` ✅ (Good)
- But `orders` resource routes are flat
- No clear domain separation in routing
- Mix of resource routes and custom routes

**Issues:**
- Difficult to find related routes
- No clear parent-child relationships
- Order + Work integration not obvious at route level

**Solution:** Organize via domain-specific route files

### 1.5 Controller Hierarchy (Missing Base Classes)
**Problem:**
- Domain controllers extend `App\Http\Controllers\Controller`
- No domain-specific base classes
- Duplicated authorization/validation logic
- Each domain reimplements similar patterns

**Solutions:**
- Create `Domains\{Domain}\Http\Controllers\{Domain}Controller` base classes
- Move common logic (authorization, media handling, etc.)

### 1.6 Livewire Media Upload Trait (Incomplete)
**Problem:** `WithMedia.php` trait exists but:
- Validation is hardcoded: `'image|max:2048'`
- Not using `MediaConfig` centralized limits
- No error message customization
- Not reusable across different media types

**Solution:** Enhance trait to use `MediaConfig` and support dynamic validation

### 1.7 Blade Component Reusability
**Problem:**
- Multiple upload zones across creator, open offers, user verification
- Each with slightly different HTML/styling but same purpose
- Text says "PNG, JPG, GIF up to 2MB" but rules enforce different limits

**Solutions:**
- Extract to reusable blade component
- Use slot pattern for customization
- Centralize limit display via `MediaConfig`

### 1.8 Database Query Patterns (N+1 Issues)
**Problem:** No evidence of eager loading optimization in initial review
- Listing/Order views may load related data inefficiently

**Solution:** Audit and optimize relationships with eager loading

---

## 2. OPTIMIZATION ROADMAP

### Phase 1: Fix Critical Media Validation Bug ⭐ PRIORITY
**Goal:** Centralize all media validation rules and fix error mismatch

#### 1.1 Enhance MediaConfig Class
```php
// app/Support/MediaConfig.php
- Add validation rule generation methods
- Add display text methods
- Create validation rule arrays per media type
```

#### 1.2 Create Media Validation Rules Trait
```php
// app/Traits/ValidatesMediaUploads.php
- Generate rules from MediaConfig
- Generate error messages from MediaConfig
- Single source of truth for validation
```

#### 1.3 Create Base MediaFormRequest
```php
// app/Http/Requests/MediaFormRequest.php
- Extends FormRequest
- Uses ValidatesMediaUploads trait
- Provides common media validation
```

#### 1.4 Update All Form Requests
- StoreServiceRequest.php
- StoreOpenOfferRequest.php
- UpdateOpenOfferRequest.php
- Any others handling file uploads

#### 1.5 Update Livewire Traits
- WithMedia.php - use MediaConfig
- Update error display logic

#### 1.6 Fix All Blade Files
- Update max size text to use dynamic values from MediaConfig
- Match displayed limits with actual validation rules

---

### Phase 2: Organize Routes into Domain Modules
**Goal:** Hierarchical, self-documenting route structure

#### 2.1 Create Route Files Per Domain
```
routes/
├── web.php (keep core routes only)
├── domains/
│  ├── orders.php (Orders + nested Work)
│  ├── listings.php (Services, OpenOffers, Bids)
│  ├── messaging.php (Messages, Order Messages, Bid Messages)
│  ├── payments.php (Payments, Disbursements, Refunds)
│  ├── work.php (Work deprecation, activities)
│  └── admin.php (Admin routes)
```

#### 2.2 Update web.php
```php
// Import domain routes
require __DIR__ . '/domains/orders.php';
require __DIR__ . '/domains/listings.php';
require __DIR__ . '/domains/messaging.php';
require __DIR__ . '/domains/payments.php';
require __DIR__ . '/domains/work.php';
require __DIR__ . '/domains/admin.php';
```

#### 2.3 Benefits
- Self-documenting: Find order routes in `routes/domains/orders.php`
- Easy to add new domain routes
- Clear parent-child nesting with `scopeBindings()`
- Team can work on different domains independently

---

### Phase 3: Create Domain-Specific Base Controllers
**Goal:** Remove duplication, enforce patterns

#### 3.1 Create Base Controllers Per Domain
```
app/Domains/{Domain}/Http/Controllers/
├── {Domain}Controller.php (base)
├── SomeResourceController.php (extends base)
```

#### 3.2 What Goes in Base Controller
- Domain-specific authorization helpers
- Common query optimization patterns
- Logging patterns
- Response formatting

#### 3.3 Examples
```php
// Orders Domain Base
class OrderController extends Controller {
    protected function authorizeUser(Order $order): void
    protected function loadOrderWithRelations(Order $order): Order
}

// Listings Domain Base
class ListingsController extends Controller {
    protected function validateMediaUploads($files): array
    protected function attachMedia(Model $model, $files): void
}
```

---

### Phase 4: Refactor Livewire Traits & Components
**Goal:** Maximize reusability, minimize duplication

#### 4.1 Enhance WithMedia Trait
```php
// app/Livewire/Traits/WithMedia.php
- Constructor injection of MediaConfig
- Dynamic validation rules from MediaConfig
- Support multiple media types (images, videos, documents)
- Error message generation from MediaConfig
- File upload hooks for services
```

#### 4.2 Create Media Upload Component
```php
// app/Livewire/Components/MediaUploader.php
- Reusable Livewire component
- Accepts: mediaType, maxFiles, model, accept
- Returns: uploaded media models
- Handles: drag/drop, preview, removal, errors
```

#### 4.3 Update Blade Partials
- Delete duplicate `media-uploader.blade.php` versions
- Keep ONE authoritative component
- Use slots for customization

---

### Phase 5: Blade Component Architecture
**Goal:** DRY up repeated HTML patterns

#### 5.1 Extract Components
```
resources/views/components/
├── forms/
│  ├── media-upload.blade.php (new)
│  ├── file-input.blade.php (new)
├── media/
│  ├── upload-area.blade.php
│  ├── preview-grid.blade.php
│  └── error-display.blade.php
```

#### 5.2 Component Props
```blade
<x-media.upload-area
    :maxSize="config('media.limits.images')"
    :acceptedFormats="config('media.extensions.images')"
    wire:model="files"
/>
```

---

### Phase 6: Query Optimization
**Goal:** Eliminate N+1 queries, optimize eager loading

#### 6.1 Audit Key Views/Livewire Components
- Creator dashboard orders tab
- Order detail with work instance
- Service listing with media
- User profile with reviews

#### 6.2 Pattern to Implement
```php
// Bad
$orders = Order::all(); // N+1 on service, user, work
foreach ($orders as $order) {
    echo $order->service->title;
}

// Good
$orders = Order::with(['service', 'workInstance', 'user'])->get();
```

---

### Phase 7: Validation Rule Centralization
**Goal:** Single source of truth for all business rules

#### 7.1 Create Validation Constants
```php
// app/Support/ValidationConstants.php
- Image validation rules
- Text field rules (name, title, description)
- Price/number rules
- Email/URL rules
```

#### 7.2 Use in Form Requests
```php
public function rules(): array {
    return [
        'images.*' => ValidationConstants::IMAGE_RULES,
        'title' => ValidationConstants::SERVICE_TITLE_RULES,
        'price' => ValidationConstants::PRICE_RULES,
    ];
}
```

---

## 3. IMPLEMENTATION PRIORITY MATRIX

| Phase | Task | Priority | Effort | Impact | Duration |
|-------|------|----------|--------|--------|----------|
| 1 | Fix Media Validation Bug | 🔴 CRITICAL | Medium | High | 2-3 hours |
| 1 | Centralize Media Config | 🔴 CRITICAL | Low | High | 1 hour |
| 2 | Organize Routes | 🟡 HIGH | Medium | Medium | 2 hours |
| 3 | Domain Base Controllers | 🟡 HIGH | Medium | Medium | 3 hours |
| 4 | Refactor Livewire Traits | 🟡 HIGH | Medium | High | 2 hours |
| 5 | Extract Blade Components | 🟢 MEDIUM | Low | Low | 2 hours |
| 6 | Query Optimization | 🟢 MEDIUM | Medium | Medium | 3 hours |
| 7 | Validation Constants | 🟢 MEDIUM | Low | Medium | 1 hour |

---

## 4. EXPECTED OUTCOMES

### Code Quality Improvements
- ✅ Validation rules centralized (1 place to change limits)
- ✅ Routes organized by domain (self-documenting)
- ✅ Controllers follow consistent patterns
- ✅ Blade components DRY (no duplication)
- ✅ Livewire traits highly reusable
- ✅ Database queries optimized

### Bug Fixes
- ✅ Media upload validation error mismatch fixed
- ✅ Inconsistent size limits resolved
- ✅ UI displays correct limits to users

### Maintenance Benefits
- ✅ Adding new domain features easier
- ✅ Changes to validation apply globally
- ✅ New team members understand structure faster
- ✅ Tests easier to write and maintain

### Performance Improvements
- ✅ Reduced N+1 queries
- ✅ Faster page loads
- ✅ Optimized database queries

---

## 5. QUICK REFERENCE - What Goes Where

| Concept | Location | Pattern |
|---------|----------|---------|
| Media limits/rules | `app/Support/MediaConfig.php` | Constants + methods |
| Validation rules | `app/Http/Requests/{Domain}Request.php` | FormRequest classes |
| File upload validation | `app/Traits/ValidatesMediaUploads.php` | Trait |
| Route definitions | `routes/domains/{domain}.php` | Domain-specific files |
| Domain logic | `app/Domains/{Domain}/Http/Controllers/` | Base + resource controllers |
| Shared UI | `resources/views/components/` | Blade components |
| Livewire reusables | `app/Livewire/Traits/` or `app/Livewire/Components/` | Traits or components |
| Database queries | `app/Domains/{Domain}/Models/` | Use with eager loading |

---

## 6. NEXT STEPS

1. **Review this plan** with the team
2. **Start Phase 1** - Fix the critical media validation bug (highest ROI)
3. **Implement Phases 2-3** - Organize code structure
4. **Continue Phases 4-7** - Refactor and optimize

Would you like to proceed with Phase 1 implementation?
