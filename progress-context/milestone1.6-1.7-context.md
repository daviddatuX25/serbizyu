### Current Task: Milestone 1.6 - AddressManager Livewire Component (Implementation Complete)

**1. Scope & UI (Modal Form)** - ✓ DONE
-   **Entities:** `User`, `Address`, `UserAddress` (Existing).
-   **Permissions:** User can only manage their own addresses.
-   **UI:** List of addresses, with buttons for "Add", "Edit", "Delete", "Set Primary". Add/Edit form is in a modal.

**2. Flow & Logic** - ✓ DONE
-   `mount()`: Loads user's addresses via `AddressService`.
-   `save()`: Creates/Updates address.
-   `edit($id)`: Loads address into form.
-   `delete($id)`: Removes address (with confirmation).
-   `setPrimary($id)`: Sets primary address.

**3. Service Layer** - ✓ DONE
-   **Action:** `AddressService.php` created with methods for `createAddressForUser`, `deleteUserAddress`, and `setPrimaryAddress`. Authorization logic is included.

**4. Frontend Implementation** - ✓ DONE
-   Livewire component generated.
-   Blade view (`address-manager.blade.php`) built.
-   Component logic (`AddressManager.php`) implemented.
-   Embedded in profile page (`profile/edit.blade.php`).

**5. Testing** - (Pending User Verification)
-   Create Livewire test file.
-   Write tests for all user actions and authorization.
