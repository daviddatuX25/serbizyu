### Current Task: Milestone 1.6 - AddressManager Livewire Component

**1. Scope & UI (Modal Form)**
-   **Entities:** `User`, `Address`, `UserAddress` (Existing).
-   **Permissions:** User can only manage their own addresses.
-   **UI:** List of addresses, with buttons for "Add", "Edit", "Delete", "Set Primary". Add/Edit form will be in a modal.

**2. Flow & Logic**
-   `mount()`: Load user's addresses via `AddressService`.
-   `save()`: Create/Update address.
-   `edit($id)`: Load address into form.
-   `delete($id)`: Remove address (with confirmation).
-   `setPrimary($id)`: Set primary address.

**3. Service Layer**
-   **Action:** Create `AddressService.php` with methods for `createAddressForUser`, `deleteUserAddress`, and `setPrimaryAddress`. Authorization logic will be placed here.

**4. Frontend Implementation**
-   Generate Livewire component.
-   Build Blade view (`address-manager.blade.php`).
-   Implement component logic (`AddressManager.php`).
-   Embed in profile page (`profile/edit.blade.php`).

**5. Testing**
-   Create Livewire test file.
-   Write tests for all user actions and authorization.
