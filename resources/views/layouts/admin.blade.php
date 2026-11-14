<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Administration Portal') — Mobile Resource Hub</title>

    <!-- Theme Initialization Script (Anti-FOUT) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('mrh_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Compiled Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Admin-specific Layout Extensions */
        .admin-sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--surface-color);
            border-right: 1px solid var(--border-color);
            transition: transform 0.25s ease;
            z-index: 1040;
        }
        .admin-content-wrapper {
            flex: 1;
            min-width: 0;
            background: var(--bg-color);
            min-height: 100vh;
        }
        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.15s ease;
            margin-bottom: 0.25rem;
        }
        .admin-nav-link:hover, .admin-nav-link.active {
            color: var(--primary-color);
            background: rgba(37, 99, 235, 0.12);
        }
        .admin-nav-link i {
            font-size: 1.15rem;
        }
        .admin-topbar {
            height: 64px;
            background: var(--glass-bg);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                transform: translateX(-100%);
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar d-flex flex-column p-3 position-relative" id="adminSidebar">
        <!-- Brand Header -->
        <div class="d-flex align-items-center justify-content-between mb-4 px-2 pt-2">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <div class="brand-icon-box" style="width: 36px; height: 36px; font-size: 1.2rem;">
                    <i class="bi bi-cpu-fill"></i>
                </div>
                <span class="fw-extrabold fs-5 text-body">MRH <span class="text-primary">Admin</span></span>
            </a>
            <button class="btn btn-sm btn-outline-secondary d-lg-none" id="closeSidebarBtn" aria-label="Close sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow-1 overflow-y-auto">
            <div class="text-uppercase small fw-bold text-muted px-3 mb-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">Core</div>
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="text-uppercase small fw-bold text-muted px-3 mt-4 mb-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">Device Catalog</div>
            <a href="{{ route('admin.brands.index') }}" class="admin-nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                <i class="bi bi-award"></i>
                <span>Brands</span>
            </a>
            <a href="{{ route('admin.devices.index') }}" class="admin-nav-link {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}">
                <i class="bi bi-phone"></i>
                <span>Devices</span>
            </a>
            <a href="{{ route('admin.specifications.index') }}" class="admin-nav-link {{ request()->routeIs('admin.specifications.*') ? 'active' : '' }}">
                <i class="bi bi-sliders2"></i>
                <span>Specifications</span>
            </a>

            <div class="text-uppercase small fw-bold text-muted px-3 mt-4 mb-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">Firmware & Downloads</div>
            <a href="{{ route('admin.firmware.index') }}" class="admin-nav-link {{ request()->routeIs('admin.firmware.*') ? 'active' : '' }}">
                <i class="bi bi-hdd-network"></i>
                <span>Firmware ROMs</span>
            </a>
            <a href="{{ route('admin.flash-files.index') }}" class="admin-nav-link {{ request()->routeIs('admin.flash-files.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-zip"></i>
                <span>Flash Files</span>
            </a>
            <a href="{{ route('admin.software.index') }}" class="admin-nav-link {{ request()->routeIs('admin.software.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i>
                <span>Flashing Tools</span>
            </a>
            <a href="{{ route('admin.drivers.index') }}" class="admin-nav-link {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
                <i class="bi bi-usb-symbol"></i>
                <span>USB Drivers</span>
            </a>

            <div class="text-uppercase small fw-bold text-muted px-3 mt-4 mb-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">Content Management</div>
            <a href="{{ route('admin.posts.index') }}" class="admin-nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Articles & Guides</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder2"></i>
                <span>Categories</span>
            </a>
            <a href="{{ route('admin.tags.index') }}" class="admin-nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span>Tags</span>
            </a>
            <a href="{{ route('admin.pages.index') }}" class="admin-nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                <i class="bi bi-layout-text-window-reverse"></i>
                <span>Static Pages</span>
            </a>
            <a href="{{ route('admin.media.index') }}" class="admin-nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                <i class="bi bi-images"></i>
                <span>Media Library</span>
            </a>

            <div class="text-uppercase small fw-bold text-muted px-3 mt-4 mb-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">Design & Monetization</div>
            <a href="{{ route('admin.theme.index') }}" class="admin-nav-link {{ request()->routeIs('admin.theme.*') ? 'active' : '' }}">
                <i class="bi bi-palette2"></i>
                <span>Theme Customizer</span>
            </a>
            <a href="{{ route('admin.homepage.index') }}" class="admin-nav-link {{ request()->routeIs('admin.homepage.*') ? 'active' : '' }}">
                <i class="bi bi-ui-checks-grid"></i>
                <span>Homepage Manager</span>
            </a>
            <a href="{{ route('admin.advertisements.index') }}" class="admin-nav-link {{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}">
                <i class="bi bi-badge-ad"></i>
                <span>Advertisements</span>
            </a>

            <div class="text-uppercase small fw-bold text-muted px-3 mt-4 mb-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">Operations</div>
            <a href="{{ route('admin.messages.index') }}" class="admin-nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-envelope"></i>
                    <span>Inquiries</span>
                </div>
                @php
                    $unreadMessagesCount = \App\Models\ContactMessage::unread()->count();
                @endphp
                @if($unreadMessagesCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $unreadMessagesCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.menus.index') }}" class="admin-nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                <i class="bi bi-menu-button-wide"></i>
                <span>Navigation Menus</span>
            </a>
            <a href="{{ route('admin.activity.index') }}" class="admin-nav-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i>
                <span>Activity Audit</span>
            </a>
            <a href="{{ route('admin.search-analytics.index') }}" class="admin-nav-link {{ request()->routeIs('admin.search-analytics.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Search Analytics</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="pt-3 border-top mt-auto">
            <a href="{{ url('/') }}" target="_blank" class="admin-nav-link text-body">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>View Public Site</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-content-wrapper d-flex flex-column">
        <!-- Admin Topbar -->
        <header class="admin-topbar px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="openSidebarBtn" aria-label="Open sidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 d-none d-sm-block">@yield('page_title', 'Admin Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Theme Toggle Button -->
                <button class="theme-toggle-btn" id="themeToggleBtn" type="button" aria-label="Toggle theme">
                    <i class="bi bi-sun-fill theme-icon-light"></i>
                    <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
                </button>

                <!-- Current User Profile & Role -->
                @auth
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 rounded-pill" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                        <span class="fw-semibold">{{ Auth::user()->name }}</span>
                        <span class="badge bg-primary text-uppercase" style="font-size: 0.65rem;">
                            {{ Auth::user()->roles->first()?->name ?? 'Admin' }}
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="px-3 py-2 border-bottom">
                            <div class="small fw-bold text-body">{{ Auth::user()->name }}</div>
                            <div class="small text-muted">{{ Auth::user()->email }}</div>
                        </li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 mt-1">
                                    <i class="bi bi-box-arrow-right"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
        </header>

        <!-- Main Body -->
        <main class="p-4 flex-grow-1">
            <!-- Flash Notifications -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('status'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Please resolve the following errors:</div>
                <ul class="mb-0 small ps-3">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @yield('content')
        </main>

        <!-- Admin Footer -->
        <footer class="p-3 text-center border-top text-muted small">
            &copy; {{ date('Y') }} Mobile Resource Hub Administration &bull; Glassmorphic CMS v2.0
        </footer>
    </div>

    <!-- Mobile Sidebar Toggle Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const openBtn = document.getElementById('openSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');

            if (openBtn && sidebar) {
                openBtn.addEventListener('click', () => sidebar.classList.add('show'));
            }
            if (closeBtn && sidebar) {
                closeBtn.addEventListener('click', () => sidebar.classList.remove('show'));
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
