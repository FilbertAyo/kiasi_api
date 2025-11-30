<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'ticket_id',
        'type',
        'category',
        'subject',
        'message',
        'rating',
        'attachments',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'rating' => 'integer',
        ];
    }

    /**
     * Get the user who submitted the feedback.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique ticket ID.
     */
    public static function generateTicketId(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('FB-%s-%06d', $year, $count);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($feedback) {
            if (empty($feedback->ticket_id)) {
                $feedback->ticket_id = static::generateTicketId();
            }
        });
    }
}

