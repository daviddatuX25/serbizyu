# Image-to-Seeder Generator Guide

## Overview
This tool converts a folder of categorized images into a Laravel seeder that:
- Creates `Service` records from images
- Uses **folder names as service categories**
- Attaches images using Mediable
- Generates proper Category records
- Associates with creator user

## Folder Structure Required

```
Your-Images-Folder/
├── Photography/
│   ├── portrait-session.jpg
│   ├── wedding-package.png
│   └── product-shoot.jpg
├── Cleaning/
│   ├── deep-clean.jpg
│   ├── window-cleaning.png
│   └── carpet-cleaning.jpg
└── Repair/
    ├── home-repair.jpg
    └── appliance-fix.png
```

Each subfolder becomes a category, and images become services.

## Usage Instructions

### Option 1: PowerShell (Windows)
```powershell
cd C:\Users\User\Documents\serbizyu

# Basic usage
.\image-to-seeder.ps1 -SourcePath "C:\path\to\your\images" -SeederName "MyServicesSeeder"

# With custom price
.\image-to-seeder.ps1 -SourcePath "C:\MyImages" -SeederName "PhotographySeeder" -DefaultPrice 500

# Full example
.\image-to-seeder.ps1 `
  -SourcePath "C:\Users\User\Documents\Images" `
  -SeederName "ProfessionalServicesSeeder" `
  -DefaultPrice 250 `
  -LaravelPath "C:\Users\User\Documents\serbizyu"
```

### Option 2: Python
```bash
cd C:\Users\User\Documents\serbizyu

# Basic usage
python image-to-seeder.py --source "C:\path\to\your\images" --seeder MyServicesSeeder

# With custom price
python image-to-seeder.py --source "C:\MyImages" --seeder PhotographySeeder --price 500

# Full example
python image-to-seeder.py \
  --source "C:\Users\User\Documents\Images" \
  --seeder ProfessionalServicesSeeder \
  --price 250 \
  --output "./database/seeders"
```

## Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `--source` / `-s` | string | **Required** | Path to folder containing image subfolders |
| `--seeder` / `-n` | string | ImageServicesSeeder | Class name for generated seeder |
| `--price` / `-p` | decimal | 0 | Default price for all services |
| `--output` / `-o` (Python only) | string | ./database/seeders | Output directory for seeder file |
| `--LaravelPath` (PS only) | string | Current directory | Path to Laravel project |

## What Gets Created

The generator creates a seeder with:

```
✅ Service Records - One per image
✅ Category Records - From folder names  
✅ Image Attachments - Via Mediable
✅ Creator Assignment - Links to admin/creator user
✅ Default Metadata - Description, title, payment method
```

### Example Generated Service:
- **Title**: "Portrait Session" (from `portrait-session.jpg`)
- **Category**: "Photography" (from folder name)
- **Description**: "Professional Photography service - portrait-session"
- **Price**: Your specified default price
- **Payment Method**: XENDIT
- **Image**: Automatically attached to gallery

## Complete Setup Workflow

### Step 1: Prepare Your Images
```
Create folder structure:
C:\Users\User\Documents\MyServices\
├── Photography/
│   ├── headshot.jpg
│   └── family-photo.png
├── Web Design/
│   ├── portfolio-site.jpg
│   └── ecommerce.png
```

### Step 2: Generate Seeder
```powershell
.\image-to-seeder.ps1 `
  -SourcePath "C:\Users\User\Documents\MyServices" `
  -SeederName "MyServicesSeeder" `
  -DefaultPrice 150
```

### Step 3: Copy Images to Laravel
```bash
# Copy the folder to storage/app/public/
# Result: storage/app/public/images/Photography/headshot.jpg
#         storage/app/public/images/Web Design/portfolio-site.jpg
```

### Step 4: Link Storage (one-time)
```bash
php artisan storage:link
```

### Step 5: Run Seeder
```bash
php artisan db:seed --class=MyServicesSeeder
```

## What Happens During Seeding

1. **Finds creator user** - Looks for `creator` role or admin user
2. **Processes each image** - Iterates through all images
3. **Creates category** - Uses folder name (creates if doesn't exist)
4. **Creates service** - Inserts Service record with metadata
5. **Attaches image** - Links image via Mediable to 'gallery' collection
6. **Logs progress** - Shows ✅ for each created service

## Seeder Output Example

```
✅ Created: Portrait Session
✅ Created: Family Photo
✅ Created: Headshot
✅ Created: Portfolio Site
✅ Created: Ecommerce Site

✨ Service seeder completed!
```

## Important Notes

⚠️ **Creator User Required**
The seeder looks for a user with:
- `role = 'creator'` OR
- `is_admin = true`

If no creator found, seeding will fail. Create one first:
```bash
php artisan tinker
# Then:
User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'is_admin' => true]);
```

⚠️ **Image Path Structure**
Images must be in: `storage/app/public/images/{category}/{image_file}`

Example:
- `storage/app/public/images/Photography/headshot.jpg`
- `storage/app/public/images/Web Design/homepage.png`

⚠️ **Category Names**
Folder names become category names directly. Use:
- Clear names: ✅ "Photography", "Web Design"
- Avoid special chars: ❌ "$pecial@Characters"
- Spaces are OK: ✅ "Home Repair"

## Troubleshooting

### "No creator user found"
```bash
php artisan tinker
User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'is_admin' => true]);
exit
```

### "No image folders found"
- Verify folder path is correct
- Check folders contain images (jpg, png, gif, webp)
- Ensure subfolders exist (not just images in root)

### Images not showing
```bash
# Ensure storage is linked
php artisan storage:link

# Check permissions
chmod -R 755 storage/app/public/images
```

### Script won't run (PowerShell)
```powershell
# Allow script execution
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Then run
.\image-to-seeder.ps1 -SourcePath "C:\images"
```

## Advanced Options

### Different User for Each Category
Edit generated seeder and replace:
```php
'creator_id' => $creator->id,
```
With:
```php
'creator_id' => User::where('name', $data['category'])->first()->id ?? $creator->id,
```

### Custom Pricing Per Category
Edit generated seeder:
```php
$prices = [
    'Photography' => 500,
    'Design' => 250,
    'Repair' => 100,
];

// Then in loop:
'price' => $prices[$data['category']] ?? $defaultPrice,
```

### Add More Fields
Edit generated seeder to add fields before `Service::create()`:
```php
'description' => $data['description'] . ' - Premium Quality',
'pay_first' => false,  // Allow payment after
```

## Questions?

Generated seeder file: `database/seeders/{SeederName}.php`
Check the file to customize before running!
