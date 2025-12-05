<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'language',
        'currency',
        'date_format',
        'number_format',
        'first_day_of_week',
        'push_enabled',
        'daily_reminder',
        'daily_reminder_time',
        'budget_alerts',
        'weekly_summary',
        'app_lock_enabled',
        'app_lock_pin',
        'biometric_enabled',
    ];

    protected $hidden = [
        'app_lock_pin',
    ];

    protected function casts(): array
    {
        return [
            'push_enabled' => 'boolean',
            'daily_reminder' => 'boolean',
            'budget_alerts' => 'boolean',
            'weekly_summary' => 'boolean',
            'app_lock_enabled' => 'boolean',
            'biometric_enabled' => 'boolean',
            'daily_reminder_time' => 'datetime:H:i',
        ];
    }

    /**
     * Get the user that owns the preferences.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get default preferences for a new user.
     */
    public static function getDefaults(): array
    {
        return [
            'language' => 'en',
            'currency' => 'TZS',
            'date_format' => 'DD/MM/YYYY',
            'number_format' => '1,000.00',
            'first_day_of_week' => 'monday',
            'push_enabled' => true,
            'daily_reminder' => true,
            'daily_reminder_time' => '20:00',
            'budget_alerts' => true,
            'weekly_summary' => true,
            'app_lock_enabled' => false,
            'biometric_enabled' => false,
        ];
    }

    /**
     * Format preferences for API response.
     */
    public function toApiResponse(): array
    {
        return [
            'language' => $this->language,
            'currency' => $this->currency,
            'date_format' => $this->date_format,
            'number_format' => $this->number_format,
            'first_day_of_week' => $this->first_day_of_week,
            'notifications' => [
                'push_enabled' => $this->push_enabled,
                'daily_reminder' => $this->daily_reminder,
                'daily_reminder_time' => $this->daily_reminder_time?->format('H:i') ?? '20:00',
                'budget_alerts' => $this->budget_alerts,
                'weekly_summary' => $this->weekly_summary,
            ],
            'privacy' => [
                'app_lock_enabled' => $this->app_lock_enabled,
                'biometric_enabled' => $this->biometric_enabled,
            ],
            'updated_at' => $this->updated_at,
        ];
    }
}

