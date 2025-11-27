<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Get all media records
$all_media = DB::table('media')->get();

$fixed = 0;
foreach ($all_media as $media) {
    // Check what file actually exists
    $base_path = "public/images/{$media->filename}";
    
    // Find the actual file (try different extensions)
    $actual_ext = null;
    foreach (['jpeg', 'jpg', 'png', 'gif', 'webp'] as $ext) {
        if (file_exists("$base_path.$ext")) {
            $actual_ext = $ext;
            break;
        }
    }
    
    if ($actual_ext && $actual_ext !== $media->extension) {
        DB::table('media')->where('id', $media->id)->update(['extension' => $actual_ext]);
        echo "✅ Fixed: {$media->filename} - Changed from .{$media->extension} to .{$actual_ext}\n";
        $fixed++;
    }
}

echo "\n✨ Fixed $fixed extension mismatches!\n";
