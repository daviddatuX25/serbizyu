<?php

$randomServicesPath = __DIR__.'/random_services';
$categoriesData = [];

if (! is_dir($randomServicesPath)) {
    echo "Error: random_services directory not found\n";
    exit(1);
}

$categories = array_filter(scandir($randomServicesPath), function ($item) use ($randomServicesPath) {
    return $item !== '.' && $item !== '..' && is_dir($randomServicesPath.'/'.$item);
});

sort($categories);

foreach ($categories as $category) {
    $categoryPath = $randomServicesPath.'/'.$category;

    // Get all files (skip directories)
    $files = array_filter(scandir($categoryPath), function ($item) use ($categoryPath) {
        return $item !== '.' && $item !== '..' && is_file($categoryPath.'/'.$item);
    });

    // Filter for image files only
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic'];
    $imageFiles = array_filter($files, function ($file) use ($imageExtensions) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        return in_array($ext, $imageExtensions);
    });

    // Sort image files alphabetically
    sort($imageFiles);

    $categoriesData[$category] = [
        'image_count' => count($imageFiles),
        'images' => array_values($imageFiles),
    ];
}

// Write to categories.json
$jsonPath = __DIR__.'/categories.json';
file_put_contents($jsonPath, json_encode($categoriesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo 'Generated categories.json with '.count($categoriesData)." categories\n";
echo 'Total images: '.array_sum(array_map(fn ($cat) => $cat['image_count'], $categoriesData))."\n";
echo "File saved to: $jsonPath\n";
