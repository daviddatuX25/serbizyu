<?php

namespace App\Domains\Users\Http\Requests;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize(): bool
    {
        // The authorization is handled by the fact that we are getting the user from the request in the controller.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => ['sometimes', 'required', 'string', 'max:255'], // Only validate if present
            'lastname' => ['sometimes', 'required', 'string', 'max:255'],  // Ditto
            'email' => [
                'sometimes', // Optional: omit if not changing
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()?->id),
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email ?? '')),
            // Add similar for names if needed: 'first_name' => trim($this->first_name ?? ''),
        ]);
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already taken.',
            // Add more as needed
        ];
    }
}