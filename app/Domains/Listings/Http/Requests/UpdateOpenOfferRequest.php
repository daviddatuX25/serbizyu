<?php

namespace App\Domains\Listings\Http\Requests;

use App\Support\MediaConfig;
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
        $maxDays = config('listings.open_offer_max_days', 30);
        $maxDate = now()->addDays($maxDays)->format('Y-m-d');
        $mediaConfig = new MediaConfig;
        $imageLimit = $mediaConfig->getUploadLimit('images');

        return [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'budget' => ['sometimes', 'required', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today', 'before_or_equal:'.$maxDate],
            'images' => ['array'],
            'images.*' => ['nullable', 'image', "max:{$imageLimit}"],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        $mediaConfig = new MediaConfig;

        return array_merge(
            [
                'title.string' => 'Title must be a string.',
                'description.string' => 'Description must be a string.',
                'budget.numeric' => 'Budget must be a valid number.',
                'category_id.exists' => 'Selected category is invalid.',
                'deadline.after_or_equal' => 'Deadline must be today or later.',
            ],
            $mediaConfig->getValidationMessages('images', 'images')
        );
    }
}
