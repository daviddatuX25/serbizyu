<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\MediaUploader;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Enums\OpenOfferStatus;

class OpenOfferService
{
    public function createOpenOffer(User $user, array $data, array $uploadedFiles = []): OpenOffer
    {
        $openOffer = $user->openOffers()->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'budget' => $data['budget'],
            'category_id' => $data['category_id'],
            'deadline' => $data['deadline'] ?? null,
            'workflow_template_id' => $data['workflow_template_id'] ?? null,
            'pay_first' => $data['pay_first'] ?? false,
            'address_id' => $data['address_id'] ?? null,
        ]);

        $this->handleUploadedFiles($openOffer, $uploadedFiles);

        return $openOffer;
    }

    public function updateOpenOffer(OpenOffer $openOffer, array $data, array $uploadedFiles = []): OpenOffer
    {
        $openOffer->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'budget' => $data['budget'],
            'category_id' => $data['category_id'],
            'deadline' => $data['deadline'] ?? null,
            'workflow_template_id' => $data['workflow_template_id'] ?? null,
            'pay_first' => $data['pay_first'] ?? false,
            'address_id' => $data['address_id'] ?? null,
        ]);

        // Handle images to remove
        if (isset($data['images_to_remove']) && !empty($data['images_to_remove'])) {
            $openOffer->media()->whereIn('id', $data['images_to_remove'])->delete();
        }

        $this->handleUploadedFiles($openOffer, $uploadedFiles);

        return $openOffer;
    }

    protected function handleUploadedFiles(OpenOffer $openOffer, array $files): void
    {
        $mediaUploader = app(MediaUploader::class);

        foreach ($files as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                try {
                    $sourcePath = $file->getRealPath();

                    $media = $mediaUploader->fromSource($sourcePath)
                        ->toDestination('public', 'open-offers')
                        ->upload();

                    $openOffer->attachMedia($media, 'images');
                } catch (\Exception $e) {
                    // Log error but continue
                    \Log::error('Failed to upload media for open offer: ' . $e->getMessage());
                }
            }
        }
    }

    public function deleteOpenOffer(OpenOffer $openOffer): void
    {
        DB::transaction(function () use ($openOffer) {
            $openOffer->detachMedia('images');
            $openOffer->delete();
        });
    }

    public function closeOpenOffer(OpenOffer $openOffer): OpenOffer
    {
        $openOffer->update(['status' => OpenOfferStatus::CLOSED]);
        return $openOffer;
    }

    public function renewOpenOffer(OpenOffer $openOffer): OpenOffer
    {
        $maxDays = config('listings.open_offer_max_days', 30);
        $newDeadline = now()->addDays($maxDays);

        $openOffer->update([
            'status' => OpenOfferStatus::OPEN,
            'deadline' => $newDeadline,
        ]);

        return $openOffer;
    }

    /**
     * Get filtered open offers (collection)
     */
    public function getFilteredOffers(array $filters = [])
    {
        $query = OpenOffer::whereHas('creator')->with(['creator.verification', 'creator.media', 'address', 'media', 'bids']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (!empty($filters['location_code'])) {
            $locationCode = $filters['location_code'];
            $query->whereHas('address', function ($q) use ($locationCode) {
                $q->where('api_id', 'like', "{$locationCode}%");
            });
        }

        $sortable = ['created_at', 'budget_from', 'budget_to', 'title'];
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_direction'] ?? 'desc';

        if (in_array($sortBy, $sortable)) {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query->get();
    }
}