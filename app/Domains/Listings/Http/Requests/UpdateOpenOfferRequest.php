<?php

namespace App\Domains\Listings\Http\Requests;

use App\Domains\Listings\Models\OpenOffer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateOpenOfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $openOffer = $this->route('offer'); // Assuming route model binding
        return Auth::user()->can('update', $openOffer);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'budget' => ['sometimes', 'required', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'images' => ['array'],
            'images.*' => ['nullable', 'image', 'max:5048'], // Max 2MB per image
        ];
    }
}
