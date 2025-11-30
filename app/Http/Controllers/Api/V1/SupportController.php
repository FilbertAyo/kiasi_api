<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactSupportRequest;
use App\Http\Requests\Api\SubmitFeedbackRequest;
use App\Models\Feedback;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Submit feedback.
     * POST /support/feedback
     */
    public function submitFeedback(SubmitFeedbackRequest $request): JsonResponse
    {
        $feedback = Feedback::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'category' => $request->category,
            'subject' => $request->subject,
            'message' => $request->message,
            'rating' => $request->rating,
            'attachments' => $request->attachments,
        ]);

        return response()->json([
            'status' => 'success',
            'code' => 'FEEDBACK_SUBMITTED',
            'message' => 'Thank you for your feedback!',
            'data' => [
                'ticket_id' => $feedback->ticket_id,
                'status' => $feedback->status,
                'created_at' => $feedback->created_at,
            ],
        ], 201);
    }

    /**
     * Contact support (public endpoint).
     * POST /support/contact
     */
    public function contactSupport(ContactSupportRequest $request): JsonResponse
    {
        $ticket = SupportTicket::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'category' => $request->category,
        ]);

        return response()->json([
            'status' => 'success',
            'code' => 'MESSAGE_SENT',
            'message' => 'Your message has been sent. We\'ll respond within 24 hours.',
            'data' => [
                'ticket_id' => $ticket->ticket_id,
                'estimated_response' => '24 hours',
            ],
        ], 201);
    }
}

