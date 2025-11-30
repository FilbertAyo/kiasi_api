<x-admin-layout>
    <x-slot name="header">
        <h3>Users Management</h3>
    </x-slot>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Users</h4>
            </div>
            <div class="card-body">
                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search by name or email..."
                                   class="form-control">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-light-secondary">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Users Table --}}
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Status</th>
                                <th>Transactions</th>
                                <th>Joined</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md bg-light-primary me-3">
                                                <span class="avatar-content">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">{{ $user->name }}</p>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->is_blocked)
                                            <span class="badge bg-light-danger">Blocked</span>
                                        @else
                                            <span class="badge bg-light-success">Active</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($user->transactions_count) }}</td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-light-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        @if($user->is_blocked)
                                            <form method="POST" action="{{ route('admin.users.unblock', $user) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-light-success">
                                                    <i class="bi bi-unlock"></i> Unblock
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.block', $user) }}" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to block this user?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-light-danger">
                                                    <i class="bi bi-lock"></i> Block
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
