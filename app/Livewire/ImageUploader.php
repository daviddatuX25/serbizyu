<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class ImageUploader extends Component
{
    use WithFileUploads;

    public $newFiles = [];
    public $imagesToRemove = [];
    public $existingImages = [];

    public function mount(array $existingImages = [])
    {
        $this->existingImages = $existingImages;
    }

    public function removeNewFile($index)
    {
        array_splice($this->newFiles, $index, 1);
    }

    public function removeExistingImage($id)
    {
        $this->imagesToRemove[] = $id;
        $this->existingImages = array_values(
            array_filter($this->existingImages, fn($img) => $img['id'] !== $id)
        );
    }

    public function render()
    {
        return view('livewire.image-uploader');
    }
}
