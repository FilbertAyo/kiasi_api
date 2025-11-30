<x-app-layout>
    <x-slot name="header">
        <h3>Profile Settings</h3>
    </x-slot>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                {{-- Profile Information --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Profile Information</h4>
                        <p class="text-muted mb-0">Update your account's profile information and email address.</p>
                    </div>
                    <div class="card-body">
                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                       required autofocus autocomplete="name"
                                       class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                       required autocomplete="username"
                                       class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="mt-2">
                                        <p class="text-muted small">
                                            Your email address is unverified.
                                            <button form="send-verification" class="btn btn-link p-0 align-baseline">
                                                Click here to re-send the verification email.
                                            </button>
                                        </p>

                                        @if (session('status') === 'verification-link-sent')
                                            <p class="text-success small mt-2">
                                                A new verification link has been sent to your email address.
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Save Changes
                                </button>

                                @if (session('status') === 'profile-updated')
                                    <span class="text-success small">Saved successfully!</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Update Password</h4>
                        <p class="text-muted mb-0">Ensure your account is using a long, random password to stay secure.</p>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="mb-3">
                                <label for="update_password_current_password" class="form-label">Current Password</label>
                                <input type="password" name="current_password" id="update_password_current_password" 
                                       autocomplete="current-password"
                                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                                @error('current_password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="update_password_password" class="form-label">New Password</label>
                                <input type="password" name="password" id="update_password_password" 
                                       autocomplete="new-password"
                                       class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                                @error('password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="update_password_password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="update_password_password_confirmation" 
                                       autocomplete="new-password" class="form-control">
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-shield-check"></i> Update Password
                                </button>

                                @if (session('status') === 'password-updated')
                                    <span class="text-success small">Password updated!</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="card border-danger">
                    <div class="card-header bg-danger">
                        <h4 class="card-title text-white">Delete Account</h4>
                        <p class="text-white-50 mb-0">Permanently delete your account and all data.</p>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Once your account is deleted, all of its resources and data will be permanently deleted. 
                            Before deleting your account, please download any data or information that you wish to retain.
                        </p>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>

            {{-- Profile Card --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar avatar-xl bg-primary mx-auto mb-3">
                            <span class="avatar-content fs-3">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        <hr>
                        <div class="text-start">
                            <p class="mb-2"><strong>Joined:</strong> {{ $user->created_at->format('F d, Y') }}</p>
                            <p class="mb-0"><strong>Email Verified:</strong> 
                                @if($user->hasVerifiedEmail())
                                    <span class="badge bg-light-success">Yes</span>
                                @else
                                    <span class="badge bg-light-warning">No</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Delete Account Modal --}}
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-body">
                        <p class="text-muted">
                            Once your account is deleted, all of its resources and data will be permanently deleted. 
                            Please enter your password to confirm you would like to permanently delete your account.
                        </p>
                        <div class="mb-3">
                            <label for="delete_password" class="form-label">Password</label>
                            <input type="password" name="password" id="delete_password" required
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                   placeholder="Enter your password">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->userDeletion->isNotEmpty())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
            deleteModal.show();
        });
    </script>
    @endpush
    @endif
</x-app-layout>
