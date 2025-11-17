<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\MediaUploader;
use Illuminate\Support\Facades\Storage;

class OpenOfferService
{
    public function createOpenOffer(User $user, array $data, array $newMedia = []): OpenOffer
    {
        return DB::transaction(function () use ($user, $data, $newMedia) {
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

            if (!empty($newMedia)) {
                $mediaUploader = app(MediaUploader::class);
                foreach ($newMedia as $file) {
                    // Assuming $file is a path to a temporary file stored by Livewire
                    $mediaUploader->fromSource(Storage::disk('local')->path($file))
                        ->toDirectory('open-offers')
                        ->upload()
                        ->attachTo($openOffer, 'images');
                    Storage::disk('local')->delete($file); // Clean up temp file
                }
            }

            return $openOffer;
        });
    }

    public function updateOpenOffer(OpenOffer $openOffer, array $data, array $newMedia = []): OpenOffer
    {
        return DB::transaction(function () use ($openOffer, $data, $newMedia) {
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

            // Attach new media
            if (!empty($newMedia)) {
                $mediaUploader = app(MediaUploader::class);
                foreach ($newMedia as $file) {
                    // Assuming $file is a path to a temporary file stored by Livewire
                    $mediaUploader->fromSource(Storage::disk('local')->path($file))
                        ->toDirectory('open-offers')
                        ->upload()
                        ->attachTo($openOffer, 'images');
                    Storage::disk('local')->delete($file); // Clean up temp file
                }
            }

            return $openOffer;
        });
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
        $openOffer->update(['status' => 'closed']);
        return $openOffer;
    }

    /**
     * Get filtered open offers (collection)
     */
    public function getFilteredOffers(array $filters = [])
    {
        $query = OpenOffer::with(['creator.media', 'address', 'media', 'bids']);

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

        $sortable = ['created_at', 'budget_from', 'budget_to', 'title'];
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_direction'] ?? 'desc';

        if (in_array($sortBy, $sortable)) {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query->get();
    }
}