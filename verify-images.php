<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$count = DB::table('mediables')->where('tag', 'gallery')->count();
echo "Total media attachments: $count\n";

$services_with_media = DB::table('services')
    ->join('mediables', 'services.id', '=', 'mediables.mediable_id')
    ->where('mediables.mediable_type', 'App\\Domains\\Listings\\Models\\Service')
    ->where('mediables.tag', 'gallery')
    ->distinct('services.id')
    ->count('services.id');
    
echo "Services with attached media: $services_with_media\n\n";

$sample = DB::table('services')
    ->join('mediables', 'services.id', '=', 'mediables.mediable_id')
    ->join('media', 'mediables.media_id', '=', 'media.id')
    ->where('mediables.tag', 'gallery')
    ->select('services.title', 'media.filename', 'media.directory')
    ->limit(10)
    ->get();

echo "Sample attachments:\n";
foreach ($sample as $row) {
    echo "  ✅ {$row->title} -> {$row->directory}/{$row->filename}\n";
}
