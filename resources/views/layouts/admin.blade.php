<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Administration Portal') — Mobile Resource Hub</title>

    <!-- Theme Initialization Script (Anti-FOUT) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('mrh_admin_theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Compiled Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="d-flex flex-row admin-body">

    <!-- Mobile Drawer Backdrop -->
    <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Admin Left Sidebar (Lumzy Reference Style) -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Sidebar Brand Header -->
        <div class="sidebar-brand-wrapper">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <div class="brand-badge-lumzy">
                    <i class="bi bi-cpu-fill"></i>
                </div>
                <div class="sidebar-brand-text">
                    <span class="fw-bold fs-6 text-body">Mobile<span class="text-primary">Hub</span></span>
                    <span class="d-block text-muted" style="font-size: 0.68rem; font-weight: 500; letter-spacing: 0.02em;">Admin Portal</span>
                </div>
            </a>
            <button class="sidebar-collapse-toggle d-none d-lg-flex" id="desktopSidebarToggle" title="Toggle Sidebar (Expand/Collapse)" aria-label="Toggle Sidebar">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>
            <button class="sidebar-collapse-toggle d-lg-none" id="closeSidebarBtn" title="Close menu" aria-label="Close sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-grow-1 py-2">
            <!-- Group A: Overview -->
            <div class="admin-nav-group-title sidebar-hide-collapsed">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard', 'admin.root') ? 'active' : '' }}" title="Dashboard Overview">
                <i class="bi bi-grid-1x2-fill"></i>
                <span class="sidebar-hide-collapsed">Dashboard</span>
            </a>
            <a href="{{ route('admin.analytics.visitors') }}" class="admin-nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}" title="Visitor Analytics">
                <i class="bi bi-graph-up"></i>
                <span class="sidebar-hide-collapsed">Visitor Analytics</span>
            </a>

            <!-- Group B: Mobile Management -->
            <div class="admin-nav-group-title sidebar-hide-collapsed">Mobile Management</div>
            <a href="{{ route('admin.brands.index') }}" class="admin-nav-link {{ request()->routeIs('admin.brands.index', 'admin.brands.edit') ? 'active' : '' }}" title="Brands">
                <i class="bi bi-award"></i>
                <span class="sidebar-hide-collapsed">Brands</span>
            </a>
            <a href="{{ route('admin.brands.create') }}" class="admin-subnav-link sidebar-hide-collapsed {{ request()->routeIs('admin.brands.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>Add New Brand</span>
            </a>
            <a href="{{ route('admin.devices.index') }}" class="admin-nav-link {{ request()->routeIs('admin.devices.index', 'admin.devices.edit') ? 'active' : '' }}" title="Mobile Devices">
                <i class="bi bi-phone"></i>
                <span class="sidebar-hide-collapsed">Mobile Devices</span>
            </a>
            <a href="{{ route('admin.devices.create') }}" class="admin-subnav-link sidebar-hide-collapsed {{ request()->routeIs('admin.devices.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>Add New Mobile</span>
            </a>
            <a href="{{ route('admin.specifications.index') }}" class="admin-nav-link {{ request()->routeIs('admin.specifications.*') ? 'active' : '' }}" title="Specifications Builder">
                <i class="bi bi-sliders2"></i>
                <span class="sidebar-hide-collapsed">Specifications</span>
            </a>

            <!-- Group C: Firmware & Resources -->
            <div class="admin-nav-group-title sidebar-hide-collapsed">Firmware & Resources</div>
            <a href="{{ route('admin.firmware.index') }}" class="admin-nav-link {{ request()->routeIs('admin.firmware.index', 'admin.firmware.edit') ? 'active' : '' }}" title="Firmware ROMs">
                <i class="bi bi-hdd-network"></i>
                <span class="sidebar-hide-collapsed">Firmware / ROMs</span>
            </a>
            <a href="{{ route('admin.firmware.create') }}" class="admin-subnav-link sidebar-hide-collapsed {{ request()->routeIs('admin.firmware.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>Add New Firmware</span>
            </a>
            <a href="{{ route('admin.flash-files.index') }}" class="admin-nav-link {{ request()->routeIs('admin.flash-files.index', 'admin.flash-files.edit') ? 'active' : '' }}" title="Flash Files">
                <i class="bi bi-file-earmark-zip"></i>
                <span class="sidebar-hide-collapsed">Flash Files</span>
            </a>
            <a href="{{ route('admin.flash-files.create') }}" class="admin-subnav-link sidebar-hide-collapsed {{ request()->routeIs('admin.flash-files.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>Add New Flash File</span>
            </a>
            <a href="{{ route('admin.software.index') }}" class="admin-nav-link {{ request()->routeIs('admin.software.*') ? 'active' : '' }}" title="Flashing Tools">
                <i class="bi bi-tools"></i>
                <span class="sidebar-hide-collapsed">Flashing Tools</span>
            </a>
            <a href="{{ route('admin.drivers.index') }}" class="admin-nav-link {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}" title="USB Drivers">
                <i class="bi bi-usb-symbol"></i>
                <span class="sidebar-hide-collapsed">USB Drivers</span>
            </a>

            <!-- Group D: Content Management -->
            <div class="admin-nav-group-title sidebar-hide-collapsed">Content Management</div>
            <a href="{{ route('admin.posts.index') }}" class="admin-nav-link {{ request()->routeIs('admin.posts.index', 'admin.posts.edit') ? 'active' : '' }}" title="Articles & Guides">
                <i class="bi bi-file-earmark-text"></i>
                <span class="sidebar-hide-collapsed">Articles & News</span>
            </a>
            <a href="{{ route('admin.posts.create') }}" class="admin-subnav-link sidebar-hide-collapsed {{ request()->routeIs('admin.posts.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>Add New Article</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" title="Categories">
                <i class="bi bi-folder2"></i>
                <span class="sidebar-hide-collapsed">Categories</span>
            </a>
            <a href="{{ route('admin.tags.index') }}" class="admin-nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}" title="Tags">
                <i class="bi bi-tags"></i>
                <span class="sidebar-hide-collapsed">Tags</span>
            </a>
            <a href="{{ route('admin.pages.index') }}" class="admin-nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" title="Static Pages CMS">
                <i class="bi bi-layout-text-window-reverse"></i>
                <span class="sidebar-hide-collapsed">Pages</span>
            </a>
            <a href="{{ route('admin.menus.index') }}" class="admin-nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}" title="Navigation Menus">
                <i class="bi bi-menu-button-wide"></i>
                <span class="sidebar-hide-collapsed">Menus</span>
            </a>
            <a href="{{ route('admin.media.index') }}" class="admin-nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" title="Media Library">
                <i class="bi bi-images"></i>
                <span class="sidebar-hide-collapsed">Media Library</span>
            </a>

            <!-- Group E: Website & Monetization -->
            <div class="admin-nav-group-title sidebar-hide-collapsed">Website Management</div>
            <a href="{{ route('admin.homepage.index') }}" class="admin-nav-link {{ request()->routeIs('admin.homepage.*') ? 'active' : '' }}" title="Homepage Sections Layout">
                <i class="bi bi-ui-checks-grid"></i>
                <span class="sidebar-hide-collapsed">Homepage Sections</span>
            </a>
            <a href="{{ route('admin.advertisements.index') }}" class="admin-nav-link {{ request()->routeIs('admin.advertisements.index', 'admin.advertisements.edit') ? 'active' : '' }}" title="Advertisements">
                <i class="bi bi-badge-ad"></i>
                <span class="sidebar-hide-collapsed">Advertisements</span>
            </a>
            <a href="{{ route('admin.advertisements.create') }}" class="admin-subnav-link sidebar-hide-collapsed {{ request()->routeIs('admin.advertisements.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>Add New Ad</span>
            </a>
            <a href="{{ route('admin.theme.index') }}" class="admin-nav-link {{ request()->routeIs('admin.theme.*') ? 'active' : '' }}" title="Theme Customizer">
                <i class="bi bi-palette2"></i>
                <span class="sidebar-hide-collapsed">Theme Settings</span>
            </a>
            <a href="{{ route('admin.settings.whatsapp') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.whatsapp*') ? 'active' : '' }}" title="WhatsApp Support Settings">
                <i class="bi bi-whatsapp text-success"></i>
                <span class="sidebar-hide-collapsed">WhatsApp Support</span>
            </a>

            <!-- Group F: SEO & Policies -->
            <div class="admin-nav-group-title sidebar-hide-collapsed">SEO & Policies</div>
            <a href="{{ route('admin.search-analytics.index') }}" class="admin-nav-link {{ request()->routeIs('admin.search-analytics.*') ? 'active' : '' }}" title="Search Analytics">
                <i class="bi bi-graph-up-arrow"></i>
                <span class="sidebar-hide-collapsed">Search Analytics</span>
            </a>
            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="admin-nav-link" title="XML Sitemap">
                <i class="bi bi-diagram-3"></i>
                <span class="sidebar-hide-collapsed">Sitemap XML &nearr;</span>
            </a>
            <a href="{{ url('/robots.txt') }}" target="_blank" class="admin-nav-link" title="Robots.txt">
                <i class="bi bi-file-earmark-code"></i>
                <span class="sidebar-hide-collapsed">Robots.txt &nearr;</span>
            </a>
            <a href="{{ route('admin.pages.index') }}" class="admin-nav-link" title="Essential Policy Pages">
                <i class="bi bi-shield-check"></i>
                <span class="sidebar-hide-collapsed">Policy Pages</span>
            </a>

            <!-- Group G: Administration & Audit -->
            <div class="admin-nav-group-title sidebar-hide-collapsed">Administration</div>
            <a href="{{ route('admin.messages.index') }}" class="admin-nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }} d-flex justify-content-between align-items-center" title="Contact Inquiries">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-envelope"></i>
                    <span class="sidebar-hide-collapsed">Contact Inquiries</span>
                </div>
                @php
                    try {
                        $unreadMessagesCount = \App\Models\ContactMessage::unread()->count();
                    } catch (\Throwable $e) {
                        $unreadMessagesCount = 0;
                    }
                @endphp
                @if($unreadMessagesCount > 0)
                <span class="badge bg-danger rounded-pill sidebar-hide-collapsed">{{ $unreadMessagesCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.activity.index') }}" class="admin-nav-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}" title="Activity Audit Logs">
                <i class="bi bi-clock-history"></i>
                <span class="sidebar-hide-collapsed">Activity Logs</span>
            </a>
        </nav>

        <!-- Sidebar Lumzy Bottom Card -->
        <div class="sidebar-footer-card sidebar-hide-collapsed mt-auto">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-patch-check-fill text-primary"></i>
                <span class="fw-bold small text-body">Mobile Resource Hub</span>
            </div>
            <p class="text-muted mb-2" style="font-size: 0.75rem;">Production v2.0 &bull; Online</p>
            <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 rounded-pill py-1" style="font-size: 0.78rem;">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Public Site
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-content-wrapper flex-grow-1 d-flex flex-column" style="min-width: 0;">
        <!-- Admin Topbar (Lumzy Reference Style) -->
        <header class="admin-topbar">
            <!-- Topbar Left: Sidebar Drawer Toggle (Mobile) + Page Title + Global Search -->
            <div class="d-flex align-items-center gap-3 flex-grow-1 me-3">
                <button class="topbar-icon-btn d-lg-none" id="openSidebarBtn" aria-label="Open navigation sidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <h5 class="fw-bold mb-0 d-none d-sm-block text-body" style="font-size: 0.98rem; white-space: nowrap;">@yield('page_title', 'Admin Dashboard')</h5>

                <!-- Global Administrative Quick Search Field -->
                <div class="admin-search-wrapper d-none d-lg-block">
                    <i class="bi bi-search admin-search-icon"></i>
                    <input 
                        type="search" 
                        class="admin-search-input" 
                        id="adminGlobalSearch" 
                        placeholder="Search devices, firmware, articles, settings..." 
                        aria-label="Global administrative search"
                        autocomplete="off"
                    >
                    <kbd class="admin-search-kbd">Ctrl+K</kbd>
                </div>
            </div>

            <!-- Topbar Right Controls -->
            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <!-- Quick Create Shortcut Dropdown -->
                <div class="dropdown d-none d-md-block">
                    <button class="btn btn-sm btn-primary rounded-pill px-3 py-1 d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-plus-lg"></i>
                        <span class="fw-semibold">New Item</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="border-radius: 12px; min-width: 200px;">
                        <li><h6 class="dropdown-header text-uppercase small text-muted" style="font-size: 0.7rem;">Quick Create</h6></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.devices.create') }}"><i class="bi bi-phone me-2 text-primary"></i> Add Mobile Device</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.brands.create') }}"><i class="bi bi-award me-2 text-primary"></i> Add Brand</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.firmware.create') }}"><i class="bi bi-hdd-network me-2 text-info"></i> Add Firmware ROM</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.flash-files.create') }}"><i class="bi bi-file-earmark-zip me-2 text-warning"></i> Add Flash File</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.posts.create') }}"><i class="bi bi-file-earmark-text me-2 text-success"></i> Add Article / Guide</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.advertisements.create') }}"><i class="bi bi-badge-ad me-2 text-danger"></i> Add Advertisement</a></li>
                    </ul>
                </div>

                <!-- Live Production Website Pill Link -->
                <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 d-none d-xl-flex align-items-center gap-1 text-muted" style="font-size: 0.8rem;" title="Open live production domain in new tab">
                    <span class="badge bg-success rounded-circle p-1 me-1"></span>
                    <span>editzy.website</span>
                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                </a>

                <!-- Notifications Dropdown (Contact Inquiries & Activity) -->
                <div class="dropdown">
                    <button class="topbar-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                        @if($unreadMessagesCount > 0)
                        <span class="topbar-notification-badge"></span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="border-radius: 14px; width: 320px;">
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-body">Notifications</span>
                            <span class="badge bg-primary-subtle text-primary">{{ $unreadMessagesCount }} Unread</span>
                        </div>
                        <div class="p-2">
                            @if($unreadMessagesCount > 0)
                            <a href="{{ route('admin.messages.index') }}" class="dropdown-item p-2 rounded d-flex gap-2 align-items-start">
                                <div class="badge bg-danger-subtle text-danger p-2 rounded-circle mt-1">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small fw-semibold text-body">New Contact Inquiries</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">You have {{ $unreadMessagesCount }} unread message(s) from visitors.</div>
                                </div>
                            </a>
                            @else
                            <div class="text-center py-3 text-muted small">
                                <i class="bi bi-check-circle text-success fs-5 d-block mb-1"></i>
                                All inquiries and notifications are clear!
                            </div>
                            @endif
                        </div>
                        <div class="p-2 border-top text-center">
                            <a href="{{ route('admin.messages.index') }}" class="small text-primary text-decoration-none fw-semibold">View All Inquiries &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Theme Toggle Button (Light / Dark) -->
                <button class="topbar-icon-btn" id="adminThemeToggleBtn" type="button" title="Toggle Light/Dark Theme" aria-label="Toggle theme">
                    <i class="bi bi-sun-fill" id="adminThemeIcon"></i>
                </button>

                <!-- Current User Profile & Role Dropdown -->
                @auth
                <div class="dropdown">
                    <button class="topbar-profile-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="topbar-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="d-none d-md-block text-start me-1">
                            <div class="fw-semibold text-body" style="font-size: 0.82rem; line-height: 1.1;">{{ Auth::user()->name }}</div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem; padding: 2px 6px;">
                                {{ Auth::user()->roles->first()?->name ?? 'Admin' }}
                            </span>
                        </div>
                        <i class="bi bi-chevron-down text-muted small me-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="border-radius: 12px; min-width: 220px;">
                        <li class="px-2 py-2 border-bottom mb-1">
                            <div class="small fw-bold text-body">{{ Auth::user()->name }}</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }}</div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded" href="{{ route('admin.settings.whatsapp') }}">
                                <i class="bi bi-whatsapp me-2 text-success"></i> WhatsApp Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded" href="{{ route('admin.activity.index') }}">
                                <i class="bi bi-clock-history me-2 text-primary"></i> Activity Log
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 rounded text-danger d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-right"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
        </header>

        <!-- Main Workspace Body -->
        <main class="p-3 p-md-4 flex-grow-1" style="background-color: #F8FAFC;">
            <!-- Flash Notifications -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #ECFDF5; color: #065F46;">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('status'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #EFF6FF; color: #1E40AF;">
                <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #FEF2F2; color: #991B1B;">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #FEF2F2; color: #991B1B;">
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
        <footer class="p-3 text-center border-top text-muted small bg-white">
            &copy; {{ date('Y') }} Mobile Resource Hub Administration &bull; Lumzy SaaS Dashboard Engine v2.0
        </footer>
    </div>

    <!-- Interactive Client Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Immediate scroll to top on navigation
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
            window.scrollTo(0, 0);

            // Sidebar drawer elements
            const sidebar = document.getElementById('adminSidebar');
            const openBtn = document.getElementById('openSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const backdrop = document.getElementById('sidebarBackdrop');
            const desktopToggle = document.getElementById('desktopSidebarToggle');

            // Mobile drawer open/close
            if (openBtn && sidebar && backdrop) {
                openBtn.addEventListener('click', () => {
                    sidebar.classList.add('show');
                    backdrop.classList.add('show');
                });
            }
            const hideMobileDrawer = () => {
                if (sidebar) sidebar.classList.remove('show');
                if (backdrop) backdrop.classList.remove('show');
            };
            if (closeBtn) closeBtn.addEventListener('click', hideMobileDrawer);
            if (backdrop) backdrop.addEventListener('click', hideMobileDrawer);

            // Desktop sidebar collapse toggle
            if (desktopToggle) {
                desktopToggle.addEventListener('click', () => {
                    document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('mrh_sidebar_collapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
                });
                if (localStorage.getItem('mrh_sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            }

            // Keyboard shortcut Ctrl+K / Cmd+K for global search
            const searchInput = document.getElementById('adminGlobalSearch');
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    if (searchInput) searchInput.focus();
                }
            });

            // Theme toggle inside Admin
            const themeBtn = document.getElementById('adminThemeToggleBtn');
            const themeIcon = document.getElementById('adminThemeIcon');
            const applyTheme = (theme) => {
                document.documentElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('mrh_admin_theme', theme);
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.className = 'bi bi-moon-stars-fill text-warning';
                    } else {
                        themeIcon.className = 'bi bi-sun-fill text-muted';
                    }
                }
            };
            const currentTheme = localStorage.getItem('mrh_admin_theme') || 'light';
            applyTheme(currentTheme);

            if (themeBtn) {
                themeBtn.addEventListener('click', () => {
                    const next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                    applyTheme(next);
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
