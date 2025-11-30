<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display a listing of admins.
     */
    public function index(Request $request): View
    {
        $query = User::admins();

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

        $admins = $query->latest()->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create(): View
    {
        return view('admin.admins.create');
    }

    /**
     * Store a newly created admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'is_blocked' => false,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $admin): View
    {
        // Ensure we're only editing admins
        if (!$admin->isAdmin()) {
            abort(404);
        }

        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin.
     */
    public function update(Request $request, User $admin): RedirectResponse
    {
        // Ensure we're only updating admins
        if (!$admin->isAdmin()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $admin->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Disable an admin.
     */
    public function disable(User $admin): RedirectResponse
    {
        // Ensure we're only disabling admins
        if (!$admin->isAdmin()) {
            abort(404);
        }

        // Prevent disabling yourself
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        $admin->update(['is_blocked' => true]);

        // Revoke all tokens
        $admin->tokens()->delete();

        return back()->with('success', "Admin {$admin->name} has been disabled.");
    }

    /**
     * Enable an admin.
     */
    public function enable(User $admin): RedirectResponse
    {
        // Ensure we're only enabling admins
        if (!$admin->isAdmin()) {
            abort(404);
        }

        $admin->update(['is_blocked' => false]);

        return back()->with('success', "Admin {$admin->name} has been enabled.");
    }
}

