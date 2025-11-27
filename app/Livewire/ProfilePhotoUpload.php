<?php

namespace App\Livewire;

use App\Livewire\Traits\WithMedia;
use App\Support\MediaConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProfilePhotoUpload extends Component
{
    use WithMedia;

    public $existingMedia;

    public function mount()
    {
        $user = Auth::user();
        $this->existingMedia = $user->media()
            ->where('tag', 'profile_image')
            ->get();
    }

    public function uploadPhoto(): void
    {
        // Validate that we have files to upload
        if (empty($this->newFiles)) {
            session()->flash('error', 'No file selected');

            return;
        }

        $user = Auth::user();

        try {
            // Process the first file from newFiles
            foreach ($this->newFiles as $file) {
                if (! $file instanceof TemporaryUploadedFile) {
                    continue;
                }

                // Validate file size
                $mediaConfig = new MediaConfig;
                $maxSizeKb = $mediaConfig->getUploadLimit('image');
                $fileSizeKb = $file->getSize() / 1024;

                if ($fileSizeKb > $maxSizeKb) {
                    session()->flash('error', "File size exceeds maximum limit of {$maxSizeKb}KB");

                    return;
                }

                // Remove existing profile images
                $user->media()
                    ->where('tag', 'profile_image')
                    ->delete();

                // Upload new profile image
                $sourcePath = $file->getRealPath();
                $destination = $mediaConfig->getDestination('image');

                $mediaUploader = app(\Plank\Mediable\MediaUploader::class);
                $media = $mediaUploader
                    ->fromSource($sourcePath)
                    ->toDestination('public', $destination)
                    ->upload();

                // Attach to user with profile_image tag
                $user->attachMedia($media, 'profile_image');

                Log::info('Profile photo uploaded', [
                    'user_id' => $user->id,
                    'media_id' => $media->id,
                    'file_size' => $fileSizeKb,
                ]);

                break; // Only upload the first file
            }

            $this->resetMediaForm();
            $this->existingMedia = $user->media()
                ->where('tag', 'profile_image')
                ->get();

            $this->dispatch('profile-photo-updated');
            session()->flash('success', 'Profile photo updated successfully');

        } catch (\Exception $e) {
            Log::error('Profile photo upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Failed to upload profile photo. Please try again.');
        }
    }

    public function deletePhoto(): void
    {
        try {
            $user = Auth::user();
            $user->media()
                ->where('tag', 'profile_image')
                ->delete();

            $this->existingMedia = collect();
            $this->dispatch('profile-photo-deleted');
            session()->flash('success', 'Profile photo removed');

            Log::info('Profile photo deleted', ['user_id' => $user->id]);

        } catch (\Exception $e) {
            Log::error('Profile photo deletion failed', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to remove profile photo');
        }
    }

    public function render()
    {
        return view('livewire.profile-photo-upload', [
            'existingMedia' => $this->existingMedia,
            'newFiles' => $this->newFiles,
        ]);
    }
}
