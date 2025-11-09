# Status & Plan: Milestones 1.3 - 1.5

This document summarizes the current implementation status of Milestones 1.3, 1.4, and 1.5 as of the last investigation.

---

## Milestone 1.3: Services Web CRUD & UI

**Status:** Partially Complete `[10/12]`

The core CRUD functionality for creators to manage their services is implemented, as is the public-facing view. Filtering, sorting, and pagination are now fully functional. Remaining tasks involve enhancing the user experience with a more robust image upload mechanism and handling soft deletes.

#### Backend Tasks
- [x] Create `ServiceController` for Web CRUD in the `Listings` domain.
- [x] Add resource routes for services CRUD under `/creator/services`.
- [x] Implement `ServicePolicy` for authorization (create, update, delete).
- [x] Add filtering, sorting, and pagination logic to the `index` method.
- [ ] **TODO:** Add image upload handling in the `ServiceService`.
- [ ] **TODO:** Handle soft deletes in all service queries.

#### Frontend Tasks
- [x] Create `resources/views/creator/services/index.blade.php`.
- [x] Enhance `resources/views/browse.blade.php` (serves as public listing).
- [x] Create `resources/views/creator/services/create.blade.php`.
- [x] Create `resources/views/creator/services/edit.blade.php`.
- [x] Create public-facing `resources/views/listings/show.blade.php`.
- [ ] **TODO:** Create a Livewire component for image uploads to provide a better UX.

---

## Milestone 1.4: Open Offers Web CRUD & UI

**Status:** Not Started `[0/12]`

No application-layer or UI components (controllers, routes, policies, views) have been implemented for this feature. While the `OpenOffer` model and migration exist, the logic to manage them is missing.

#### Backend Tasks
- [ ] **TODO:** Create `OpenOfferController` for Web CRUD in the `Listings` domain.
- [ ] **TODO:** Add routes for offers CRUD.
- [ ] **TODO:** Add "close offer" endpoint/method.
- [ ] **TODO:** Implement `OpenOfferPolicy`.
- [ ] **TODO:** Add auto-expiration job for offers (optional field).
- [ ] **TODO:** Handle offer fulfillment status changes.

#### Frontend Tasks
- [ ] **TODO:** Create `resources/views/offers/create.blade.php`.
- [ ] **TODO:** Create `resources/views/offers/edit.blade.php`.
- [ ] **TODO:** Create `resources/views/offers/show.blade.php`.
- [ ] **TODO:** Create a Livewire component for budget calculation or other interactive elements.
- [ ] **TODO:** Show bid count dynamically.
- [ ] **TODO:** Add close offer button (owner only, using a Livewire action).

---

## Milestone 1.5: Bidding System Web & UI

**Status:** Not Started `[0/14]`

Similar to Open Offers, no application-layer or UI components have been implemented for the bidding system. The `OpenOfferBid` model and migration exist, but the surrounding logic and UI are missing.

#### Backend Tasks
- [ ] **TODO:** Create `OpenOfferBidController` for handling bid actions.
- [ ] **TODO:** Add routes for bid CRUD.
- [ ] **TODO:** Add accept/reject bid methods.
- [ ] **TODO:** Validate: no duplicate bids, service owner matches bidder.
- [ ] **TODO:** Implement `BidPolicy`.
- [ ] **TODO:** Auto-close offer when bid accepted.
- [ ] **TODO:** Send notifications (email for now).

#### Frontend Tasks
- [ ] **TODO:** Add bid form to offer detail page (as a Livewire component).
- [ ] **TODO:** Create a `BidList` Livewire component.
- [ ] **TODO:** Add accept/reject buttons with `wire:click` actions.
- [ ] **TODO:** Show bid status badges.
- [ ] **TODO:** Add "My Bids" section to the user dashboard.
- [ ] **TODO:** Create a view for the bid list.
