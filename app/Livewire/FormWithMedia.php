<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

abstract class FormWithMedia extends Component
{
    use WithFileUploads;

    public array $newFiles = [];
    public array $imagesToRemove = [];
    public array $existingImages = [];

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
