## Table of Contents

## Current Status (As of 2025-11-12)

### Media Library Integration

We have successfully integrated the `plank/laravel-mediable` library to handle all media uploads in the application. This is a significant improvement over the previous custom `ImageService`, as it provides a more robust and feature-rich solution.

**Key Changes:**

*   **Installed and configured `plank/laravel-mediable`:** The library is now part of the project and configured to use both `public` and `local` disks.
*   **Refactored Models:** The `User`, `Service`, and `OpenOffer` models have been updated to use the `Mediable` trait, allowing them to have media attached to them.
*   **Refactored Controllers and Services:** The `UserVerificationController` and `ServiceService` have been refactored to use the `MediaUploader` service for handling file uploads.
*   **Secure Media Serving:** A new controller and route have been created to securely serve private media files, such as user verification documents.
*   **Cleaned up old code:** The old `ImageService` and `Image` model have been deleted.

### Current Roadblock: Failing Tests

We are currently facing an issue with the feature tests failing with a `419 TokenMismatchException`. This is preventing us from writing new tests for the media upload features and ensuring the stability of the application.

**Debugging Steps Taken:**

*   Disabled the `VerifyCsrfToken` middleware in `TestCase.php` and directly in the tests.
*   Manually added the CSRF token to the request headers.
*   Used the `from()` method to set the referer header.
*   Cleared the application cache, config, and route cache.
*   Added logging and exception handling to the controllers and services.

**Next Steps:**

The immediate next step is to resolve the testing issue. We need to investigate the root cause of the `419` error and find a solution. Once the tests are passing, we can proceed with writing the tests for the new media upload features.

## Project Overview

This is a Laravel project that appears to be a service marketplace application. It uses a domain-driven design approach, with clear separation of concerns between different domains like `Listings` and `Users`. The application includes features like user authentication, service listings, open offers, and a creator space. The front-end is built with Vite, Tailwind CSS, and Alpine.js.

## Project Progress

### Milestone 5.1: User Verification System - COMPLETED

We have successfully implemented the full web-based feature for user identity verification. This includes the user-facing forms to submit documents, a status page, and a complete admin panel for reviewing, approving, and rejecting submissions. The feature is now manually tested and confirmed to be working.

**Key changes:**
*   **Functionality:** Users can submit ID documents, see their verification status (pending, approved, rejected), and receive a "Verified" badge on their dashboard. Admins can process the queue of pending verifications.
*   **Architecture:** Created new controllers under the `Users` domain, adhering to the project's DDD structure. This involved correcting controller paths and namespaces to match the established conventions.
*   **Database:** Added the `user_verifications` table and updated the `users` table to support the feature.
*   **Next Step:** The immediate next step is to write the automated feature tests for this system to ensure its stability and prevent regressions.

### Completed Tasks:

**System Architecture Analysis - Already Implemented:**
*   User authentication (Laravel Breeze)
*   Domain structure (Users, Common, Listings)
*   Service layer architecture
*   Exception handling (Custom domain exceptions)
*   Models: User, Address, UserAddress, Service, OpenOffer, OpenOfferBid
*   Models: Category, WorkflowTemplate, WorkTemplate, WorkCatalog, ListingImage
*   Services: UserService, AddressService, CategoryService, ServiceService
*   Services: OpenOfferService, OpenOfferBidService, WorkflowTemplateService
*   Services: WorkTemplateService, WorkCatalogService, ListingImageService
*   Seeders: All core data seeded
*   Views: Home, Browse, Auth pages (Blade)
*   Components: Navbar, Form components, Modal
*   CSS: Tailwind with custom components
*   Alpine.js: Interactive components

**Phase 1: Foundation & Core API - Milestone 1.2: Categories API [5/6]:**
*   Create `CategoryController` API (and moved to `app/Domains/Listings/Http/Controllers/Api`)
*   Add routes: GET, POST, PUT, DELETE `/api/categories`
*   Add filtering/search query parameters
*   Create `CategoryResource` and `CategoryCollection` (and moved to `app/Domains/Listings/Http/Resources`)
*   Add authorization (admin only for write) with `CategoryPolicy` (and moved to `app/Domains/Listings/Policies`)

### Our Approach & Key Learnings

Our development process has been a collaborative and iterative one, with a strong emphasis on adhering to the principles of Domain-Driven Design (DDD). We've established a set of development guidelines to ensure consistency and maintainability, which are documented in this file. A key part of our workflow is to ensure that all new components (Controllers, Policies, Resources, etc.) are placed in their respective domain directories.

A significant portion of our recent efforts has been dedicated to configuring the testing environment, specifically for running feature tests with a `testing.sqlite` database in a Laravel Sail setup on Windows. This has been a challenging process, and we've encountered and worked through several issues, including:

*   **Database Connection Errors:** Persistent `QueryException` errors due to the test environment attempting to connect to the MySQL database instead of the configured SQLite database.
*   **Configuration Loading:** Difficulties in ensuring that the `php artisan test` command correctly loads the testing environment configuration from `phpunit.xml` and `.env.testing`.
*   **Syntax Errors:** Accidental introduction of syntax errors in configuration files during our attempts to resolve the database connection issues.

Through persistent problem-solving, we've explored various solutions, including modifying `phpunit.xml`, `.env.testing`, `config/database.php`, and `config/cache.php`. The key takeaway is the importance of a robust and explicit configuration for the testing environment, especially in a complex setup like Laravel Sail on Windows. We also learned the importance of clearing the configuration cache (`php artisan config:clear`) after making changes to configuration files to ensure that the changes are applied.

### Next Steps

1.  **Run `CategoryApiTest.php`:** With the recent fixes to the configuration files, the next immediate step is to run the `CategoryApiTest.php` and ensure all tests pass. The tests that need to pass are:
    *   `test_guest_cannot_create_category`
    *   `test_regular_user_cannot_create_category`
    *   `test_admin_can_create_category`
    *   `test_categories_can_be_listed`
    *   `test_categories_can_be_listed_with_search_filter`
    *   `test_specific_category_can_be_retrieved`
    *   `test_guest_cannot_update_category`
    *   `test_regular_user_cannot_update_category`
    *   `test_admin_can_update_category`
    *   `test_guest_cannot_delete_category`
    *   `test_regular_user_cannot_delete_category`
    *   `test_admin_can_delete_category`

2.  **Complete Milestone 1.2:** Once the tests are passing, we will have completed the backend development for the Categories API. The final step for this milestone will be to mark it as complete in this `GEMINI.md` file.

3.  **Proceed to Milestone 1.3: Services API & UI Enhancement:** After completing Milestone 1.2, we will move on to the next milestone, which includes:
    *   Creating the `ServiceController` API.
    *   Adding routes for services CRUD.
    *   Implementing image uploads for services.
    *   Creating the `ServiceResource`.

This detailed plan will allow us to pick up where we left off and continue making progress efficiently in our next session.

## Building and Running

### Prerequisites

*   PHP >= 8.2
*   Node.js and npm
*   Composer

### Installation

1.  Clone the repository.
2.  Install PHP dependencies: `composer install`
3.  Install front-end dependencies: `npm install`
4.  Copy the `.env.example` file to `.env`: `cp .env.example .env`
5.  Generate an application key: `php artisan key:generate`
6.  Configure your database in the `.env` file.
7.  Run database migrations and seed the database: `php artisan migrate --seed`

### Development

To start the development servers for both the back-end and front-end, run the following command:

```bash
composer run-script dev
```

This will start the Laravel development server on `http://127.0.0.1:8000` and the Vite development server on `http://localhost:5173`.

### Testing

To run the test suite, use the following command:

```bash
composer test
```

## Development Conventions

*   **Domain-Driven Design:** The application follows a domain-driven design approach, with services and models organized into domains.
*   **Service Layer:** Business logic is encapsulated in service classes.
*   **Blade Components:** The front-end uses Blade components for reusable UI elements.
*   **Tailwind CSS:** The application uses Tailwind CSS for styling.
*   **Alpine.js:** Alpine.js is used for front-end interactivity.

## Development Guidelines

*   **Domain-Centric Architecture:** Strictly adhere to the domain-driven design for all new files and modifications. Policies, HTTP controllers, form requests, and other related components must be placed within their respective domain directories (e.g., `app/Domains/Listings/Policies`, `app/Domains/Listings/Http/Controllers`, `app/Domains/Listings/Http/Requests`).
*   **Confidence Threshold:** Before making any changes, ensure you have at least 95% confidence in the approach and implementation. If unsure, ask follow-up questions for clarification.
*   **Domain-Driven Design Adherence:** Always prioritize adhering to the established Domain-Driven Design principles and project structure. New features and modifications should fit naturally within the existing domain boundaries.
*   **Master Plan First:** Refer to the 'Master Plan' as the primary source for tasks and priorities. Address items in the plan before considering new additions, unless explicitly instructed otherwise.
*   **Clarification:** If any part of a task or instruction is unclear, ambiguous, or requires further detail, always ask follow-up questions to gain full understanding before proceeding.
*   **Testing:** For every new feature or bug fix, ensure appropriate unit and feature tests are written to verify correctness and prevent regressions. Tests are a permanent part of the codebase.
*   **Code Conventions:** Strictly follow existing code style, formatting, naming conventions, and architectural patterns observed in the project.
*   **Incremental Changes:** Prefer making small, focused, and verifiable changes. Avoid large, sweeping modifications that are difficult to review and debug.

## Master Plan

# 🎯 Serbizyu - Refined Master Development Plan

## 📋 Project Overview

**Tech Stack Confirmed:**
- **Backend:** Laravel 12 with Domain-Driven Design
- **Frontend:** Blade + Alpine.js + Tailwind CSS (existing setup)
- **Real-time:** Laravel Broadcasting + Laravel Echo
- **Payments:** Xendit/PayMongo with manual disbursement
- **Current Structure:** Services already built, need API + UI completion

---

## 🏗️ System Architecture Analysis

### ✅ Already Implemented (From Code Map)
```
✓ User authentication (Laravel Breeze)
✓ Domain structure (Users, Common, Listings)
✓ Service layer architecture
✓ Exception handling (Custom domain exceptions)
✓ Models: User, Address, UserAddress, Service, OpenOffer, OpenOfferBid
✓ Models: Category, WorkflowTemplate, WorkTemplate, WorkCatalog, ListingImage
✓ Services: UserService, AddressService, CategoryService, ServiceService
✓ Services: OpenOfferService, OpenOfferBidService, WorkflowTemplateService
✓ Services: WorkTemplateService, WorkCatalogService, ListingImageService
✓ Seeders: All core data seeded
✓ Views: Home, Browse, Auth pages (Blade)
✓ Components: Navbar, Form components, Modal
✓ CSS: Tailwind with custom components
✓ Alpine.js: Interactive components
```

### 🚧 Needs Implementation
```
✗ API endpoints (controllers + routes)
✗ Order system
✗ Work instance execution
✗ Payment integration
✗ Messaging system
✗ Real-time notifications
✗ Review system
✗ Quick deals
✗ Admin panel
✗ User verification
✗ Dispute resolution
```

---

## 📜 Iteration Process & Rules

This guide refines the iterative development process for Laravel applications, incorporating frontend considerations alongside backend fundamentals. It maintains DDD for backend organization (domains as bounded contexts) and Spatie for RBAC, while extending to frontend using Blade templates for primary rendering, Livewire for interactive components (leveraging Alpine for reactivity), and minimal APIs (favor server-rendered Blade over SPA-style APIs unless explicitly needed for dynamic updates). The process emphasizes user collaboration on UI/UX, task decomposition for manageability, and holistic feature delivery.

### Fundamentals:

*   **Backend DDD:** Domains encapsulate logic; services orchestrate invariants; repositories abstract data; events decouple side effects.
*   **Spatie RBAC:** Permissions are domain-specific and role-agnostic; enforce at policy level for both backend actions and frontend visibility.
*   **Frontend Approach:** Prioritize Blade for static/server-rendered views. Use Livewire for stateful, interactive components (e.g., forms, real-time updates) integrated with Alpine for client-side reactivity. Avoid heavy APIs—use Livewire's `wire:model`/`action` for data binding and server calls. If dynamic, fall back to Alpine alone.
*   **User Collaboration:** For frontend, always re-query the user on UI expectations, especially for follow-ups or iterations.
*   **Task Decomposition:** If a step reveals complexity (e.g., a flow needs role-specific UIs), break into subtasks immediately to maintain momentum.
*   **Quality Gates:** Lint/test at each step; use Livewire's testing utils; ensure responsive design (Tailwind) and accessibility.
*   **Subtask Handling:** Nest as decimals; if frontend-specific, prefix with "F" (e.g., 9.1F: UI variant).
*   **Contextual Scaffolding:** For complex or multi-step tasks, create a temporary context file in the `progress-context/` directory (e.g., `milestone-X.Y-context.md`). This file should outline the sub-tasks, dependencies, and agent's plan, and can be removed upon task completion.

### Iteration Process Checklist
Execute sequentially per feature/subfeature. Document in a tracker; re-engage user for clarifications.

1.  **Brainstorm and Scope Definition:** Define domain entities, invariants, and roles/permissions (Spatie). Extend to frontend: Sketch high-level UI needs (e.g., views, interactions). **Re-query user:** "Based on this scope, describe the desired look and feel for [feature], including any follow-up interactions." Output: Domain model + initial wireframes (text-based).
2.  **Logic and Flow Mapping:** Map backend flows with DDD patterns, including role branches. For frontend: Map UI flows (e.g., Blade view → Livewire component → Alpine event). If task complexity emerges (e.g., multi-step form), break down here. **Re-query user if UI unclear:** "For [subflow], how should it appear on follow-up (e.g., modal, redirect)?" Output: Integrated flow map (backend + frontend paths).
3.  **Database and Persistence Setup:** Create migrations/repositories aligned with domain. No frontend here, but consider data shaping for views (e.g., eager loading for Blade loops).
4.  **Model Implementation:** Define entities with relationships/scopes. Integrate Spatie on relevant models. Prepare for frontend by adding accessors (e.g., formatted attributes for display).
5.  **Service and Repository Layer:** Implement domain logic in services; use transactions/events. If sublogic arises (e.g., notification integration), nest subtasks. Ensure outputs are view-friendly (e.g., DTOs for Blade). **Subtask Rule:** Decompose oversized tasks (e.g., if service handles complex UI data prep, split to 5.1: Dedicated prep method).
6.  **Validation with Requests:** Create requests with domain rules; tie authorization to Spatie. For Livewire: Use similar validation in components (e.g., `validateOnly()`).
7.  **Authorization with Policies:** Define policy methods; register for models. Extend to frontend: Use policies in Livewire/Blade for conditional rendering (e.g., using the `@can` directive: `@can('update', $post)`).
8.  **Controller and Routing:** Orchestrate backend; return Blade views or Livewire responses. Favor `return view()` over JSON APIs. Route with role middleware. **Subtask Rule:** For role/UI variants, nest (e.g., 8.1: Owner route with filtered data; 8.1F: Custom Blade partial).
9.  **Frontend Implementation:** Build Blade views as primary (e.g., layouts, loops). Embed Livewire components for interactivity (e.g., `<livewire:entity-form />`). Add Alpine for client-side (e.g., `x-data` for toggles). **Re-query user:** "Confirm the look for [UI element]; any follow-ups like modals?" If complex, decompose (e.g., 9.1F: Break form into subcomponents). Ensure RBAC: Hide elements via directives. **Subtask Rule:** If UI task is large (e.g., dashboard with tabs), split (e.g., 9.1F: Tab 1; 9.2F: Tab 2 with Livewire polling).
10. **Testing and Validation:** Unit/feature tests for backend; Livewire tests for components (e.g., `Livewire::test()`). Browser tests for full flows. Cover roles and UI states.
11. **Refinement and Closure:** Optimize (e.g., Livewire defer loading). Review DDD/Spatie fidelity and UI consistency. If new iterations needed, re-query user and spawn cycle.

---

## 📦 PHASE 1: Foundation & Core API (Week 1-2)

### Milestone 1.2: Categories Web CRUD [6/6]
**Goal:** Complete Web CRUD for categories (foundation for listings)

#### Backend Tasks
- [ ] Create `CategoryController` for Web CRUD in the `Listings` domain.
- [ ] Add routes: GET, POST, PUT, DELETE `/creator/categories`
- [ ] Add filtering/search query parameters to the `index` method.
- [ ] Implement authorization (admin/creator only for write actions).
- [ ] Test all CRUD operations via the browser.

#### Frontend Tasks
- [ ] Create Blade views for category management (`index`, `create`, `edit`).
- [ ] Use Alpine.js for confirmations or minor UI enhancements.
- [ ] Display success/error feedback messages.

#### Files:
```
app/Domains/Listings/Http/Controllers/
└── CategoryController.php

app/Domains/Listings/Http/Resources/
├── CategoryResource.php
└── CategoryCollection.php

resources/views/creator/categories/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

**Estimated Time:** 4 hours

---

### Milestone 1.3: Services Web CRUD & UI [10/12]

#### Backend Tasks
- [x] Create `ServiceController` for Web CRUD in the `Listings` domain.
- [x] Add resource routes for services CRUD under `/creator/services`.
- [ ] Add image upload handling in the `ServiceService`.
- [x] Implement `ServicePolicy` for authorization (create, update, delete).
- [ ] Handle soft deletes in all service queries.
- [x] Add filtering and sorting logic to the `index` method.

#### Frontend Tasks
- [x] Create `resources/views/creator/services/index.blade.php`.
- [x] Enhance `resources/views/browse.blade.php` with Livewire for dynamic filtering.
- [x] Create `resources/views/creator/services/create.blade.php`.
- [x] Create `resources/views/creator/services/edit.blade.php`.
- [x] Create public-facing `resources/views/listings/show.blade.php`.
- [ ] Create a Livewire component for image uploads to provide a better UX.

#### Files:
```
Backend:
├── app/Domains/Listings/Http/Controllers/ServiceController.php
├── app/Domains/Listings/Policies/ServicePolicy.php
└── app/Http/Requests/StoreServiceRequest.php

Frontend:
├── resources/views/creator/services/create.blade.php
├── resources/views/creator/services/edit.blade.php
├── resources/views/listings/show.blade.php
├── app/Http/Livewire/ImageUploader.php
└── resources/views/livewire/image-uploader.blade.php
```

**Estimated Time:** 8 hours

---

### Milestone 1.4: Open Offers Web CRUD & UI [0/12]

#### Backend Tasks
- [ ] Create `OpenOfferController` for Web CRUD in the `Listings` domain.
- [ ] Add routes for offers CRUD.
- [ ] Add "close offer" endpoint/method.
- [ ] Implement `OpenOfferPolicy`.
- [ ] Add auto-expiration job for offers (optional field).
- [ ] Handle offer fulfillment status changes.

#### Frontend Tasks
- [ ] Create `resources/views/offers/create.blade.php`.
- [ ] Create `resources/views/offers/edit.blade.php`.
- [ ] Create `resources/views/offers/show.blade.php`.
- [ ] Create a Livewire component for budget calculation or other interactive elements.
- [ ] Show bid count dynamically.
- [ ] Add close offer button (owner only, using a Livewire action).

#### Files:
```
Backend:
├── app/Domains/Listings/Http/Controllers/OpenOfferController.php
├── app/Domains/Listings/Policies/OpenOfferPolicy.php
└── app/Jobs/CloseExpiredOffers.php (optional)

Frontend:
├── resources/views/offers/create.blade.php
├── resources/views/offers/edit.blade.php
├── resources/views/offers/show.blade.php
└── app/Http/Livewire/OfferForm.php
```

**Estimated Time:** 8 hours

---

### Milestone 1.5: Bidding System Web & UI [0/14]

#### Backend Tasks
- [ ] Create `OpenOfferBidController` for handling bid actions.
- [ ] Add routes for bid CRUD.
- [ ] Add accept/reject bid methods.
- [ ] Validate: no duplicate bids, service owner matches bidder.
- [ ] Implement `BidPolicy`.
- [ ] Auto-close offer when bid accepted.
- [ ] Send notifications (email for now).

#### Frontend Tasks
- [ ] Add bid form to offer detail page (as a Livewire component).
- [ ] Create a `BidList` Livewire component.
- [ ] Add accept/reject buttons with `wire:click` actions.
- [ ] Show bid status badges.
- [ ] Add "My Bids" section to the user dashboard.

#### Files:
```
Backend:
├── app/Domains/Listings/Http/Controllers/OpenOfferBidController.php
├── app/Domains/Listings/Policies/BidPolicy.php
└── app/Notifications/BidPlacedNotification.php

Frontend:
├── app/Http/Livewire/BidForm.php
├── app/Http/Livewire/BidList.php
└── resources/views/dashboard/bids.blade.php
```

**Estimated Time:** 6 hours

---

### Milestone 1.6: User Profile & Address Web UI [10/10]

**Goal:** Allow users to manage their addresses via a Livewire component on their profile page.

#### Backend Tasks
- [x] Refactor `AddressService` to the `Common` domain.
- [x] Ensure `AddressService` correctly handles creating, updating, and deleting addresses.
- [x] Ensure `AddressService` correctly handles setting a primary address and loading pivot data.
- [x] Fix namespace conflicts in `AppServiceProvider` and dependent classes.

#### Frontend Tasks
- [x] Create `AddressManager` Livewire component for full CRUD functionality.
- [x] Implement a modal form within the component for adding/editing addresses.
- [x] Update `profile/edit.blade.php` to include the `AddressManager` component.
- [x] Update `livewire/address-manager.blade.php` view to use correct form fields and logic.

#### Testing Tasks
- [x] Create `AddressManagerTest.php` feature test.
- [x] Write and pass tests for all component actions (create, update, delete, set primary, authorization).

**Summary:** The address management feature is complete and fully tested. The initial API-based plan was superseded by a more interactive Livewire component. All underlying namespace and service layer bugs have been resolved.

**Estimated Time:** 5 hours

---

### Milestone 1.7: Workflow Management Web UI [18/18]

**Goal:** Create a web UI for creators to build, manage, and reuse workflow templates for their services.

**Summary:** A comprehensive workflow builder has been implemented using Livewire. Creators can now create, update, and delete workflow templates. The builder interface allows for adding steps from scratch or from a reusable catalog, editing step details, and reordering steps via drag-and-drop. The entire feature is protected by authorization policies to ensure users can only manage their own workflows.

**Key Features:**
-   **Backend:** Controllers, services, and policies for `WorkflowTemplate`, `WorkTemplate`, and `WorkCatalog`.
-   **Frontend:** A full-featured, interactive workflow builder powered by a `WorkflowBuilder` Livewire component.
-   **Functionality:** Create, read, update, delete (CRUD) for workflows and their steps, drag-and-drop reordering, and the ability to add steps from a catalog.

**Files Created/Modified:**
-   `app/Domains/Listings/Http/Controllers/WorkflowTemplateController.php`
-   `app/Domains/Listings/Http/Controllers/WorkTemplateController.php`
-   `app/Domains/Listings/Http/Controllers/WorkCatalogController.php`
-   `app/Domains/Listings/Policies/WorkflowPolicy.php`
-   `app/Domains/Listings/Services/WorkflowTemplateService.php`
-   `app/Domains/Listings/Services/WorkTemplateService.php`
-   `app/Livewire/WorkflowBuilder.php`
-   `resources/views/creator/workflows/index.blade.php`
-   `resources/views/creator/workflows/builder.blade.php`
-   `resources/views/livewire/workflow-builder.blade.php`
-   `routes/web.php` (updated)
-   `app/Providers/AuthServiceProvider.php` (updated)
-   `tests/Feature/WorkflowManagementTest.php`

---

## 📦 PHASE 2: Order System & Execution (Week 3-4)

### Milestone 2.1: Order System Foundation [0/16]

#### Backend Tasks
- [ ] Create `Order` model + migration in the `Orders` domain.
- [ ] Create `OrderController` in the `Orders` domain.
- [ ] Add create order endpoint (from accepted bid).
- [ ] Add order status enum (pending, in_progress, completed, cancelled, disputed).
- [ ] Add cancel order endpoint (if no work started).
- [ ] Implement order state machine logic in the `OrderService`.
- [ ] Create `OrderPolicy`.
- [ ] Send order notifications (email).

#### Frontend Tasks
- [ ] Create `resources/views/orders/index.blade.php`.
- [ ] Create `resources/views/orders/show.blade.php`.
- [ ] Add order status timeline component.
- [ ] Add cancel button (if eligible).
- [ ] Show order details clearly.
- [ ] Add "My Orders" dashboard section.
- [ ] Create order creation flow from bid acceptance.

#### Database:
```sql
orders table:
- id
- buyer_id (user who made offer)
- seller_id (service creator)
- service_id
- open_offer_id
- open_offer_bid_id
- price (agreed price)
- platform_fee (calculated)
- total_amount (price + fee)
- status (enum)
- payment_status (enum)
- cancelled_at
- cancellation_reason
- timestamps
```

#### Files:
```
Backend:
├── app/Domains/Orders/Models/Order.php
├── app/Domains/Orders/Http/Controllers/OrderController.php
├── app/Domains/Orders/Policies/OrderPolicy.php
├── app/Enums/OrderStatus.php
└── database/migrations/xxxx_create_orders_table.php

Frontend:
├── resources/views/orders/index.blade.php
├── resources/views/orders/show.blade.php
└── resources/views/components/order-timeline.blade.php
```

**Estimated Time:** 8 hours

---

### Milestone 2.2: Work Instance Execution [0/20]

#### Backend Tasks
- [ ] Create `WorkInstance` model + migration in the `Work` domain.
- [ ] Clone workflow on order creation.
- [ ] Create `WorkInstanceController` in the `Work` domain.
- [ ] Add start/complete step endpoints.
- [ ] Add work instance timeline endpoint.
- [ ] Create `ActivityThread` and `ActivityMessage` models.
- [ ] Add activity CRUD endpoints via `ActivityController`.
- [ ] Add file upload to activities.
- [ ] Send activity notifications.

#### Frontend Tasks
- [ ] Create `resources/views/work/show.blade.php`.
- [ ] Create a `WorkProgress` Livewire component for the step-by-step UI.
- [ ] Create an `ActivityThread` Livewire component for discussions.
- [ ] Add a file upload component.
- [ ] Show real-time progress with Livewire polling or broadcasting.
- [ ] Create a work dashboard for sellers.

#### Database:
```sql
work_instances:
- id, order_id, workflow_template_id
- current_step_index
- status, started_at, completed_at

work_instance_steps:
- id, work_instance_id, work_template_id
- step_index, status, started_at, completed_at

activity_threads:
- id, work_instance_step_id
- title, description

activity_messages:
- id, activity_thread_id, user_id
- content, created_at

activity_attachments:
- id, activity_message_id
- file_path, file_type
```

#### Files:
```
Backend:
├── app/Domains/Work/Models/WorkInstance.php
├── app/Domains/Work/Models/WorkInstanceStep.php
├── app/Domains/Work/Models/ActivityThread.php
├── app/Domains/Work/Models/ActivityMessage.php
├── app/Domains/Work/Http/Controllers/WorkInstanceController.php
└── app/Domains/Work/Http/Controllers/ActivityController.php

Frontend:
├── resources/views/work/show.blade.php
├── app/Http/Livewire/WorkProgress.php
└── app/Http/Livewire/ActivityThread.php
```

**Estimated Time:** 12 hours

---

## 📦 PHASE 3: Real-time Features (Week 5)

### Milestone 3.1: Broadcasting Setup [0/10]

#### Setup Tasks
- [ ] Install Laravel WebSockets or configure Pusher
- [ ] Configure `config/broadcasting.php`
- [ ] Set up `.env` variables
- [ ] Create channel authorization in `routes/channels.php`
- [ ] Start queue worker for broadcasting
- [ ] Test broadcasting connection

#### Frontend Tasks
- [ ] Install Laravel Echo + Socket.io (via CDN or npm)
- [ ] Configure Echo in `resources/js/bootstrap.js`
- [ ] Add connection status indicator
- [ ] Test Echo connection

#### Files:
```
Backend:
├── routes/channels.php (configure)
└── config/broadcasting.php (configure)

Frontend:
├── resources/js/echo.js (if needed)
└── Update resources/js/bootstrap.js
```

**Estimated Time:** 4 hours

---

### Milestone 3.2: Real-time Notifications [0/15]

#### Backend Tasks
- [ ] Create notification system (database notifications).
- [ ] Create events: `BidPlaced`, `BidAccepted`, `OrderCreated`.
- [ ] Create events: `WorkStepCompleted`, `ActivityMessageSent`.
- [ ] Broadcast to private user channels.
- [ ] Add `NotificationController` with a `markAsRead` endpoint.
- [ ] Add user preferences for notifications.

#### Frontend Tasks
- [ ] Add a `NotificationDropdown` Livewire component to the navbar.
- [ ] Listen for notifications in real-time using Echo.
- [ ] Show toast/banner for new notifications.
- [ ] Add unread count badge.
- [ ] Create a full notifications page/view.

#### Files:
```
Backend:
├── app/Events/BidPlaced.php
├── app/Events/BidAccepted.php
├── app/Events/OrderCreated.php
├── app/Events/WorkStepCompleted.php
├── app/Domains/Notifications/Http/Controllers/NotificationController.php
└── database/migrations/xxxx_create_notifications_table.php

Frontend:
├── app/Http/Livewire/NotificationDropdown.php
└── resources/views/notifications/index.blade.php
```

**Estimated Time:** 6 hours

---

### Milestone 3.3: Messaging System [0/18]

#### Backend Tasks
- [ ] Create `MessageThread`, `Message`, and `MessageAttachment` models in the `Messaging` domain.
- [ ] Create `MessageController` in the `Messaging` domain.
- [ ] Add create thread endpoint.
- [ ] Add send message endpoint.
- [ ] Add mark as read endpoint.
- [ ] Add message listing with pagination.
- [ ] Broadcast `MessageSent` event.
- [ ] Add file attachment handling.

#### Frontend Tasks
- [ ] Create `resources/views/messages/index.blade.php`.
- [ ] Create a `ChatInterface` Livewire component.
- [ ] Listen for real-time messages with Echo.
- [ ] Add file attachment preview.
- [ ] Show unread count.
- [ ] Add message search.

#### Database:
```sql
message_threads:
- id, creator_id, title
- parent_type (WorkInstance, OpenOfferBid, QuickDeal)
- parent_id, created_at

messages:
- id, thread_id, sender_id
- content, read_at, created_at

message_attachments:
- id, message_id
- file_path, file_type
```

#### Files:
```
Backend:
├── app/Domains/Messaging/Models/MessageThread.php
├── app/Domains/Messaging/Models/Message.php
├── app/Domains/Messaging/Models/MessageAttachment.php
├── app/Domains/Messaging/Http/Controllers/MessageController.php
├── app/Events/MessageSent.php
└── database/migrations/xxxx_create_messages_tables.php

Frontend:
├── resources/views/messages/index.blade.php
└── app/Http/Livewire/ChatInterface.php
```

**Estimated Time:** 8 hours

---

## 📦 PHASE 4: Payments & Financial (Week 6-7)

### Milestone 4.1: Payment Integration [0/18]

#### Backend Tasks
- [ ] Choose provider (Xendit recommended for PH).
- [ ] Set up Xendit/PayMongo account.
- [ ] Install payment SDK via Composer.
- [ ] Create `Payment` model + migration in the `Payments` domain.
- [ ] Create `PaymentController` in the `Payments` domain.
- [ ] Add create payment intent endpoint.
- [ ] Add webhook endpoint for payment status.
- [ ] Handle payment callbacks in `PaymentWebhookController`.
- [ ] Create payment verification logic in `PaymentService`.
- [ ] Calculate platform fee.
- [ ] Update order `payment_status` on success.

#### Frontend Tasks
- [ ] Create `resources/views/payments/checkout.blade.php`.
- [ ] Add payment method selector (GCash, Card, BankTransfer).
- [ ] Integrate payment SDK (Xendit checkout).
- [ ] Show payment instructions.
- [ ] Create payment success page.
- [ ] Create payment failed page.
- [ ] Show payment history.

#### Database:
```sql
payments:
- id, order_id, user_id
- amount (service price)
- platform_fee (calculated)
- total_amount (amount + fee)
- payment_method
- provider_reference (xendit_id)
- status (pending, paid, failed, refunded)
- paid_at, metadata (JSON)
```

#### Files:
```
Backend:
├── app/Domains/Payments/Services/PaymentService.php
├── app/Domains/Payments/Models/Payment.php
├── app/Domains/Payments/Http/Controllers/PaymentController.php
├── app/Domains/Payments/Http/Controllers/PaymentWebhookController.php
└── config/payment.php

Frontend:
├── resources/views/payments/checkout.blade.php
├── resources/views/payments/success.blade.php
├── resources/views/payments/failed.blade.php
└── resources/views/payments/history.blade.php
```

**Estimated Time:** 10 hours

---

### Milestone 4.2: Escrow & Disbursement [0/16]

#### Backend Tasks
- [ ] Create `Disbursement` model + migration in the `Payments` domain.
- [ ] Hold payment in escrow on order creation.
- [ ] Add release payment endpoint (buyer confirms work).
- [ ] Calculate platform fee deduction.
- [ ] Create manual disbursement dashboard (admin).
- [ ] Add earnings calculation per seller.
- [ ] Track pending disbursements.
- [ ] Create disbursement request system.
- [ ] Send disbursement notifications.

#### Frontend Tasks
- [ ] Show escrow status in order detail.
- [ ] Add "Release Payment" button (buyer, after work completed).
- [ ] Create `resources/views/earnings/index.blade.php` (seller).
- [ ] Show pending balance.
- [ ] Show disbursement history.
- [ ] Add request payout button.
- [ ] Show earnings timeline.

#### Database:
```sql
disbursements:
- id, order_id, seller_id
- amount (after platform fee)
- platform_fee_amount
- status (pending, requested, processing, completed)
- requested_at, processed_at
- payment_method (bank_transfer)
- bank_details (JSON: bank_name, account_number, account_name)
```

#### Files:
```
Backend:
├── app/Domains/Payments/Models/Disbursement.php
├── app/Domains/Payments/Http/Controllers/DisbursementController.php
└── database/migrations/xxxx_create_disbursements_table.php

Frontend:
├── resources/views/earnings/index.blade.php
├── resources/views/components/earnings-summary.blade.php
└── resources/views/admin/disbursements.blade.php
```

**Estimated Time:** 8 hours

---

### Milestone 4.3: Refunds & Cancellations [0/12]

#### Backend Tasks
- [ ] Create `Refund` model + migration in the `Payments` domain.
- [ ] Add refund request endpoint.
- [ ] Add approve/reject refund endpoint (admin).
- [ ] Handle order cancellation (before work starts).
- [ ] Process refund via bank transfer (manual for now).
- [ ] Update order and payment status.
- [ ] Send refund notifications.

#### Frontend Tasks
- [ ] Add cancel order button (if no work started).
- [ ] Add refund request form.
- [ ] Show refund status.
- [ ] Create admin refund management page.
- [ ] Show refund history.

#### Database:
```sql
refunds:
- id, payment_id, order_id
- amount, reason
- status (requested, approved, rejected, completed)
- bank_details (JSON)
- processed_at
```

#### Files:
```
Backend:
├── app/Domains/Payments/Models/Refund.php
├── app/Domains/Payments/Http/Controllers/RefundController.php
└── database/migrations/xxxx_create_refunds_table.php

Frontend:
├── resources/views/components/refund-form.blade.php
└── resources/views/admin/refunds.blade.php
```

**Estimated Time:** 6 hours

---

## 📦 PHASE 5: Trust & Safety (Week 8)

### Milestone 5.1: User Verification System [12/14]

#### Backend Tasks
- [x] Create `UserVerification` model + migration
- [x] Add ID upload endpoint (via web controller)
- [x] Add verification status to users table
- [x] Create admin verification review endpoint (via web controller)
- [x] Add approve/reject verification
- [ ] Send verification notifications (TODO)
- [x] Add "verified" badge logic

#### Frontend Tasks
- [x] Create `resources/views/verification/submit.blade.php`
- [x] Add ID upload form (front & back)
- [x] Show verification status on profile
- [x] Add verified badge to listings (implemented on dashboard)
- [x] Create admin verification queue
- [x] Add verification review page (admin)
- [ ] Add tests for the feature (TODO)

#### Database:
```sql
user_verifications:
- id, user_id
- id_type (national_id, drivers_license, passport)
- id_front_path, id_back_path
- status (pending, approved, rejected)
- reviewed_by, reviewed_at
- rejection_reason

Add to users table:
- is_verified (boolean)
- verified_at
```

#### Files Created/Modified:
- `app/Domains/Users/Models/UserVerification.php`
- `app/Domains/Users/Http/Controllers/UserVerificationController.php`
- `app/Domains/Users/Http/Controllers/Admin/UserVerificationController.php`
- `database/migrations/0001_01_01_000000_create_users_table.php` (modified)
- `database/migrations/xxxx_create_user_verifications_table.php`
- `database/factories/Domains/Users/Models/UserVerificationFactory.php`
- `resources/views/verification/submit.blade.php`
- `resources/views/verification/status.blade.php`
- `resources/views/admin/verifications/index.blade.php`
- `resources/views/admin/verifications/show.blade.php`
- `resources/views/dashboard.blade.php` (modified)
- `routes/web.php` (modified)

**Estimated Time:** 6 hours

---

### Milestone 5.2: Reviews & Ratings [0/16]

#### Backend Tasks
- [ ] Create `ListingReview` and `UserReview` models in the `Reviews` domain.
- [ ] Create `ReviewController` in the `Reviews` domain.
- [ ] Add submit review endpoint (after order completion).
- [ ] Prevent duplicate reviews.
- [ ] Calculate average ratings (services & users).
- [ ] Add review moderation flags.
- [ ] Update rating on relevant models.

#### Frontend Tasks
- [ ] Create review submission form.
- [ ] Add star rating component (Alpine.js).
- [ ] Display reviews on service page.
- [ ] Display reviews on user profile.
- [ ] Add review filtering.
- [ ] Show average rating badges.
- [ ] Add "Leave Review" prompt after order.

#### Database:
```sql
listing_reviews:
- id, listing_type, listing_id
- order_id, reviewer_id
- rating (1-5), comment

user_reviews:
- id, order_id
- reviewer_id, reviewed_id
- rating, comment

Add to services/users:
- average_rating, review_count
```

#### Files:
```
Backend:
├── app/Domains/Reviews/Models/ListingReview.php
├── app/Domains/Reviews/Models/UserReview.php
├── app/Domains/Reviews/Http/Controllers/ReviewController.php
└── database/migrations/xxxx_create_reviews_tables.php

Frontend:
├── resources/views/components/review-form.blade.php
├── resources/views/components/review-list.blade.php
└── resources/views/components/star-rating.blade.php
```

**Estimated Time:** 7 hours

---

### Milestone 5.3: Dispute Resolution [0/12]

#### Backend Tasks
- [ ] Create `Dispute` model + migration in the `Disputes` domain.
- [ ] Add file dispute endpoint.
- [ ] Add dispute statuses (open, under_review, resolved, closed).
- [ ] Add dispute response endpoint.
- [ ] Create admin dispute management.
- [ ] Link disputes to orders.
- [ ] Send dispute notifications.

#### Frontend Tasks
- [ ] Add "File Dispute" button on orders.
- [ ] Create dispute submission form.
- [ ] Show dispute status timeline.
- [ ] Add dispute chat/thread.
- [ ] Create admin dispute dashboard.

#### Database:
```sql
disputes:
- id, order_id
- filed_by, filed_against
- reason, description
- status
- resolved_at, resolution_notes
```

#### Files:
```
Backend:
├── app/Domains/Disputes/Models/Dispute.php
├── app/Domains/Disputes/Http/Controllers/DisputeController.php
└── database/migrations/xxxx_create_disputes_table.php

Frontend:
├── resources/views/components/dispute-form.blade.php
└── resources/views/admin/disputes.blade.php
```

**Estimated Time:** 6 hours

---

### Milestone 5.4: Content Moderation [0/10]

#### Backend Tasks
- [ ] Create `Report` model + migration in the `Moderation` domain.
- [ ] Add report listing endpoint.
- [ ] Add flag reasons (spam, inappropriate, fraud, etc.).
- [ ] Create admin moderation dashboard.
- [ ] Add hide/remove listing actions.
- [ ] Add ban user action.
- [ ] Send moderation notifications.

#### Frontend Tasks
- [ ] Add "Report" button on listings.
- [ ] Create report modal.
- [ ] Show report status.
- [ ] Create admin moderation queue.

#### Database:
```sql
reports:
- id, reportable_type, reportable_id
- reporter_id, reason, description
- status (pending, reviewed, actioned)
- reviewed_by, reviewed_at
```

#### Files:
```
Backend:
├── app/Domains/Moderation/Models/Report.php
├── app/Domains/Moderation/Http/Controllers/ReportController.php
└── database/migrations/xxxx_create_reports_table.php

Frontend:
├── resources/views/components/report-modal.blade.php
└── resources/views/admin/reports.blade.php
```

**Estimated Time:** 5 hours

---

## 📦 PHASE 6: Quick Deals (Week 9)

### Milestone 6.1: Quick Deal Core [0/16]

#### Backend Tasks
- [ ] Create `QuickDeal` and `QuickDealRequest` models in the `Deals` domain.
- [ ] Create `QuickDealController` in the `Deals` domain.
- [ ] Add create deal endpoint.
- [ ] Add propose service endpoint.
- [ ] Add accept/reject proposal endpoints.
- [ ] Add deal expiration logic (30 min default).
- [ ] Broadcast real-time proposals.
- [ ] Convert accepted deal to order.

#### Frontend Tasks
- [ ] Create `resources/views/quick-deals/start.blade.php`.
- [ ] Create deal room interface.
- [ ] Add service proposal form.
- [ ] Show live proposals (Livewire + Echo).
- [ ] Add countdown timer.
- [ ] Create deal history page.
- [ ] Add QR code scanner integration.

#### Database:
```sql
quick_deals:
- id, creator_id
- status (open, closed, expired)
- expires_at, created_at

quick_deal_requests:
- id, quick_deal_id
- proposer_id, service_id
- proposed_price, workflow_template_id
- status (proposed, accepted, rejected, expired)
```

#### Files:
```
Backend:
├── app/Domains/Deals/Models/QuickDeal.php
├── app/Domains/Deals/Models/QuickDealRequest.php
├── app/Domains/Deals/Http/Controllers/QuickDealController.php
├── app/Events/DealProposed.php
└── database/migrations/xxxx_create_quick_deals_tables.php

Frontend:
├── resources/views/quick-deals/start.blade.php
├── resources/views/quick-deals/room.blade.php
└── app/Http/Livewire/DealProposal.php
```

**Estimated Time:** 8 hours

---

### Milestone 6.2: QR Code System [0/12]

#### Backend Tasks
- [ ] Install `simple-qrcode` package.
- [ ] Create `QRCode` model + migration in the `Deals` domain.
- [ ] Create QR generation endpoint in `QRCodeController`.
- [ ] Generate static QR for services.
- [ ] Generate dynamic QR for quick deals.
- [ ] Add QR scan/decode endpoint.
- [ ] Link QR to entities via `QRCodeService`.

#### Frontend Tasks
- [ ] Add "Generate QR" button on services.
- [ ] Display generated QR codes.
- [ ] Add QR scanner (use phone camera or library).
- [ ] Show QR on service detail page.
- [ ] Add download QR functionality.

#### Database:
```sql
qr_codes:
- id, code (unique token)
- qr_type (service, quick_deal)
- entity_id
- expires_at (for dynamic)
- created_at
```

#### Files:
```
Backend:
├── app/Domains/Deals/Models/QRCode.php
├── app/Domains/Deals/Services/QRCodeService.php
├── app/Domains/Deals/Http/Controllers/QRCodeController.php
└── database/migrations/xxxx_create_qr_codes_table.php

Frontend:
├── resources/views/components/qr-generator.blade.php
└── resources/views/quick-deals/scan.blade.php
```

**Estimated Time:** 6 hours

---

## 📦 PHASE 7: Admin & Analytics (Week 10)

### Milestone 7.1: Admin Dashboard [0/15]

#### Backend Tasks
- [ ] Create `DashboardController` in the `Admin` domain.
- [ ] Add platform statistics endpoint.
- [ ] Add analytics endpoint (users, orders, revenue).
- [ ] Add user management endpoints in `UserManagementController`.
- [ ] Add listing management endpoint.
- [ ] Add platform settings endpoint in `SettingsController`.
- [ ] Create admin middleware/gate.
- [ ] Add activity logs.

#### Frontend Tasks
- [ ] Create `resources/views/admin/dashboard.blade.php`.
- [ ] Add statistics cards (total users, orders, revenue).
- [ ] Create charts with Chart.js.
- [ ] Add recent activity feed.
- [ ] Create user management page.
- [ ] Create settings page.

#### Files:
```
Backend:
├── app/Domains/Admin/Http/Controllers/DashboardController.php
├── app/Domains/Admin/Http/Controllers/UserManagementController.php
├── app/Domains/Admin/Http/Controllers/SettingsController.php
└── app/Http/Middleware/EnsureUserIsAdmin.php

Frontend:
├── resources/views/admin/dashboard.blade.php
├── resources/views/admin/users.blade.php
├── resources/views/admin/settings.blade.php
└── resources/views/components/admin/stat-card.blade.php
```

**Estimated Time:** 8 hours

---

### Milestone 7.2: Search & Discovery [0/14]

#### Backend Tasks
- [ ] Install Laravel Scout (database driver for now).
- [ ] Make models searchable (Service, OpenOffer).
- [ ] Create `SearchController` in the `Search` domain.
- [ ] Add full-text search endpoint.
- [ ] Add location-based filtering (address).
- [ ] Add advanced filters (price range, category, ratings).
- [ ] Add sorting options.
- [ ] Optimize queries with eager loading.
- [ ] Add search suggestions.

#### Frontend Tasks
- [ ] Create `resources/views/search/index.blade.php`.
- [ ] Add a `SearchAutocomplete` Livewire component.
- [ ] Create a filter sidebar.
- [ ] Add a sorting dropdown.
- [ ] Show search results.
- [ ] Add "No results" state.

#### Files:
```
Backend:
├── app/Domains/Search/Http/Controllers/SearchController.php
└── app/Domains/Search/Services/SearchService.php

Frontend:
├── resources/views/search/index.blade.php
├── app/Http/Livewire/SearchAutocomplete.php
└── resources/views/components/filter-sidebar.blade.php
```

**Estimated Time:** 7 hours

---

### Milestone 7.3: Activity Logs & Audit Trail [0/8]

#### Backend Tasks
- [ ] Install `spatie/laravel-activitylog` package.
- [ ] Configure activity logging.
- [ ] Log critical actions (order creation, payment, disputes).
- [ ] Create activity log viewer in `ActivityLogController`.
- [ ] Add export functionality.

#### Frontend Tasks
- [ ] Create `resources/views/admin/activity-logs.blade.php`.
- [ ] Add filtering by user/action/date.
- [ ] Show activity timeline.

#### Files:
```
Backend:
├── config/activitylog.php (configure)
└── app/Domains/Admin/Http/Controllers/ActivityLogController.php

Frontend:
└── resources/views/admin/activity-logs.blade.php
```

**Estimated Time:** 4 hours

---

## 📦 PHASE 8: Polish & Optimization (Week 11)

### Milestone 8.1: Performance Optimization [0/12]

#### Backend Tasks
- [ ] Add database indexes (frequently queried columns)
- [ ] Implement Redis caching for categories, settings
- [ ] Add query optimization (N+1 prevention)
- [ ] Set up queue workers for jobs
- [ ] Add image optimization (intervention/image)
- [ ] Configure opcache for production
- [ ] Add API response caching

#### Frontend Tasks
- [ ] Optimize images (webp conversion)
- [ ] Add lazy loading for images
- [ ] Minify CSS/JS for production
- [ ] Add loading skeletons
- [ ] Optimize Alpine.js components

#### Files:
```
Backend:
├── config/cache.php (configure Redis)
├── app/Jobs/* (convert to queued)
└── database/migrations/xxxx_add_indexes.php

Frontend:
└── resources/views/components/skeleton-loader.blade.php
```

**Estimated Time:** 6 hours

---

### Milestone 8.2: SEO & Meta Tags [0/8]

#### Backend Tasks
- [ ] Add dynamic meta tags to Blade layouts
- [ ] Create sitemap generation
- [ ] Add robots.txt
- [ ] Implement Open Graph tags
- [ ] Add structured data (JSON-LD)

#### Frontend Tasks
- [ ] Update layout with dynamic meta
- [ ] Add social sharing preview
- [ ] Optimize page titles and descriptions

#### Files:
```
Backend:
├── app/Http/Controllers/SitemapController.php
└── public/robots.txt

Frontend:
├── resources/views/layouts/app.blade.php (update meta)
└── resources/views/components/social-meta.blade.php
```

**Estimated Time:** 4 hours

---

### Milestone 8.3: Email Notifications [0/10]

#### Backend Tasks
- [ ] Configure mail driver (Gmail/SendGrid)
- [ ] Create notification mailable classes
- [ ] Design email templates (Blade)
- [ ] Add notification preferences per user
- [ ] Queue email sending
- [ ] Test all notification emails

#### Notifications to Implement:
- [ ] Welcome email
- [ ] Bid placed/accepted
- [ ] Order created/completed
- [ ] Payment received
- [ ] Message received
- [ ] Verification approved
- [ ] Dispute filed

#### Files:
```
Backend:
├── app/Mail/WelcomeEmail.php
├── app/Mail/BidPlacedEmail.php
├── app/Mail/OrderCreatedEmail.php
└── resources/views/emails/* (templates)

Frontend:
└── resources/views/settings/notifications.blade.php
```

**Estimated Time:** 5 hours

---

### Milestone 8.4: Mobile Responsiveness [0/6]

#### Frontend Tasks
- [ ] Audit all pages on mobile
- [ ] Fix responsive issues
- [ ] Optimize touch interactions
- [ ] Test on multiple devices
- [ ] Add mobile-specific navigation
- [ ] Optimize for mobile performance

**Estimated Time:** 4 hours

---

### Milestone 8.5: Error Handling & User Feedback [0/8]

#### Backend Tasks
- [ ] Improve error messages (user-friendly)
- [ ] Add validation error translations
- [ ] Configure error logging (Sentry optional)
- [ ] Add health check endpoint

#### Frontend Tasks
- [ ] Create consistent error pages (404, 500, 403)
- [ ] Add toast notifications for actions
- [ ] Improve form validation feedback
- [ ] Add loading states everywhere

#### Files:
```
Backend:
├── resources/lang/en/validation.php (enhance)
└── app/Http/Controllers/HealthCheckController.php

Frontend:
├── resources/views/errors/404.blade.php
├── resources/views/errors/500.blade.php
├── resources/views/errors/403.blade.php
└── resources/views/components/toast.blade.php
```

**Estimated Time:** 4 hours

---

## 📦 PHASE 9: Testing & Documentation (Week 12)

### Milestone 9.1: Testing [0/15]

#### Backend Tests
- [ ] Set up testing database
- [ ] Write feature tests for API endpoints
- [ ] Write unit tests for services
- [ ] Test authentication flows
- [ ] Test payment flows
- [ ] Test order workflows
- [ ] Test real-time features
- [ ] Test admin functions

#### Frontend Tests
- [ ] Manual testing checklist
- [ ] Cross-browser testing
- [ ] Mobile device testing
- [ ] Performance testing

#### Files:
```
tests/Feature/
├── CategoryTest.php
├── ServiceTest.php
├── OpenOfferTest.php
├── BidTest.php
├── OrderTest.php
├── PaymentTest.php
└── WorkInstanceTest.php

tests/Unit/
├── ServiceServiceTest.php
├── PaymentServiceTest.php
└── QRCodeServiceTest.php
```

**Estimated Time:** 10 hours

---

### Milestone 9.2: Documentation [0/10]

#### Documentation Tasks
- [ ] Write API documentation (Postman/Swagger)
- [ ] Create user guide (PDF)
- [ ] Write admin manual
- [ ] Document deployment process
- [ ] Create developer setup guide
- [ ] Write backup/restore procedures
- [ ] Document payment integration
- [ ] Create troubleshooting guide
- [ ] Add inline code comments
- [ ] Create README.md

#### Files:
```
docs/
├── API.md
├── USER_GUIDE.md
├── ADMIN_MANUAL.md
├── DEPLOYMENT.md
├── DEVELOPMENT.md
└── TROUBLESHOOTING.md

README.md (update)
```

**Estimated Time:** 8 hours

---

### Milestone 9.3: Security Audit [0/10]

#### Security Tasks
- [ ] Review authentication implementation
- [ ] Check authorization (policies/gates)
- [ ] Audit input validation
- [ ] Test CSRF protection
- [ ] Review SQL injection prevention
- [ ] Check XSS prevention
- [ ] Audit file upload security
- [ ] Review API rate limiting
- [ ] Test for common vulnerabilities
- [ ] Add security headers

#### Files:
```
app/Http/Middleware/
└── SecurityHeaders.php

config/
└── security.php
```

**Estimated Time:** 6 hours

---

## 📦 PHASE 10: Deployment & Launch (Week 13)

### Milestone 10.1: Production Setup [0/15]

#### Infrastructure Tasks
- [ ] Choose hosting (Digital Ocean, AWS, or local VPS)
- [ ] Set up Ubuntu server
- [ ] Install LEMP stack (Linux, Nginx, MySQL, PHP 8.2+)
- [ ] Configure PHP-FPM
- [ ] Set up MySQL database
- [ ] Configure domain and DNS
- [ ] Install SSL certificate (Let's Encrypt)
- [ ] Configure Nginx virtual host
- [ ] Set up Redis
- [ ] Configure supervisor for queues
- [ ] Set up cron jobs
- [ ] Configure file permissions
- [ ] Test server setup

**Estimated Time:** 8 hours

---

### Milestone 10.2: Deployment Pipeline [0/10]

#### CI/CD Tasks
- [ ] Set up Git repository (GitHub/GitLab)
- [ ] Create deployment script
- [ ] Configure environment variables
- [ ] Set up database migrations
- [ ] Configure automated backups
- [ ] Set up staging environment
- [ ] Test deployment process
- [ ] Document deployment steps

#### Files:
```
deploy.sh
.env.production (template)
.github/workflows/deploy.yml (optional)
```

**Estimated Time:** 5 hours

---

### Milestone 10.3: Monitoring & Logging [0/8]

#### Monitoring Tasks
- [ ] Set up Laravel logs
- [ ] Configure log rotation
- [ ] Install monitoring tool (optional: New Relic, Sentry)
- [ ] Set up uptime monitoring
- [ ] Configure error alerting
- [ ] Add performance monitoring
- [ ] Set up database monitoring

**Estimated Time:** 4 hours

---

### Milestone 10.4: Pre-Launch Checklist [0/20]

#### Final Checks
- [ ] Test all critical user flows
- [ ] Verify payment integration (test transactions)
- [ ] Test email sending
- [ ] Verify real-time features work
- [ ] Check all forms and validations
- [ ] Test on multiple browsers
- [ ] Test on mobile devices
- [ ] Verify SSL certificate
- [ ] Test backup and restore
- [ ] Check error pages
- [ ] Verify SEO meta tags
- [ ] Test search functionality
- [ ] Check admin dashboard
- [ ] Verify user verification flow
- [ ] Test dispute resolution
- [ ] Check quick deals
- [ ] Test QR codes
- [ ] Verify all notifications
- [ ] Check performance (page load times)
- [ ] Final security review

**Estimated Time:** 8 hours

---

### Milestone 10.5: Beta Testing [0/8]

#### Beta Tasks
- [ ] Recruit 10-20 beta testers
- [ ] Create feedback form
- [ ] Monitor beta testing
- [ ] Collect feedback
- [ ] Fix critical bugs
- [ ] Implement high-priority suggestions
- [ ] Final regression testing
- [ ] Prepare for launch

**Estimated Time:** 2 weeks (ongoing)

---

### Milestone 10.6: Launch! 🚀 [0/10]

#### Launch Day Tasks
- [ ] Final database backup
- [ ] Deploy to production
- [ ] Verify all systems operational
- [ ] Monitor error logs
- [ ] Monitor performance
- [ ] Announce launch (social media, etc.)
- [ ] Monitor user registrations
- [ ] Respond to support requests
- [ ] Fix any critical issues immediately
- [ ] Celebrate! 🎉

---

## 📊 Summary Statistics

### Total Development Effort
- **Total Phases:** 10
- **Total Milestones:** 46
- **Total Tasks:** ~450
- **Estimated Time:** ~260 hours (6-7 weeks full-time)

### Phase Breakdown
1. **Phase 1:** Foundation & Core API (34 hours)
2. **Phase 2:** Order System (20 hours)
3. **Phase 3:** Real-time Features (18 hours)
4. **Phase 4:** Payments (24 hours)
5. **Phase 5:** Trust & Safety (24 hours)
6. **Phase 6:** Quick Deals (14 hours)
7. **Phase 7:** Admin & Analytics (19 hours)
8. **Phase 8:** Polish (23 hours)
9. **Phase 9:** Testing & Docs (24 hours)
10. **Phase 10:** Deployment (25 hours + 2 weeks beta)

---

## 🎯 Priority Levels

### P0 - Critical (Must Have for Launch)
- ✅ Phase 1: Core API (Services, Offers, Bids)
- ✅ Phase 2: Order System
- ✅ Phase 4: Payment Integration
- ✅ Phase 3.3: Messaging
- ✅ Phase 8.3: Email Notifications

### P1 - Important (Should Have)
- ✅ Phase 3.2: Real-time Notifications
- ✅ Phase 5.1-5.2: Verification & Reviews
- ✅ Phase 5.3-5.4: Disputes & Moderation
- ✅ Phase 7.2: Search

### P2 - Nice to Have (Can Be Post-Launch)
- 🔵 Phase 6: Quick Deals
- 🔵 Phase 7.1: Advanced Admin Dashboard
- 🔵 Phase 7.3: Activity Logs

---

## 📝 Technical Decisions Made

### Stack Confirmation
- **Backend:** Laravel 12 + PHP 8.2+
- **Frontend:** Blade + Alpine.js + Tailwind CSS
- **Database:** MySQL 8.0+
- **Cache:** Redis
- **Queue:** Redis
- **Real-time:** Laravel Broadcasting + Pusher/Laravel WebSockets
- **Payments:** Xendit (primary choice for Philippines)

### Architecture Patterns
- Domain-Driven Design (existing structure)
- Service Layer for business logic
- Policy-based authorization
- Event-driven for real-time features
- Queue-based for async tasks

### Key Business Rules
- **Platform Fee:** Added on top of service price (e.g., 5%)
- **Payment Flow:** Buyer pays (price + fee) → Escrow → Released to seller (price - 0) after completion
- **Refunds:** Manual bank transfers for now
- **Cancellation:** Allowed only if no work started (no activity in WorkInstance)
- **Verification:** Manual review by admin/moderator
- **Disputes:** Simple system with admin resolution

---

## 🎓 Learning Resources Needed

### New Technologies to Learn
- [ ] Laravel Broadcasting & Echo
- [ ] Payment Gateway Integration (Xendit API)
- [ ] QR Code Generation & Scanning
- [ ] Alpine.js Advanced Patterns
- [ ] Real-time UI Updates

### Recommended Reading
- Laravel Broadcasting Documentation
- Xendit API Documentation
- Alpine.js Essentials
- Laravel Queue Workers Best Practices

---

## 🚨 Risk Mitigation

### Potential Challenges
1. **Real-time Feature Complexity**
   - Mitigation: Start with polling, upgrade to WebSockets later

2. **Payment Integration Testing**
   - Mitigation: Use sandbox mode extensively

3. **Performance with Real-time**
   - Mitigation: Implement proper caching, optimize queries

4. **User Adoption**
   - Mitigation: Beta testing, gather feedback early

5. **Dispute Resolution Scaling**
   - Mitigation: Start manual, automate later

---

## 📅 Recommended Sprint Schedule

### Sprint 1 (Week 1): Foundation
- Milestone 1.1: API Infrastructure
- Milestone 1.2: Categories API
- Milestone 1.3: Services API (partial)

### Sprint 2 (Week 2): Core Listings
- Milestone 1.3: Services API (complete)
- Milestone 1.4: Open Offers API
- Milestone 1.5: Bidding System

### Sprint 3 (Week 3): Workflows & Orders
- Milestone 1.6: User Profile & Address
- Milestone 1.7: Workflow Management
- Milestone 2.1: Order System

### Sprint 4 (Week 4): Work Execution
- Milestone 2.2: Work Instances

### Sprint 5 (Week 5): Real-time
- Milestone 3.1: Broadcasting Setup
- Milestone 3.2: Notifications
- Milestone 3.3: Messaging

### Sprint 6 (Week 6-7): Payments
- Milestone 4.1: Payment Integration
- Milestone 4.2: Escrow
- Milestone 4.3: Refunds

### Sprint 7 (Week 8): Trust & Safety
- Milestone 5.1: Verification
- Milestone 5.2: Reviews
- Milestone 5.3: Disputes
- Milestone 5.4: Moderation

### Sprint 8 (Week 9): Quick Deals
- Milestone 6.1: Quick Deal Core
- Milestone 6.2: QR Codes

### Sprint 9 (Week 10): Admin & Polish
- Milestone 7.1: Admin Dashboard
- Milestone 7.2: Search
- Milestone 8.1: Performance

### Sprint 10 (Week 11): Final Polish
- Milestone 8.2-8.5: All polish tasks

### Sprint 11 (Week 12): Testing
- Milestone 9.1-9.3: All testing tasks

### Sprint 12 (Week 13+): Launch
- Milestone 10.1-10.6: Deployment & Launch

--- 

## 📊 Summary Statistics