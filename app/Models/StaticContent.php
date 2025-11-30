<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticContent extends Model
{
    use HasFactory;

    protected $table = 'static_content';

    protected $fillable = [
        'type',
        'language',
        'title',
        'content',
        'version',
        'effective_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by language.
     */
    public function scopeInLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope to get only active content.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get content by type and language.
     */
    public static function getContent(string $type, string $language = 'sw'): ?self
    {
        return static::ofType($type)
            ->inLanguage($language)
            ->active()
            ->first();
    }

    /**
     * Format content for API response.
     */
    public function toApiResponse(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'format' => 'markdown',
            'language' => $this->language,
            'version' => $this->version,
            'effective_date' => $this->effective_date?->format('Y-m-d'),
            'last_updated' => $this->updated_at,
        ];
    }
}

