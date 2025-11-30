<x-admin-layout>
    <x-slot name="header">
        <h3>Admin Dashboard</h3>
    </x-slot>

    <section class="row">
        <div class="col-12 col-lg-9">
            <div class="row">
                {{-- Total Users --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Users</h6>
                                    <h6 class="font-extrabold mb-0">{{ number_format($totalUsers) }}</h6>
                                    <p class="text-muted mb-0">
                                        <span class="text-success">{{ $activeUsers }}</span> active,
                                        <span class="text-danger">{{ $blockedUsers }}</span> blocked
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Transactions --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Transactions</h6>
                                    <h6 class="font-extrabold mb-0">{{ number_format($totalTransactions) }}</h6>
                                    <p class="text-muted mb-0">{{ $todayTransactions }} today</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Income --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-arrow-down-circle-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Income</h6>
                                    <h6 class="font-extrabold mb-0 text-success">TZS {{ number_format($totalIncome) }}</h6>
                                    <p class="text-muted mb-0">TZS {{ number_format($monthlyIncome) }} this month</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Expense --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon red mb-2">
                                        <i class="bi bi-arrow-up-circle-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Expense</h6>
                                    <h6 class="font-extrabold mb-0 text-danger">TZS {{ number_format($totalExpense) }}</h6>
                                    <p class="text-muted mb-0">TZS {{ number_format($monthlyExpense) }} this month</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category Breakdown --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Top Expense Categories (This Month)</h4>
                        </div>
                        <div class="card-body">
                            @forelse($categoryBreakdown as $category)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-sm">{{ $category['name'] }}</span>
                                        <span class="text-sm fw-bold">TZS {{ number_format($category['total']) }}</span>
                                    </div>
                                    @php
                                        $maxTotal = $categoryBreakdown->max('total');
                                        $percentage = $maxTotal > 0 ? ($category['total'] / $maxTotal) * 100 : 0;
                                    @endphp
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" 
                                             aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No expenses this month.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Recent Transactions</h4>
                            <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-lg">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Category</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $transaction)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-md bg-light-primary me-3">
                                                            <span class="avatar-content">{{ substr($transaction->user->name ?? 'U', 0, 1) }}</span>
                                                        </div>
                                                        <span>{{ $transaction->user->name ?? 'Unknown' }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $transaction->category->name ?? 'Unknown' }}</td>
                                                <td>
                                                    <span class="badge bg-light-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($transaction->type) }}
                                                    </span>
                                                </td>
                                                <td class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                    TZS {{ number_format($transaction->amount) }}
                                                </td>
                                                <td>{{ $transaction->date->format('M d, Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No transactions yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-3">
            {{-- User Profile Card --}}
            <div class="card">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl bg-primary">
                            <span class="avatar-content fs-4">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ms-3 name">
                            <h5 class="font-bold">{{ Auth::user()->name }}</h5>
                            <h6 class="text-muted mb-0">Administrator</h6>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Users --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Recent Users</h4>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light-primary">View All</a>
                </div>
                <div class="card-content pb-4">
                    @forelse($recentUsers as $user)
                        <div class="recent-message d-flex px-4 py-3">
                            <div class="avatar avatar-lg bg-light-primary">
                                <span class="avatar-content">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="name ms-4">
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <h6 class="text-muted mb-0">{{ $user->created_at->diffForHumans() }}</h6>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-3">
                            <p class="text-muted mb-0">No users yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
