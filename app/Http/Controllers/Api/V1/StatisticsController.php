<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * Get comprehensive statistics overview.
     * 
     * GET /api/v1/statistics/overview?period={period}
     * period: week, month, or year (default: month)
     */
    public function overview(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'nullable|in:week,month,year',
        ]);

        $period = $request->get('period', 'month');
        $user = $request->user();

        // Calculate date range based on period
        $endDate = Carbon::now();
        switch ($period) {
            case 'week':
                $startDate = $endDate->copy()->startOfWeek();
                $previousStartDate = $startDate->copy()->subWeek();
                $previousEndDate = $previousStartDate->copy()->endOfWeek();
                break;
            case 'year':
                $startDate = $endDate->copy()->startOfYear();
                $previousStartDate = $startDate->copy()->subYear();
                $previousEndDate = $previousStartDate->copy()->endOfYear();
                break;
            case 'month':
            default:
                $startDate = $endDate->copy()->startOfMonth();
                $previousStartDate = $startDate->copy()->subMonth();
                $previousEndDate = $previousStartDate->copy()->endOfMonth();
                break;
        }

        // Get current period transactions
        $transactions = $user->transactions()
            ->with('category')
            ->betweenDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'))
            ->get();

        // Get previous period transactions for comparison
        $previousTransactions = $user->transactions()
            ->betweenDates($previousStartDate->format('Y-m-d'), $previousEndDate->format('Y-m-d'))
            ->get();

        // Calculate totals
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Previous period totals
        $previousIncome = $previousTransactions->where('type', 'income')->sum('amount');
        $previousExpense = $previousTransactions->where('type', 'expense')->sum('amount');

        // Calculate trends
        $incomeChangePercent = $previousIncome > 0 
            ? round((($totalIncome - $previousIncome) / $previousIncome) * 100, 2)
            : ($totalIncome > 0 ? 100 : 0);
        
        $expenseChangePercent = $previousExpense > 0
            ? round((($totalExpense - $previousExpense) / $previousExpense) * 100, 2)
            : ($totalExpense > 0 ? 100 : 0);

        // Expenses by category
        $expenseTransactions = $transactions->where('type', 'expense');
        $expensesByCategory = $expenseTransactions->groupBy('category_id')->map(function ($categoryTransactions) {
            return (float) $categoryTransactions->sum('amount');
        })->mapWithKeys(function ($total, $categoryId) use ($expenseTransactions) {
            $category = $expenseTransactions->firstWhere('category_id', $categoryId)?->category;
            $categoryName = $category ? $category->name : 'Unknown';
            return [$categoryName => $total];
        })->sortDesc();

        // Income by category
        $incomeTransactions = $transactions->where('type', 'income');
        $incomeByCategory = $incomeTransactions->groupBy('category_id')->map(function ($categoryTransactions) {
            return (float) $categoryTransactions->sum('amount');
        })->mapWithKeys(function ($total, $categoryId) use ($incomeTransactions) {
            $category = $incomeTransactions->firstWhere('category_id', $categoryId)?->category;
            $categoryName = $category ? $category->name : 'Unknown';
            return [$categoryName => $total];
        })->sortDesc();

        return response()->json([
            'period' => $period,
            'period_start' => $startDate->format('Y-m-d'),
            'period_end' => $endDate->format('Y-m-d'),
            'totals' => [
                'total_income' => (float) $totalIncome,
                'total_expense' => (float) $totalExpense,
                'net_balance' => (float) $netBalance,
                'transaction_count' => $transactions->count(),
            ],
            'expenses_by_category' => $expensesByCategory,
            'income_by_category' => $incomeByCategory,
            'trends' => [
                'income_change_percent' => $incomeChangePercent,
                'expense_change_percent' => $expenseChangePercent,
                'compared_to_previous_period' => $period,
            ],
        ]);
    }
}

