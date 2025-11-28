<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Users\Models\User;
use App\Enums\OpenOfferStatus;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Plank\Mediable\MediaUploader;

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
        if (isset($data['images_to_remove']) && ! empty($data['images_to_remove'])) {
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
                    \Log::error('Failed to upload media for open offer: '.$e->getMessage());
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

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }

        if (! empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        // Filter by region, province, and/or city
        if (! empty($filters['region']) || ! empty($filters['province']) || ! empty($filters['city'])) {
            $query->whereHas('address', function ($q) use ($filters) {
                if (! empty($filters['city'])) {
                    // Normalize city code from 9 digits to 6 digits for database matching
                    $cityCode = strlen($filters['city']) === 9 ? substr($filters['city'], 0, 6) : $filters['city'];
                    $q->where('city', $cityCode);
                } elseif (! empty($filters['province'])) {
                    // Normalize province code from 9 digits to 6 digits for database matching
                    $provinceCode = strlen($filters['province']) === 9 ? substr($filters['province'], 0, 6) : $filters['province'];
                    $q->where('province', $provinceCode);
                } elseif (! empty($filters['region'])) {
                    // Normalize region code from 9 digits to 6 digits for database matching
                    $regionCode = strlen($filters['region']) === 9 ? substr($filters['region'], 0, 6) : $filters['region'];
                    $q->where('region', $regionCode);
                }
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

    /**
     * Get latest open offers (collection) with limit
     */
    public function getLatestOffers(int $limit = 10)
    {
        return OpenOffer::whereHas('creator')->with(['creator.verification', 'creator.media', 'address', 'media', 'bids'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
