<?php

namespace App\Domains\Common\Services;

use App\Domains\Common\Models\Address;
use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;


class AddressService
{
    /**
     * Get all addresses for the currently authenticated user.
     *
     * @return Collection
     */
    public function getAddressesForUser(): Collection
    {
        /** @var \App\Domains\Users\Models\User $user */
        $user = Auth::user();
        return $user->addresses()->withPivot('is_primary')->get();
    }

    public function getAddress(int $addressId): ?Address
    {
        return Address::find($addressId);
    }

    /**
     * Create a new address for the authenticated user.
     *
     * @param array $data
     * @return Address
     */
    public function createAddressForUser(array $data): Address
    {
        /** @var User $user */
        $user = Auth::user();

        // Extract is_primary from data
        $isPrimary = isset($data['is_primary']) ? (bool) $data['is_primary'] : false;
        
        // Remove is_primary and id from address data since they're not address fields
        // Don't filter empty strings as they might be intentional
        $addressData = collect($data)
            ->except(['is_primary', 'id'])
            ->toArray();

        return DB::transaction(function () use ($user, $addressData, $isPrimary) {
            // If the new address is primary, unset other primary addresses
            if ($isPrimary) {
                $this->unsetAllPrimaryAddresses($user);
            }

            // Create the address
            $address = Address::create($addressData);

            // Attach to user with pivot data
            $user->addresses()->attach($address->id, [
                'is_primary' => $isPrimary,
            ]);

            return $address->fresh();
        });
    }

    /**
     * Update an existing address for a user.
     *
     * @param int $addressId
     * @param array $data
     * @return Address
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateService(Service $service, array $data, \Plank\Mediable\MediaUploader $uploader): Service
    {
        $service->update(collect($data)->except(['new_images', 'images_to_remove'])->toArray());

        // Remove images
        if (!empty($data['images_to_remove'])) {
            foreach ($data['images_to_remove'] as $mediaId) {
                $service->detachMedia($mediaId);
            }
        }

        \Debugbar::info('Images to remove: ', $data['images_to_remove'] ?? []);
        \Debugbar::info('New images: ', $data['new_images'] ?? []);
        
        // Attach new images from temp storage (Livewire uploaded)
        if (!empty($data['new_images']) && is_array($data['new_images'])) {
            foreach ($data['new_images'] as $tempPath) {
                try {
                    // Get full path to temp file
                    $fullPath = \Storage::disk('local')->path($tempPath);
                    
                    if (file_exists($fullPath)) {
                        // Upload to Mediable
                        $media = $uploader->fromSource($tempPath)
                            ->toDestination('public', 'services')
                            ->upload();
                        
                        $service->attachMedia($media, 'gallery');
                        
                        // Clean up temp file
                        \Storage::disk('local')->delete($tempPath);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to process temp image: ' . $e->getMessage());
                    // Continue with other images
                }
            }
        }

        return $service->loadMedia('gallery');
    }

    /**
     * Delete an address for a user.
     *
     * @param int $addressId
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteUserAddress(int $addressId): void
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Verify the address belongs to the user
        $user->addresses()->findOrFail($addressId);

        DB::transaction(function () use ($user, $addressId) {
            // Detach the address from the user
            $user->addresses()->detach($addressId);

            // Check if the address is now orphaned and delete it
            $relatedUsersCount = DB::table('user_addresses')
                ->where('address_id', $addressId)
                ->count();
                
            if ($relatedUsersCount === 0) {
                Address::destroy($addressId);
            }
        });
    }

    /**
     * Set a specific address as the primary one for the user.
     *
     * @param int $addressId
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function setPrimaryAddress(int $addressId): void
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Verify the address belongs to the user
        $user->addresses()->findOrFail($addressId);

        DB::transaction(function () use ($user, $addressId) {
            // Unset all primary addresses
            $this->unsetAllPrimaryAddresses($user);
            
            // Set the specified address as primary
            $user->addresses()->updateExistingPivot($addressId, [
                'is_primary' => true,
            ]);
        });
    }

    /**
     * Unset all primary addresses for a user.
     *
     * @param User $user
     * @return void
     */
    private function unsetAllPrimaryAddresses(User $user): void
    {
        // Get all address IDs for this user that are currently primary
        $primaryAddressIds = $user->addresses()
            ->wherePivot('is_primary', true)
            ->pluck('addresses.id');

        // Update each one individually
        foreach ($primaryAddressIds as $addressId) {
            $user->addresses()->updateExistingPivot($addressId, [
                'is_primary' => false,
            ]);
        }
    }
}