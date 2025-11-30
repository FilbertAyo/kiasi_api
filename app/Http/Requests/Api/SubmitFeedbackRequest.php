<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitFeedbackRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:feedback,bug_report,feature_request'],
            'category' => ['sometimes', 'nullable', 'string', 'in:general,feature_request,bug,performance,ui_ux'],
            'subject' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:2000'],
            'rating' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:5'],
            'attachments' => ['sometimes', 'array', 'max:3'],
            'attachments.*' => ['string'], // Base64 encoded images
        ];
    }
}

