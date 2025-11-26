<?php

namespace App\Domains\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserReviewRequest extends FormRequest
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
            'reviewee_id' => 'required|integer|exists:users,id|different:reviewer_id',
            'rating' => 'required|integer|between:1,5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|max:1000',
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
            'reviewee_id.required' => 'The user being reviewed is required.',
            'reviewee_id.exists' => 'The selected user does not exist.',
            'reviewee_id.different' => 'You cannot review yourself.',
            'rating.required' => 'Please provide a rating.',
            'rating.between' => 'Rating must be between 1 and 5.',
            'comment.required' => 'Please provide a review comment.',
            'comment.max' => 'Comment cannot exceed 1000 characters.',
        ];
    }
}
