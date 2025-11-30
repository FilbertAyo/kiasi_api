<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the reports page.
     */
    public function index(): View
    {
        return view('admin.reports.index');
    }

    /**
     * Export transactions to CSV.
     */
    public function exportTransactions(Request $request): Response
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'type' => ['nullable', 'in:expense,income'],
        ]);

        $query = Transaction::with(['user', 'category'])
            ->betweenDates($request->start_date, $request->end_date);

        if ($request->has('type') && $request->type) {
            $query->ofType($request->type);
        }

        $transactions = $query->orderBy('date')->get();

        $csv = "ID,User,Category,Type,Amount,Description,Date,Created At\n";

        foreach ($transactions as $transaction) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%.2f,\"%s\",%s,%s\n",
                $transaction->id,
                str_replace(',', '', $transaction->user->name ?? 'Unknown'),
                str_replace(',', '', $transaction->category->name ?? 'Unknown'),
                $transaction->type,
                $transaction->amount,
                str_replace('"', '""', $transaction->description ?? ''),
                $transaction->date->format('Y-m-d'),
                $transaction->created_at->format('Y-m-d H:i:s')
            );
        }

        $filename = "transactions_{$request->start_date}_to_{$request->end_date}.csv";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Export users to CSV.
     */
    public function exportUsers(Request $request): Response
    {
        $query = User::regularUsers()->withCount('transactions');

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            }
        }

        $users = $query->orderBy('created_at')->get();

        $csv = "ID,Name,Email,Status,Transactions Count,Created At\n";

        foreach ($users as $user) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%d,%s\n",
                $user->id,
                str_replace(',', '', $user->name),
                $user->email,
                $user->is_blocked ? 'Blocked' : 'Active',
                $user->transactions_count,
                $user->created_at->format('Y-m-d H:i:s')
            );
        }

        $filename = "users_" . Carbon::now()->format('Y-m-d') . ".csv";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Export monthly summary to CSV.
     */
    public function exportMonthlySummary(Request $request): Response
    {
        $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $year = $request->year;
        $month = $request->month;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $csv = "Date,Total Income,Total Expense,Balance,Transaction Count\n";

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dayTransactions = Transaction::forDate($date)->get();

            $income = $dayTransactions->where('type', 'income')->sum('amount');
            $expense = $dayTransactions->where('type', 'expense')->sum('amount');

            $csv .= sprintf(
                "%s,%.2f,%.2f,%.2f,%d\n",
                $date->format('Y-m-d'),
                $income,
                $expense,
                $income - $expense,
                $dayTransactions->count()
            );
        }

        $filename = "monthly_summary_{$year}_{$month}.csv";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}

