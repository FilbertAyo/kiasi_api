<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Expense categories
            ['id' => 1, 'name' => 'Food & Drinks', 'icon' => 'restaurant', 'color' => '0xFFFF6B6B', 'type' => 'expense'],
            ['id' => 2, 'name' => 'Transport', 'icon' => 'directions_car', 'color' => '0xFF54A0FF', 'type' => 'expense'],
            ['id' => 3, 'name' => 'Shopping', 'icon' => 'shopping_bag', 'color' => '0xFFFF9F43', 'type' => 'expense'],
            ['id' => 4, 'name' => 'Entertainment', 'icon' => 'movie', 'color' => '0xFF5F27CD', 'type' => 'expense'],
            ['id' => 5, 'name' => 'Bills & Utilities', 'icon' => 'receipt', 'color' => '0xFFEE5A24', 'type' => 'expense'],
            ['id' => 6, 'name' => 'Health', 'icon' => 'local_hospital', 'color' => '0xFF00D09C', 'type' => 'expense'],
            ['id' => 7, 'name' => 'Education', 'icon' => 'school', 'color' => '0xFF00CEC9', 'type' => 'expense'],
            ['id' => 8, 'name' => 'Other', 'icon' => 'category', 'color' => '0xFF8B949E', 'type' => 'expense'],

            // Income categories
            ['id' => 101, 'name' => 'Salary', 'icon' => 'work', 'color' => '0xFF00D09C', 'type' => 'income'],
            ['id' => 102, 'name' => 'Business', 'icon' => 'account_balance', 'color' => '0xFF54A0FF', 'type' => 'income'],
            ['id' => 103, 'name' => 'Gift', 'icon' => 'card_giftcard', 'color' => '0xFF5F27CD', 'type' => 'income'],
            ['id' => 104, 'name' => 'Investment', 'icon' => 'trending_up', 'color' => '0xFFFF9F43', 'type' => 'income'],
            ['id' => 105, 'name' => 'Other', 'icon' => 'category', 'color' => '0xFF8B949E', 'type' => 'income'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],
                $category
            );
        }
    }
}

