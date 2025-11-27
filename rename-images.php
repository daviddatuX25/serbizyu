<?php
$imagesPath = 'storage/app/public/images';
$categoryFolders = array_filter(
    scandir($imagesPath),
    fn($f) => is_dir("$imagesPath/$f") && !str_starts_with($f, '.')
);

$renamed = 0;

foreach ($categoryFolders as $categoryFolder) {
    $categoryPath = "$imagesPath/$categoryFolder";
    $files = array_filter(
        scandir($categoryPath),
        fn($file) => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])
            && is_file("$categoryPath/$file")
    );
    
    foreach ($files as $file) {
        $oldPath = "$categoryPath/$file";
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        
        // New path WITHOUT extension (matches what we stored in database)
        $newPath = "$categoryPath/$filename";
        
        if ($oldPath !== $newPath && !file_exists($newPath)) {
            rename($oldPath, $newPath);
            echo "✅ Renamed: $file -> $filename\n";
            $renamed++;
        }
    }
}

echo "\n✨ Renamed $renamed files!\n";
