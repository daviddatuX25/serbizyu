<?php

namespace App\Domains\Common\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'full_address',
        'address_hash',
        'api_source',
        'api_id',
        'lat',
        'lng',
        'street_address',
        'barangay',
        'city',
        'province',
        'region',
        'region_name',
        'province_name',
    ];

    /**
     * Generate full_address from component fields if not explicitly set
     */
    public function getFullAddressAttribute(): ?string
    {
        // If full_address is explicitly stored, return it
        if ($this->attributes['full_address'] ?? null) {
            return $this->attributes['full_address'];
        }

        // Otherwise, compose from components
        $parts = [];
        if ($this->street_address) {
            $parts[] = $this->street_address;
        }
        if ($this->barangay) {
            $parts[] = $this->barangay;
        }
        if ($this->city) {
            $parts[] = $this->city;
        }
        if ($this->province_name) {
            $parts[] = $this->province_name;
        } elseif ($this->province) {
            $parts[] = $this->province;
        }

        return ! empty($parts) ? implode(', ', $parts) : null;
    }

    protected static function newFactory()
    {
        return \Database\Factories\AddressFactory::new();
    }
}
