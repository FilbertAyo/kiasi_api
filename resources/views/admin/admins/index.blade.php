<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Admins Management</h3>
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Admin
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Admins</h4>
            </div>
            <div class="card-body">
                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.admins.index') }}" class="mb-4">
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
                                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('admin.admins.index') }}" class="btn btn-light-secondary">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Admins Table --}}
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Admin</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $admin)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md bg-light-primary me-3">
                                                <span class="avatar-content">{{ substr($admin->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">{{ $admin->name }}</p>
                                                <small class="text-muted">{{ $admin->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($admin->is_blocked)
                                            <span class="badge bg-light-danger">Disabled</span>
                                        @else
                                            <span class="badge bg-light-success">Active</span>
                                        @endif
                                    </td>
                                    <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-light-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        @if($admin->id !== auth()->id())
                                            @if($admin->is_blocked)
                                                <form method="POST" action="{{ route('admin.admins.enable', $admin) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light-success">
                                                        <i class="bi bi-check-circle"></i> Enable
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.admins.disable', $admin) }}" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to disable this admin?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light-danger">
                                                        <i class="bi bi-x-circle"></i> Disable
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-muted small">Current User</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No admins found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $admins->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>

