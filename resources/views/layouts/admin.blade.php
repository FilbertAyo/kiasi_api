<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Kiasi Daily') }} - Admin</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
    @stack('styles')
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="{{ route('admin.dashboard') }}">
                                <span class="h4 text-primary fw-bold">Kiasi Daily</span>
                            </a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Main</li>

                        <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.index') }}" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Users</span>
                            </a>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->routeIs('admin.transactions.*') || request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-wallet-fill"></i>
                                <span>Finance</span>
                            </a>
                            <ul class="submenu {{ request()->routeIs('admin.transactions.*') || request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <li class="submenu-item {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.transactions.index') }}">Transactions</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.reports.index') }}">Reports</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.content.*') || request()->routeIs('admin.faq.*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-folder-fill"></i>
                                <span>Content</span>
                            </a>
                            <ul class="submenu {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.content.*') || request()->routeIs('admin.faq.*') ? 'active' : '' }}">
                                <li class="submenu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.categories.index') }}">Categories</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.content.index') }}">Terms & Privacy</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('admin.faq.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.faq.index') }}">FAQ</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-title">System</li>

                        <li class="sidebar-item {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.admins.index') }}" class='sidebar-link'>
                                <i class="bi bi-shield-check"></i>
                                <span>Admins</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.settings.index') }}" class='sidebar-link'>
                                <i class="bi bi-gear-fill"></i>
                                <span>Settings</span>
                            </a>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-person-circle"></i>
                                <span>Account</span>
                            </a>
                            <ul class="submenu {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                                <li class="submenu-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                                    <a href="{{ route('profile.edit') }}">Profile</a>
                                </li>
                                <li class="submenu-item">
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                        @csrf
                                    </form>
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            @isset($header)
            <div class="page-heading">
                {{ $header }}
            </div>
            @endisset

            <div class="page-content">
                {{-- Flash Messages --}}
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                {{ $slot }}
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>{{ date('Y') }} &copy; {{ config('app.name', 'Kiasi Daily') }}</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>

</html>
