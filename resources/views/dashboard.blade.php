<x-app-layout>
    <x-slot name="header">
        <h3>Dashboard</h3>
    </x-slot>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-xl bg-light-success me-3">
                        <span class="avatar-content"><i class="bi bi-check-circle fs-3"></i></span>
                    </div>
                    <div>
                        <h4 class="mb-1">Welcome back, {{ Auth::user()->name }}!</h4>
                        <p class="text-muted mb-0">You're logged in successfully.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
