# Seeder JSON Structure Analysis

## Database Relationships Verified

### Service Model
- **Table**: `services`
- **Key Relationships**:
  - `workflow_template_id` → **ONE** WorkflowTemplate
  - `category_id` → Category
  - `creator_id` → User
  - `address_id` → Address
  - Attached images via Mediable (laravel/mediable)
- **Fields**: title, description, price, pay_first, payment_method, category_id, creator_id, workflow_template_id, address_id, average_rating, status

### OpenOffer Model
- **Table**: `open_offers`
- **Key Relationships**:
  - `workflow_template_id` → **ONE** WorkflowTemplate
  - `category_id` → Category
  - `creator_id` → User
  - `address_id` → Address
  - Attached images via Mediable
- **Fields**: title, description, budget, pay_first, payment_method, category_id, creator_id, workflow_template_id, address_id, deadline, status

### WorkflowTemplate Model
- **Table**: `workflow_templates`
- **Key Relationships**:
  - `hasMany(WorkTemplate)` → Multiple steps (the "workflow_steps")
- **Fields**: name, description, creator_id, is_public, category_id

### WorkTemplate Model (Workflow Step)
- **Table**: `work_templates`
- **Key Relationships**:
  - `belongsTo(WorkflowTemplate)` → Parent workflow
  - `belongsTo(WorkCatalog, nullable)` → Optional catalog reference
- **Fields**: workflow_template_id, work_catalog_id (nullable), name, description, price, duration_minutes, order

### WorkCatalog Model
- **Table**: `work_catalogs`
- **Key Relationships**:
  - `hasMany(WorkTemplate)` → Steps that reference this catalog
  - `belongsTo(Category, nullable)` → Optional category scope
- **Fields**: name, description, category_id

---

## Correct JSON Structure

### Hierarchy
```
Listing (Service/OpenOffer)
    ↓
    └─ WorkflowTemplate (ONE workflow per listing)
        ↓
        └─ WorkTemplate[] (Multiple steps)
            ↓
            └─ WorkCatalog (Optional reference to catalog item)
```

### Valid JSON Format

```json
{
  "filename": "video_editing.jpg",
  "relative_path": "Video_Editing/video_editing.jpg",
  "listing_type": "service",
  "listing_name": "Professional Video Editing Service",
  "listing_description": "High-quality video editing with effects and color grading",
  "listing_price_or_budget": 150,
  "workflow_name": "Website Design Service",
  "workflow_steps": [
    {
      "name": "Initial Consultation",
      "duration_minutes": 30,
      "work_catalog_ref": "Initial Consultation"
    },
    {
      "name": "Video Review & Planning",
      "duration_minutes": 60,
      "work_catalog_ref": null
    },
    {
      "name": "Editing & Effects",
      "duration_minutes": 180,
      "work_catalog_ref": "Work Execution"
    },
    {
      "name": "Client Review",
      "duration_minutes": 45,
      "work_catalog_ref": "Client Approval"
    },
    {
      "name": "Final Delivery",
      "duration_minutes": 15,
      "work_catalog_ref": "Completion & Documentation"
    }
  ]
}
```

---

## Field Explanations

### Top-Level Listing Fields

| Field | Type | Required | Example | Notes |
|-------|------|----------|---------|-------|
| `filename` | string | Yes | "video_editing.jpg" | Original filename |
| `relative_path` | string | Yes | "Video_Editing/video_editing.jpg" | Path from listing_seeder/ |
| `listing_type` | string | Yes | "service" or "offer" | Determines Service vs OpenOffer model |
| `listing_name` | string | Yes | "Professional Video Editing" | Will become title in Service/OpenOffer |
| `listing_description` | string | Yes | "High-quality editing..." | Will become description |
| `listing_price_or_budget` | number | Yes | 150 | For service: price, for offer: budget |
| `workflow_name` | string | Yes | "Website Design Service" | Name of ONE WorkflowTemplate |
| `workflow_steps` | array | Yes | [...] | Array of WorkTemplate objects (steps) |

### Workflow Step Fields

Each object in `workflow_steps` array:

| Field | Type | Required | Example | Notes |
|-------|------|----------|---------|-------|
| `name` | string | Yes | "Initial Consultation" | Step name (can be custom or catalog match) |
| `duration_minutes` | number | Optional | 30 | How long this step typically takes |
| `work_catalog_ref` | string | Optional | "Initial Consultation" | Name of WorkCatalog item to link to (or null) |

---

## Important Rules

### ✅ CORRECT
- **ONE workflow_name per listing** - Each Service/OpenOffer has exactly ONE WorkflowTemplate
- **MULTIPLE workflow_steps** - But steps can have custom names beyond catalog items
- **NULLABLE work_catalog_ref** - Not all steps need to reference a catalog
- **Custom step names** - Steps with custom names (not in catalog) are allowed
- **duration_minutes optional** - Can be omitted or null

### ❌ INCORRECT
- Multiple `workflow_name` values per listing (NOT an array)
- Nested `workflow` object wrapper (flat structure only)
- `work_catalog` as array at listing level (steps reference catalogs individually)
- `stepname` (JavaScript naming) - Use `name` (PHP naming)

---

## Seeding Process Flow

### Phase 1: Create WorkCatalogs
- Creates/updates all WorkCatalog entries
- Optional: scoped by category_id

### Phase 2: Create WorkflowTemplates and WorkTemplates
- Reads unique `workflow_name` values
- Creates WorkflowTemplate with that name
- Creates WorkTemplate records for each step in `workflow_steps`
- Links each step to WorkCatalog if `work_catalog_ref` provided

### Phase 3: Create Listings
- Creates Service or OpenOffer based on `listing_type`
- Associates with WorkflowTemplate via `workflow_template_id`
- Attaches image using Mediable
- Assigns random creator (User with 'user' role)
- Assigns random address

---

## Example: Complete Valid Listing

```json
{
  "filename": "Culinary_Chef_Plating_01.jpg",
  "relative_path": "Hospitality_and_Culinary_Arts/Culinary_Chef_Plating_01.jpg",
  "listing_type": "service",
  "listing_name": "Professional Catering Service",
  "listing_description": "Full-service catering for corporate events, weddings, and private parties. Menu customization available.",
  "listing_price_or_budget": 500,
  "workflow_name": "Event Catering Service",
  "workflow_steps": [
    {
      "name": "Initial Consultation",
      "duration_minutes": 45,
      "work_catalog_ref": "Initial Consultation"
    },
    {
      "name": "Menu Planning",
      "duration_minutes": 120,
      "work_catalog_ref": null
    },
    {
      "name": "Ingredient Sourcing",
      "duration_minutes": 180,
      "work_catalog_ref": "Material/Resource Preparation"
    },
    {
      "name": "Preparation & Cooking",
      "duration_minutes": 480,
      "work_catalog_ref": "Work Execution"
    },
    {
      "name": "Event Day Setup",
      "duration_minutes": 120,
      "work_catalog_ref": null
    },
    {
      "name": "Quality Verification",
      "duration_minutes": 60,
      "work_catalog_ref": "Quality Check"
    },
    {
      "name": "Client Approval",
      "duration_minutes": 30,
      "work_catalog_ref": "Client Approval"
    },
    {
      "name": "Final Payment",
      "duration_minutes": 15,
      "work_catalog_ref": "Payment Processing"
    }
  ]
}
```

---

## Available Workflow Names

These are the WorkflowTemplate names you can reference:
1. Basic Plumbing Service
2. House Painting Service
3. Event Catering Service
4. Event Decoration Service
5. Electrical Repair Service
6. Small Construction Project
7. AC Repair Service
8. Car Maintenance Service
9. Website Design Service
10. Cleaning Service

---

## Available Work Catalogs

These can be referenced in `work_catalog_ref`:
1. Initial Consultation
2. Site Inspection
3. Material/Resource Preparation
4. Work Execution
5. Quality Check
6. Client Approval
7. Payment Processing
8. Completion & Documentation

---

## Validation Rules

The `SeedFromJson` command requires:
- ✅ `listing_name` - NOT empty
- ✅ `listing_description` - NOT empty
- ✅ `workflow_name` - MUST match existing WorkflowTemplate
- ✅ `workflow_steps` - MUST be array with at least 1 step
- ✅ `relative_path` - File MUST exist in listing_seeder/
- ✅ `listing_type` - MUST be "service" or "offer"
- ✅ `listing_price_or_budget` - MUST be numeric (not null)

---

## Common Mistakes to Avoid

```javascript
// ❌ WRONG - Multiple workflows
"workflow": {
  "workflows": ["Workflow 1", "Workflow 2"]
}

// ❌ WRONG - Nested structure
"workflow": {
  "workflow_name": "Service Name",
  "workflow_steps": [...]
}

// ❌ WRONG - work_catalog as array at listing level
"work_catalog": [
  {stepname: "Item 1"},
  {stepname: "Item 2"}
]

// ✅ CORRECT - Flat structure
"workflow_name": "Service Name",
"workflow_steps": [
  {name: "Step 1", work_catalog_ref: "Catalog Item"},
  {name: "Step 2", work_catalog_ref: null}
]
```
