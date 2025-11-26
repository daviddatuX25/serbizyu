<?php

namespace App\Domains\Users\Http\Requests;

use App\Support\MediaConfig;
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
        $mediaConfig = new MediaConfig;

        return [
            'id_type' => ['required', 'string', 'in:national_id,drivers_license,passport'],
            'id_front' => ['required', $mediaConfig->getValidationRule('images')],
            'id_back' => ['required', $mediaConfig->getValidationRule('images')],
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
                'id_type.required' => 'Please select a valid ID type.',
                'id_type.in' => 'Invalid ID type selected.',
                'id_front.required' => 'Front side of your ID is required.',
                'id_back.required' => 'Back side of your ID is required.',
            ],
            $mediaConfig->getValidationMessages('images', 'id_front'),
            $mediaConfig->getValidationMessages('images', 'id_back')
        );
    }
}
