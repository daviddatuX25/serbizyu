# Admin System Implementation Checklist

## Phase 1: Database & Models
- [ ] Create `flags` table migration for flagging Services/OpenOffers
- [ ] Create `Flag` model with relationships to flaggable content
- [ ] Create `FlagCategory` enum for flag types (spam, inappropriate, fraud, etc.)
- [ ] Add flag-related accessors/mutators to Service and OpenOffer models

## Phase 2: Admin Controllers
- [ ] Implement full `UserManagementController` (CRUD + role assignment)
- [ ] Implement full `ListingManagementController` (Services + OpenOffers CRUD)
- [ ] Create `OpenOfferManagementController` (separate from Services)
- [ ] Create `OrderManagementController` (view, filter, status updates)
- [ ] Create `PaymentManagementController` (view, filter, status tracking)
- [ ] Create `RefundManagementController` (view, approve, reject, process)
- [ ] Enhance `UserVerificationController` (admin view, approve, reject with documents)
- [ ] Create `FlagManagementController` (view, approve, resolve, reject flags)
- [ ] Enhance `DashboardController` (stats, pending items, overview)
- [ ] Create `CategoryManagementController` (CRUD for listing categories)

## Phase 3: Admin Authorization Policies
- [ ] Create/enhance `UserPolicy` for admin access
- [ ] Create `ServicePolicy` for admin management
- [ ] Create `OpenOfferPolicy` for admin management
- [ ] Create `OrderPolicy` for admin viewing/updating
- [ ] Create `PaymentPolicy` for admin viewing
- [ ] Create `RefundPolicy` for admin approval workflow
- [ ] Create `UserVerificationPolicy` enhancements
- [ ] Create `FlagPolicy` for admin flag management

## Phase 4: Admin Routes
- [ ] Add admin resource routes for all management controllers
- [ ] Add custom action routes (approve, reject, flag, resolve)
- [ ] Add filter/search routes for better data management
- [ ] Register all routes in web.php under admin middleware

## Phase 5: Admin Views (Blade)
- [ ] Create `resources/views/admin/dashboard/index.blade.php` (enhanced stats)
- [ ] Create users CRUD views (index, show, edit)
- [ ] Create services CRUD views (index, show, edit)
- [ ] Create open offers CRUD views (index, show, edit)
- [ ] Create orders management views (index, show)
- [ ] Create payments management views (index, show)
- [ ] Create refunds management views (index, show, approve/reject forms)
- [ ] Create verifications management views (index, show with documents, approve/reject)
- [ ] Create flags management views (index, show, resolution interface)
- [ ] Create categories CRUD views (index, create, edit)
- [ ] Create shared admin layout/components for consistency

## Phase 6: Admin Features
- [ ] Implement user role assignment (admin/moderator/user)
- [ ] Implement service/offer flagging workflow
- [ ] Implement verification document viewing
- [ ] Implement admin approval workflows (flag resolution, verification review)
- [ ] Implement activity logging for admin actions
- [ ] Implement search/filter functionality on all admin tables
- [ ] Implement pagination on all admin lists

## Phase 7: Testing
- [ ] Write feature tests for user management CRUD
- [ ] Write feature tests for listing management CRUD
- [ ] Write feature tests for order management views
- [ ] Write feature tests for payment management views
- [ ] Write feature tests for refund approval workflow
- [ ] Write feature tests for verification approval workflow
- [ ] Write feature tests for flag management workflow
- [ ] Write feature tests for admin authorization (policies)
- [ ] Write feature tests for activity logging
- [ ] Run all admin tests and verify passing

## Phase 8: Integration & Polish
- [ ] Verify all admin routes are protected with admin middleware
- [ ] Test admin dashboard loading and displaying data correctly
- [ ] Verify search/filter functionality on all admin views
- [ ] Test pagination on all admin lists
- [ ] Verify role-based authorization on sensitive operations
- [ ] Test admin action confirmations and success messages
- [ ] Verify admin activity logging captures all significant actions
- [ ] Code formatting with Pint

## Tasks Summary
- **Total Controllers**: 10 (partial or new implementations)
- **Total Views**: 20+ (index, show, edit, action forms)
- **Total Policies**: 8 (create or enhance)
- **Total Tests**: 10+ feature test classes
- **Routes**: ~80+ admin routes total
- **Models**: 1 new (Flag model)
- **Migrations**: 1 new (flags table)

---

## Implementation Notes

### Flagging System
- Flags are created when users/admins report content
- Admin can view flagged items with reason/evidence
- Admin can approve (take action), reject (false report), or resolve (already handled)
- Flagged listings can be hidden/suspended based on flag type

### Verification System
- Enhanced with document viewing in admin panel
- Approvals/rejections with optional admin notes
- Activity tracked for audit trail
- Status tracking: pending, approved, rejected, expired

### Admin Dashboard
- Pending verifications count
- Flagged content count
- Recent transactions overview
- User activity stats
- System health/performance stats

### Search & Filtering
- All admin tables searchable by relevant fields
- Filter by date ranges, status, user type
- Sortable columns
- Export functionality (future enhancement)

