# GEMINI.md

## Project Overview

This is a Laravel project that appears to be a service marketplace application. It uses a domain-driven design approach, with clear separation of concerns between different domains like `Listings` and `Users`. The application includes features like user authentication, service listings, open offers, and a creator space. The front-end is built with Vite, Tailwind CSS, and Alpine.js.

## Project Progress

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

**Phase 1: Foundation & Core API - Milestone 1.1: API Infrastructure Setup:**
*   Create `app/Http/Controllers/Api/ApiController.php` (base)
*   Update `app/Exceptions/Handler.php` for JSON error responses
*   Create `app/Http/Middleware/ForceJsonResponse.php`
*   Set up `routes/api.php` with web middleware (use session auth)
*   Configure CORS in `config/cors.php`
*   Add API rate limiting
*   Create response helper traits
*   Test JSON error responses

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

*   **Clarification First:** Before implementing any feature or making a change, take a moment to clarify the requirements with the user. This ensures that we are building the right thing and avoids rework later. This is a crucial step to ensure we build everything right and well, even if it takes a little more time upfront.

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
- **Backend:** Laravel 11 with Domain-Driven Design
- **Frontend:** Blade + Alpine.js + Tailwind CSS (existing setup)
- **Real-time:** Laravel Broadcasting + Laravel Echo
- **Payments:** Xendit with manual disbursement
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

## 📦 PHASE 1: Foundation & Core API (Week 1-2)

### Milestone 1.1: API Infrastructure Setup [8/8]

#### Files to Create:
```
app/Http/Controllers/Api/
├── ApiController.php
└── .gitkeep

app/Http/Middleware/
└── ForceJsonResponse.php

app/Traits/
└── ApiResponses.php
```

**Estimated Time:** 3 hours

---

### Milestone 1.2: Categories API [6/6]
**Goal:** Complete CRUD API for categories (foundation for listings)

#### Backend Tasks
- [x] Create `CategoryController` API (extends existing functionality)
- [x] Add routes: GET, POST, PUT, DELETE `/api/categories`
- [x] Add filtering/search query parameters
- [x] Create `CategoryResource` for JSON transformation
- [x] Add authorization (admin only for write)
- [ ] Test with Postman/browser console

#### Frontend Tasks
- [ ] Update existing category filter to use AJAX
- [ ] Add Alpine.js component for category management (admin)
- [ ] Show loading states with Alpine

#### Files:
```
app/Http/Controllers/Api/
└── CategoryController.php

app/Http/Resources/
├── CategoryResource.php
└── CategoryCollection.php

resources/views/components/admin/
└── category-manager.blade.php
```

**Estimated Time:** 4 hours

---

### Milestone 1.3: Services API & UI Enhancement [0/12]

#### Backend Tasks
- [ ] Create `ServiceController` API (full CRUD)
- [ ] Add routes for services CRUD
- [ ] Add image upload endpoint `/api/services/{id}/images`
- [ ] Create `ServiceResource` with relationships
- [ ] Add filtering (category, location, price range, search)
- [ ] Add sorting (price, created_at, rating)
- [ ] Implement `ServicePolicy` for authorization
- [ ] Handle soft deletes properly

#### Frontend Tasks
- [ ] Enhance `resources/views/browse.blade.php` with AJAX
- [ ] Create `resources/views/services/create.blade.php`
- [ ] Create `resources/views/services/edit.blade.php`
- [ ] Create `resources/views/services/show.blade.php`
- [ ] Add Alpine.js image uploader component
- [ ] Add real-time filter updates
- [ ] Show loading states and skeletons

#### Files:
```
Backend:
├── app/Http/Controllers/Api/ServiceController.php
├── app/Http/Resources/ServiceResource.php
├── app/Policies/ServicePolicy.php
└── app/Http/Requests/StoreServiceRequest.php

Frontend:
├── resources/views/services/create.blade.php
├── resources/views/services/edit.blade.php
├── resources/views/services/show.blade.php
├── resources/views/components/service-form.blade.php
└── resources/views/components/image-uploader.blade.php
```

**Estimated Time:** 8 hours

---

### Milestone 1.4: Open Offers API & UI [0/12]

#### Backend Tasks
- [ ] Create `OpenOfferController` API (full CRUD)
- [ ] Add routes for offers CRUD
- [ ] Add "close offer" endpoint
- [ ] Create `OpenOfferResource`
- [ ] Add filtering and search
- [ ] Implement `OpenOfferPolicy`
- [ ] Add auto-expiration job (optional field)
- [ ] Handle offer fulfillment status

#### Frontend Tasks
- [ ] Create `resources/views/offers/create.blade.php`
- [ ] Create `resources/views/offers/edit.blade.php`
- [ ] Create `resources/views/offers/show.blade.php`
- [ ] Add Alpine.js budget calculator
- [ ] Show bid count dynamically
- [ ] Add close offer button (owner only)

#### Files:
```
Backend:
├── app/Http/Controllers/Api/OpenOfferController.php
├── app/Http/Resources/OpenOfferResource.php
├── app/Policies/OpenOfferPolicy.php
└── app/Jobs/CloseExpiredOffers.php (optional)

Frontend:
├── resources/views/offers/create.blade.php
├── resources/views/offers/edit.blade.php
├── resources/views/offers/show.blade.php
└── resources/views/components/offer-form.blade.php
```

**Estimated Time:** 8 hours

---

### Milestone 1.5: Bidding System API & UI [0/14]

#### Backend Tasks
- [ ] Create `OpenOfferBidController` API
- [ ] Add routes for bid CRUD
- [ ] Add accept/reject bid endpoints
- [ ] Create `OpenOfferBidResource`
- [ ] Validate: no duplicate bids, service owner matches bidder
- [ ] Implement `BidPolicy`
- [ ] Auto-close offer when bid accepted
- [ ] Send notifications (email for now)

#### Frontend Tasks
- [ ] Add bid form to offer detail page
- [ ] Create `resources/views/components/bid-list.blade.php`
- [ ] Add accept/reject buttons (Alpine.js)
- [ ] Show bid status badges
- [ ] Add "My Bids" section to dashboard
- [ ] Show bid notifications

#### Files:
```
Backend:
├── app/Http/Controllers/Api/OpenOfferBidController.php
├── app/Http/Resources/OpenOfferBidResource.php
├── app/Policies/BidPolicy.php
└── app/Notifications/BidPlacedNotification.php

Frontend:
├── resources/views/components/bid-form.blade.php
├── resources/views/components/bid-list.blade.php
└── resources/views/dashboard/bids.blade.php
```

**Estimated Time:** 6 hours

---

### Milestone 1.6: User Profile & Address API [0/10]

#### Backend Tasks
- [ ] Create `ProfileController` API (update existing)
- [ ] Create `AddressController` API
- [ ] Add address CRUD endpoints
- [ ] Add "set primary" endpoint
- [ ] Create `AddressResource`
- [ ] Add public profile endpoint

#### Frontend Tasks
- [ ] Update `resources/views/profile/edit.blade.php` to use AJAX
- [ ] Create address manager component (Alpine.js)
- [ ] Add address form modal
- [ ] Show user's services/offers on public profile

#### Files:
```
Backend:
├── app/Http/Controllers/Api/ProfileController.php
├── app/Http/Controllers/Api/AddressController.php
└── app/Http/Resources/AddressResource.php

Frontend:
├── resources/views/components/address-manager.blade.php
└── resources/views/profile/public.blade.php
```

**Estimated Time:** 5 hours

---

### Milestone 1.7: Workflow Management API & UI [0/15]

#### Backend Tasks
- [ ] Create `WorkflowTemplateController` API
- [ ] Create `WorkTemplateController` API
- [ ] Create `WorkCatalogController` API
- [ ] Add all necessary resources
- [ ] Add step reordering endpoint
- [ ] Handle public/private workflows
- [ ] Add workflow duplication feature

#### Frontend Tasks
- [ ] Create `resources/views/workflows/index.blade.php`
- [ ] Create `resources/views/workflows/builder.blade.php`
- [ ] Add drag-drop step builder (Alpine.js + Sortable.js)
- [ ] Create step editor component
- [ ] Add workflow selector for services/offers

#### Files:
```
Backend:
├── app/Http/Controllers/Api/WorkflowTemplateController.php
├── app/Http/Controllers/Api/WorkTemplateController.php
├── app/Http/Controllers/Api/WorkCatalogController.php
└── app/Http/Resources/WorkflowTemplateResource.php

Frontend:
├── resources/views/workflows/index.blade.php
├── resources/views/workflows/builder.blade.php
└── resources/views/components/workflow-builder.blade.php
```

**Estimated Time:** 10 hours

---

## 📦 PHASE 2: Order System & Execution (Week 3-4)

### Milestone 2.1: Order System Foundation [0/16]

#### Backend Tasks
- [ ] Create `Order` model + migration
- [ ] Create `OrderController` API
- [ ] Add create order endpoint (from accepted bid)
- [ ] Add order status enum (pending, in_progress, completed, cancelled, disputed)
- [ ] Add cancel order endpoint (if no work started)
- [ ] Implement order state machine
- [ ] Create `OrderPolicy`
- [ ] Send order notifications (email)

#### Frontend Tasks
- [ ] Create `resources/views/orders/index.blade.php`
- [ ] Create `resources/views/orders/show.blade.php`
- [ ] Add order status timeline component
- [ ] Add cancel button (if eligible)
- [ ] Show order details clearly
- [ ] Add "My Orders" dashboard section
- [ ] Create order creation flow from bid acceptance

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
├── app/Models/Order.php
├── app/Http/Controllers/Api/OrderController.php
├── app/Http/Resources/OrderResource.php
├── app/Policies/OrderPolicy.php
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
- [ ] Create `WorkInstance` model + migration
- [ ] Clone workflow on order creation
- [ ] Create `WorkInstanceController` API
- [ ] Add start/complete step endpoints
- [ ] Add work instance timeline endpoint
- [ ] Create `ActivityThread` model (discussions per step)
- [ ] Create `ActivityMessage` model
- [ ] Add activity CRUD endpoints
- [ ] Add file upload to activities
- [ ] Send activity notifications

#### Frontend Tasks
- [ ] Create `resources/views/work/show.blade.php`
- [ ] Add step-by-step progress UI
- [ ] Create activity thread component (per step)
- [ ] Add file upload component
- [ ] Show real-time progress
- [ ] Add notes/comment section
- [ ] Create work dashboard for sellers
- [ ] Add buyer work monitoring view

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
├── app/Models/WorkInstance.php
├── app/Models/WorkInstanceStep.php
├── app/Models/ActivityThread.php
├── app/Models/ActivityMessage.php
├── app/Http/Controllers/Api/WorkInstanceController.php
└── app/Http/Controllers/Api/ActivityController.php

Frontend:
├── resources/views/work/show.blade.php
├── resources/views/components/work-progress.blade.php
├── resources/views/components/activity-thread.blade.php
└── resources/views/components/file-uploader.blade.php
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
- [ ] Create notification system (database notifications)
- [ ] Create `Notification` component
- [ ] Create events: `BidPlaced`, `BidAccepted`, `OrderCreated`
- [ ] Create events: `WorkStepCompleted`, `ActivityMessageSent`
- [ ] Broadcast to private user channels
- [ ] Add mark as read endpoint
- [ ] Add notification preferences

#### Frontend Tasks
- [ ] Add notification dropdown (navbar)
- [ ] Listen for notifications in real-time
- [ ] Show toast/banner for new notifications
- [ ] Add unread count badge
- [ ] Create notifications page
- [ ] Add notification sound (optional)

#### Files:
```
Backend:
├── app/Events/BidPlaced.php
├── app/Events/BidAccepted.php
├── app/Events/OrderCreated.php
├── app/Events/WorkStepCompleted.php
├── app/Http/Controllers/Api/NotificationController.php
└── database/migrations/xxxx_create_notifications_table.php

Frontend:
├── resources/views/components/notification-dropdown.blade.php
└── resources/views/notifications/index.blade.php
```

**Estimated Time:** 6 hours

---

### Milestone 3.3: Messaging System [0/18]

#### Backend Tasks
- [ ] Create `MessageThread` model + migration
- [ ] Create `Message` model + migration
- [ ] Create `MessageAttachment` model
- [ ] Create `MessageController` API
- [ ] Add create thread endpoint
- [ ] Add send message endpoint
- [ ] Add mark as read endpoint
- [ ] Add message listing with pagination
- [ ] Broadcast `MessageSent` event
- [ ] Add file attachment handling

#### Frontend Tasks
- [ ] Create `resources/views/messages/index.blade.php`
- [ ] Create thread list component
- [ ] Create chat interface (Alpine.js)
- [ ] Listen for real-time messages
- [ ] Add file attachment preview
- [ ] Show unread count
- [ ] Add message search
- [ ] Link from orders/bids

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
├── app/Models/MessageThread.php
├── app/Models/Message.php
├── app/Models/MessageAttachment.php
├── app/Http/Controllers/Api/MessageController.php
├── app/Events/MessageSent.php
└── database/migrations/xxxx_create_messages_tables.php

Frontend:
├── resources/views/messages/index.blade.php
├── resources/views/components/message-thread.blade.php
└── resources/views/components/chat-interface.blade.php
```

**Estimated Time:** 8 hours

---

## 📦 PHASE 4: Payments & Financial (Week 6-7)

### Milestone 4.1: Payment Integration [0/18]

#### Backend Tasks
- [ ] Choose provider (Xendit recommended for PH)
- [ ] Set up Xendit/PayMongo account
- [ ] Install payment SDK via Composer
- [ ] Create `Payment` model + migration
- [ ] Create `PaymentController` API
- [ ] Add create payment intent endpoint
- [ ] Add webhook endpoint for payment status
- [ ] Handle payment callbacks
- [ ] Create payment verification logic
- [ ] Calculate platform fee (e.g., 5% on top of service price)
- [ ] Update order payment_status on success

#### Frontend Tasks
- [ ] Create `resources/views/payments/checkout.blade.php`
- [ ] Add payment method selector (GCash, Card, BankTransfer)
- [ ] Integrate payment SDK (Xendit checkout)
- [ ] Show payment instructions
- [ ] Create payment success page
- [ ] Create payment failed page
- [ ] Show payment history

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
├── app/Services/PaymentService.php
├── app/Models/Payment.php
├── app/Http/Controllers/Api/PaymentController.php
├── app/Http/Controllers/PaymentWebhookController.php
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
- [ ] Create `Disbursement` model + migration
- [ ] Hold payment in escrow on order creation
- [ ] Add release payment endpoint (buyer confirms work)
- [ ] Calculate platform fee deduction
- [ ] Create manual disbursement dashboard (admin)
- [ ] Add earnings calculation per seller
- [ ] Track pending disbursements
- [ ] Create disbursement request system
- [ ] Send disbursement notifications

#### Frontend Tasks
- [ ] Show escrow status in order detail
- [ ] Add "Release Payment" button (buyer, after work completed)
- [ ] Create `resources/views/earnings/index.blade.php` (seller)
- [ ] Show pending balance
- [ ] Show disbursement history
- [ ] Add request payout button
- [ ] Show earnings timeline

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
├── app/Models/Disbursement.php
├── app/Http/Controllers/Api/DisbursementController.php
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
- [ ] Create `Refund` model + migration
- [ ] Add refund request endpoint
- [ ] Add approve/reject refund endpoint (admin)
- [ ] Handle order cancellation (before work starts)
- [ ] Process refund via bank transfer (manual for now)
- [ ] Update order and payment status
- [ ] Send refund notifications

#### Frontend Tasks
- [ ] Add cancel order button (if no work started)
- [ ] Add refund request form
- [ ] Show refund status
- [ ] Create admin refund management page
- [ ] Show refund history

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
├── app/Models/Refund.php
├── app/Http/Controllers/Api/RefundController.php
└── database/migrations/xxxx_create_refunds_table.php

Frontend:
├── resources/views/components/refund-form.blade.php
└── resources/views/admin/refunds.blade.php
```

**Estimated Time:** 6 hours

---

## 📦 PHASE 5: Trust & Safety (Week 8)

### Milestone 5.1: User Verification System [0/14]

#### Backend Tasks
- [ ] Create `UserVerification` model + migration
- [ ] Add ID upload endpoint
- [ ] Add verification status to users table
- [ ] Create admin verification review endpoint
- [ ] Add approve/reject verification
- [ ] Send verification notifications
- [ ] Add "verified" badge logic

#### Frontend Tasks
- [ ] Create `resources/views/verification/submit.blade.php`
- [ ] Add ID upload form (front & back)
- [ ] Show verification status on profile
- [ ] Add verified badge to listings
- [ ] Create admin verification queue
- [ ] Add verification review page (admin)

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

#### Files:
```
Backend:
├── app/Models/UserVerification.php
├── app/Http/Controllers/Api/VerificationController.php
└── database/migrations/xxxx_create_user_verifications_table.php

Frontend:
├── resources/views/verification/submit.blade.php
├── resources/views/verification/status.blade.php
└── resources/views/admin/verifications.blade.php
```

**Estimated Time:** 6 hours

---

### Milestone 5.2: Reviews & Ratings [0/16]

#### Backend Tasks
- [ ] Create `ListingReview` model + migration
- [ ] Create `UserReview` model + migration
- [ ] Create `ReviewController` API
- [ ] Add submit review endpoint (after order completion)
- [ ] Prevent duplicate reviews
- [ ] Calculate average ratings (services & users)
- [ ] Add review moderation flags
- [ ] Update rating on models

#### Frontend Tasks
- [ ] Create review submission form
- [ ] Add star rating component (Alpine.js)
- [ ] Display reviews on service page
- [ ] Display reviews on user profile
- [ ] Add review filtering
- [ ] Show average rating badges
- [ ] Add "Leave Review" prompt after order

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
├── app/Models/ListingReview.php
├── app/Models/UserReview.php
├── app/Http/Controllers/Api/ReviewController.php
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
- [ ] Create `Dispute` model + migration
- [ ] Add file dispute endpoint
- [ ] Add dispute statuses (open, under_review, resolved, closed)
- [ ] Add dispute response endpoint
- [ ] Create admin dispute management
- [ ] Link disputes to orders
- [ ] Send dispute notifications

#### Frontend Tasks
- [ ] Add "File Dispute" button on orders
- [ ] Create dispute submission form
- [ ] Show dispute status timeline
- [ ] Add dispute chat/thread
- [ ] Create admin dispute dashboard

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
├── app/Models/Dispute.php
├── app/Http/Controllers/Api/DisputeController.php
└── database/migrations/xxxx_create_disputes_table.php

Frontend:
├── resources/views/components/dispute-form.blade.php
└── resources/views/admin/disputes.blade.php
```

**Estimated Time:** 6 hours

---

### Milestone 5.4: Content Moderation [0/10]

#### Backend Tasks
- [ ] Create `Report` model + migration
- [ ] Add report listing endpoint
- [ ] Add flag reasons (spam, inappropriate, fraud, etc.)
- [ ] Create admin moderation dashboard
- [ ] Add hide/remove listing actions
- [ ] Add ban user action
- [ ] Send moderation notifications

#### Frontend Tasks
- [ ] Add "Report" button on listings
- [ ] Create report modal
- [ ] Show report status
- [ ] Create admin moderation queue

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
├── app/Models/Report.php
├── app/Http/Controllers/Api/ReportController.php
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
- [ ] Create `QuickDeal` model + migration
- [ ] Create `QuickDealRequest` model + migration
- [ ] Create `QuickDealController` API
- [ ] Add create deal endpoint
- [ ] Add propose service endpoint
- [ ] Add accept/reject proposal endpoints
- [ ] Add deal expiration logic (30 min default)
- [ ] Broadcast real-time proposals
- [ ] Convert accepted deal to order

#### Frontend Tasks
- [ ] Create `resources/views/quick-deals/start.blade.php`
- [ ] Create deal room interface
- [ ] Add service proposal form
- [ ] Show live proposals (Alpine + Echo)
- [ ] Add countdown timer
- [ ] Create deal history page
- [ ] Add QR code scanner integration

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
├── app/Models/QuickDeal.php
├── app/Models/QuickDealRequest.php
├── app/Http/Controllers/Api/QuickDealController.php
├── app/Events/DealProposed.php
└── database/migrations/xxxx_create_quick_deals_tables.php

Frontend:
├── resources/views/quick-deals/start.blade.php
├── resources/views/quick-deals/room.blade.php
└── resources/views/components/deal-proposal.blade.php
```

**Estimated Time:** 8 hours

---

### Milestone 6.2: QR Code System [0/12]

#### Backend Tasks
- [ ] Install `simple-qrcode` package
- [ ] Create `QRCode` model + migration
- [ ] Create QR generation endpoint
- [ ] Generate static QR for services
- [ ] Generate dynamic QR for quick deals
- [ ] Add QR scan/decode endpoint
- [ ] Link QR to entities

#### Frontend Tasks
- [ ] Add "Generate QR" button on services
- [ ] Display generated QR codes
- [ ] Add QR scanner (use phone camera or library)
- [ ] Show QR on service detail page
- [ ] Add download QR functionality

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
├── app/Models/QRCode.php
├── app/Services/QRCodeService.php
├── app/Http/Controllers/Api/QRCodeController.php
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
- [ ] Create `AdminController`
- [ ] Add platform statistics endpoint
- [ ] Add analytics endpoint (users, orders, revenue)
- [ ] Add user management endpoints (ban, unban, verify)
- [ ] Add listing management endpoint
- [ ] Add platform settings endpoint
- [ ] Create admin middleware/gate
- [ ] Add activity logs

#### Frontend Tasks
- [ ] Create `resources/views/admin/dashboard.blade.php`
- [ ] Add statistics cards (total users, orders, revenue)
- [ ] Create charts with Chart.js
- [ ] Add recent activity feed
- [ ] Create user management page
- [ ] Create settings page

#### Files:
```
Backend:
├── app/Http/Controllers/Admin/DashboardController.php
├── app/Http/Controllers/Admin/UserManagementController.php
├── app/Http/Controllers/Admin/SettingsController.php
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
- [ ] Install Laravel Scout (database driver for now)
- [ ] Make models searchable (Service, OpenOffer)
- [ ] Create `SearchController` API
- [ ] Add full-text search endpoint
- [ ] Add location-based filtering (address)
- [ ] Add advanced filters (price range, category, ratings)
- [ ] Add sorting options
- [ ] Optimize queries with eager loading
- [ ] Add search suggestions

#### Frontend Tasks
- [ ] Create `resources/views/search/index.blade.php`
- [ ] Add search autocomplete (Alpine.js)
- [ ] Create filter sidebar
- [ ] Add sorting dropdown
- [ ] Show search results
- [ ] Add "No results" state

#### Files:
```
Backend:
├── app/Http/Controllers/Api/SearchController.php
└── app/Services/SearchService.php

Frontend:
├── resources/views/search/index.blade.php
├── resources/views/components/search-autocomplete.blade.php
└── resources/views/components/filter-sidebar.blade.php
```

**Estimated Time:** 7 hours

---

### Milestone 7.3: Activity Logs & Audit Trail [0/8]

#### Backend Tasks
- [ ] Install `spatie/laravel-activitylog` package
- [ ] Configure activity logging
- [ ] Log critical actions (order creation, payment, disputes)
- [ ] Create activity log viewer (admin)
- [ ] Add export functionality

#### Frontend Tasks
- [ ] Create `resources/views/admin/activity-logs.blade.php`
- [ ] Add filtering by user/action/date
- [ ] Show activity timeline

#### Files:
```
Backend:
├── config/activitylog.php (configure)
└── app/Http/Controllers/Admin/ActivityLogController.php

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

### Total Development Effort (2-Week MVP)
- **Total Phases:** 5 (1, 2, 3, 4, 8)
- **Total Milestones:** 12
- **Total Tasks:** ~130
- **Estimated Time:** ~91 hours (2 weeks full-time)

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
- **Backend:** Laravel 12.28.1 + PHP 8.2+
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

## 📅 Accelerated 2-Week MVP Schedule

**Goal:** Deliver a functional core marketplace including APIs, ordering, messaging, and payment integration within a two-week timeframe.

---

### **WEEK 1: Core Platform & Communication (Est. 47 hours)**

*   **Focus:** Build the foundational APIs for all marketplace entities and establish core communication channels.

*   **Milestones:**
    1.  **Phase 1: Foundation & Core API (34 hours)**
        *   M1.1: API Infrastructure Setup
        *   M1.2: Categories API
        *   M1.3: Services API & UI Enhancement
        *   M1.4: Open Offers API & UI
        *   M1.5: Bidding System API & UI
        *   M1.6: User Profile & Address API
    2.  **Phase 3.3: Messaging System (8 hours)**
    3.  **Phase 8.3: Email Notifications (5 hours)**

---

### **WEEK 2: Transactions & Fulfillment (Est. 44 hours)**

*   **Focus:** Implement the entire transaction lifecycle, from order creation and work execution to payment and disbursement.

*   **Milestones:**
    1.  **Phase 2: Order System & Execution (20 hours)**
        *   M2.1: Order System Foundation
        *   M2.2: Work Instance Execution
    2.  **Phase 4: Payments & Financial (24 hours)**
        *   M4.1: Payment Integration (Xendit)
        *   M4.2: Escrow & Disbursement
        *   M4.3: Refunds & Cancellations

---

This schedule prioritizes the P0 features and is achievable if we maintain a high pace. All other items (P1 and P2) will be considered post-launch features.

---

## ✅ Next Steps

**Ready to begin Phase 1, Milestone 1.1?**

I'll create:
1. Base `ApiController` with response helpers
2. Updated `Handler.php` for JSON exceptions
3. `ForceJsonResponse` middleware
4. API routes structure
5. CORS configuration

**Type "Let's start Phase 1.1" when ready!** 🚀