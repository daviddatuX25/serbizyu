<?php

namespace App\Livewire\Traits;

use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait WithMedia
{
    use WithFileUploads;

    // Temporary property for new uploads via wire:model
    public $newFileUploads;

    // Accumulates all new file uploads for the session
    public array $newFiles = [];
    
    // Media IDs that user explicitly removed (marked for deletion)
    public array $imagesToRemove = [];
    
    // Existing media currently attached to model
    public array $existingImages = [];
    
    // Not used in non-destructive approach, but kept for compatibility
    public array $selectedImages = [];

    /**
     * Livewire hook to accumulate new file uploads instead of replacing them.
     */
    public function updatedNewFileUploads()
    {
        if (!$this->newFileUploads) {
            return;
        }

        // First, validate the newly uploaded files.
        $this->validate([
            'newFileUploads.*' => 'image|max:2048', // Adjust as needed
        ]);

        $filesToMerge = is_array($this->newFileUploads)
            ? $this->newFileUploads
            : [$this->newFileUploads];
        
        // Merge the new valid files into the main array
        $this->newFiles = array_merge($this->newFiles, $filesToMerge);

        // Clear the temporary property so the file input is reset
        $this->newFileUploads = null;

        Log::info('New files added', ['count' => count($filesToMerge), 'total' => count($this->newFiles)]);
    }

    /**
     * Load existing media from a model
     */
    protected function loadExistingMedia(Model $model)
    {
        // Ensure the relation is loaded to avoid N+1 issues if called in a loop
        if ($model->exists && !$model->relationLoaded('media')) {
            $model->load('media');
        }

        if ($model->exists) {
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
     * Remove a newly added file (not yet saved)
     */
    public function removeNewFile(int $index)
    {
        if (isset($this->newFiles[$index])) {
            array_splice($this->newFiles, $index, 1);
            
            Log::info('Removed new file', ['index' => $index]);
            session()->flash('info', 'New upload removed');
        }
    }

    /**
     * Mark an existing image for removal
     */
    public function removeExistingImage(int $id)
    {
        if (!in_array($id, $this->imagesToRemove)) {
            $this->imagesToRemove[] = $id;
            Log::info('Marked media for removal', ['media_id' => $id]);
        }
    }

    /**
     * Restore an image that was marked for removal
     */
    public function restoreExistingImage(int $id)
    {
        $this->imagesToRemove = array_filter(
            $this->imagesToRemove,
            fn($mediaId) => $mediaId !== $id
        );
        
        Log::info('Restored media from removal', ['media_id' => $id]);
        session()->flash('success', 'Image restored');
    }

    /**
     * Get uploaded files ready for processing
     */
    protected function getUploadedFiles(): array
    {
        return array_filter($this->newFiles, fn($file) => $file instanceof TemporaryUploadedFile);
    }
    
    /**
     * Reset media form state after save
     */
    protected function resetMediaForm()
    {
        $this->newFiles = [];
        $this->imagesToRemove = [];
        $this->selectedImages = [];
        $this->newFileUploads = null;
        
        Log::info('Media form reset');
    }
}