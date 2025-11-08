<?php

namespace App\Domains\Users\Services;

use App\Domains\Common\Models\Address;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Auth;
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
        // Logic to retrieve addresses for the auth user will go here
        return Auth::user()->addresses;
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

        // If the new address is primary, unset other primary addresses.
        if ($data['is_primary'] ?? false) {
            $user->addresses()->wherePivot('is_primary', true)->update(['is_primary' => false]);
        }

        $address = Address::create($data);

        $user->addresses()->attach($address->id, [
            'is_primary' => $data['is_primary'] ?? false,
        ]);

        return $address;
    }

    /**
     * Update an existing address for a user.
     *
     * @param int $addressId
     * @param array $data
     * @return Address
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateUserAddress(int $addressId, array $data): Address
    {
        /** @var User $user */
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($addressId);

        // If the address is being set as primary, unset others.
        if ($data['is_primary'] ?? false) {
            $user->addresses()->wherePivot('is_primary', true)->update(['is_primary' => false]);
        }

        // Update the address attributes
        $address->update($data);

        // Update the pivot table
        $user->addresses()->updateExistingPivot($addressId, [
            'is_primary' => $data['is_primary'] ?? false,
        ]);

        return $address->fresh();
    }

    /**
     * Delete an address for a user.
     *
     * @param int $addressId
     * @return void
     */
    public function deleteUserAddress(int $addressId): void
    {
        /** @var User $user */
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($addressId);

        // Detach the address from the user
        $user->addresses()->detach($addressId);

        // Optional: Check if the address is now orphaned and delete it
        $relatedUsersCount = \DB::table('user_addresses')->where('address_id', $addressId)->count();
        if ($relatedUsersCount === 0) {
            $address->delete();
        }
    }

    /**
     * Set a specific address as the primary one for the user.
     *
     * @param int $addressId
     * @return void
     */
    public function setPrimaryAddress(int $addressId): void
    {
        /** @var User $user */
        $user = Auth::user();
        $user->addresses()->findOrFail($addressId);

        // Atomically update the primary status
        \DB::transaction(function () use ($user, $addressId) {
            $user->addresses()->wherePivot('is_primary', true)->update(['is_primary' => false]);
            $user->addresses()->updateExistingPivot($addressId, ['is_primary' => true]);
        });
    }
}
