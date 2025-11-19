<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Database\Eloquent\Model;

abstract class FormWithMedia extends Component
{
    use WithFileUploads;

    public array $newFiles = [];
    public array $imagesToRemove = [];
    public array $existingImages = [];
    public array $selectedImages = [];

    /**
     * Populate the existingImages array from a model's media relationship.
     *
     * @param Model $model The model instance that has media.
     */
    protected function loadExistingMedia(Model $model)
    {
        if ($model->exists && $model->relationLoaded('media')) {
            $this->existingImages = $model->media->map(fn($m) => [
                'id' => $m->id,
                'url' => $m->getUrl()
            ])->toArray();
        }
    }

    /**
     * Update new files from input
     */
    public function updatedNewFiles()
    {
        $this->validate([
            'newFiles.*' => 'image|max:2048', // max 2MB
        ]);
    }

    /**
     * Remove a newly added file
     */
    public function removeNewFile(int $index)
    {
        if (isset($this->newFiles[$index])) {
            array_splice($this->newFiles, $index, 1);
        }
    }

    /**
     * Remove an existing image
     */
    public function removeExistingImage(int $id)
    {
        if (!in_array($id, $this->imagesToRemove)) {
            $this->imagesToRemove[] = $id;
        }

        $this->existingImages = array_values(
            array_filter($this->existingImages, fn($img) => $img['id'] !== $id)
        );
    }

    /**
     * Select all images for batch operations.
     */
    public function selectAllImages()
    {
        $this->selectedImages = array_merge(
            collect($this->existingImages)->pluck('id')->all(),
            array_keys($this->newFiles)
        );
        // In a real UI, you would bind checkboxes to this array.
        // For now, this method simulates selecting all.
        session()->flash('info', 'All images selected. (Backend only)');
    }

    /**
     * Delete all selected images.
     */
    public function deleteSelected()
    {
        if (empty($this->selectedImages)) {
            session()->flash('error', 'No images selected to delete.');
            return;
        }

        // This is a placeholder implementation.
        // A real implementation would require checkboxes in the UI
        // bound to the $selectedImages property.
        session()->flash('info', 'Delete selected functionality not fully implemented.');

        // Reset selection
        $this->selectedImages = [];
    }


    /**
     * Return TemporaryUploadedFiles for processing
     */
    protected function getUploadedFiles(): array
    {
        // Add debug
        \Illuminate\Support\Facades\Log::info('getUploadedFiles called', [
            'newFiles_count' => count($this->newFiles),
            'newFiles' => $this->newFiles,
        ]);
        
        // Check each file
        $filtered = [];
        foreach ($this->newFiles as $file) {
            \Illuminate\Support\Facades\Log::info('Checking file', [
                'file' => $file,
                'is_temp' => $file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile,
                'class' => get_class($file),
            ]);
            
            if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $filtered[] = $file;
            }
        }
        
        \Illuminate\Support\Facades\Log::info('Filtered files', ['count' => count($filtered)]);
        
        return $filtered;
}
}
