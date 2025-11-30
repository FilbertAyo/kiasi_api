<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'rating',
        'reason',
        'last_prompted_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'last_prompted_at' => 'datetime',
        ];
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user should be shown rating prompt.
     */
    public static function shouldShowPrompt(User $user): array
    {
        $rating = static::where('user_id', $user->id)->first();

        // Already rated
        if ($rating && $rating->action === 'rated') {
            return [
                'should_show' => false,
                'reason' => 'already_rated',
                'rated_at' => $rating->updated_at,
                'rating' => $rating->rating,
            ];
        }

        // User said never
        if ($rating && $rating->action === 'never') {
            return [
                'should_show' => false,
                'reason' => 'user_declined',
            ];
        }

        // User said later - wait at least 7 days
        if ($rating && $rating->action === 'later') {
            $daysSincePrompt = $rating->last_prompted_at?->diffInDays(now()) ?? 100;
            if ($daysSincePrompt < 7) {
                return [
                    'should_show' => false,
                    'reason' => 'recently_dismissed',
                    'next_prompt_in_days' => 7 - $daysSincePrompt,
                ];
            }
        }

        // Check milestones
        $transactionCount = $user->transactions()->count();
        
        // First milestone: 10 transactions
        if ($transactionCount >= 10 && $transactionCount < 50) {
            return [
                'should_show' => true,
                'reason' => 'user_milestone',
                'milestone' => '10_transactions',
                'last_prompted' => $rating?->last_prompted_at,
            ];
        }

        // Second milestone: 50 transactions
        if ($transactionCount >= 50 && $transactionCount < 100) {
            return [
                'should_show' => true,
                'reason' => 'user_milestone',
                'milestone' => '50_transactions',
                'last_prompted' => $rating?->last_prompted_at,
            ];
        }

        // Third milestone: 100 transactions
        if ($transactionCount >= 100) {
            return [
                'should_show' => true,
                'reason' => 'user_milestone',
                'milestone' => '100_transactions',
                'last_prompted' => $rating?->last_prompted_at,
            ];
        }

        // Time-based: 30 days after registration
        $daysRegistered = $user->created_at->diffInDays(now());
        if ($daysRegistered >= 30) {
            return [
                'should_show' => true,
                'reason' => 'time_based',
                'days_registered' => $daysRegistered,
                'last_prompted' => $rating?->last_prompted_at,
            ];
        }

        return [
            'should_show' => false,
            'reason' => 'no_milestone_reached',
        ];
    }
}

