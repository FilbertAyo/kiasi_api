<x-admin-layout>
    <x-slot name="header">
        <h3>All Transactions</h3>
    </x-slot>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Transactions</h4>
            </div>
            <div class="card-body">
                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.transactions.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search description..."
                                   class="form-control">
                        </div>
                        <div class="col-md-2">
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                   placeholder="Start Date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                   placeholder="End Date" class="form-control">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'user_id', 'category_id', 'type', 'start_date', 'end_date']))
                            <a href="{{ route('admin.transactions.index') }}" class="btn btn-light-secondary">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Transactions Table --}}
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $transaction->user) }}" class="text-primary">
                                            {{ $transaction->user->name ?? 'Unknown' }}
                                        </a>
                                    </td>
                                    <td>{{ $transaction->category->name ?? 'Unknown' }}</td>
                                    <td>{{ Str::limit($transaction->description, 40) ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-light-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                        TZS {{ number_format($transaction->amount) }}
                                    </td>
                                    <td>{{ $transaction->date->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $transactions->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
