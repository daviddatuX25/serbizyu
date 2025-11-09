<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Domains\Common\Models\Image;
use Illuminate\Support\Collection;

class ImageUploader extends Component
{
    use WithFileUploads;

    public $model;
    public Collection $existingImages;
    public $newImages = [];
    public $imagesToRemove = [];

    public function mount($model = null)
    {
        $this->model = $model;
        $this->existingImages = $model ? $model->images()->orderBy('order_index')->get() : collect();
    }

    public function updatedNewImages()
    {
        $this->validate([
            'newImages.*' => 'image|max:2048', // 2MB Max
        ]);
    }

    public function removeNewImage($index)
    {
        array_splice($this->newImages, $index, 1);
    }

    public function markForRemoval($imageId)
    {
        // Add to removal list
        $this->imagesToRemove[] = $imageId;

        // Remove from the displayed existing images
        $this->existingImages = $this->existingImages->filter(function ($image) use ($imageId) {
            return $image->id != $imageId;
        });
    }

    public function render()
    {
        return view('livewire.image-uploader');
    }
}