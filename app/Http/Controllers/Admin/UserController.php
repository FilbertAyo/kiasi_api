<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::regularUsers()->withCount('transactions');

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            }
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->loadCount('transactions');

        // Get user's transaction summary
        $totalIncome = $user->transactions()->income()->sum('amount');
        $totalExpense = $user->transactions()->expenses()->sum('amount');

        // Get recent transactions
        $recentTransactions = $user->transactions()
            ->with('category')
            ->latest('date')
            ->take(10)
            ->get();

        return view('admin.users.show', compact('user', 'totalIncome', 'totalExpense', 'recentTransactions'));
    }

    /**
     * Block a user.
     */
    public function block(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot block an admin user.');
        }

        $user->update(['is_blocked' => true]);

        // Revoke all tokens
        $user->tokens()->delete();

        return back()->with('success', "User {$user->name} has been blocked.");
    }

    /**
     * Unblock a user.
     */
    public function unblock(User $user): RedirectResponse
    {
        $user->update(['is_blocked' => false]);

        return back()->with('success', "User {$user->name} has been unblocked.");
    }
}

