<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
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
            'language' => ['sometimes', 'string', 'in:sw,en'],
            'currency' => ['sometimes', 'string', 'in:TZS,USD,KES,UGX'],
            'date_format' => ['sometimes', 'string', 'in:DD/MM/YYYY,MM/DD/YYYY,YYYY-MM-DD'],
            'number_format' => ['sometimes', 'string', 'in:1,000.00,1.000,00,1 000,00'],
            'first_day_of_week' => ['sometimes', 'string', 'in:monday,sunday,saturday'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'language.in' => 'The selected language is invalid.',
            'currency.in' => 'The selected currency is not supported.',
            'date_format.in' => 'The selected date format is invalid.',
            'number_format.in' => 'The selected number format is invalid.',
            'first_day_of_week.in' => 'The selected first day of week is invalid.',
        ];
    }
}

