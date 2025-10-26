<?php

namespace App\Domains\Common\Services;

use App\Domains\Common\Models\Category;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use App\Domains\Common\Models\Address;

class AddressService
{
    
    public function getAllAddresses(): Collection
    {
        $addresses = Address::all();

        if ($addresses->isEmpty()) {
            throw new ResourceNotFoundException('No addresses found.');
        }

        if ($addresses->every->trashed()) {
            throw new ResourceNotFoundException('Addresses have all been deleted.');
        }

        return $addresses;
    }

    public function getAddress(int $id): Address
    {
        $address = Address::find($id);

        if ($address == null) {
            throw new ResourceNotFoundException('Address not found');
        }
        if($address->trashed())
        {
            throw new ResourceNotFoundException('Address does not exist.');
        }
        return $address;
    }
}