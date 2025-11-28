<?php

$categoriesPath = json_decode(file_get_contents(__DIR__.'/categories.json'), true);
$randomServicesPath = __DIR__.'/random_services';

if (! $categoriesPath) {
    echo "Error: Could not read categories.json\n";
    exit(1);
}

foreach ($categoriesPath as $category => $data) {
    $categoryPath = $randomServicesPath.'/'.$category;

    // Create services and openoffers directories
    $servicesDir = $categoryPath.'/services';
    $openoffersDir = $categoryPath.'/openoffers';

    if (! is_dir($servicesDir)) {
        mkdir($servicesDir, 0755, true);
        echo "Created: $servicesDir\n";
    }

    if (! is_dir($openoffersDir)) {
        mkdir($openoffersDir, 0755, true);
        echo "Created: $openoffersDir\n";
    }

    // Split images sequentially: 1st -> services, 2nd -> openoffers, 3rd -> services, etc.
    $images = $data['images'];
    foreach ($images as $index => $image) {
        $source = $categoryPath.'/'.$image;

        if (! file_exists($source)) {
            echo "Warning: File not found - $source\n";

            continue;
        }

        // Sequential assignment: odd (0, 2, 4...) -> services, even (1, 3, 5...) -> openoffers
        if ($index % 2 === 0) {
            $destination = $servicesDir.'/'.$image;
            copy($source, $destination);
            echo "  ✓ Copied to services: $image\n";
        } else {
            $destination = $openoffersDir.'/'.$image;
            copy($source, $destination);
            echo "  ✓ Copied to openoffers: $image\n";
        }
    }

    echo "\nCategory [$category]: ".count($images)." images split\n";
    echo "---\n";
}

echo "\n✅ All images organized into services/ and openoffers/ subdirectories!\n";
