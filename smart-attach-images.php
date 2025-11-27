<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Get all services and media
$services = DB::table('services')->select('id', 'title', 'category_id')->orderBy('id')->get();
$mediaList = DB::table('media')->select('id', 'filename')->get();
$categories = DB::table('categories')->select('id', 'name')->get()->keyBy('id');

echo "Matching " . count($services) . " services to " . count($mediaList) . " images by keyword...\n\n";

// Clear existing attachments
DB::table('mediables')->delete();

$matched = 0;
$used_media = [];

foreach ($services as $service) {
    $cat_name = $categories[$service->category_id]->name ?? 'Unknown';
    
    // Create search keywords from service title and category
    $search_keywords = strtolower($service->title . ' ' . $cat_name);
    $search_words = preg_split('/\s+|[_-]/', $search_keywords, -1, PREG_SPLIT_NO_EMPTY);
    
    // Find best matching media
    $best_match = null;
    $best_score = 0;
    
    foreach ($mediaList as $media) {
        // Skip if already used 3+ times
        if (isset($used_media[$media->id]) && count($used_media[$media->id]) >= 3) continue;
        
        $media_keywords = strtolower(str_replace('_', ' ', $media->filename));
        $media_words = preg_split('/\s+/', $media_keywords, -1, PREG_SPLIT_NO_EMPTY);
        
        // Count matching words
        $matching_words = count(array_intersect($search_words, $media_words));
        
        // Calculate similarity score
        similar_text($search_keywords, $media_keywords, $percent);
        $score = ($matching_words * 30) + ($percent * 0.7);
        
        if ($score > $best_score) {
            $best_score = $score;
            $best_match = $media;
        }
    }
    
    // If no good match found, pick first available
    if (!$best_match) {
        foreach ($mediaList as $media) {
            if (!isset($used_media[$media->id]) || count($used_media[$media->id]) < 3) {
                $best_match = $media;
                break;
            }
        }
    }
    
    if ($best_match) {
        // Track usage
        if (!isset($used_media[$best_match->id])) {
            $used_media[$best_match->id] = [];
        }
        $used_media[$best_match->id][] = $service->id;
        
        // Insert mediable record
        DB::table('mediables')->insert([
            'media_id' => $best_match->id,
            'mediable_id' => $service->id,
            'mediable_type' => 'App\\Domains\\Listings\\Models\\Service',
            'order' => 1,
            'tag' => 'gallery',
        ]);
        
        $match_indicator = $best_score >= 50 ? "✅" : "⚠️";
        echo "$match_indicator {$service->title} ({$cat_name}) -> {$best_match->filename}\n";
        $matched++;
    }
}

echo "\n✨ Matched $matched services!\n";
