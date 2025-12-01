<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    /**
     * Get daily summary for a specific date.
     */
    public function daily(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;
        $transactions = $request->user()->transactions()->forDate($date)->get();

        $incomeTransactions = $transactions->where('type', 'income');
        $expenseTransactions = $transactions->where('type', 'expense');

        $totalIncome = $incomeTransactions->sum('amount');
        $totalExpense = $expenseTransactions->sum('amount');

        return response()->json([
            'date' => $date,
            'total_income' => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
            'transaction_count' => $transactions->count(),
            'income_count' => $incomeTransactions->count(),
            'expense_count' => $expenseTransactions->count(),
        ]);
    }

    /**
     * Get weekly summary starting from a specific date.
     */
    public function weekly(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays(6);

        $transactions = $request->user()->transactions()
            ->betweenDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'))
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');

        // Generate daily summaries for each day of the week
        $dailySummaries = [];
        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dateStr = $currentDate->format('Y-m-d');

            $dayTransactions = $transactions->filter(function ($t) use ($dateStr) {
                return $t->date->format('Y-m-d') === $dateStr;
            });

            $dayIncomeTransactions = $dayTransactions->where('type', 'income');
            $dayExpenseTransactions = $dayTransactions->where('type', 'expense');

            $dailySummaries[] = [
                'date' => $dateStr,
                'total_income' => (float) $dayIncomeTransactions->sum('amount'),
                'total_expense' => (float) $dayExpenseTransactions->sum('amount'),
                'transaction_count' => $dayTransactions->count(),
                'income_count' => $dayIncomeTransactions->count(),
                'expense_count' => $dayExpenseTransactions->count(),
            ];
        }

        return response()->json([
            'daily_summaries' => $dailySummaries,
            'week_start' => $startDate->format('Y-m-d'),
            'week_end' => $endDate->format('Y-m-d'),
            'total_income' => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
        ]);
    }

    /**
     * Get monthly summary.
     */
    public function monthly(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = $request->year;
        $month = $request->month;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $transactions = $request->user()->transactions()
            ->betweenDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'))
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');

        // Generate daily summaries for each day of the month
        $dailySummaries = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::createFromDate($year, $month, $day);
            $dateStr = $currentDate->format('Y-m-d');

            $dayTransactions = $transactions->filter(function ($t) use ($dateStr) {
                return $t->date->format('Y-m-d') === $dateStr;
            });

            $dayIncomeTransactions = $dayTransactions->where('type', 'income');
            $dayExpenseTransactions = $dayTransactions->where('type', 'expense');

            $dailySummaries[] = [
                'date' => $dateStr,
                'total_income' => (float) $dayIncomeTransactions->sum('amount'),
                'total_expense' => (float) $dayExpenseTransactions->sum('amount'),
                'transaction_count' => $dayTransactions->count(),
                'income_count' => $dayIncomeTransactions->count(),
                'expense_count' => $dayExpenseTransactions->count(),
            ];
        }

        $incomeTransactions = $transactions->where('type', 'income');
        $expenseTransactions = $transactions->where('type', 'expense');
        $daysWithData = collect($dailySummaries)->filter(function ($summary) {
            return $summary['transaction_count'] > 0;
        })->count();

        return response()->json([
            'year' => (int) $year,
            'month' => (int) $month,
            'daily_summaries' => $dailySummaries,
            'monthly_totals' => [
                'total_income' => (float) $totalIncome,
                'total_expense' => (float) $totalExpense,
                'total_transactions' => $transactions->count(),
                'total_income_count' => $incomeTransactions->count(),
                'total_expense_count' => $expenseTransactions->count(),
            ],
            'days_with_data' => $daysWithData,
            'days_in_month' => $daysInMonth,
        ]);
    }

    /**
     * Get expenses breakdown by category.
     */
    public function category(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $transactions = $request->user()->transactions()
            ->with('category')
            ->betweenDates($request->start_date, $request->end_date)
            ->expenses()
            ->get();

        $totalExpense = $transactions->sum('amount');

        // Group by category
        $byCategory = $transactions->groupBy('category_id');

        $expensesByCategory = [];
        $categoryDetails = [];

        foreach ($byCategory as $categoryId => $categoryTransactions) {
            $category = $categoryTransactions->first()->category;
            $categoryTotal = $categoryTransactions->sum('amount');
            $percentage = $totalExpense > 0 ? round(($categoryTotal / $totalExpense) * 100, 2) : 0;

            $categoryName = $category ? $category->name : 'Unknown';

            $expensesByCategory[$categoryName] = (float) $categoryTotal;

            $categoryDetails[] = [
                'category_id' => (int) $categoryId,
                'category_name' => $categoryName,
                'total' => (float) $categoryTotal,
                'percentage' => $percentage,
                'transaction_count' => $categoryTransactions->count(),
            ];
        }

        // Sort by total descending
        usort($categoryDetails, fn($a, $b) => $b['total'] <=> $a['total']);

        return response()->json([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_expense' => (float) $totalExpense,
            'expenses_by_category' => $expensesByCategory,
            'category_details' => $categoryDetails,
        ]);
    }

    /**
     * Get date range summaries.
     * Returns daily summaries for any date range (max 90 days).
     */
    public function range(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Limit range to 90 days to prevent performance issues
        $daysDiff = $startDate->diffInDays($endDate);
        if ($daysDiff > 90) {
            return response()->json([
                'message' => 'Date range cannot exceed 90 days',
                'errors' => [
                    'end_date' => ['The date range must be 90 days or less']
                ]
            ], 422);
        }

        $transactions = $request->user()->transactions()
            ->betweenDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'))
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');

        // Generate daily summaries for each day in range
        $dailySummaries = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            
            $dayTransactions = $transactions->filter(function ($t) use ($dateStr) {
                return $t->date->format('Y-m-d') === $dateStr;
            });

            $dayIncomeTransactions = $dayTransactions->where('type', 'income');
            $dayExpenseTransactions = $dayTransactions->where('type', 'expense');

            $dailySummaries[] = [
                'date' => $dateStr,
                'total_income' => (float) $dayIncomeTransactions->sum('amount'),
                'total_expense' => (float) $dayExpenseTransactions->sum('amount'),
                'transaction_count' => $dayTransactions->count(),
                'income_count' => $dayIncomeTransactions->count(),
                'expense_count' => $dayExpenseTransactions->count(),
            ];

            $currentDate->addDay();
        }

        $daysWithData = collect($dailySummaries)->filter(function ($summary) {
            return $summary['transaction_count'] > 0;
        })->count();

        return response()->json([
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'daily_summaries' => $dailySummaries,
            'range_totals' => [
                'total_income' => (float) $totalIncome,
                'total_expense' => (float) $totalExpense,
                'total_transactions' => $transactions->count(),
            ],
            'days_in_range' => $daysDiff + 1,
            'days_with_data' => $daysWithData,
        ]);
    }
}

