<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationsRequest extends FormRequest
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
            'push_enabled' => ['sometimes', 'boolean'],
            'daily_reminder' => ['sometimes', 'boolean'],
            'daily_reminder_time' => ['sometimes', 'date_format:H:i'],
            'budget_alerts' => ['sometimes', 'boolean'],
            'weekly_summary' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'daily_reminder_time.date_format' => 'The daily reminder time must be in HH:MM format (24-hour).',
        ];
    }
}

