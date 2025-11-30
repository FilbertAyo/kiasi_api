<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_en',
        'name_sw',
        'icon',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the questions for this category.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(FaqQuestion::class, 'category_id');
    }

    /**
     * Scope to get only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Get the name based on language.
     */
    public function getName(string $language = 'sw'): string
    {
        return $language === 'en' ? $this->name_en : $this->name_sw;
    }

    /**
     * Get all categories with questions for a language.
     */
    public static function getWithQuestions(string $language = 'sw'): array
    {
        $categories = static::active()
            ->ordered()
            ->with(['questions' => function ($query) use ($language) {
                $query->where('language', $language)
                    ->where('is_active', true)
                    ->orderBy('display_order');
            }])
            ->get();

        return $categories->map(function ($category) use ($language) {
            return [
                'id' => $category->slug,
                'name' => $category->getName($language),
                'icon' => $category->icon,
                'questions' => $category->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question' => $question->question,
                        'answer' => $question->answer,
                        'helpful_count' => $question->helpful_count,
                    ];
                })->values()->toArray(),
            ];
        })->filter(function ($category) {
            return count($category['questions']) > 0;
        })->values()->toArray();
    }
}

