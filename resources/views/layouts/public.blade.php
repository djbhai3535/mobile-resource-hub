<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>@yield('title', 'Mobile Resource Hub — Phone Specs, Firmware, Drivers & Guides')</title>
    <meta name="description" content="@yield('meta_description', 'Discover verified mobile phone specifications, firmware releases, official flash files, USB drivers, and in-depth repair tutorials.')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    @hasSection('robots')
    <meta name="robots" content="@yield('robots')">
    @endif
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'Mobile Resource Hub')">
    <meta property="og:description" content="@yield('meta_description', 'Verified mobile specs, firmware, drivers, and technical guides.')">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Mobile Resource Hub')">
    <meta name="twitter:description" content="@yield('meta_description', 'Verified mobile specs, firmware, drivers, and technical guides.')">

    <!-- Preload & Anti-FOUT Theme Script -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('mrh-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme ? savedTheme : (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <!-- Schema.org WebSite & SearchAction JSON-LD -->
    <script type="application/ld+json">
    {
        "{{ '@' }}context": "https://schema.org",
        "@type": "WebSite",
        "name": "Mobile Resource Hub",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ url('/search') }}?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Compiled Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    @stack('scripts_head')

    <!-- Dynamic Theme Customizer Styles -->
    <style id="mrh-dynamic-theme-css">
        {!! app('App\Services\ThemeService')->getCachedThemeCss() !!}
    </style>
</head>
<body>

    <!-- Sticky Floating Glass Header -->
    <header class="glass-header-wrapper">
        <div class="container-xl">
            <nav class="navbar navbar-expand-lg glass-navbar" aria-label="Main Navigation">
                <a class="navbar-brand navbar-brand-logo" href="{{ url('/') }}">
                    <div class="brand-icon-box">
                        <i class="bi bi-phone-flip"></i>
                    </div>
                    <span>Mobile<span class="text-primary">Hub</span></span>
                </a>

                <!-- Mobile Header Right Actions (Search & Theme Toggle) -->
                <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-2">
                    <a href="{{ url('/search') }}" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;" aria-label="Search">
                        <i class="bi bi-search"></i>
                    </a>
                    <button type="button" class="theme-toggle-btn" id="theme-toggle-btn-mobile" onclick="document.getElementById('theme-toggle-btn').click();" aria-label="Toggle theme">
                        <i class="bi bi-circle-half"></i>
                    </button>
                </div>

                <!-- Hamburger Toggler for Mobile Offcanvas -->
                <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavOffcanvas" aria-controls="mobileNavOffcanvas" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Desktop Navigation Menu -->
                <div class="collapse navbar-collapse d-none d-lg-flex" id="desktopNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('mobile-phones*') ? 'active' : '' }}" href="{{ url('/mobile-phones') }}">Phones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('brands*') ? 'active' : '' }}" href="{{ url('/brands') }}">Brands</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('firmware*') ? 'active' : '' }}" href="{{ url('/firmware') }}">Firmware</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('flash-files*') ? 'active' : '' }}" href="{{ url('/flash-files') }}">Flash Files</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('software*') ? 'active' : '' }}" href="{{ url('/software') }}">Software</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('drivers*') ? 'active' : '' }}" href="{{ url('/drivers') }}">Drivers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('news*') ? 'active' : '' }}" href="{{ url('/news') }}">News</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('tutorials*') ? 'active' : '' }}" href="{{ url('/tutorials') }}">Tutorials</a>
                        </li>
                    </ul>

                    <!-- Header Quick Search Bar -->
                    <div class="search-wrapper position-relative me-3" style="width: 260px;">
                        <form action="{{ url('/search') }}" method="GET">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="pointer-events: none;"></i>
                            <input type="search" name="q" class="form-control form-control-sm search-input-header mrh-search-input" placeholder="Search devices, ROMs, drivers..." value="{{ request('q') }}" autocomplete="off" aria-label="Global search">
                        </form>
                        <div class="search-suggestions-panel"></div>
                    </div>

                    <!-- Theme Switcher Button -->
                    <button type="button" class="theme-toggle-btn" id="theme-toggle-btn" title="Toggle color theme" aria-label="Toggle theme">
                        <i class="bi bi-moon-stars-fill text-primary" id="theme-toggle-icon"></i>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Mobile Navigation Offcanvas Drawer -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNavOffcanvas" aria-labelledby="mobileNavLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title d-flex align-items-center gap-2" id="mobileNavLabel">
                <div class="brand-icon-box" style="width: 32px; height: 32px; font-size: 1rem;">
                    <i class="bi bi-phone-flip"></i>
                </div>
                <span>Mobile<span class="text-primary">Hub</span></span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 d-flex flex-column justify-content-between">
            <div>
                <!-- Mobile Search -->
                <form action="{{ url('/search') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text bg-body border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" name="q" class="form-control border-start-0" placeholder="Search phone, firmware, driver..." value="{{ request('q') }}">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>

                <h6 class="text-uppercase text-muted fw-bold small mb-3">Explore Catalog</h6>
                <ul class="nav flex-column gap-1 mb-4">
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded {{ request()->is('mobile-phones*') ? 'active bg-primary-subtle text-primary' : '' }}" href="{{ url('/mobile-phones') }}">
                            <i class="bi bi-phone me-2"></i> Mobile Phones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded {{ request()->is('brands*') ? 'active bg-primary-subtle text-primary' : '' }}" href="{{ url('/brands') }}">
                            <i class="bi bi-tags me-2"></i> Brands Directory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded {{ request()->is('firmware*') ? 'active bg-primary-subtle text-primary' : '' }}" href="{{ url('/firmware') }}">
                            <i class="bi bi-cpu me-2"></i> Firmware & ROMs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded {{ request()->is('flash-files*') ? 'active bg-primary-subtle text-primary' : '' }}" href="{{ url('/flash-files') }}">
                            <i class="bi bi-file-earmark-zip me-2"></i> Flash Files
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded {{ request()->is('software*') ? 'active bg-primary-subtle text-primary' : '' }}" href="{{ url('/software') }}">
                            <i class="bi bi-tools me-2"></i> Software & Tools
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded {{ request()->is('drivers*') ? 'active bg-primary-subtle text-primary' : '' }}" href="{{ url('/drivers') }}">
                            <i class="bi bi-usb-symbol me-2"></i> USB Drivers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded {{ request()->is('news*') ? 'active bg-primary-subtle text-primary' : '' }}" href="{{ url('/news') }}">
                            <i class="bi bi-newspaper me-2"></i> Tech News
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded {{ request()->is('tutorials*') ? 'active bg-primary-subtle text-primary' : '' }}" href="{{ url('/tutorials') }}">
                            <i class="bi bi-journal-code me-2"></i> Repair Tutorials
                        </a>
                    </li>
                </ul>
            </div>

            <div class="border-top pt-3 text-muted small">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Dark / Light Mode</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('theme-toggle-btn').click();">
                        Switch Theme
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <main>
        <div class="container-xl">
            @ad('header_below_nav')
        </div>
        @yield('content')
        <div class="container-xl">
            @ad('global_footer')
        </div>
    </main>

    <!-- Global Responsive Footer -->
    <footer class="mrh-footer">
        <div class="container-xl">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="brand-icon-box" style="width: 32px; height: 32px; font-size: 1rem;">
                            <i class="bi bi-phone-flip"></i>
                        </div>
                        <span class="fs-5 fw-bold">Mobile<span class="text-primary">Hub</span></span>
                    </div>
                    <p class="text-muted small pe-lg-4">
                        Mobile Resource Hub is a comprehensive database of verified mobile phone technical specifications, official firmware releases, flash files, USB drivers, and step-by-step repair guides.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="https://github.com" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;" aria-label="GitHub">
                            <i class="bi bi-github"></i>
                        </a>
                        <a href="{{ url('/contact-us') }}" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;" aria-label="Contact">
                            <i class="bi bi-envelope"></i>
                        </a>
                    </div>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="fw-bold mb-3">Device Catalog</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ url('/mobile-phones') }}" class="footer-link">All Mobile Phones</a></li>
                        <li><a href="{{ url('/brands') }}" class="footer-link">Brand Directory</a></li>
                        <li><a href="{{ url('/brands/samsung') }}" class="footer-link">Samsung Devices</a></li>
                        <li><a href="{{ url('/brands/apple') }}" class="footer-link">Apple iPhones</a></li>
                        <li><a href="{{ url('/brands/xiaomi') }}" class="footer-link">Xiaomi Phones</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="fw-bold mb-3">Resources</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ url('/firmware') }}" class="footer-link">Firmware Releases</a></li>
                        <li><a href="{{ url('/flash-files') }}" class="footer-link">Flash File Hub</a></li>
                        <li><a href="{{ url('/software') }}" class="footer-link">Flashtools & Utilities</a></li>
                        <li><a href="{{ url('/drivers') }}" class="footer-link">USB Drivers</a></li>
                        <li><a href="{{ url('/tutorials') }}" class="footer-link">Flashing Tutorials</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="fw-bold mb-3">Editorial</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ url('/news') }}" class="footer-link">Mobile Tech News</a></li>
                        <li><a href="{{ url('/tutorials') }}" class="footer-link">Guides & Walkthroughs</a></li>
                        <li><a href="{{ url('/search') }}" class="footer-link">Search Catalog</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="fw-bold mb-3">Company & Legal</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ url('/about-us') }}" class="footer-link">About Us</a></li>
                        <li><a href="{{ url('/contact-us') }}" class="footer-link">Contact Us</a></li>
                        <li><a href="{{ url('/privacy-policy') }}" class="footer-link">Privacy Policy</a></li>
                        <li><a href="{{ url('/terms-and-conditions') }}" class="footer-link">Terms of Service</a></li>
                        <li><a href="{{ url('/dmca') }}" class="footer-link">DMCA & Copyright</a></li>
                    </ul>
                </div>
            </div>

            <hr class="border-secondary opacity-25 my-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 small text-muted">
                <div>
                    &copy; {{ date('Y') }} Mobile Resource Hub. All rights reserved.
                </div>
                <div>
                    Disclaimer: All product names, logos, brands, and trademarks are property of their respective owners.
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
