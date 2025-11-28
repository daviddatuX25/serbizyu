# Complete Seeding System Guide

**Last Updated:** November 28, 2025  
**Status:** ✅ Production Ready

## Overview

This guide documents the complete seeding system for Serbizyu, including:
- Database initialization
- Listing generation and seeding
- Image attachment
- Workflow management
- User and address setup

## System Architecture

```
┌─────────────────────────────────────────────────────────┐
│ STAGE 1: Database Setup                                 │
│ (php artisan migrate:fresh --seed)                      │
│                                                          │
│ • RolesSeeder - Creates 'user' and 'admin' roles       │
│ • UserSeeder - Creates 10 users (1 admin, 9 users)     │
│ • AddressSeeder - Creates 15 addresses                 │
│ • WorkCatalogSeeder - Creates 8 work catalog items    │
│ • CategorySeeder - Creates listing categories          │
│ • WorkflowSeeder - Creates 10 workflow templates       │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ STAGE 2: Generate Seeder JSON                           │
│ (python generate-and-fill-seeder.py)                   │
│                                                          │
│ • Scans listing_seeder/ directory (65 images)          │
│ • Fills listings with data and workflows               │
│ • Balances 70% services / 30% offers                   │
│ • Outputs: seeder.json (ready to seed)                │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ STAGE 3: Seed Listings & Images                         │
│ (php artisan seed:from-json)                           │
│                                                          │
│ • Creates 45 Services + 19 Offers (64 total)           │
│ • Attaches images to listings                          │
│ • Links workflows to listings                          │
│ • Assigns random 'user' role users as creators         │
│ • Stores images in public/services/images/             │
└─────────────────────────────────────────────────────────┘
```

## Quick Start

### One-Command Setup
```bash
# Step 1: Fresh database with all seeders
php artisan migrate:fresh --seed

# Step 2: Generate seeder.json (70% services, 30% offers)
python generate-and-fill-seeder.py

# Step 3: Seed all listings
php artisan seed:from-json
```

### Final Database State
```
✅ Users: 10 (1 admin, 9 regular)
✅ Addresses: 15 (various regions)
✅ Services: 45 (69.2% of listings)
✅ OpenOffers: 19 (30.8% of listings)
✅ Total Listings: 64
✅ Categories: 24
✅ Images: 63 uploaded successfully
```

## Detailed Components

### 1. RolesSeeder (`database/seeders/RolesSeeder.php`)
Creates application roles using Spatie Laravel-Permission:
- ✅ `user` - Regular users who can create listings
- ✅ `admin` - Administrative users

**Note:** Moderator role removed (not needed)

### 2. UserSeeder (`database/seeders/UserSeeder.php`)
Creates 10 users:
```
1 Admin User (admin@localhost)
  + 9 Regular Users:
    - user1@localhost (John Santos)
    - user2@localhost (Maria Garcia)
    - user3@localhost (Carlos Lopez)
    - user4@localhost (Ana Rodriguez)
    - user5@localhost (Pedro Martinez)
    - user6@localhost (Rosa Hernandez)
    - user7@localhost (Juan Flores)
    - user8@localhost (Sofia Morales)
    - user9@localhost (Miguel Reyes)
```

All use password: `password123`

### 3. AddressSeeder (`database/seeders/AddressSeeder.php`)
Creates 15 diversified addresses across Philippines:
- Metro Manila (BGC, Makati, Quiapo, Pasay, Taguig, etc.)
- CALABARZON (Cavite, Laguna)
- Central Visayas (Cebu)
- Central Luzon (Bulacan)
- Davao

Includes:
- Street addresses
- Barangay/City information
- Geographic coordinates (lat/lng)
- API integration data

### 4. generate-and-fill-seeder.py
**Complete one-script pipeline that:**

✅ **Step 1**: Scans `listing_seeder/` directory
- Finds all 65 images across 24 categories
- Creates empty listing templates

✅ **Step 2**: Loads workflow definitions
- 10 pre-defined workflows
- Each with multiple workflow steps

✅ **Step 3**: Fills all 65 listings
- Assigns names and descriptions
- Sets prices/budgets
- Links workflows

✅ **Step 4**: Auto-balances distribution
- 70% Services (45 listings)
- 30% OpenOffers (20 listings)

✅ **Step 5**: Generates `seeder.json`
- Ready for `php artisan seed:from-json`

### 5. SeedFromJson (`app/Console/Commands/SeedFromJson.php`)
**Advanced command that processes seeder.json:**

Key Features:
- ✅ Creates workflows with creator_id (fixed)
- ✅ Finds image paths flexibly (listing_seeder or absolute)
- ✅ Uses `gallery` tag for all images (consistent path)
- ✅ Stores all images in `public/services/images/`
- ✅ Assigns creators from users with 'user' role only (not admin)
- ✅ Handles webp format gracefully (with fallback)
- ✅ Supports dry-run mode for validation

**Usage:**
```bash
# Dry-run (preview, no changes)
php artisan seed:from-json --dry-run

# Full seeding
php artisan seed:from-json

# Custom seeder file
php artisan seed:from-json --file=custom.json
```

## Important Notes

### Role Filtering
**Only users with the `'user'` role can be assigned as listing creators.**

The system explicitly filters:
```php
$user = User::whereHas('roles', function ($query) {
    $query->where('name', 'user');
})->inRandomOrder()->first();
```

This ensures:
- ✅ Admin accounts stay clean
- ✅ Regular users create and manage listings
- ✅ Proper authorization/access control

### Image Storage
**All images stored in single location: `public/services/images/`**

Both services and offers:
- Upload to same directory
- Use 'gallery' tag
- Organized by timestamp/hash

```
public/services/images/
├── [service-id-1].jpg
├── [service-id-2].jpg
├── [offer-id-1].jpg
├── [offer-id-2].jpg
└── ...
```

### Workflow Management
**10 Pre-defined Workflows:**
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

**Each with 3-6 steps linked to WorkCatalogs:**
- Initial Consultation
- Site Inspection
- Material/Resource Preparation
- Work Execution
- Quality Check
- Client Approval
- Payment Processing
- Completion & Documentation

## Database Schema

```
Users (10)
├── Roles (2: user, admin)
├── Addresses (15) [via user_addresses pivot]
└── Services (45) [creator_id]
    ├── Category
    ├── WorkflowTemplate
    ├── Address
    └── Media (images)

OpenOffers (19)
├── Category
├── WorkflowTemplate
├── Address
└── Media (images)

WorkflowTemplates (10)
├── Creator (User)
└── WorkTemplates (steps)
    └── WorkCatalogs

Categories (24)
└── Services/Offers
```

## Scripts Reference

### Migration & Seeders
```bash
# Fresh start
php artisan migrate:fresh

# Migrate with seeds
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=UserSeeder
```

### JSON Seeding
```bash
# Generate and fill seeder.json
python generate-and-fill-seeder.py

# Preview seeding (dry-run)
php artisan seed:from-json --dry-run

# Execute seeding
php artisan seed:from-json
```

### Verification
```bash
# Check counts
php artisan tinker
>>> User::count()              # Should be 10
>>> Address::count()           # Should be 15
>>> Service::count()           # Should be 45
>>> OpenOffer::count()         # Should be 19
>>> exit
```

## Troubleshooting

### Issue: "creator_id NOT NULL constraint failed"
**Solution:** Ensure SeedFromJson sets creator_id when creating workflows
- ✅ Fixed in current version

### Issue: "Image not found"
**Solution:** Check image paths resolve correctly
- Images should be in: `listing_seeder/[category]/[filename]`
- Command tries both relative and absolute paths

### Issue: "webp format not recognized"
**Solution:** This is expected for webp files
- Media library may not support webp
- Command gracefully handles this
- JPEG/PNG files work fine

### Issue: "Skip duplicate listings"
**Solution:** Check `skip_duplicates` setting in seeder.json
- Default: true (prevents re-seeding same listings)
- Set to false if you want to allow duplicates

## Performance Notes

- **UserSeeder**: ~4 seconds (bcrypt hashing)
- **AddressSeeder**: ~0.2 seconds
- **generate-and-fill-seeder.py**: ~2 seconds
- **seed:from-json**: ~30 seconds (image processing)
- **Total**: ~1 minute for complete setup

## Future Enhancements

- [ ] Bulk image optimization before seeding
- [ ] Support for video media
- [ ] Service review auto-generation
- [ ] Order/transaction seeding
- [ ] Payment history seeding
- [ ] Messaging thread creation

## Related Documentation

- [Seeding Pipeline Guide](SEEDING_PIPELINE_GUIDE.md) - High-level overview
- [Architecture Diagrams](../architecture/ARCHITECTURE_DIAGRAMS.md) - System design
- [QUICK_START_GUIDE.md](../../QUICK_START_GUIDE.md) - Project setup
