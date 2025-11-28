# Complete Seeding System Documentation

**Last Updated**: November 28, 2025

## Overview

This document provides a complete guide to the seeding pipeline for Serbizyu, including database seeding and listing generation with automatic balancing.

---

## Quick Start

### One-Command Workflow

```bash
# 1. Fresh database with all base seeders
php artisan migrate:fresh --seed

# 2. Generate and fill seeder.json (70% services, 30% offers)
python generate-and-fill-seeder.py

# 3. Seed all listings with images
php artisan seed:from-json
```

**Expected Results:**
- ✅ 10 Users (1 admin + 9 regular users)
- ✅ 15 Addresses across different regions
- ✅ 45 Services + 19 OpenOffers = 64 Listings
- ✅ All with images, workflows, and work steps
- ✅ All assigned to 'user' role creators (not admin)

---

## Component 1: Database Seeders

### Location
`database/seeders/`

### Updated Seeders

#### 1. RolesSeeder.php
Creates two roles (removed moderator):
- `user` - Regular users who create listings
- `admin` - Administrative access

```bash
# Only creates user + admin roles now
Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
```

#### 2. UserSeeder.php
Creates 10 total users:
- 1 Admin user (admin@localhost)
- 9 Regular users (user1@localhost to user9@localhost)

**Names Used:** John, Maria, Carlos, Ana, Pedro, Rosa, Juan, Sofia, Miguel
**Password:** password123 (all users)

```bash
# Creates diverse user base with realistic names
```

#### 3. AddressSeeder.php
Creates 15 addresses across Philippines:
- Metro Manila (NCR)
- Cavite (CALABARZON)
- Laguna (CALABARZON)
- Cebu (Central Visayas)
- Bulacan (Central Luzon)
- Davao City

**Includes:** Latitude/longitude, regional codes, proper addresses

#### 4. CategorySeeder.php
Creates standard service categories (unchanged)

#### 5. WorkCatalogSeeder.php
Creates 8 work catalog items:
- Initial Consultation
- Site Inspection
- Material/Resource Preparation
- Work Execution
- Quality Check
- Client Approval
- Payment Processing
- Completion & Documentation

#### 6. WorkflowAndWorkTemplateSeeder.php
Creates 10 standard workflows with predefined steps

#### 7. ServiceReviewSeeder.php
Generates service reviews for existing services
- Ratings: 1-5 stars
- Comments: Realistic feedback
- Tags: professional, reliable, quality, etc.

#### 8. UserReviewSeeder.php
Generates user reviews (feedback on service creators)

---

## Component 2: Listing Seeding Pipeline

### Script: generate-and-fill-seeder.py

**Location:** Root directory
**Language:** Python 3
**Size:** ~300 lines

### Workflow

#### Step 1: Directory Scanning
```python
# Scans listing_seeder/ for images
# Found: 24 categories × 65 images
```

**Categories Scanned:**
- Agriculture_and_Landscaping
- Care_and_Cleaning_Services
- Community_and_Social_Services
- Construction & Engineering
- Construction_and_Property_Maintenance
- Construction_and_Skilled_Trades
- Education_and_Training
- Facility_and_Grounds_Maintenance
- Food & Agriculture
- Graphic_Design
- Health_and_Wellness
- Hospitality_and_Culinary_Arts
- Hospitality_and_Food_Services
- Laundry_and_Cleaning_Services
- Manufacturing & Technology
- Mechanical_and_Repair_Services
- Printing_Service
- Repair_and_Technical_Services
- Skilled_Trades_and_Plumbing
- Skilled_Trades_and_Systems
- Specialized_Personal_Services
- Technical_Maintenance_and_IT
- Video_Editing
- Web_Designing

#### Step 2: Data Definition
Preloads 65 listings with:
- Filename
- Category
- Type (service/offer)
- Name
- Description
- Price/Budget
- Workflow assignment

#### Step 3: Filling
Matches images to predefined listings and fills:
- `listing_type` (service or offer)
- `listing_name`
- `listing_description`
- `listing_price_or_budget`
- `workflow_name`
- `workflow_steps` (array of objects with catalogs)

#### Step 4: Balancing
Automatically distributes to **70/30 split**:
- 45 Services (69.2%)
- 20 Offers (30.8%)

#### Step 5: Output
Creates `seeder.json` with structure:
```json
{
  "_instructions": { ... },
  "dir_path": "/absolute/path/to/listing_seeder",
  "settings": {
    "auto_create_categories": true,
    "skip_duplicates": true
  },
  "images_and_categories": {
    "Category_Name": [
      {
        "filename": "image.jpg",
        "relative_path": "Category/image.jpg",
        "listing_type": "service",
        "listing_name": "Service Name",
        "listing_description": "Description...",
        "listing_price_or_budget": 500,
        "workflow_name": "Basic Plumbing Service",
        "workflow_steps": [
          {
            "name": "Initial Consultation",
            "work_catalog_ref": "Initial Consultation"
          },
          ...
        ]
      }
    ]
  }
}
```

---

## Component 3: Artisan Command - seed:from-json

### Location
`app/Console/Commands/SeedFromJson.php`

### Features

#### Image Storage
- **Services**: `public/services/images/`
- **OpenOffers**: `public/open-offers/images/`
- Uses Mediable for image management
- Supports: jpg, jpeg, png, gif, webp

#### Role Filtering
Only creates listings for users with `'user'` role:
```php
$user = User::whereHas('roles', function ($query) {
    $query->where('name', 'user');
})->inRandomOrder()->first();
```

**Not assigned to:** admin, moderator roles

#### Workflow Creation
- Creates missing workflows automatically
- Sets `creator_id` to random user
- Links workflow steps to work catalogs
- Support both old and new formats

#### Error Handling
- Skips duplicate listings
- Reports image not found errors
- Logs all errors to `storage/logs/`
- Continues processing on errors

### Usage

```bash
# Preview without making changes
php artisan seed:from-json --dry-run

# Execute full seeding
php artisan seed:from-json

# Use custom seeder.json file
php artisan seed:from-json --file=custom-seeder.json

# Resume from previous seeding
php artisan seed:from-json --resume
```

### Output Example
```
🚀 Processing seeder.json...

📋 Creating workflows and work templates...
   ✓ Workflow: Basic Plumbing Service with 4 steps
   ✓ Workflow: House Painting Service with 4 steps
   ...

✅ Found 24 categories
📸 Total listings: 65

📦 Processing category: Agriculture_and_Landscaping
   ✓ Service: Professional Crop Planting
   ✓ Service: Tree Removal Service
...

✅ Seeding complete!
   📊 Services created: 45
   📊 Offers created: 19
   📸 Images uploaded: 63
```

---

## Workflow Definitions (10 Total)

Each workflow has predefined steps linked to work catalogs:

### 1. Basic Plumbing Service
- Initial Consultation
- Diagnostic Assessment
- Repair Execution
- Final Inspection

### 2. House Painting Service
- Design Consultation
- Surface Preparation
- Painting Execution
- Quality Inspection

### 3. Event Catering Service
- Menu Planning
- Ingredient Sourcing
- Food Preparation
- Service & Setup

### 4. Event Decoration Service
- Venue Assessment
- Design Planning
- Decoration Setup
- Final Walkthrough

### 5. Electrical Repair Service
- Initial Consultation
- Electrical Inspection
- Repair Work
- Safety Testing

### 6. Small Construction Project
- Project Consultation
- Site Inspection
- Material Preparation
- Construction Work
- Quality Inspection
- Client Walkthrough

### 7. AC Repair Service
- Initial Assessment
- System Inspection
- Repair/Installation
- System Testing

### 8. Car Maintenance Service
- Vehicle Assessment
- Diagnostic Check
- Maintenance Work
- Quality Inspection

### 9. Website Design Service
- Consultation & Planning
- Design Preparation
- Development Work
- Testing & Review

### 10. Cleaning Service
- Initial Assessment
- Cleaning Execution
- Quality Verification

---

## Database Final State

### Users
- **Count**: 10
- **1 Admin**: admin@localhost (password123)
- **9 Users**: user1-user9@localhost (password123)
- **All verified**: Role assignment complete
- **No moderators**: Removed from system

### Addresses
- **Count**: 15
- **Coverage**: 6 regions/provinces
- **Each user**: 1-2 addresses assigned
- **All geolocated**: Latitude/longitude included

### Services
- **Count**: 45
- **Distribution**: Across all 24 categories
- **Images**: Attached and stored in `public/services/images/`
- **Prices**: Range from ₱120 to ₱1,200
- **All assigned**: To random 'user' role creators

### OpenOffers
- **Count**: 19
- **Distribution**: Across all 24 categories
- **Images**: Attached and stored in `public/open-offers/images/`
- **Budgets**: Range from ₱150 to ₱5,000
- **All assigned**: To random 'user' role creators

### Workflows
- **Count**: 10
- **Steps**: 40 total (3-6 steps each)
- **Work Catalogs**: All linked to steps
- **Automatic**: Created on-demand if missing

### Reviews
- **Service Reviews**: 0 (created separately via ServiceReviewSeeder)
- **User Reviews**: Created via UserReviewSeeder
- **Can be generated**: Via separate artisan command

---

## Troubleshooting

### Issue: Image Not Found
**Error**: `Image not found: Category/image.webp`
**Solution**: 
1. Check file exists in `listing_seeder/`
2. Some formats (webp) may not be supported by media library
3. Use jpg/png instead

### Issue: creator_id NOT NULL constraint
**Error**: Failed to create workflow - creator_id missing
**Solution**: Already fixed in SeedFromJson.php
- Automatically assigns random 'user' role creator
- No manual action needed

### Issue: Services not created
**Error**: Seeding completes but 0 services
**Solution**:
1. Check seeder.json file exists
2. Verify images are in listing_seeder/
3. Run dry-run: `php artisan seed:from-json --dry-run`
4. Check logs: `storage/logs/laravel.log`

### Issue: Duplicate entries
**Error**: Service already exists
**Solution**: 
- Skip duplicates is enabled by default
- Clear database and re-run: `php artisan migrate:fresh --seed`

---

## Performance Notes

- **Generation**: `generate-and-fill-seeder.py` takes ~2-3 seconds
- **Seeding**: `php artisan seed:from-json` takes ~30-60 seconds
- **Total Pipeline**: ~3-5 minutes for complete setup
- **Image Upload**: Uses Mediable for efficient storage

---

## Files Included

### Core Scripts
- ✅ `generate-and-fill-seeder.py` - Main generation script
- ✅ `app/Console/Commands/SeedFromJson.php` - Artisan command

### Seeders
- ✅ `database/seeders/RolesSeeder.php` (updated - no moderator)
- ✅ `database/seeders/UserSeeder.php` (updated - 10 users)
- ✅ `database/seeders/AddressSeeder.php` (updated - 15 addresses)
- ✅ `database/seeders/CategorySeeder.php` (unchanged)
- ✅ `database/seeders/WorkCatalogSeeder.php` (unchanged)
- ✅ `database/seeders/WorkflowAndWorkTemplateSeeder.php` (unchanged)
- ✅ `database/seeders/ServiceReviewSeeder.php` (included)
- ✅ `database/seeders/UserReviewSeeder.php` (included)

### Configuration
- ✅ `seeder.json` - Generated listing data
- ✅ `listing_seeder/` - 65 images across 24 categories

---

## Next Steps

1. **Run full pipeline**:
   ```bash
   php artisan migrate:fresh --seed
   python generate-and-fill-seeder.py
   php artisan seed:from-json
   ```

2. **Verify in browser**:
   - Check services display on frontend
   - Verify images load correctly
   - Test order creation

3. **Generate additional reviews**:
   ```bash
   php artisan db:seed --class=ServiceReviewSeeder
   ```

4. **Export data** (if needed):
   - Use admin panel exports
   - Or direct database query

---

## Key Design Decisions

✅ **70/30 Split**: Services > Offers (realistic marketplace balance)
✅ **Random Distribution**: Each category gets listings
✅ **Role-Based**: Only 'user' role creators (admin separation)
✅ **One-Click Generation**: All in one Python script
✅ **Duplicate Prevention**: Skips already-seeded items
✅ **Automatic Workflows**: Creates missing workflows on-demand
✅ **Image Validation**: Checks file exists before seeding
✅ **Error Resilience**: Continues on errors, logs them

---

## Related Documentation

- `SEEDING_PIPELINE_GUIDE.md` - Historical guide
- `SEEDER_JSON_STRUCTURE_ANALYSIS.md` - JSON structure details
- `database/seeders/*.php` - Individual seeder code

