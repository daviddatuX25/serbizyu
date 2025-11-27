<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Domains\Listings\Models\Service;

$service = Service::first();
echo "Service: " . $service->title . "\n";
echo "Media count: " . $service->media->count() . "\n";

if($service->media->isNotEmpty()) {
    $media = $service->media->first();
    echo "First media filename: " . $media->filename . "\n";
    echo "First media disk: " . $media->disk . "\n";
    echo "First media directory: " . $media->directory . "\n";
    echo "First media URL: " . $media->getUrl() . "\n";
    
    // Check file existence
    $filePath = storage_path('app/public/' . $media->directory . '/' . $media->filename);
    echo "File path: " . $filePath . "\n";
    echo "File exists: " . (file_exists($filePath) ? "YES" : "NO") . "\n";
}
