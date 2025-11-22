<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Non-Destructive Media Form Handler
 * 
 * Keeps all existing media unless explicitly removed by user
 * Appends new uploads to existing collection
 */
abstract class FormWithMedia extends Component
{
    use WithFileUploads;

    // New uploads from user
    public array $newFiles = [];
    
    // Media IDs that user explicitly removed (marked for deletion)
    public array $imagesToRemove = [];
    
    // Existing media currently attached to model
    public array $existingImages = [];
    
    // Not used in non-destructive approach, but kept for compatibility
    public array $selectedImages = [];

    /**
     * Load existing media from a model
     * 
     * This captures the CURRENT state of media.
     * When user removes an image, it goes to $imagesToRemove
     * When user adds files, they go to $newFiles
     */
    protected function loadExistingMedia(Model $model)
    {
        if ($model->exists && $model->relationLoaded('media')) {
            $this->existingImages = $model->media->map(fn($m) => [
                'id' => $m->id,
                'url' => $m->getUrl(),
                'filename' => $m->filename,
                'created_at' => $m->created_at->format('M d, Y'),
            ])->toArray();

            Log::info('Loaded existing media', [
                'model' => class_basename($model),
                'count' => count($this->existingImages)
            ]);
        }
    }

    /**
     * Validate new file uploads
     */
    public function updatedNewFiles()
    {
        $this->validate([
            'newFiles.*' => 'image|max:2048', // Adjust as needed
        ]);

        Log::info('New files validated', ['count' => count($this->newFiles)]);
    }

    /**
     * Remove a newly added file (not yet saved)
     */
    public function removeNewFile(int $index)
    {
        if (isset($this->newFiles[$index])) {
            $removed = $this->newFiles[$index];
            array_splice($this->newFiles, $index, 1);
            
            Log::info('Removed new file', ['index' => $index]);
            session()->flash('info', 'New upload removed');
        }
    }

    /**
     * Mark an existing image for removal
     * 
     * Does NOT delete immediately - just marks it for deletion on save
     */
    public function removeExistingImage(int $id)
    {
        // Add to removal queue only if not already there
        if (!in_array($id, $this->imagesToRemove)) {
            $this->imagesToRemove[] = $id;
            
            Log::info('Marked media for removal', ['media_id' => $id]);
        }

        // Remove from display (cosmetic - shows as "pending deletion")
        $this->existingImages = array_values(
            array_filter($this->existingImages, fn($img) => $img['id'] !== $id)
        );

        session()->flash('info', 'Image marked for removal');
    }

    /**
     * Restore an image that was marked for removal
     * 
     * Call this if user changes their mind
     */
    public function restoreExistingImage(int $id, Model $model)
    {
        // Remove from deletion queue
        $this->imagesToRemove = array_filter(
            $this->imagesToRemove,
            fn($mediaId) => $mediaId !== $id
        );

        // Reload existing images to show it again
        $this->loadExistingMedia($model);
        
        Log::info('Restored media from removal', ['media_id' => $id]);
        session()->flash('success', 'Image restored');
    }

    /**
     * Get uploaded files ready for processing
     */
    protected function getUploadedFiles(): array
    {
        $filtered = [];
        
        foreach ($this->newFiles as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $filtered[] = $file;
            }
        }

        Log::info('Preparing uploaded files', [
            'total_newFiles' => count($this->newFiles),
            'filtered_count' => count($filtered),
        ]);

        return $filtered;
    }

    /**
     * Get data to pass to service layer
     * 
     * Returns both the files to add AND the IDs to remove
     */
    protected function getMediaData(): array
    {
        return [
            'newFiles' => $this->getUploadedFiles(),
            'imagesToRemove' => $this->imagesToRemove,
        ];
    }

    /**
     * Reset media form state after save
     */
    protected function resetMediaForm()
    {
        $this->newFiles = [];
        $this->imagesToRemove = [];
        $this->selectedImages = [];
        
        Log::info('Media form reset');
    }
}