<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrivacyRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'app_lock_enabled' => ['sometimes', 'boolean'],
            'app_lock_pin' => ['required_if:app_lock_enabled,true', 'nullable', 'string', 'regex:/^\d{4,6}$/'],
            'biometric_enabled' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'app_lock_pin.required_if' => 'A PIN is required when enabling app lock.',
            'app_lock_pin.regex' => 'PIN must be 4-6 digits.',
        ];
    }
}

