<?php

namespace App\Domains\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUserVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_type' => ['required', 'string', 'in:national_id,drivers_license,passport'],
            'id_front' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,gif,heic'],
            'id_back' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,gif,heic'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'id_type.required' => 'Please select an ID type.',
            'id_type.in' => 'Invalid ID type selected.',
            'id_front.required' => 'Front side photo is required.',
            'id_front.file' => 'Front side must be a valid image file.',
            'id_front.max' => 'Front side image must not exceed 5MB.',
            'id_front.mimes' => 'Front side must be JPG, PNG, GIF, or HEIC.',
            'id_back.required' => 'Back side photo is required.',
            'id_back.file' => 'Back side must be a valid image file.',
            'id_back.max' => 'Back side image must not exceed 5MB.',
            'id_back.mimes' => 'Back side must be JPG, PNG, GIF, or HEIC.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $errors = $validator->errors();

        // Check for missing files which typically means upload size exceeded
        if (! $this->hasFile('id_front') && $this->input('id_front_name')) {
            $errors->add('id_front', 'Front side image file size is greater than 5MB.');
        }
        if (! $this->hasFile('id_back') && $this->input('id_back_name')) {
            $errors->add('id_back', 'Back side image file size is greater than 5MB.');
        }

        parent::failedValidation($validator);
    }
}
