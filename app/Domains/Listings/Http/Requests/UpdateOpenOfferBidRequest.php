<?php

namespace App\Domains\Listings\Http\Requests;

use App\Domains\Listings\Models\OpenOfferBid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateOpenOfferBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $bid = $this->route('bid'); // Assuming route model binding
        return Auth::user()->can('update', $bid);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['nullable', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
