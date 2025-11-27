<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Update media directory paths to just 'images'  
DB::table('media')->update([
    'directory' => DB::raw("'images'")
]);

echo "✅ Updated all media directory paths to 'images'\n";

// Verify
$count = DB::table('media')->count();
echo "Total media records: $count\n";

$sample = DB::table('media')->select('filename', 'extension', 'directory')->limit(3)->get();
foreach ($sample as $media) {
    echo "  - {$media->filename}.{$media->extension} in {$media->directory}\n";
}
