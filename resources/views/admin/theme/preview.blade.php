<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Live Preview — Mobile Resource Hub</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Compiled Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Live Draft CSS Overrides -->
    <style id="draft-theme-styles">
        {!! $draftCss !!}
    </style>
</head>
<body>
    <!-- Floating Glass Header -->
    <header class="glass-header-wrapper">
        <div class="container-xl">
            <nav class="navbar navbar-expand-lg glass-navbar">
                <a class="navbar-brand navbar-brand-logo" href="#">
                    <div class="brand-icon-box">
                        <i class="bi bi-phone-flip"></i>
                    </div>
                    <span>{{ $draftSettings['branding']['site_name'] ?? 'MobileHub' }}</span>
                </a>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <span class="badge text-bg-warning">Live Theme Preview</span>
                </div>
            </nav>
        </div>
    </header>

    <main class="py-4">
        <div class="container-xl">
            <!-- Hero Card -->
            <div class="mrh-card p-5 mb-4 text-center">
                <h1 class="display-6 fw-bold mb-2">{{ $draftSettings['branding']['tagline'] ?? 'Search Specs, Firmware, & Drivers' }}</h1>
                <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
                    Experience your live theme modifications, glassmorphism transparency, custom color palettes, and typography tokens.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-primary rounded-pill px-4">Primary Button</button>
                    <button class="btn btn-outline-secondary rounded-pill px-4">Secondary Button</button>
                </div>
            </div>

            <!-- Sample Catalog Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="mrh-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge text-bg-primary">Flagship Device</span>
                            <small class="text-muted">6.8" AMOLED</small>
                        </div>
                        <h5 class="fw-bold mb-1">Samsung Galaxy S24 Ultra</h5>
                        <p class="small text-muted mb-3">Model: <code>SM-S928B</code> &bull; Snapdragon 8 Gen 3</p>
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-pill">View Specs &rarr;</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mrh-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge text-bg-success">Stock ROM</span>
                            <small class="text-muted">Android 15</small>
                        </div>
                        <h5 class="fw-bold mb-1">Galaxy S24 Ultra OneUI 7.0</h5>
                        <p class="small text-muted mb-3">Build: S928BXXU3BXK2 &bull; Region: Global</p>
                        <a href="#" class="btn btn-sm btn-outline-success rounded-pill">Download ROM &rarr;</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mrh-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge text-bg-info text-dark">Service Tool</span>
                            <small class="text-muted">Windows</small>
                        </div>
                        <h5 class="fw-bold mb-1">Samsung Odin 3.14.4</h5>
                        <p class="small text-muted mb-3">Official flashing utility with BL/AP/CP/CSC support.</p>
                        <a href="#" class="btn btn-sm btn-outline-info rounded-pill">Get Tool &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Sample Form Controls -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3">Interactive Theme Elements</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Sample text input focus state" value="Search model number...">
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary rounded-pill">Primary</button>
                            <button class="btn btn-secondary rounded-pill">Secondary</button>
                            <button class="btn btn-outline-primary rounded-pill">Outline</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="mrh-footer py-4 mt-5 border-top">
        <div class="container-xl text-center small text-muted">
            &copy; {{ date('Y') }} {{ $draftSettings['branding']['site_name'] ?? 'Mobile Resource Hub' }}. All rights reserved.
        </div>
    </footer>
</body>
</html>
