<?php

namespace App\Domains\Listings\Http\Requests;

use App\Domains\Listings\Models\OpenOffer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Domains\Listings\Models\OpenOfferBid;
use Illuminate\Validation\Rule; // Added Rule import

class StoreOpenOfferBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $openOffer = $this->route('openoffer'); // Assuming route model binding
        return Auth::user()->can('create', [OpenOfferBid::class, $openOffer]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => [
                'required',
                'numeric', // Added numeric validation for service_id
                Rule::exists('services', 'id'),
                function ($attribute, $value, $fail) {
                    if (!Auth::user()->services()->where('id', $value)->exists()) {
                        $fail('The selected service does not belong to you.');
                    }
                },
            ],
            'amount' => ['required', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
