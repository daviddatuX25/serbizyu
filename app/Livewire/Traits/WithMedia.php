<?php

namespace App\Livewire\Traits;

use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Trait for Non-Destructive Media Handling in Livewire Components.
 *
 * This trait provides functionality to manage file uploads, and the removal of existing media
 * in a non-destructive manner. It queues changes (new uploads, removals) and provides
 * methods to get this data, which can then be passed to a service layer for processing.
 */
trait WithMedia
{
    use WithFileUploads;

    // New uploads from user, stored as TemporaryUploadedFile objects.
    public array $newFiles = [];
    
    // An array of media IDs that the user has marked for deletion.
    public array $imagesToRemove = [];
    
    // An array of existing media currently attached to the model, formatted for display.
    public array $existingImages = [];
    
    // This can be used if you need to manage a selection of images, but is not central to the non-destructive approach.
    public array $selectedImages = [];

    /**
     * Load existing media from a model and format it for display.
     * This should be called in the component's mount method.
     */
    protected function loadExistingMedia(Model $model): void
    {
        if ($model->exists && $model->relationLoaded('media')) {
            $this->existingImages = $model->media->map(fn($m) => [
                'id' => $m->id,
                'url' => $m->getUrl(),
                'filename' => $m->filename,
                'created_at' => $m->created_at->format('M d, Y'),
            ])->toArray();
        }
    }

    /**
     * Livewire lifecycle hook that validates newly uploaded files.
     */
    public function updatedNewFiles(): void
    {
        $this->validate([
            'newFiles.*' => 'image|max:5120', // 5MB max, adjust as needed.
        ]);
    }

    /**
     * Remove a new file from the upload queue before it is saved.
     */
    public function removeNewFile(int $index): void
    {
        if (isset($this->newFiles[$index])) {
            array_splice($this->newFiles, $index, 1);
            session()->flash('info', 'New upload removed.');
        }
    }

    /**
     * Mark an existing, saved image for removal upon saving the form.
     * This does not delete the file immediately.
     */
    public function removeExistingImage(int $id): void
    {
        if (!in_array($id, $this->imagesToRemove)) {
            $this->imagesToRemove[] = $id;
        }

        // Cosmetically remove from the display list.
        $this->existingImages = array_values(
            array_filter($this->existingImages, fn($img) => $img['id'] !== $id)
        );

        session()->flash('info', 'Image marked for removal.');
    }

    /**
     * Restore an image that was previously marked for removal.
     * This requires the original model to reload the media relationship.
     */
    public function restoreExistingImage(int $id, Model $model): void
    {
        $this->imagesToRemove = array_filter(
            $this->imagesToRemove,
            fn($mediaId) => $mediaId !== $id
        );

        // Reload all existing images to bring the restored one back into the display.
        $this->loadExistingMedia($model);
        
        session()->flash('success', 'Image restored.');
    }

    /**
     * Filter the $newFiles array to get only the actual uploaded file objects.
     */
    protected function getUploadedFiles(): array
    {
        return array_filter($this->newFiles, fn($file) => $file instanceof TemporaryUploadedFile);
    }

    /**
     * A helper to get a structured array of media changes to pass to a service.
     */
    protected function getMediaData(): array
    {
        return [
            'newFiles' => $this->getUploadedFiles(),
            'imagesToRemove' => $this->imagesToRemove,
        ];
    }

    /**
     * Reset the media-related properties of the form.
     * Call this after a successful save operation.
     */
    protected function resetMediaForm(): void
    {
        $this->newFiles = [];
        $this->imagesToRemove = [];
        $this->selectedImages = [];
        $this->reset('newFiles'); // Clears the file input visually for the user.
    }
}
