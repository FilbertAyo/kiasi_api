<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'formatted_amount' => $this->formatted_amount,
            'description' => $this->description,
            'category_id' => (string) $this->category_id,
            'category_name' => $this->category?->name ?? $this->getCategoryName(),
            'category_icon' => $this->category?->icon ?? $this->getCategoryIcon(),
            'category_color' => $this->category?->color ?? $this->getCategoryColor(),
            'type' => $this->type,
            'date' => $this->date->format('Y-m-d'),
            'formatted_date' => $this->date->format('l, F j, Y'),
            'relative_date' => $this->getRelativeDate(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Get relative date string.
     */
    private function getRelativeDate(): string
    {
        if ($this->date->isToday()) {
            return 'Today';
        }
        if ($this->date->isYesterday()) {
            return 'Yesterday';
        }
        if ($this->date->isTomorrow()) {
            return 'Tomorrow';
        }
        if ($this->date->isCurrentWeek()) {
            return $this->date->format('l'); // Day name
        }
        return $this->date->diffForHumans();
    }

    /**
     * Get fallback category name.
     */
    private function getCategoryName(): string
    {
        $categories = [
            'food' => 'Food & Dining',
            'transport' => 'Transport',
            'shopping' => 'Shopping',
            'bills' => 'Bills & Utilities',
            'health' => 'Health',
            'entertainment' => 'Entertainment',
            'education' => 'Education',
            'other_expense' => 'Other',
            'salary' => 'Salary',
            'freelance' => 'Freelance',
            'business' => 'Business',
            'investment' => 'Investment',
            'gift' => 'Gift',
            'other_income' => 'Other',
        ];

        return $categories[$this->category_id] ?? 'Unknown';
    }

    /**
     * Get fallback category icon.
     */
    private function getCategoryIcon(): string
    {
        $icons = [
            'food' => 'restaurant',
            'transport' => 'directions_car',
            'shopping' => 'shopping_bag',
            'bills' => 'receipt_long',
            'health' => 'local_hospital',
            'entertainment' => 'movie',
            'education' => 'school',
            'other_expense' => 'more_horiz',
            'salary' => 'payments',
            'freelance' => 'work',
            'business' => 'storefront',
            'investment' => 'trending_up',
            'gift' => 'card_giftcard',
            'other_income' => 'more_horiz',
        ];

        return $icons[$this->category_id] ?? 'circle';
    }

    /**
     * Get fallback category color.
     */
    private function getCategoryColor(): string
    {
        $colors = [
            'food' => '#FF6B6B',
            'transport' => '#4ECDC4',
            'shopping' => '#FFE66D',
            'bills' => '#95E1D3',
            'health' => '#F38181',
            'entertainment' => '#AA96DA',
            'education' => '#6C5CE7',
            'other_expense' => '#636E72',
            'salary' => '#00B894',
            'freelance' => '#00CEC9',
            'business' => '#0984E3',
            'investment' => '#6C5CE7',
            'gift' => '#E17055',
            'other_income' => '#636E72',
        ];

        return $colors[$this->category_id] ?? '#636E72';
    }
}

