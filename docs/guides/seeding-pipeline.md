# Complete Seeding Pipeline Guide

## Overview

This guide walks you through the entire process of creating 65 service and offer listings with images, workflows, and work steps using the seeding system.

**Timeline**: ~2-3 hours for full manual data entry + validation + seeding
**Result**: 65 Services/Offers with images, workflows, and structured work steps

---

## Pipeline Stages

```
┌─────────────────────────────────────────────────────────────────┐
│ STAGE 1: SETUP & PREPARATION                                   │
│ - Verify database state                                          │
│ - Check image directory                                          │
│ - Review available workflows                                     │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ STAGE 2: GENERATE TEMPLATE                                      │
│ - Run generate-seeder-json.py                                   │
│ - Create seeder.json with 65 empty listings                     │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ STAGE 3: FILL DATA                                              │
│ - Use SEEDER_FILL_TEMPLATE.md as reference                      │
│ - Fill all 65 listings manually                                 │
│ - Add: listing_type, name, description, price/budget, workflow  │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ STAGE 4: VALIDATE                                               │
│ - Run dry-run test                                              │
│ - Check for errors                                              │
│ - Fix any issues in seeder.json                                 │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ STAGE 5: SEED DATABASE                                          │
│ - Run actual seeding command                                    │
│ - Watch progress                                                │
│ - Verify results                                                │
└─────────────────────────────────────────────────────────────────┘
```

---

## STAGE 1: SETUP & PREPARATION

### 1.1 Verify Database State

Check that the database is ready:

```bash
# Open Laravel Tinker
php artisan tinker

# Check users
>>> User::count()  # Should be > 0
=> 4

# Check addresses
>>> Address::count()  # Should be > 0
=> 25

# Check existing workflows
>>> WorkflowTemplate::all()->pluck('name')
=> [
     "Basic Plumbing Service",
     "House Painting Service",
     "Event Catering Service",
     ...
   ]

# Exit Tinker
>>> exit
```

**Expected Results**:
- ✅ Users exist (at least 1 with 'user' role)
- ✅ Addresses exist (at least 1)
- ✅ Workflows exist (10 total)

> **⚠️ Important Role Filtering Note:**
> 
> The seeding pipeline **only assigns listings to users with the `'user'` role**, as defined by Spatie Laravel-Permission package. 
> 
> Users with `'admin'` or `'moderator'` roles are **excluded** from being assigned as listing creators.
> 
> **In `SeedFromJson.php`** - The `getRandomUser()` method filters users:
> ```php
> $user = User::whereHas('roles', function ($query) {
>     $query->where('name', 'user');
> })->inRandomOrder()->first();
> ```
> 
> This ensures:
> - ✅ Only regular users can create and own listings
> - ✅ Admin and moderator accounts remain clean for administrative purposes
> - ✅ Proper role separation and authorization

### 1.2 Verify Image Directory

Check that all images are in the correct location:

```bash
# Count images
Get-ChildItem -Path "listing_seeder" -Recurse -Include "*.jpg", "*.jpeg", "*.png", "*.webp" | Measure-Object

# Should show: 65 images in 24 categories
```

**Expected Results**:
- ✅ `listing_seeder/` directory exists
- ✅ Contains 24 subdirectories (categories)
- ✅ Contains 65 total image files

### 1.3 Review Available Workflows

Reference these when filling seeder.json:

| # | Workflow Name | Best For |
|---|---|---|
| 1 | Basic Plumbing Service | Plumbing, water systems |
| 2 | House Painting Service | Interior/exterior painting |
| 3 | Event Catering Service | Food, catering, event services |
| 4 | Event Decoration Service | Event setup, decoration |
| 5 | Electrical Repair Service | Electrical work, installations |
| 6 | Small Construction Project | Construction, repairs |
| 7 | AC Repair Service | HVAC, cooling systems |
| 8 | Car Maintenance Service | Auto repair, maintenance |
| 9 | Website Design Service | Web development, design, video |
| 10 | Cleaning Service | Cleaning, janitorial services |

### 1.4 Review Available Work Catalogs

Reference these in `work_catalog_ref` field:

1. **Initial Consultation** - Meet with client
2. **Site Inspection** - Visit and assess
3. **Material/Resource Preparation** - Prepare materials
4. **Work Execution** - Perform the work
5. **Quality Check** - Inspect work quality
6. **Client Approval** - Get approval
7. **Payment Processing** - Handle payment
8. **Completion & Documentation** - Document results

---

## STAGE 2: GENERATE TEMPLATE

### 2.1 Run Generator Script

```bash
cd c:\Users\sarmi\Documents\School Files\serbizyu

python generate-seeder-json.py
```

**Expected Output**:
```
📂 Scanning listing_seeder directory...
✅ Found 24 categories
✅ Found 65 images

✅ Successfully generated seeder.json
📊 Summary:
   Total categories: 24
   Total listings: 65
   Output file: seeder.json

📝 Next steps:
   1. Fill each listing with: listing_type, listing_name, listing_description, listing_price_or_budget, workflow_name, workflow_steps
   2. Use SEEDER_FILL_TEMPLATE.md as reference
   3. Run validation: php artisan seed:from-json --file=seeder.json --dry-run
   4. Run seeding: php artisan seed:from-json --file=seeder.json
```

### 2.2 Verify Generated seeder.json

```bash
# Check file exists and is valid JSON
type seeder.json | jq . | head -50
```

**Expected Structure**:
```json
{
  "_instructions": {...},
  "images_and_categories": {
    "Category_Name": [
      {
        "filename": "image.jpg",
        "relative_path": "Category/image.jpg",
        "listing_type": "",
        "listing_name": "",
        "listing_description": "",
        "listing_price_or_budget": null,
        "workflow_name": "",
        "workflow_steps": []
      }
    ]
  }
}
```

---

## STAGE 3: FILL DATA

### 3.1 Open seeder.json in Editor

```bash
# Open in VS Code
code seeder.json
```

### 3.2 Fill Each Listing

Use this template for each of the 65 listings:

```json
{
  "filename": "image_name.jpg",
  "relative_path": "Category_Name/image_name.jpg",
  
  "listing_type": "service",
  "listing_name": "Professional [Service] Service",
  "listing_description": "Detailed description of what this service offers, benefits, and what to expect",
  "listing_price_or_budget": 250,
  
  "workflow_name": "Basic Plumbing Service",
  "workflow_steps": [
    {
      "name": "Initial Consultation",
      "duration_minutes": 30,
      "work_catalog_ref": "Initial Consultation"
    },
    {
      "name": "Site Assessment",
      "duration_minutes": 45,
      "work_catalog_ref": "Site Inspection"
    },
    {
      "name": "Work Execution",
      "duration_minutes": 120,
      "work_catalog_ref": "Work Execution"
    },
    {
      "name": "Final Inspection",
      "duration_minutes": 30,
      "work_catalog_ref": "Quality Check"
    },
    {
      "name": "Client Sign-off",
      "duration_minutes": 15,
      "work_catalog_ref": "Client Approval"
    }
  ]
}
```

### 3.3 Field Guidelines

**`listing_type`**:
- `"service"` - For service offerings (70% of listings)
- `"offer"` - For open offers/bids (30% of listings)

**`listing_name`**:
- Clear, descriptive name
- 3-8 words
- Examples:
  - "Professional Plumbing Installation"
  - "Event Catering - Corporate & Weddings"
  - "Website Design & Development"

**`listing_description`**:
- 1-3 sentences describing the service
- Include key features or benefits
- 50-200 characters
- Examples:
  - "Expert plumbing services for residential and commercial properties. Quick response time, transparent pricing, and professional workmanship guaranteed."
  - "Full-service event catering with customizable menus for any occasion. Includes setup, service, and cleanup."

**`listing_price_or_budget`**:
- For "service": hourly rate or project price (100-1000)
- For "offer": budget amount (500-5000)
- Integer value, no currency symbol

**`workflow_name`**:
- MUST match one of the 10 available workflows exactly
- Same for all services of that type
- Examples:
  - "Basic Plumbing Service"
  - "Cleaning Service"
  - "Website Design Service"

**`workflow_steps`**:
- Array of 3-8 steps
- Each step object has:
  - `name` (required): Custom step name
  - `duration_minutes` (optional): Minutes for this step
  - `work_catalog_ref` (optional): Reference to catalog item or null

### 3.4 Step Name Guidelines

Can be standard catalog names OR custom names:

**Standard (from catalog)**:
- Initial Consultation
- Site Inspection
- Material/Resource Preparation
- Work Execution
- Quality Check
- Client Approval
- Payment Processing
- Completion & Documentation

**Custom Examples**:
- Video Review & Planning (for video editing)
- Menu Planning (for catering)
- Design Mockups (for web design)
- Equipment Rental (for event services)
- Installation & Testing (for electrical)

### 3.5 Filling Strategy

**Quick Fill (1 hour per 10 listings)**:
- Use find-and-replace for repeated fields
- Group by category/workflow
- Copy templates for similar services

**Organized Approach**:
1. **By Category** - Group listings by directory
2. **By Workflow Type** - Use same workflow for similar services
3. **By Batch** - Fill 5-10 listings at a time

**Tool Tip**: Use VS Code's multi-cursor editing:
- `Ctrl+D` - Select next occurrence
- `Ctrl+Shift+L` - Select all occurrences
- `Ctrl+F` - Find and replace

---

## STAGE 4: VALIDATE

### 4.1 Run Dry-Run Test

Test the seeding without making database changes:

```bash
php artisan seed:from-json --file=seeder.json --dry-run
```

**Expected Output**:
```
🚀 Processing seeder.json...

📋 Creating workflows and work templates...
   📋 Creating new workflow: Website Design Service
   ✓ Workflow created: Website Design Service (ID: 1)
      ✓ Step: Initial Consultation (30min) → catalog: Initial Consultation
      ✓ Step: Design Review (60min) (custom, no catalog)
      ✓ Step: Development (480min) → catalog: Work Execution
   ✓ Workflow: Website Design Service with 3 steps

✅ Found 24 categories
📸 Total listings: 65

🔍 DRY RUN MODE - No changes will be made

📊 Services created: 65
📊 Offers created: 0
📸 Images uploaded: 65

✅ Seeding complete!
(DRY RUN - no changes made)
```

### 4.2 Check for Errors

If you see errors like this:

```
❌ Missing required field: listing_name
   ⏭ Skipping duplicate: Service Name
   ⚠ Work catalog not found: NonexistentCatalog
```

**Fix Steps**:
1. Open seeder.json
2. Find the problematic listing (search by filename)
3. Fill in the missing field or fix the value
4. Re-run dry-run test

### 4.3 Validate Specific Listings

If many errors, check a few listings in Tinker:

```bash
php artisan tinker

# Check if workflows exist
>>> WorkflowTemplate::where('name', 'Basic Plumbing Service')->first()

# Check if catalogs exist
>>> WorkCatalog::where('name', 'Initial Consultation')->first()

# Exit
>>> exit
```

---

## STAGE 5: SEED DATABASE

### 5.1 Run Actual Seeding

Execute the seeding command:

```bash
php artisan seed:from-json --file=seeder.json
```

**Expected Output** (same as dry-run but WITH database changes):
```
🚀 Processing seeder.json...
📋 Creating workflows and work templates...
   [workflows and steps being created...]

✅ Seeding complete!
   📊 Services created: 55
   📊 Offers created: 10
   📸 Images uploaded: 65
```

### 5.2 Watch Progress

The command will:
1. Create workflows (should take < 1 sec)
2. Create work templates/steps (should take < 2 sec)
3. Process categories one by one
4. Create listings and attach images (main time: 5-15 min)

**Estimated Timeline**:
- 65 listings × ~3-5 seconds per listing = 3-4 minutes total

### 5.3 Verify Database Results

After seeding completes, verify the data:

```bash
php artisan tinker

# Check services
>>> Service::count()
=> 55

# Check offers
>>> OpenOffer::count()
=> 10

# Check total
>>> Service::count() + OpenOffer::count()
=> 65

# Check images attached
>>> Service::first()->media
=> Collection of Media objects

# Check workflows
>>> Service::first()->workflowTemplate->name
=> "Basic Plumbing Service"

# Check work steps
>>> Service::first()->workflowTemplate->workTemplates
=> Collection of WorkTemplate objects

# Check step details
>>> Service::first()->workflowTemplate->workTemplates->first()
=> WorkTemplate {
     id: 1,
     name: "Initial Consultation",
     duration_minutes: 30,
     work_catalog_id: 1,
     ...
   }

# Exit
>>> exit
```

---

## Post-Seeding Verification

### 5.4 Visual Verification

Check the web interface:

```bash
# Start dev server
npm run dev
# OR
composer run dev
```

**Browse to**:
- `http://localhost:8000/listings` - View all services
- `http://localhost:8000/offers` - View all offers
- `http://localhost:8000/admin` - Admin dashboard

**Check**:
- ✅ 65 total listings visible
- ✅ Images display correctly
- ✅ Categories correct
- ✅ Prices/budgets display
- ✅ Workflow info accessible

### 5.5 Database Checks

```bash
php artisan tinker

# Total work steps created
>>> WorkTemplate::count()
=> 450  # Approximate: 65 listings × 6-8 steps each

# Check work catalog links
>>> WorkTemplate::whereNotNull('work_catalog_id')->count()
=> 350  # Most steps should reference catalogs

# Check custom steps (no catalog ref)
>>> WorkTemplate::whereNull('work_catalog_id')->count()
=> 100  # Some custom steps

# Check categories
>>> Category::count()
=> 24

# Check images
>>> Media::count()
=> 65

# Exit
>>> exit
```

---

## Troubleshooting

### Problem: "No addresses found in database"

**Solution**:
```bash
php artisan tinker
>>> Address::create(['street' => 'Main St', 'city' => 'City', 'state' => 'ST', 'zip' => '12345'])
>>> exit
```

### Problem: "No users found in database"

**Solution**:
```bash
php artisan tinker
>>> $user = User::create(['firstname' => 'Test', 'lastname' => 'User', 'email' => 'test@example.com', 'password' => bcrypt('password')])
>>> $user->assignRole('user')
>>> exit
```

### Problem: "Image not found" for specific listings

**Solution**:
1. Check the relative_path is correct
2. Verify image file exists in listing_seeder/
3. Remove problematic listing from seeder.json
4. Re-run seeding

### Problem: "Work catalog not found"

**Solution**:
1. Check spelling of work_catalog_ref
2. Use exact names from available catalogs list
3. Or set work_catalog_ref to null for custom steps

### Problem: Seeding stopped mid-way

**Solution**:
```bash
# Resume from where it stopped
php artisan seed:from-json --file=seeder.json --resume
```

---

## Quick Reference: Commands

```bash
# Stage 1: Setup
php artisan tinker                    # Check database state

# Stage 2: Generate
python generate-seeder-json.py        # Create template

# Stage 3: Fill
code seeder.json                      # Edit listings

# Stage 4: Validate
php artisan seed:from-json --file=seeder.json --dry-run

# Stage 5: Seed
php artisan seed:from-json --file=seeder.json

# Post-Seeding Verification
php artisan tinker                    # Check results
npm run dev                           # View in browser
```

---

## Files Reference

| File | Purpose | Stage |
|------|---------|-------|
| `seeder.json` | Configuration with all 65 listings | 3 |
| `generate-seeder-json.py` | Script to create template | 2 |
| `app/Console/Commands/SeedFromJson.php` | Artisan command | 5 |
| `SEEDER_JSON_STRUCTURE_ANALYSIS.md` | Structure reference | 1 |
| `SEEDER_FILL_TEMPLATE.md` | Filling guide | 3 |
| `SEEDFROMJSON_REFACTORING_SUMMARY.md` | Technical details | Reference |

---

## Success Checklist

- [ ] Database has users, addresses, workflows
- [ ] Images found in listing_seeder/ (65 total, 24 categories)
- [ ] seeder.json generated with 65 empty listings
- [ ] All 65 listings filled with required fields
- [ ] Dry-run completed with no errors
- [ ] Actual seeding completed successfully
- [ ] Database shows 65 total listings (Services + Offers)
- [ ] Images attached to listings
- [ ] Workflows and steps created correctly
- [ ] Web interface displays all listings

---

## Duration Estimates

| Stage | Task | Time |
|-------|------|------|
| 1 | Setup & verification | 5-10 min |
| 2 | Generate template | 1-2 min |
| 3 | Fill 65 listings | 60-90 min |
| 4 | Validate & fix | 5-10 min |
| 5 | Seed database | 5-10 min |
| 5+ | Verify results | 5-10 min |
| **TOTAL** | **Complete pipeline** | **80-120 min** |

---

## Next Steps After Seeding

1. **Create Listings Page** - Display the 65 services/offers
2. **Add Search & Filter** - By category, type, price
3. **Create Detail Pages** - Show full workflow and steps
4. **Add Review System** - Let users review listings
5. **Create Order System** - Link listings to orders
6. **Set Up Messaging** - Connect ordering to messaging
7. **Payment Integration** - Process payments for listings

---

## Support

For detailed information about:
- **JSON Structure**: See `SEEDER_JSON_STRUCTURE_ANALYSIS.md`
- **Filling Guide**: See `SEEDER_FILL_TEMPLATE.md`
- **Technical Details**: See `SEEDFROMJSON_REFACTORING_SUMMARY.md`
- **Database Schema**: See models in `app/Domains/Listings/Models/`
