<?php

namespace App\Domains\Listings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOpenOfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check(); // Only authenticated users can create open offers
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxDays = config('listings.open_offer_max_days', 30);
        $maxDate = now()->addDays($maxDays)->format('Y-m-d');

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'budget' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today', 'before_or_equal:' . $maxDate],
            'images' => ['array'],
            'images.*' => ['nullable', 'image', 'max:5000'],  // Max 5MB per image
        ];
    }
}
