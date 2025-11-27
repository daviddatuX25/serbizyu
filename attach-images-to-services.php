<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Domains\Listings\Models\Service;
use Plank\Mediable\Media;
use Illuminate\Support\Facades\Storage;

$imagesPath = storage_path('app/public/images');
$categoryFolders = array_filter(
    scandir($imagesPath),
    fn($f) => is_dir("$imagesPath/$f") && !str_starts_with($f, '.')
);

$imageIndex = 0;
$images = [];

// Collect all images
foreach ($categoryFolders as $categoryFolder) {
    $categoryPath = "$imagesPath/$categoryFolder";
    $categoryImages = array_filter(
        scandir($categoryPath),
        fn($file) => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])
            && is_file("$categoryPath/$file")
    );
    
    foreach ($categoryImages as $image) {
        $images[] = [
            'path' => "images/$categoryFolder/$image",
            'filename' => $image,
            'fullPath' => "$categoryPath/$image"
        ];
    }
}

echo "Found " . count($images) . " images\n";

// Get all services
$services = Service::all();
echo "Found " . count($services) . " services\n\n";

$attached = 0;

// Clear existing media to avoid UNIQUE constraint errors
Media::truncate();

// Attach one image per service (cycle through images)
foreach ($services as $index => $service) {
    $imageData = $images[$index % count($images)];
    
    try {
        // Extract filename without extension
        $filename = pathinfo($imageData['filename'], PATHINFO_FILENAME);
        $extension = pathinfo($imageData['filename'], PATHINFO_EXTENSION);
        
        // Create media record with filename WITHOUT extension
        $media = new Media();
        $media->filename = $filename;  // Without extension
        $media->extension = $extension;  // Just the extension
        $media->disk = 'public';
        $media->directory = dirname($imageData['path']);
        $media->mime_type = mime_content_type($imageData['fullPath']);
        $media->aggregate_type = 'image';
        $media->size = filesize($imageData['fullPath']);
        $media->save();
        
        // Attach to service using attachMedia method
        $service->attachMedia($media, ['gallery']);
        
        echo "✅ {$service->title} -> {$imageData['filename']}\n";
        $attached++;
    } catch (\Exception $e) {
        echo "❌ {$service->title}: {$e->getMessage()}\n";
    }
}

echo "\n✨ Attached $attached images to services!\n";
