<?php

namespace App\Domains\Common\Services;

use App\Domains\Common\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Attaches an uploaded file to a model and stores it.
     *
     * @param Model $model The Eloquent model to attach the image to.
     * @param UploadedFile $file The file to upload.
     * @param string|null $collection The collection name to group the image under.
     * @param array $attributes Additional attributes for the Image model.
     * @return Image
     */
    public function attach(Model $model, UploadedFile $file, ?string $collection = 'default', array $attributes = []): Image
    {
        // Define a dynamic path based on the model type and collection
        $modelType = strtolower(class_basename($model));
        $path = $file->store("public/{$modelType}/{$collection}");

        // If setting this as primary, ensure no others in the same collection are.
        if (!empty($attributes['is_primary'])) {
            $model->images()->where('collection_name', $collection)->update(['is_primary' => false]);
        }

        $defaults = [
            'path' => Storage::url($path),
            'collection_name' => $collection,
            'alt_text' => $model->title ?? $model->name ?? 'image',
        ];

        return $model->images()->create(array_merge($defaults, $attributes));
    }

    /**
     * Deletes an Image record and its corresponding file from storage.
     *
     * @param Image $image
     * @return bool|null
     */
    public function delete(Image $image): ?bool
    {
        // Convert public URL back to a storage path
        $storagePath = str_replace('/storage/', 'public/', $image->path);
        Storage::delete($storagePath);

        return $image->delete();
    }

    /**
     * Synchronizes a collection of images for a model.
     *
     * @param Model $model The model to sync images for.
     * @param string $collection The collection to synchronize.
     * @param array $newImageIdsToRemove IDs of existing images to remove.
     * @param array $newFilesToAttach Array of new UploadedFile objects to attach.
     */
    public function sync(Model $model, string $collection, array $newImageIdsToRemove = [], array $newFilesToAttach = []): void
    {
        // 1. Remove images marked for deletion
        if (!empty($newImageIdsToRemove)) {
            $images = Image::whereIn('id', $newImageIdsToRemove)->get();
            foreach ($images as $image) {
                if ($image->imageable_id === $model->id && $image->imageable_type === get_class($model)) {
                    $this->delete($image);
                }
            }
        }

        // 2. Attach new files
        if (!empty($newFilesToAttach)) {
            foreach ($newFilesToAttach as $file) {
                $this->attach($model, $file, $collection);
            }
        }
    }
}
