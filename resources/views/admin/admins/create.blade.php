<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Add Admin</h3>
            <a href="{{ route('admin.admins.index') }}" class="btn btn-light-secondary">
                <i class="bi bi-arrow-left"></i> Back to Admins
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create New Admin</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.admins.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter admin full name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Enter email address">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" required
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter password">
                                <small class="text-muted">Password must be at least 8 characters.</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="form-control"
                                       placeholder="Confirm password">
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Create Admin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>

