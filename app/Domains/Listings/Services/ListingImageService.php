<?php
namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\ListingImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; 

class ListingImageService
{
    public function attachToModel(Model $model, $file)
    {
        // Step 1: Upload image
        $path = $file->store('uploads/services', 'public');

        // Step 2: Create image record and attach morphically
        return $model->images()->create([
            'path' => $path,
            'alt_text' => $file->getClientOriginalName(),
            'is_primary' => false,
        ]);
    }

    // Optional helper for direct create (not attached)
    public function createListingImage($file): ListingImage
    {
        $path = $file->store('uploads/temp', 'public');
        return ListingImage::create(['path' => $path]);
    }
}


?>