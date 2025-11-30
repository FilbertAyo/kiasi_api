<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ExportDataRequest;
use App\Http\Requests\Api\SubmitRatingRequest;
use App\Http\Requests\Api\UpdateNotificationsRequest;
use App\Http\Requests\Api\UpdatePreferencesRequest;
use App\Http\Requests\Api\UpdatePrivacyRequest;
use App\Http\Requests\Api\VerifyPinRequest;
use App\Models\AppRating;
use App\Models\DataExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Get user preferences.
     * GET /settings/preferences
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $preferences = $request->user()->getOrCreatePreferences();

        return response()->json([
            'status' => 'success',
            'code' => 'PREFERENCES_FETCHED',
            'message' => 'Preferences retrieved successfully',
            'data' => $preferences->toApiResponse(),
        ]);
    }

    /**
     * Update user preferences.
     * PUT /settings/preferences
     */
    public function updatePreferences(UpdatePreferencesRequest $request): JsonResponse
    {
        $preferences = $request->user()->getOrCreatePreferences();
        
        $preferences->update($request->validated());

        return response()->json([
            'status' => 'success',
            'code' => 'PREFERENCES_UPDATED',
            'message' => 'Preferences updated successfully',
            'data' => $preferences->fresh()->toApiResponse(),
        ]);
    }

    /**
     * Update notification settings.
     * PUT /settings/notifications
     */
    public function updateNotifications(UpdateNotificationsRequest $request): JsonResponse
    {
        $preferences = $request->user()->getOrCreatePreferences();
        
        $preferences->update($request->validated());

        return response()->json([
            'status' => 'success',
            'code' => 'PREFERENCES_UPDATED',
            'message' => 'Notification settings updated successfully',
            'data' => [
                'push_enabled' => $preferences->push_enabled,
                'daily_reminder' => $preferences->daily_reminder,
                'daily_reminder_time' => $preferences->daily_reminder_time?->format('H:i') ?? '20:00',
                'budget_alerts' => $preferences->budget_alerts,
                'weekly_summary' => $preferences->weekly_summary,
            ],
        ]);
    }

    /**
     * Update privacy/security settings.
     * PUT /settings/privacy
     */
    public function updatePrivacy(UpdatePrivacyRequest $request): JsonResponse
    {
        $preferences = $request->user()->getOrCreatePreferences();
        
        $data = $request->validated();
        
        // Hash the PIN if provided
        if (isset($data['app_lock_pin']) && $data['app_lock_pin']) {
            $data['app_lock_pin'] = Hash::make($data['app_lock_pin']);
        }
        
        $preferences->update($data);

        return response()->json([
            'status' => 'success',
            'code' => 'PREFERENCES_UPDATED',
            'message' => 'Privacy settings updated successfully',
            'data' => [
                'app_lock_enabled' => $preferences->app_lock_enabled,
                'biometric_enabled' => $preferences->biometric_enabled,
            ],
        ]);
    }

    /**
     * Verify app lock PIN.
     * POST /settings/verify-pin
     */
    public function verifyPin(VerifyPinRequest $request): JsonResponse
    {
        $preferences = $request->user()->getOrCreatePreferences();

        if (!$preferences->app_lock_enabled || !$preferences->app_lock_pin) {
            return response()->json([
                'status' => 'error',
                'code' => 'APP_LOCK_NOT_ENABLED',
                'message' => 'App lock is not enabled.',
            ], 400);
        }

        if (!Hash::check($request->pin, $preferences->app_lock_pin)) {
            return response()->json([
                'status' => 'error',
                'code' => 'INVALID_PIN',
                'message' => 'The PIN you entered is incorrect.',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'code' => 'PIN_VALID',
            'message' => 'PIN verified successfully',
        ]);
    }

    /**
     * Export user data.
     * POST /settings/export-data
     */
    public function exportData(ExportDataRequest $request): JsonResponse
    {
        $user = $request->user();
        $format = $request->input('format', 'csv');
        $emailDelivery = $request->boolean('email_delivery', false);

        // Create export record
        $export = DataExport::create([
            'user_id' => $user->id,
            'format' => $format,
            'email_delivery' => $emailDelivery,
            'expires_at' => now()->addDay(),
        ]);

        // For simplicity, we'll generate the export immediately
        // In production, this would be a queued job
        $this->generateExport($export, $user);

        if ($emailDelivery) {
            return response()->json([
                'status' => 'success',
                'code' => 'EXPORT_PROCESSING',
                'message' => 'Your data export is being processed. You will receive an email when it\'s ready.',
                'data' => [
                    'email' => $user->email,
                    'estimated_time' => '5 minutes',
                    'format' => $format,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'code' => 'EXPORT_READY',
            'message' => 'Your data export is ready',
            'data' => [
                'download_url' => $export->getDownloadUrl(),
                'expires_at' => $export->expires_at,
                'format' => $format,
                'file_size' => $export->file_size,
            ],
        ]);
    }

    /**
     * Get export history.
     * GET /settings/export-history
     */
    public function exportHistory(Request $request): JsonResponse
    {
        $exports = DataExport::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($export) => $export->toApiResponse());

        return response()->json([
            'status' => 'success',
            'code' => 'HISTORY_FETCHED',
            'message' => 'Export history retrieved',
            'data' => $exports,
        ]);
    }

    /**
     * Check if should show rating prompt.
     * GET /settings/should-rate
     */
    public function shouldRate(Request $request): JsonResponse
    {
        $result = AppRating::shouldShowPrompt($request->user());

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    /**
     * Submit app rating.
     * POST /settings/rate-app
     */
    public function rateApp(SubmitRatingRequest $request): JsonResponse
    {
        $user = $request->user();
        
        $rating = AppRating::updateOrCreate(
            ['user_id' => $user->id],
            [
                'action' => $request->action,
                'rating' => $request->action === 'rated' ? $request->rating : null,
                'last_prompted_at' => now(),
            ]
        );

        $message = match ($request->action) {
            'rated' => 'Thank you for rating Kiasi Daily!',
            'later' => 'We\'ll remind you later.',
            'never' => 'Got it, we won\'t ask again.',
        };

        return response()->json([
            'status' => 'success',
            'code' => 'RATING_RECORDED',
            'message' => $message,
            'data' => [
                'action' => $rating->action,
                'rating' => $rating->rating,
                'recorded_at' => $rating->updated_at,
            ],
        ]);
    }

    /**
     * Generate export file (simplified version).
     */
    private function generateExport(DataExport $export, $user): void
    {
        // Get user's transactions
        $transactions = $user->transactions()
            ->with('category')
            ->orderBy('date', 'desc')
            ->get();

        $content = '';
        $fileSize = 0;

        if ($export->format === 'csv') {
            $rows = ["Date,Type,Amount,Category,Description"];
            foreach ($transactions as $t) {
                $rows[] = implode(',', [
                    $t->date->format('Y-m-d'),
                    $t->type,
                    $t->amount,
                    $t->category?->name ?? '',
                    '"' . str_replace('"', '""', $t->description ?? '') . '"',
                ]);
            }
            $content = implode("\n", $rows);
        } elseif ($export->format === 'json') {
            $content = json_encode([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'transactions' => $transactions->map(fn ($t) => [
                    'date' => $t->date->format('Y-m-d'),
                    'type' => $t->type,
                    'amount' => $t->amount,
                    'category' => $t->category?->name,
                    'description' => $t->description,
                ]),
                'exported_at' => now()->toISOString(),
            ], JSON_PRETTY_PRINT);
        }

        $fileSize = strlen($content);
        $filePath = "exports/{$export->export_id}.{$export->format}";
        
        // Store the file
        \Storage::disk('local')->put($filePath, $content);

        // Update export record
        $export->update([
            'status' => 'completed',
            'file_path' => $filePath,
            'file_size' => $this->formatBytes($fileSize),
        ]);
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . 'B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . 'KB';
        return round($bytes / 1048576, 1) . 'MB';
    }
}

