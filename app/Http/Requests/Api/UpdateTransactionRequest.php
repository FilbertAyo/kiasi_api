<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTransactionRequest extends FormRequest
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
            'amount' => ['sometimes', 'numeric', 'min:1', 'max:999999999'],
            'description' => ['nullable', 'string', 'max:500'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'type' => ['sometimes', 'in:expense,income'],
            'date' => ['sometimes', 'date', 'before_or_equal:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.numeric' => 'Amount must be a valid number',
            'amount.min' => 'Amount must be at least TZS 1',
            'amount.max' => 'Amount exceeds maximum limit',
            'description.max' => 'Description cannot exceed 500 characters',
            'category_id.exists' => 'Selected category is invalid',
            'type.in' => 'Type must be expense or income',
            'date.date' => 'Invalid date format. Use YYYY-MM-DD',
            'date.before_or_equal' => 'Date cannot be in the future',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status' => 'error',
            'message' => 'Please fix the errors below',
            'errors' => $validator->errors()->toArray(),
            'error_code' => 'VALIDATION_ERROR',
            'meta' => [
                'timestamp' => now()->toISOString(),
            ],
        ], 422));
    }
}
