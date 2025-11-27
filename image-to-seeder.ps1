# ============================================================================
# Laravel Service Seeder Generator from Sorted Image Folders
# ============================================================================
# This script converts a folder structure of sorted images into a Laravel 
# seeder that creates Service records with categories and attaches images.
#
# Usage:
#   .\image-to-seeder.ps1 -SourcePath "C:\path\to\images" -SeederName "MyServicesSeeder"
# ============================================================================

param(
    [Parameter(Mandatory = $true, HelpMessage = "Path to folder containing sorted image folders")]
    [string]$SourcePath,
    
    [Parameter(Mandatory = $false, HelpMessage = "Name of the seeder class (default: ImageServicesSeeder)")]
    [string]$SeederName = "ImageServicesSeeder",
    
    [Parameter(Mandatory = $false, HelpMessage = "Price for all services (optional)")]
    [decimal]$DefaultPrice = 0,
    
    [Parameter(Mandatory = $false, HelpMessage = "Laravel project path")]
    [string]$LaravelPath = (Get-Location).Path
)

# Validate source path
if (-not (Test-Path $SourcePath -PathType Container)) {
    Write-Host "❌ Source path not found: $SourcePath" -ForegroundColor Red
    exit 1
}

# Get all subdirectories (categories)
$categories = Get-ChildItem -Path $SourcePath -Directory | Where-Object { $_.BaseName -notmatch '^\..*' }

if ($categories.Count -eq 0) {
    Write-Host "❌ No category folders found in: $SourcePath" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Found $($categories.Count) categories:" -ForegroundColor Green
$categories | ForEach-Object { Write-Host "   - $_" }

# Build seeder content
$phpContent = "<?php`r`n`r`nnamespace Database\Seeders;`r`n`r`nuse Illuminate\Database\Seeder;`r`nuse App\Domains\Listings\Models\Service;`r`nuse App\Domains\Listings\Models\Category;`r`nuse App\Domains\Users\Models\User;`r`nuse App\Enums\PaymentMethod;`r`nuse Illuminate\Support\Facades\Storage;`r`n`r`nclass $SeederName extends Seeder`r`n{`r`n    /**`r`n     * Run the database seeds.`r`n     *`r`n     * This seeder creates Service records from images organized in category folders.`r`n     * Images should be in: storage/app/public/images/{category_name}/{image_file}`r`n     */`r`n    public function run(): void`r`n    {`r`n        // Get or create the default creator (usually admin user)`r`n        `$creator = User::where('role', 'creator')->orWhere('is_admin', true)->first();`r`n        `r`n        if (!`$creator) {`r`n            `$this->command->error('No creator user found. Please create a user first.');`r`n            return;`r`n        }`r`n`r`n        // Services to create with their images`r`n        `$servicesData = [`r`n"

# Process each category folder
$categories | ForEach-Object {
    $categoryName = $_.BaseName
    $categoryFolder = $_.FullName
    
    # Get all image files
    $imageExtensions = @('*.jpg', '*.jpeg', '*.png', '*.gif', '*.webp')
    $images = $imageExtensions | ForEach-Object { 
        Get-ChildItem -Path $categoryFolder -Filter $_ -File 
    }
    
    if ($images.Count -gt 0) {
        Write-Host "   📁 $categoryName - $($images.Count) image(s)" -ForegroundColor Cyan
        
        # Add service data for each image
        $images | ForEach-Object {
            $imageName = $_.BaseName
            $imageRelativePath = "images/$categoryName/$($_.Name)"
            $title = [cultureinfo]::CurrentCulture.TextInfo.ToTitleCase(($imageName -replace '_', ' ' -replace '-', ' '))
            
            $phpContent += "            [`r`n"
            $phpContent += "                'category' => '$categoryName',`r`n"
            $phpContent += "                'title' => '$title',`r`n"
            $phpContent += "                'description' => 'Professional $categoryName service - $imageName',`r`n"
            $phpContent += "                'price' => $DefaultPrice,`r`n"
            $phpContent += "                'image_path' => '$imageRelativePath',`r`n"
            $phpContent += "            ],`r`n"
        }
    }
}

$phpContent += "        ];`r`n`r`n"
$phpContent += "        foreach (`$servicesData as `$data) {`r`n"
$phpContent += "            // Get or create category`r`n"
$phpContent += "            `$category = Category::firstOrCreate(`r`n"
$phpContent += "                ['name' => `$data['category']],`r`n"
$phpContent += "                ['slug' => strtolower(str_replace(' ', '-', `$data['category']))]`r`n"
$phpContent += "            );`r`n`r`n"
$phpContent += "            // Create service`r`n"
$phpContent += "            `$service = Service::create([`r`n"
$phpContent += "                'title' => `$data['title'],`r`n"
$phpContent += "                'description' => `$data['description'],`r`n"
$phpContent += "                'price' => `$data['price'] ?? 0,`r`n"
$phpContent += "                'pay_first' => true,`r`n"
$phpContent += "                'payment_method' => PaymentMethod::XENDIT,`r`n"
$phpContent += "                'category_id' => `$category->id,`r`n"
$phpContent += "                'creator_id' => `$creator->id,`r`n"
$phpContent += "            ]);`r`n`r`n"
$phpContent += "            // Attach image if exists`r`n"
$phpContent += "            if (`$data['image_path'] && Storage::disk('public')->exists(`$data['image_path'])) {`r`n"
$phpContent += "                `$fullPath = Storage::disk('public')->path(`$data['image_path']);`r`n"
$phpContent += "                `$service->attachMedia(`$fullPath, ['gallery'], ['description' => `$data['title']]);`r`n"
$phpContent += "            }`r`n`r`n"
$phpContent += "            `$this->command->info(`"✅ Created: {`$service->title}`");`r`n"
$phpContent += "        }`r`n`r`n"
$phpContent += "        `$this->command->info(`"✨ Service seeder completed!`");`r`n"
$phpContent += "    }`r`n"
$phpContent += "}`r`n"

# Create seeder file
$seederPath = Join-Path $LaravelPath "database/seeders" $($SeederName + ".php")

# Ensure directory exists
if (-not (Test-Path (Split-Path $seederPath))) {
    New-Item -ItemType Directory -Path (Split-Path $seederPath) -Force | Out-Null
}

# Write seeder file
$phpContent | Out-File -FilePath $seederPath -Encoding UTF8 -Force

Write-Host "`n✅ Seeder created: $seederPath" -ForegroundColor Green
Write-Host "`n📋 Next steps:" -ForegroundColor Yellow
Write-Host "   1. Copy your image folders to: storage/app/public/images/" -ForegroundColor Gray
Write-Host "   2. Run: php artisan storage:link" -ForegroundColor Gray
Write-Host "   3. Run: php artisan db:seed --class=$SeederName" -ForegroundColor Gray
Write-Host "`n💡 Usage example:" -ForegroundColor Cyan
Write-Host "   ./image-to-seeder.ps1 -SourcePath 'C:\MyImages' -SeederName 'MyServicesSeeder' -DefaultPrice 100" -ForegroundColor Gray
