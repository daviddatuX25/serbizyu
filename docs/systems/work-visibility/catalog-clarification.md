# Work Catalog vs Workflow Steps - Clarification

## The Problem With Current Design

Current system treats Work Catalog and Workflow Steps as tightly coupled:
```
If workflow_steps = ["Site Inspection", "Work Execution"]
And work_catalog = ["Site Inspection", "Work Execution"]
→ Assumes they're the same thing (1:1 mapping)
→ Creates unnecessary duplication
→ Limits flexibility
```

## The Solution: Decoupled Design

### 1. Work Catalog = Generic Library
**Purpose**: Reusable, predefined steps that many workflows can reference

**Example - Minimal Catalog** (5-7 items):
```json
"workcatalogs": [
  {
    "name": "Initial Consultation",
    "description": "Meet with client to discuss requirements",
    "config": { "duration_hours": 1 }
  },
  {
    "name": "Site Visit",
    "description": "Visit and assess work location",
    "config": { "duration_hours": 2 }
  },
  {
    "name": "Execution",
    "description": "Perform the actual work",
    "config": {}
  },
  {
    "name": "Quality Inspection",
    "description": "Verify work meets standards",
    "config": { "duration_hours": 1 }
  },
  {
    "name": "Client Sign-off",
    "description": "Get client approval",
    "config": {}
  }
]
```

**Key Characteristics**:
- ✅ Generic, broad descriptions
- ✅ Reusable across many workflows
- ✅ Static - rarely changes
- ✅ Small number (5-10 items typically)
- ✅ Used as a lookup/reference library

### 2. Workflow Steps = Specific Process
**Purpose**: Define the exact steps for THIS workflow

**Example - Plumbing Service Workflow**:
```json
"workflow_name": "Basic Plumbing Service",
"workflow_steps": [
  {
    "name": "Initial Consultation",
    "description": "Meet homeowner to assess plumbing issue",
    "work_catalog_ref": "Initial Consultation",  // ← Optional reference to catalog
    "order": 1
  },
  {
    "name": "Site Inspection",
    "description": "Examine pipes, fixtures, and water damage",
    "work_catalog_ref": "Site Visit",  // ← References catalog
    "order": 2
  },
  {
    "name": "Provide Estimate",
    "description": "Provide written repair estimate to client",
    "work_catalog_ref": null,  // ← Custom step, NOT in catalog
    "order": 3
  },
  {
    "name": "Plumbing Repair",
    "description": "Execute repair work",
    "work_catalog_ref": "Execution",  // ← References catalog
    "order": 4
  },
  {
    "name": "Pressure Test",
    "description": "Test system for leaks",
    "work_catalog_ref": null,  // ← Custom step
    "order": 5
  },
  {
    "name": "Quality Inspection",
    "description": "Final inspection and cleanup",
    "work_catalog_ref": "Quality Inspection",  // ← References catalog
    "order": 6
  },
  {
    "name": "Client Approval",
    "description": "Get client sign-off on completed work",
    "work_catalog_ref": "Client Sign-off",
    "order": 7
  }
]
```

**Key Characteristics**:
- ✅ Specific to this workflow
- ✅ MAY reference work catalog items (optional)
- ✅ CAN have custom steps not in catalog
- ✅ Ordered, sequential
- ✅ Varies per workflow

### 3. Data Model Relationship

```
┌─────────────────┐
│  Work Catalog   │  (Generic library)
│  ───────────    │
│  • Initial Consultation
│  • Site Visit
│  • Execution
│  • Quality Inspection
│  • Client Sign-off
└────────┬────────┘
         │
         │ (optional reference)
         │
         ↓
┌─────────────────────────────────┐
│  WorkTemplate (workflow step)    │  (Specific to workflow)
│  ───────────────────────────────│
│  • name
│  • description
│  • work_catalog_id (nullable)   │ ← OPTIONAL link
│  • workflow_template_id
│  • order
└─────────────────────────────────┘
```

## Updated JSON Structure

### seeder.json - New Format

```json
{
  "_instructions": {
    "workcatalog_note": "These are generic steps available across ALL workflows. List only common, reusable ones.",
    "workflow_steps_note": "These are specific to THIS workflow. Each can optionally reference a work catalog item."
  },
  
  "work_catalogs": [
    { "name": "Initial Consultation", "description": "Meet with client" },
    { "name": "Site Visit", "description": "Visit and assess" },
    { "name": "Execution", "description": "Do the work" },
    { "name": "Quality Inspection", "description": "Verify quality" },
    { "name": "Client Sign-off", "description": "Get approval" }
  ],
  
  "images_and_categories": {
    "Construction_and_Skilled_Trades": [
      {
        "filename": "Plumbing_Service2.jpg",
        "listing_type": "service",
        "listing_name": "Residential Plumbing Repair",
        "listing_description": "Professional plumbing repair and maintenance",
        "listing_price_or_budget": 150,
        
        "workflow_name": "Basic Plumbing Service",
        "workflow_steps": [
          {
            "name": "Initial Consultation",
            "work_catalog_ref": "Initial Consultation"
          },
          {
            "name": "Diagnostic Inspection",
            "work_catalog_ref": "Site Visit"
          },
          {
            "name": "Repair Work",
            "work_catalog_ref": "Execution"
          },
          {
            "name": "Final Inspection",
            "work_catalog_ref": "Quality Inspection"
          }
        ]
      }
    ]
  }
}
```

## Implementation Changes Needed

### In SeedFromJson Command:

```php
// PHASE 1: Create WorkCatalogs (global library - once)
// This happens ONCE when setting up, not per listing

// PHASE 2: Create Workflows with WorkTemplates
// When creating a workflow step:
// - Check if work_catalog_ref is provided
// - If yes: link to existing WorkCatalog
// - If no: create WorkTemplate without catalog link

$workTemplate = WorkTemplate::create([
    'workflow_template_id' => $workflow->id,
    'name' => $step['name'],
    'description' => $step['description'],
    'work_catalog_id' => $step['work_catalog_ref'] 
        ? WorkCatalog::where('name', $step['work_catalog_ref'])->first()?->id 
        : null,  // ← NULL if not in catalog
    'order' => $step['order'] ?? 0,
]);
```

### Updated seeder.json Instructions

Instead of:
```
"work_catalog": "Subset of workflow_steps that are from work catalog"
```

Change to:
```
"workflow_steps": [
  {
    "name": "Step name",
    "work_catalog_ref": "Reference to catalog item (or null)"
  }
]
```

## Benefits of This Approach

✅ **Flexibility**: Steps can use catalog OR be completely custom
✅ **Reusability**: One catalog, many workflows reference it
✅ **Maintainability**: Catalog changes don't break workflows (only suggestions)
✅ **Scalability**: 1000 workflows can reference 5-10 catalog items
✅ **Clarity**: Clear distinction between templates and instances
✅ **Less JSON**: Catalog is small, easier to maintain

## Example: Real-World Scenario

### Work Catalog (System-wide):
```
- Initial Consultation
- Site Visit
- Execution
- Quality Inspection
- Client Sign-off
```

### Plumbing Workflow:
```
1. Initial Consultation      → catalog ref ✓
2. Diagnostic Inspection     → catalog ref (Site Visit)
3. Repair Execution          → catalog ref (Execution)
4. Final Check               → catalog ref (Quality Inspection)
5. Client Approval           → catalog ref (Client Sign-off)
```

### Event Catering Workflow:
```
1. Initial Consultation      → catalog ref ✓
2. Menu Planning             → NO catalog ref (custom)
3. Food Preparation          → catalog ref (Execution)
4. Setup & Serving           → catalog ref (Execution)
5. Quality Control           → catalog ref (Quality Inspection)
6. Cleanup                   → NO catalog ref (custom)
7. Final Sign-off            → catalog ref (Client Sign-off)
```

Both use the catalog, but neither requires it. Both can have custom steps.

