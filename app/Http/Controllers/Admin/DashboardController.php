<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with analytics.
     */
    public function index(): View
    {
        // Get overall stats
        $totalUsers = User::regularUsers()->count();
        $activeUsers = User::regularUsers()->active()->count();
        $blockedUsers = User::regularUsers()->where('is_blocked', true)->count();

        $totalTransactions = Transaction::count();
        $totalIncome = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expenses()->sum('amount');

        // Get today's stats
        $today = Carbon::today();
        $todayTransactions = Transaction::forDate($today)->count();
        $todayIncome = Transaction::forDate($today)->income()->sum('amount');
        $todayExpense = Transaction::forDate($today)->expenses()->sum('amount');

        // Get this month's stats
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthlyTransactions = Transaction::betweenDates($startOfMonth, $endOfMonth)->count();
        $monthlyIncome = Transaction::betweenDates($startOfMonth, $endOfMonth)->income()->sum('amount');
        $monthlyExpense = Transaction::betweenDates($startOfMonth, $endOfMonth)->expenses()->sum('amount');

        // Get recent users
        $recentUsers = User::regularUsers()
            ->latest()
            ->take(5)
            ->get();

        // Get recent transactions
        $recentTransactions = Transaction::with(['user', 'category'])
            ->latest()
            ->take(10)
            ->get();

        // Get daily transactions for chart (last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayTransactions = Transaction::forDate($date)->get();

            $chartData[] = [
                'date' => $date->format('M d'),
                'income' => (float) $dayTransactions->where('type', 'income')->sum('amount'),
                'expense' => (float) $dayTransactions->where('type', 'expense')->sum('amount'),
            ];
        }

        // Get category breakdown for expenses this month
        $categoryBreakdown = Transaction::with('category')
            ->betweenDates($startOfMonth, $endOfMonth)
            ->expenses()
            ->get()
            ->groupBy('category_id')
            ->map(function ($transactions) {
                $category = $transactions->first()->category;
                return [
                    'name' => $category ? $category->name : 'Unknown',
                    'color' => $category ? $category->color : '0xFF8B949E',
                    'total' => (float) $transactions->sum('amount'),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->take(5);

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'blockedUsers',
            'totalTransactions',
            'totalIncome',
            'totalExpense',
            'todayTransactions',
            'todayIncome',
            'todayExpense',
            'monthlyTransactions',
            'monthlyIncome',
            'monthlyExpense',
            'recentUsers',
            'recentTransactions',
            'chartData',
            'categoryBreakdown'
        ));
    }
}

