<?php

namespace App\Domains\Listings\Http\Requests;

use App\Domains\Listings\Models\OpenOffer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOpenOfferBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $openOffer = $this->route('openOffer'); // Assuming route model binding
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
            'amount' => ['required', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
