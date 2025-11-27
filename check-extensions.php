<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Check extension mismatches
$media_list = DB::table('media')->select('filename', 'extension')->limit(10)->get();

foreach ($media_list as $m) {
    $expected_file = "public/images/{$m->filename}.{$m->extension}";
    $exists = file_exists($expected_file);
    echo ($exists ? "✅" : "❌") . " {$m->filename}.{$m->extension} - Exists: $exists\n";
}
