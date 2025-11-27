<?php
$sourcePath = 'storage/app/public/images';
$destPath = 'public/images';

// Create destination if not exists
if (!is_dir($destPath)) {
    mkdir($destPath, 0755, true);
}

$categoryFolders = array_filter(
    scandir($sourcePath),
    fn($f) => is_dir("$sourcePath/$f") && !str_starts_with($f, '.')
);

$moved = 0;

foreach ($categoryFolders as $categoryFolder) {
    $categoryPath = "$sourcePath/$categoryFolder";
    $files = array_filter(
        scandir($categoryPath),
        fn($file) => is_file("$categoryPath/$file")
    );
    
    foreach ($files as $file) {
        $sourceFile = "$categoryPath/$file";
        $destFile = "$destPath/$file";
        
        copy($sourceFile, $destFile);
        echo "✅ Copied: $file\n";
        $moved++;
    }
}

echo "\n✨ Moved $moved images to public/images/\n";
