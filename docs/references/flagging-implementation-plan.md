# Flagging System Implementation Plan

## Overview
Implement automated content moderation workflow where flagged content (OpenOffers, Services, Orders) triggers appropriate actions based on flag approval by admins.

## Goals
- **OpenOffers/Services**: Auto-suspend on flag approval, track creator violations
- **Orders**: Auto-initiate refund when flagged for fraud/dispute
- **Creator Accountability**: Monitor high-risk creators, escalate actions based on flag count

---

## Implementation Tasks

### Phase 1: Core Flag Approval Actions

#### Task 1.1: Update FlagManagementController::approve()
**Status**: NOT STARTED  
**File**: `app/Domains/Admin/Http/Controllers/FlagManagementController.php`

**Changes**:
- Check `flaggable_type` and route to appropriate handler
- For `OpenOffer`: suspend content
- For `Service`: suspend content  
- For `Order`: initiate auto-refund
- Increment creator flag count counter
- Log all automated actions

**Edge Cases**:
- What if content is already suspended/deleted? → Skip action, log as "already handled"
- What if creator has been banned? → Don't increment counter, notify admin
- What if order refund already exists? → Don't create duplicate, log as "refund exists"

---

#### Task 1.2: Create FlagActionService
**Status**: NOT STARTED  
**File**: `app/Domains/Admin/Services/FlagActionService.php` (NEW)

**Methods**:
- `suspendOpenOffer(OpenOffer $offer)` - Set status to suspended
- `suspendService(Service $service)` - Set status to suspended  
- `initiateOrderRefund(Order $order, Flag $flag)` - Create refund request
- `incrementCreatorFlagCount(User $creator)` - Track flags per creator
- `checkCreatorEscalation(User $creator)` - Determine if thresholds reached

**Edge Cases**:
- Suspended items can't be flagged again → Add check in flag creation
- Service soft-delete state → Verify soft-deleted services can't be flagged
- Order refunds with existing disputes → Don't create if disputed

---

### Phase 2: Creator Accountability

#### Task 2.1: Add Creator Flag Count Model
**Status**: NOT STARTED  
**File**: `app/Domains/Users/Models/CreatorFlagStats.php` (NEW)

**Fields**:
- `user_id` (FK)
- `total_flags` (counter)
- `flags_last_30_days` (counter)
- `last_flagged_at` (timestamp)
- `escalation_level` (0=none, 1=warned, 2=restricted, 3=banned)
- `escalation_triggered_at` (timestamp)

**Escalation Thresholds**:
- 1 flag → tracked
- 3 flags (30 days) → send warning email
- 5 flags (30 days) → restrict account (can't create new content)
- 10 flags total → permanent ban

---

#### Task 2.2: Update FlagManagementController::approve() with Escalation
**Status**: NOT STARTED  
**Updates to Task 1.1**

**New Logic**:
- After suspending content, check `CreatorFlagStats`
- If threshold reached:
  - Level 1 (3 flags): Update creator stats, log for admin
  - Level 2 (5 flags): Disable creator account creation, send admin alert
  - Level 3 (10 flags): Ban creator, soft-delete all content
  - Store escalation history for appeals

**Edge Cases**:
- New creators (no stats record) → Auto-create on first flag
- Rejected flags shouldn't count → Only approved flags increment count
- Flags older than 30 days → Don't count toward escalation

---

### Phase 3: Order Flagging & Auto-Refund

#### Task 3.1: Implement Order Auto-Refund on Flag Approval
**Status**: NOT STARTED  
**Updates to Task 1.2 (FlagActionService)**

**Method**: `initiateOrderRefund(Order $order, Flag $flag)`

**Logic**:
1. Check if order is refundable (payment paid, work not started)
2. Check if refund already exists → Skip if yes
3. Create Refund record with status `approved` (auto-approved by flag)
4. Set refund reason: `"Flagged for: {flag.category->value}"`
5. Update order status to `DISPUTED` or `REFUND_INITIATED`
6. Log action with timestamp

**Edge Cases**:
- Order already cancelled → Don't create refund, log as "order cancelled"
- Work already started → Can't refund, mark flag for manual review
- Payment failed → Don't create refund, log as "payment not received"
- Multiple flags on same order → Don't create duplicate refunds

---

### Phase 4: Admin UI & Visibility

#### Task 4.1: Add Flag History to Creator Profile
**Status**: NOT STARTED  
**File**: `resources/views/admin/users/show.blade.php` or dedicated tab

**Display**:
- Total flags received
- Flags in last 30 days
- List of recent flags (Date, Category, Reason, Status)
- Current escalation level with indicator
- Last flag timestamp

---

#### Task 4.2: Add Flags to Services & Orders Admin Index
**Status**: NOT STARTED  
**Files**: 
- `resources/views/admin/listings/index.blade.php` (Services)
- `resources/views/admin/orders/index.blade.php` (Orders)

**Add Columns**:
- Flag status indicator (pending, approved, rejected, none)
- Number of flags if 2+
- Click to view flag details modal

---

### Phase 5: Testing & Validation

#### Task 5.1: Create Feature Tests
**Status**: NOT STARTED  
**File**: `tests/Feature/Admin/FlagApprovalActionsTest.php` (NEW)

**Test Cases**:
1. Flag approved on OpenOffer → verify suspended
2. Flag approved on Service → verify suspended
3. Flag approved on Order → verify refund created
4. Creator reaches 3 flags → verify stats updated
5. Creator reaches 5 flags → verify restricted
6. Creator reaches 10 flags → verify banned
7. Suspended content can't be flagged again
8. Rejected flags don't increment counter

---

## Data Model Changes

### New Tables
- `creator_flag_stats` - Track creator violations

### New Columns
- `flags.creator_id` (derived from flaggable.creator_id) - Denormalize for queries
- `orders.flagged_at` (nullable) - Track flag timestamp
- `orders.flag_id` (nullable, FK) - Link to triggering flag

### Status Updates
- Service: Add explicit `suspended` enum value (currently implicit)
- Order: Add `DISPUTED`, `REFUND_INITIATED` enum values (or handle via refund)

---

## Error Handling & Rollback

### Scenarios
1. **Refund creation fails** → Log error, mark flag needs manual review, alert admin
2. **Content already deleted** → Log as "already handled", continue
3. **Creator stats corruption** → Add recovery script, notify admin
4. **Concurrent flag approvals** → Use DB locks to prevent race conditions

---

## Edge Cases Summary

| Case | Action | Result |
|------|--------|--------|
| Flag approved, content already deleted | Skip suspension | Log "already handled" |
| Multiple flags on same content | Only first flag triggers action | Others tracked for history |
| Creator already banned | Don't increment stats | Admin alerted |
| Order refund exists | Don't create duplicate | Log as "existing refund" |
| Work already started on order | Can't auto-refund | Mark for manual review |
| Suspended content re-flagged | Reject flag | Log as "already suspended" |
| New creator first flag | Create stats record | Start tracking |
| Rejected flag | Don't increment counter | Track separately |

---

## Success Criteria

✅ When approved, OpenOffers/Services are automatically suspended  
✅ When approved, Order flags trigger refund workflow  
✅ Creator flag counts are accurate and tracked  
✅ Escalation happens at defined thresholds  
✅ No duplicate refunds created  
✅ Admin can see creator violation history  
✅ All actions are logged for audit trail  
✅ Tests pass for all workflows  

---

## Dependencies

- Flag model with polymorphic relationship (✅ exists)
- OpenOffer and Service models with status fields (✅ exists)
- Order and Refund models (✅ exists)
- FlagManagementController (✅ exists, needs updates)
- CreatorFlagStats model (❌ NEW)

---

## Estimated Effort

- Phase 1 (Core Actions): 2 hours
- Phase 2 (Accountability): 2 hours  
- Phase 3 (Order Refunds): 1.5 hours
- Phase 4 (Admin UI): 1.5 hours
- Phase 5 (Testing): 2 hours
- **Total**: ~9 hours

