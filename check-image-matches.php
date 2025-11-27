<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$count = DB::table('mediables')->count();
echo "Total mediables: $count\n";

$sample = DB::table('mediables')->limit(5)->get();
echo "Sample mediables:\n";
foreach ($sample as $m) {
    echo "  ID: {$m->id}, Service ID: {$m->mediable_id}, Media ID: {$m->media_id}, Tag: {$m->tag}\n";
}

$media_count = DB::table('media')->count();
echo "\nTotal media: $media_count\n";

$services_count = DB::table('services')->count();
echo "Total services: $services_count\n";
