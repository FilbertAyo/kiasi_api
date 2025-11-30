<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FaqQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'language',
        'question',
        'answer',
        'helpful_count',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'helpful_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the category of this question.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }

    /**
     * Get users who found this question helpful.
     */
    public function helpfulVoters(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'faq_helpful_votes')
            ->withTimestamps();
    }

    /**
     * Check if a user has voted for this question.
     */
    public function hasUserVoted(int $userId): bool
    {
        return $this->helpfulVoters()->where('user_id', $userId)->exists();
    }

    /**
     * Increment helpful count from a user.
     */
    public function markHelpfulByUser(User $user): bool
    {
        if ($this->hasUserVoted($user->id)) {
            return false; // Already voted
        }

        $this->helpfulVoters()->attach($user->id);
        $this->increment('helpful_count');
        
        return true;
    }
}

