<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>User Details</h3>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="row">
            {{-- User Info Card --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar avatar-xl bg-primary me-3">
                                <span class="avatar-content fs-4">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $user->name }}</h4>
                                <p class="text-muted mb-0">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Status</label>
                            <div>
                                @if($user->is_blocked)
                                    <span class="badge bg-light-danger">Blocked</span>
                                @else
                                    <span class="badge bg-light-success">Active</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Joined</label>
                            <p class="mb-0 fw-bold">{{ $user->created_at->format('F d, Y') }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small">Total Transactions</label>
                            <p class="mb-0 fw-bold">{{ number_format($user->transactions_count) }}</p>
                        </div>

                        <hr>

                        @if($user->is_blocked)
                            <form method="POST" action="{{ route('admin.users.unblock', $user) }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-unlock"></i> Unblock User
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.block', $user) }}" 
                                  onsubmit="return confirm('Are you sure you want to block this user?')">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-lock"></i> Block User
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stats & Transactions --}}
            <div class="col-lg-8">
                {{-- Stats Cards --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon green me-3">
                                        <i class="bi bi-arrow-down-circle-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Total Income</h6>
                                        <h4 class="text-success mb-0">TZS {{ number_format($totalIncome) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon red me-3">
                                        <i class="bi bi-arrow-up-circle-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Total Expense</h6>
                                        <h4 class="text-danger mb-0">TZS {{ number_format($totalExpense) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Transactions --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Transactions</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->category->name ?? 'Unknown' }}</td>
                                            <td>{{ Str::limit($transaction->description, 30) ?? '-' }}</td>
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
                                            <td colspan="5" class="text-center text-muted py-4">No transactions yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
