<x-guest-layout>
    <div class="row h-100">
        <div class="col-lg-6 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="{{ url('/') }}">
                        <span class="h2 text-primary fw-bold">Kiasi Daily</span>
                    </a>
                </div>
                <h1 class="auth-title">Log in.</h1>
                <p class="auth-subtitle mb-5">Log in with your admin credentials to access the portal.</p>

                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="email" class="form-control form-control-xl @error('email') is-invalid @enderror" 
                               placeholder="Email" name="email" value="{{ old('email') }}" required autofocus>
                        <div class="form-control-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
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
                    
                    <div class="form-check form-check-lg d-flex align-items-end">
                        <input class="form-check-input me-2" type="checkbox" id="remember_me" name="remember">
                        <label class="form-check-label text-gray-600" for="remember_me">
                            Keep me logged in
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>
                </form>
                
                <div class="text-center mt-5 text-lg fs-4">
                    @if (Route::has('password.request'))
                        <p><a class="font-bold" href="{{ route('password.request') }}">Forgot password?</a></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-none d-lg-block">
            <div id="auth-right">
            </div>
        </div>
    </div>
</x-guest-layout>
