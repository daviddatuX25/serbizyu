# Milestone 1.7: Workflow Management Web UI - Detailed Plan

This plan outlines the step-by-step implementation for building the workflow management feature.

---

### **Phase 1: Backend Foundation (Controllers, Routes, and Policies)**

This phase sets up the non-visual, server-side structure.

*   **✓ Step 1.1: Create Controllers**
    *   ✓ Create `app/Domains/Listings/Http/Controllers/WorkflowTemplateController.php`.
    *   ✓ Create `app/Domains/Listings/Http/Controllers/WorkTemplateController.php`.
    *   ✓ Create `app/Domains/Listings/Http/Controllers/WorkCatalogController.php`.

*   **✓ Step 1.2: Define Web Routes**
    *   ✓ In `routes/web.php`, add a new route group protected by `auth` middleware.
    *   ✓ Define a resource route for `WorkflowTemplateController` to handle the main CRUD for workflows.
    *   ✓ Define nested routes for `WorkTemplateController` to handle adding, updating, and reordering steps within a workflow.
    *   ✓ Define a route for `WorkCatalogController` to list catalog items (e.g., `GET /work-catalogs`).

*   **✓ Step 1.3: Implement Authorization Policies**
    *   ✓ Create `app/Domains/Listings/Policies/WorkflowPolicy.php`.
    *   ✓ Define rules: A user can only manage (`create`, `update`, `delete`) their own workflows.
    *   ✓ Register the policy in `AuthServiceProvider`.

---

### **Phase 2: Backend Logic (Services and Testing)**

This phase implements the business logic and validates it.

*   **✓ Step 2.1: Implement WorkCatalog Service & Controller**
    *   ✓ In `WorkCatalogService`, implement methods to retrieve and manage `WorkCatalog` items.
    *   ✓ In `WorkCatalogController`, implement the `index` method to display the catalog items.

*   **✓ Step 2.2: Enhance WorkTemplate Service for Catalog Integration**
    *   ✓ Update `WorkTemplateService` to include a method (e.g., `createWorkTemplateFromCatalog(WorkflowTemplate $workflow, WorkCatalog $catalogItem)`) that creates a new `WorkTemplate` step for a given workflow, pre-populating its details from a selected `WorkCatalog` item.

*   **✓ Step 2.3: Implement WorkflowTemplate Service Logic**
    *   ✓ In `WorkflowTemplateService`, flesh out the methods for `create`, `update`, `delete`, and `duplicate`.

*   **✓ Step 2.4: Implement WorkTemplate Service Logic**
    *   ✓ In `WorkTemplateService`, flesh out the methods for adding, updating, deleting, and reordering steps, ensuring they are always tied to a parent `WorkflowTemplate`.

*   **✓ Step 2.5: Write Feature Tests**
    *   ✓ Create a new test file: `tests/Feature/WorkflowManagementTest.php`.
    *   ✓ Write tests to ensure:
        1.  An authenticated user can create a workflow.
        2.  A user can add a step to their workflow.
        3.  A user can add a step to their workflow *from the WorkCatalog*.
        4.  A user can reorder steps in their workflow.
        5.  A user can delete a step from their workflow.
        6.  A user can delete their workflow.
        7.  A user **cannot** manage a workflow that does not belong to them.

---

### **Phase 3: Frontend Views & Livewire Component Shell**

This phase creates the visual containers for our feature.

*   **✓ Step 3.1: Create the Workflow Index Page**
    *   ✓ Create the view `resources/views/creator/workflows/index.blade.php`.
    *   ✓ This page will list the user's workflows and have a "Create Workflow" button that links to the builder page.

*   **✓ Step 3.2: Create the Workflow Builder Page**
    *   ✓ Create the view `resources/views/creator/workflows/builder.blade.php`.
    *   ✓ This view will simply contain the Livewire component tag: `<livewire:workflow-builder />`.

*   **✓ Step 3.3: Create the Livewire Component**
    *   ✓ Generate the Livewire component: `WorkflowBuilder`. This will create `app/Http/Livewire/WorkflowBuilder.php` and `resources/views/livewire/workflow-builder.blade.php`.

---

### **Phase 4: Frontend Logic (The Livewire Builder)**

This is where we'll spend most of our time, building the interactive UI.

*   **✓ Step 4.1: Implement Workflow State**
    *   ✓ In `WorkflowBuilder.php`, add public properties to hold the `WorkflowTemplate` and its collection of `WorkTemplate` steps.
    *   ✓ Implement the `mount()` method to load an existing workflow or initialize a new one.

*   **✓ Step 4.2: Build the UI**
    *   ✓ In `workflow-builder.blade.php`, create the UI to display the workflow's name and description.
    *   ✓ Loop through the steps and display them in a list. Each item should have "Edit" and "Delete" buttons.

*   **✓ Step 4.3: Implement WorkCatalog Integration in UI**
    *   ✓ Add a UI element (e.g., a modal or a sidebar) within the `WorkflowBuilder` component that displays items from the `WorkCatalog`.
    *   ✓ Implement a method in `WorkflowBuilder.php` (e.g., `addStepFromCatalog($catalogItemId)`) to allow the user to select a `WorkCatalog` item and add it as a new `WorkTemplate` step to the current workflow.

*   **✓ Step 4.4: Implement Core Actions**
    *   ✓ Create methods in the component to handle:
        *   ✓ `save()`: Creates or updates the workflow's details.
        *   ✓ `addStep()`: Adds a new, blank step to the list.
        *   ✓ `deleteStep($stepId)`: Removes a step from the list.

*   **✓ Step 4.5: Implement Drag-and-Drop Reordering**
    *   ✓ Integrate a JavaScript library like `livewire-sortable.js` to make the list of steps draggable.
    *   ✓ When the order is changed, call a public method on the component, like `updateStepOrder($newOrder)`, which will then call our backend service.
