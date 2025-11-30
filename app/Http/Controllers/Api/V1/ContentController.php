<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use App\Models\StaticContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Get the language from request.
     */
    private function getLanguage(Request $request): string
    {
        $lang = $request->query('lang', $request->header('Accept-Language', 'sw'));
        
        // Handle Accept-Language header formats like "en-US,en;q=0.9"
        if (str_contains($lang, ',')) {
            $lang = explode(',', $lang)[0];
        }
        if (str_contains($lang, '-')) {
            $lang = explode('-', $lang)[0];
        }
        
        // Validate language
        return in_array($lang, ['sw', 'en']) ? $lang : 'sw';
    }

    /**
     * Get Terms and Conditions.
     * GET /content/terms
     */
    public function terms(Request $request): JsonResponse
    {
        $language = $this->getLanguage($request);
        $content = StaticContent::getContent('terms', $language);

        if (!$content) {
            return response()->json([
                'status' => 'error',
                'code' => 'NOT_FOUND',
                'message' => 'Content not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'code' => 'CONTENT_FETCHED',
            'message' => 'Content retrieved successfully',
            'data' => $content->toApiResponse(),
        ]);
    }

    /**
     * Get Privacy Policy.
     * GET /content/privacy
     */
    public function privacy(Request $request): JsonResponse
    {
        $language = $this->getLanguage($request);
        $content = StaticContent::getContent('privacy', $language);

        if (!$content) {
            return response()->json([
                'status' => 'error',
                'code' => 'NOT_FOUND',
                'message' => 'Content not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'code' => 'CONTENT_FETCHED',
            'message' => 'Content retrieved successfully',
            'data' => $content->toApiResponse(),
        ]);
    }

    /**
     * Get About page content.
     * GET /content/about
     */
    public function about(Request $request): JsonResponse
    {
        $language = $this->getLanguage($request);
        $content = StaticContent::getContent('about', $language);

        if (!$content) {
            return response()->json([
                'status' => 'error',
                'code' => 'NOT_FOUND',
                'message' => 'Content not found',
            ], 404);
        }

        // Get company info and credits from config
        $company = AppConfig::getValue('company', [
            'name' => 'Kiasi Daily',
            'address' => 'Dar es Salaam, Tanzania',
            'email' => 'info@kiasidaily.com',
            'website' => 'https://kiasidaily.com',
        ]);

        $credits = AppConfig::getValue('credits', [
            [
                'name' => 'Flutter',
                'url' => 'https://flutter.dev',
                'license' => 'BSD-3-Clause',
            ],
        ]);

        $data = $content->toApiResponse();
        $data['company'] = $company;
        $data['credits'] = $credits;

        return response()->json([
            'status' => 'success',
            'code' => 'CONTENT_FETCHED',
            'message' => 'Content retrieved successfully',
            'data' => $data,
        ]);
    }

    /**
     * Get FAQ content.
     * GET /content/faq
     */
    public function faq(Request $request): JsonResponse
    {
        $language = $this->getLanguage($request);
        
        $title = $language === 'en' 
            ? 'Frequently Asked Questions' 
            : 'Maswali Yanayoulizwa Mara kwa Mara';

        $categories = FaqCategory::getWithQuestions($language);

        return response()->json([
            'status' => 'success',
            'code' => 'CONTENT_FETCHED',
            'message' => 'Content retrieved successfully',
            'data' => [
                'title' => $title,
                'language' => $language,
                'categories' => $categories,
            ],
        ]);
    }

    /**
     * Mark FAQ as helpful.
     * POST /content/faq/{id}/helpful
     */
    public function markFaqHelpful(Request $request, int $id): JsonResponse
    {
        $question = FaqQuestion::find($id);

        if (!$question) {
            return response()->json([
                'status' => 'error',
                'code' => 'NOT_FOUND',
                'message' => 'Question not found',
            ], 404);
        }

        $marked = $question->markHelpfulByUser($request->user());

        if (!$marked) {
            return response()->json([
                'status' => 'error',
                'code' => 'ALREADY_VOTED',
                'message' => 'You have already marked this as helpful',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'code' => 'FEEDBACK_RECORDED',
            'message' => 'Thank you for your feedback',
            'data' => [
                'question_id' => $question->id,
                'helpful_count' => $question->helpful_count,
            ],
        ]);
    }
}

