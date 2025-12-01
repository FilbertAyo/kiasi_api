<x-guest-layout>
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none">
                        <img src="{{ asset('assets/images/logo/kiasi_logo.svg') }}" alt="Kiasi Daily" style="width: 50px; height: 50px; margin-right: 12px;">
                        <span class="h2 text-primary fw-bold mb-0">Kiasi Daily</span>
                    </a>
                </div>
                <h1 class="auth-title">Verify Email</h1>
                <p class="auth-subtitle mb-5">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success mb-4">
                        A new verification link has been sent to the email address you provided during registration.
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg shadow-lg">Resend Verification Email</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-light-secondary btn-lg">Log Out</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right">
            </div>
        </div>
    </div>
</x-guest-layout>
