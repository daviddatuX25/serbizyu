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
