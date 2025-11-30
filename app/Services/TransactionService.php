<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TransactionService
{
    /**
     * Get filtered transactions with pagination.
     */
    public function getFiltered(
        User $user,
        array $filters,
        int $perPage = 20,
        string $sortBy = 'date',
        string $sortOrder = 'desc'
    ): LengthAwarePaginator {
        $query = Transaction::where('user_id', $user->id)->with('category');

        // Filter by specific date
        if (isset($filters['date']) && $filters['date']) {
            $query->forDate($filters['date']);
        }

        // Filter by date range
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->betweenDates($filters['start_date'], $filters['end_date']);
        }

        // Filter by type
        if (isset($filters['type']) && $filters['type']) {
            $query->ofType($filters['type']);
        }

        // Filter by category
        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->forCategory($filters['category_id']);
        }

        // Validate sort field
        $allowedSortFields = ['date', 'amount', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'date';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        return $query
            ->orderBy($sortBy, $sortOrder)
            ->orderBy('created_at', 'desc')
            ->paginate(min($perPage, 100));
    }

    /**
     * Create a new transaction.
     */
    public function create(User $user, array $data): Transaction
    {
        return Transaction::create([
            'user_id' => $user->id,
            ...$data,
        ]);
    }

    /**
     * Update an existing transaction.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->update($data);
        return $transaction->fresh()->load('category');
    }

    /**
     * Calculate summary statistics for transactions.
     */
    public function calculateSummary(LengthAwarePaginator $transactions): array
    {
        $items = $transactions->getCollection();
        
        $totalIncome = $items->where('type', 'income')->sum('amount');
        $totalExpense = $items->where('type', 'expense')->sum('amount');

        return [
            'total_income' => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
            'net_balance' => (float) ($totalIncome - $totalExpense),
            'transaction_count' => $items->count(),
            'expense_count' => $items->where('type', 'expense')->count(),
            'income_count' => $items->where('type', 'income')->count(),
        ];
    }

    /**
     * Get daily impact after a transaction change.
     */
    public function getDailyImpact(User $user, $date): array
    {
        $transactions = Transaction::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->get();

        $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
        $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');

        return [
            'new_daily_total_expense' => $totalExpense,
            'new_daily_total_income' => $totalIncome,
            'new_daily_balance' => $totalIncome - $totalExpense,
        ];
    }

    /**
     * Get context information for a single transaction.
     */
    public function getTransactionContext(Transaction $transaction): array
    {
        $user = $transaction->user;
        $date = $transaction->date;
        $categoryId = $transaction->category_id;

        // Daily total for this category
        $dailyCategoryTotal = Transaction::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->where('category_id', $categoryId)
            ->where('type', $transaction->type)
            ->sum('amount');

        // Monthly total for this category
        $monthlyCategoryTotal = Transaction::where('user_id', $user->id)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('category_id', $categoryId)
            ->where('type', $transaction->type)
            ->sum('amount');

        // Check if highest in category today
        $highestInCategory = Transaction::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->where('category_id', $categoryId)
            ->where('type', $transaction->type)
            ->max('amount');

        return [
            'daily_category_total' => (float) $dailyCategoryTotal,
            'monthly_category_total' => (float) $monthlyCategoryTotal,
            'is_highest_in_category_today' => $transaction->amount >= $highestInCategory,
        ];
    }

    /**
     * Detect changes between old and new transaction data.
     */
    public function getChanges(array $oldData, Transaction $newTransaction): array
    {
        $changes = [];
        $fieldsToCompare = ['amount', 'description', 'category_id', 'date'];

        foreach ($fieldsToCompare as $field) {
            $oldValue = $oldData[$field] ?? null;
            $newValue = $newTransaction->$field;

            // Handle date comparison
            if ($field === 'date') {
                if ($newValue instanceof \DateTime || $newValue instanceof \Carbon\Carbon) {
                    $newValue = $newValue->format('Y-m-d');
                }
                if ($oldValue instanceof \DateTime || $oldValue instanceof \Carbon\Carbon) {
                    $oldValue = $oldValue->format('Y-m-d');
                }
            }

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];

                if ($field === 'amount') {
                    $changes[$field]['difference'] = (float) $newValue - (float) $oldValue;
                }
            }
        }

        return $changes;
    }

    /**
     * Format currency amount.
     */
    public function formatCurrency(float $amount): string
    {
        return 'TZS ' . number_format($amount, 0, '.', ',');
    }

    /**
     * Generate a changes summary message.
     */
    public function formatChangesSummary(array $changes): string
    {
        if (isset($changes['amount'])) {
            $old = $this->formatCurrency($changes['amount']['old']);
            $new = $this->formatCurrency($changes['amount']['new']);
            return "Amount changed from {$old} to {$new}";
        }
        
        if (isset($changes['description'])) {
            return 'Description updated';
        }
        
        if (isset($changes['category_id'])) {
            return 'Category changed';
        }
        
        if (isset($changes['date'])) {
            return 'Date changed';
        }
        
        return 'Transaction details updated';
    }
}

