<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    private ?string $serverKey;

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
    }

    /**
     * Send push notification to a specific user.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        // Check if user has push notifications enabled
        $preferences = $user->preferences;
        if ($preferences && !$preferences->push_enabled) {
            return [
                'success' => false,
                'message' => 'Push notifications disabled for user',
                'sent_count' => 0,
            ];
        }

        $tokens = $user->deviceTokens()->active()->pluck('token')->toArray();

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No active device tokens found',
                'sent_count' => 0,
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send push notification to multiple users.
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = []): array
    {
        $tokens = DeviceToken::whereIn('user_id', $userIds)
            ->active()
            ->whereHas('user.preferences', function ($query) {
                $query->where('push_enabled', true);
            })
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No active device tokens found',
                'sent_count' => 0,
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send push notification to specific device tokens.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        if (!$this->serverKey) {
            Log::warning('FCM server key not configured');
            return [
                'success' => false,
                'message' => 'FCM not configured',
                'sent_count' => 0,
            ];
        }

        $results = [
            'success' => true,
            'sent_count' => 0,
            'failed_count' => 0,
            'failed_tokens' => [],
        ];

        // FCM supports max 1000 tokens per request
        $chunks = array_chunk($tokens, 1000);

        foreach ($chunks as $tokenChunk) {
            $response = $this->sendFcmRequest($tokenChunk, $title, $body, $data);
            
            if ($response['success']) {
                $results['sent_count'] += $response['success_count'];
                $results['failed_count'] += $response['failure_count'];
                
                // Collect failed tokens for cleanup
                if (!empty($response['failed_tokens'])) {
                    $results['failed_tokens'] = array_merge(
                        $results['failed_tokens'],
                        $response['failed_tokens']
                    );
                }
            } else {
                $results['success'] = false;
                $results['message'] = $response['message'] ?? 'FCM request failed';
            }
        }

        // Deactivate invalid tokens
        if (!empty($results['failed_tokens'])) {
            DeviceToken::whereIn('token', $results['failed_tokens'])->update(['is_active' => false]);
        }

        return $results;
    }

    /**
     * Send FCM request.
     */
    private function sendFcmRequest(array $tokens, string $title, string $body, array $data): array
    {
        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => array_merge($data, [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]),
            'priority' => 'high',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                $failedTokens = [];
                if (isset($result['results'])) {
                    foreach ($result['results'] as $index => $item) {
                        if (isset($item['error'])) {
                            // Collect tokens that should be removed
                            if (in_array($item['error'], ['NotRegistered', 'InvalidRegistration'])) {
                                $failedTokens[] = $tokens[$index];
                            }
                        }
                    }
                }

                return [
                    'success' => true,
                    'success_count' => $result['success'] ?? 0,
                    'failure_count' => $result['failure'] ?? 0,
                    'failed_tokens' => $failedTokens,
                ];
            }

            Log::error('FCM request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'FCM request failed: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('FCM request exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'FCM request exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send daily reminder notification.
     */
    public function sendDailyReminder(User $user): array
    {
        $preferences = $user->preferences;
        
        if (!$preferences || !$preferences->daily_reminder) {
            return ['success' => false, 'message' => 'Daily reminder disabled'];
        }

        return $this->sendToUser(
            $user,
            'Kiasi Daily Reminder',
            'Don\'t forget to record your expenses today! 📝',
            ['type' => 'daily_reminder']
        );
    }

    /**
     * Send budget alert notification.
     */
    public function sendBudgetAlert(User $user, string $category, float $percentage): array
    {
        $preferences = $user->preferences;
        
        if (!$preferences || !$preferences->budget_alerts) {
            return ['success' => false, 'message' => 'Budget alerts disabled'];
        }

        $body = $percentage >= 100
            ? "You've exceeded your {$category} budget! 💸"
            : "You've used {$percentage}% of your {$category} budget ⚠️";

        return $this->sendToUser(
            $user,
            'Budget Alert',
            $body,
            [
                'type' => 'budget_alert',
                'category' => $category,
                'percentage' => $percentage,
            ]
        );
    }

    /**
     * Send weekly summary notification.
     */
    public function sendWeeklySummary(User $user, array $summaryData): array
    {
        $preferences = $user->preferences;
        
        if (!$preferences || !$preferences->weekly_summary) {
            return ['success' => false, 'message' => 'Weekly summary disabled'];
        }

        $totalSpent = number_format($summaryData['total_spent'] ?? 0);
        
        return $this->sendToUser(
            $user,
            'Weekly Summary',
            "This week you spent TSh {$totalSpent}. Tap to see details 📊",
            [
                'type' => 'weekly_summary',
                'summary' => $summaryData,
            ]
        );
    }
}

