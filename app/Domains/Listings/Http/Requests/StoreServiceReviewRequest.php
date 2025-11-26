<?php

namespace App\Domains\Listings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required|integer|exists:services,id',
            'order_id' => 'nullable|integer|exists:orders,id',
            'rating' => 'required|integer|between:1,5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|max:2000',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'The service being reviewed is required.',
            'service_id.exists' => 'The selected service does not exist.',
            'rating.required' => 'Please provide a rating.',
            'rating.between' => 'Rating must be between 1 and 5.',
            'comment.required' => 'Please provide a review comment.',
            'comment.max' => 'Comment cannot exceed 2000 characters.',
        ];
    }
}
