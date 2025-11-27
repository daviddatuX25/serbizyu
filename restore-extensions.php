<?php
$imagesPath = 'storage/app/public/images';
$categoryFolders = array_filter(
    scandir($imagesPath),
    fn($f) => is_dir("$imagesPath/$f") && !str_starts_with($f, '.')
);

$renamed = 0;

foreach ($categoryFolders as $categoryFolder) {
    $categoryPath = "$imagesPath/$categoryFolder";
    $files = scandir($categoryPath);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $fullPath = "$categoryPath/$file";
        if (!is_file($fullPath)) continue;
        
        // Check if file has no extension (our renamed files)
        if (pathinfo($file, PATHINFO_EXTENSION) === '') {
            // Detect MIME type to determine extension
            $mimeType = mime_content_type($fullPath);
            $extension = match($mimeType) {
                'image/jpeg' => 'jpeg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => null
            };
            
            if ($extension) {
                $newPath = "$fullPath.$extension";
                rename($fullPath, $newPath);
                echo "✅ Restored extension: $file -> {$file}.$extension\n";
                $renamed++;
            }
        }
    }
}

echo "\n✨ Restored extensions on $renamed files!\n";
