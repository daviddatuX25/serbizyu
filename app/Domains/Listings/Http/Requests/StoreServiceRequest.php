<?php

namespace App\Domains\Listings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'workflow_template_id' => 'required|integer|exists:workflow_templates,id',
            'new_images' => 'nullable|array',
            'new_images.*' => 'string',
            'images_to_remove' => 'nullable|array',
            'images_to_remove.*' => 'integer',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'new_images.*.string' => 'Invalid image data provided.',
            'images_to_remove.*.integer' => 'Invalid image ID provided.',
        ];
    }
}
