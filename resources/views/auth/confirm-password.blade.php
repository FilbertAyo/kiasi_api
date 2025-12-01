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
                <h1 class="auth-title">Confirm Password</h1>
                <p class="auth-subtitle mb-5">This is a secure area of the application. Please confirm your password before continuing.</p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf
                    
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" class="form-control form-control-xl @error('password') is-invalid @enderror" 
                               placeholder="Password" name="password" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Confirm</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right">
            </div>
        </div>
    </div>
</x-guest-layout>
