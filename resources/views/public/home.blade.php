@extends('layouts.public')

@section('title', 'Mobile Resource Hub — Phone Specs, Firmware, Drivers & Guides')
@section('meta_description', 'Search and discover verified mobile phone specifications, official firmware downloads, flash files, USB drivers, and step-by-step repair guides.')

@section('content')
<!-- Hero Section with Prominent Global Search (PUB-001, PUB-002) -->
<section class="py-5 py-lg-6 position-relative overflow-hidden">
    <div class="container-xl text-center">
        <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-3 fw-semibold">
            <i class="bi bi-shield-check me-1"></i> Verified Mobile Technology & Resource Portal
        </div>
        
        <h1 class="display-4 fw-extrabold mb-3">
            Search Specs, Firmware, & <span class="text-primary">Flash Resources</span>
        </h1>
        
        <p class="lead text-muted mx-auto mb-4" style="max-width: 650px;">
            The ultimate open database for smartphones, verified technical specifications, firmware builds, USB drivers, and certified flashing tutorials.
        </p>

        <!-- Prominent Hero Search Bar (PRD Section 6.3) -->
        <div class="search-wrapper search-wrapper-hero mb-4">
            <form action="{{ url('/search') }}" method="GET">
                <i class="bi bi-search search-prefix-icon"></i>
                <input 
                    type="search" 
                    name="q" 
                    class="search-input-glass mrh-search-input" 
                    placeholder="Search model (e.g. SM-S928B), brand, chipset, or firmware..." 
                    autocomplete="off" 
                    required 
                    aria-label="Search mobile resources"
                >
                <button type="submit" class="btn btn-primary search-action-btn">
                    <span>Search</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>
            <div class="search-suggestions-panel text-start"></div>
        </div>

        <!-- Quick Trending Pills -->
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 small">
            <span class="text-muted fw-semibold">Popular searches:</span>
            <a href="{{ url('/search?q=Galaxy+S24+Ultra') }}" class="badge text-bg-light border text-decoration-none px-3 py-2 rounded-pill">Galaxy S24 Ultra</a>
            <a href="{{ url('/search?q=SM-S928B') }}" class="badge text-bg-light border text-decoration-none px-3 py-2 rounded-pill">SM-S928B</a>
            <a href="{{ url('/search?q=Odin+3.14') }}" class="badge text-bg-light border text-decoration-none px-3 py-2 rounded-pill">Odin 3.14</a>
            <a href="{{ url('/search?q=Qualcomm+Driver') }}" class="badge text-bg-light border text-decoration-none px-3 py-2 rounded-pill">Qualcomm Driver</a>
            <a href="{{ url('/search?q=HyperOS+2.0') }}" class="badge text-bg-light border text-decoration-none px-3 py-2 rounded-pill">HyperOS 2.0</a>
        </div>
    </div>
</section>

<!-- Configurable Advertisement Placeholder (PUB-008) -->
<div class="container-xl my-2">
    <div class="p-3 text-center border rounded-3 text-muted small bg-body-tertiary">
        <i class="bi bi-megaphone me-1"></i> Advertisement Slot (Responsive Banner — Configurable from Admin)
    </div>
</div>

<!-- Popular Brands Section (PUB-003) -->
<section class="py-5">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="h3 fw-bold mb-1">Browse by Brand</h2>
                <p class="text-muted mb-0 small">Explore official catalogs across leading smartphone manufacturers</p>
            </div>
            <a href="{{ url('/brands') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                View All Brands &rarr;
            </a>
        </div>

        <div class="row g-3">
            @foreach($popularBrands as $brand)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ url('/brands/' . $brand['slug']) }}" class="mrh-card p-3 text-center text-decoration-none">
                    <div class="brand-icon-box mx-auto mb-2" style="width: 44px; height: 44px;">
                        <i class="bi {{ $brand['icon'] }}"></i>
                    </div>
                    <h6 class="fw-bold mb-1 text-body">{{ $brand['name'] }}</h6>
                    <span class="small text-muted">{{ $brand['count'] }} Devices</span>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Latest Mobile Devices Section (PUB-004) -->
<section class="py-5 bg-body-tertiary">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary fw-bold text-uppercase small">Hardware Directory</span>
                <h2 class="h3 fw-bold mb-1">Latest Mobile Devices</h2>
                <p class="text-muted mb-0 small">Verified hardware specifications, model numbers, and technical benchmarks</p>
            </div>
            <a href="{{ url('/mobile-phones') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                Browse All Phones &rarr;
            </a>
        </div>

        <div class="row g-4">
            @foreach($latestDevices as $device)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="mrh-card">
                    <div class="p-4 bg-body text-center border-bottom position-relative">
                        <span class="mrh-badge mrh-badge-success position-absolute top-0 start-0 m-3">
                            <i class="bi bi-patch-check-fill"></i> Verified
                        </span>
                        <div class="brand-icon-box mx-auto mb-2" style="width: 64px; height: 64px; font-size: 2rem;">
                            <i class="bi bi-phone"></i>
                        </div>
                        <span class="small text-muted text-uppercase fw-semibold">{{ $device['brand'] }}</span>
                        <h5 class="fw-bold mb-1 mt-1 text-truncate">{{ $device['name'] }}</h5>
                        <code class="small text-primary">{{ $device['model'] }}</code>
                    </div>
                    <div class="p-3 small flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                            <i class="bi bi-display text-primary"></i>
                            <span class="text-truncate">{{ $device['screen'] }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                            <i class="bi bi-cpu text-primary"></i>
                            <span class="text-truncate">{{ $device['chipset'] }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                            <i class="bi bi-camera text-primary"></i>
                            <span class="text-truncate">{{ $device['camera'] }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="bi bi-battery-charging text-primary"></i>
                            <span>{{ $device['battery'] }}</span>
                        </div>
                    </div>
                    <div class="p-3 pt-0">
                        <a href="{{ url('/mobile-phones/' . $device['slug']) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-semibold">
                            Full Specifications &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Recently Published Firmware & Flash Resources (PUB-005) -->
<section class="py-5">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-warning fw-bold text-uppercase small">ROMs & Binaries</span>
                <h2 class="h3 fw-bold mb-1">Recent Firmware & Flash Files</h2>
                <p class="text-muted mb-0 small">Official manufacturer updates, service firmware, and CSC packages</p>
            </div>
            <a href="{{ url('/firmware') }}" class="btn btn-sm btn-outline-warning text-body rounded-pill px-3">
                View Firmware Hub &rarr;
            </a>
        </div>

        <div class="row g-3">
            @foreach($recentFirmware as $fw)
            <div class="col-12 col-md-4">
                <div class="mrh-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="mrh-badge mrh-badge-warning"><i class="bi bi-cpu"></i> Firmware</span>
                        <span class="small text-muted">{{ $fw['date'] }}</span>
                    </div>
                    <h6 class="fw-bold mb-2">
                        <a href="{{ url('/firmware/' . $fw['slug']) }}" class="text-body text-decoration-none">
                            {{ $fw['title'] }}
                        </a>
                    </h6>
                    <div class="small text-muted mb-3">
                        <div><strong>Model:</strong> <code>{{ $fw['model_code'] }}</code></div>
                        <div><strong>Region / CSC:</strong> {{ $fw['region'] }}</div>
                        <div><strong>Build:</strong> <code>{{ $fw['build'] }}</code></div>
                        <div><strong>Archive Size:</strong> {{ $fw['size'] }}</div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ url('/firmware/' . $fw['slug']) }}" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">
                            <i class="bi bi-download me-1"></i> Download Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Popular Software, Tools & Drivers (PUB-006) -->
<section class="py-5 bg-body-tertiary">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-12 col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h4 fw-bold mb-0">Essential Flash Tools</h3>
                    <a href="{{ url('/software') }}" class="small fw-bold">All Software &rarr;</a>
                </div>
                <div class="mrh-card p-3 mb-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">Samsung Odin 3.14.4</h6>
                            <span class="small text-muted">Official flashing tool for Galaxy devices (AP, BL, CP, CSC)</span>
                        </div>
                        <a href="{{ url('/software/odin-3-14') }}" class="btn btn-sm btn-primary rounded-pill">Get Tool</a>
                    </div>
                </div>
                <div class="mrh-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">SP Flash Tool (Smart Phone Flash Tool)</h6>
                            <span class="small text-muted">MediaTek MTK chipset firmware flashing & unbricking utility</span>
                        </div>
                        <a href="{{ url('/software/sp-flash-tool') }}" class="btn btn-sm btn-primary rounded-pill">Get Tool</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h4 fw-bold mb-0">Official USB Drivers</h3>
                    <a href="{{ url('/drivers') }}" class="small fw-bold">All Drivers &rarr;</a>
                </div>
                <div class="mrh-card p-3 mb-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">Qualcomm HS-USB QDLoader 9008 Driver</h6>
                            <span class="small text-muted">Emergency Download (EDL) mode Windows x64/x86 driver</span>
                        </div>
                        <a href="{{ url('/drivers/qualcomm-usb-driver') }}" class="btn btn-sm btn-outline-primary rounded-pill">Download</a>
                    </div>
                </div>
                <div class="mrh-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">Samsung Android USB Driver v1.7.59</h6>
                            <span class="small text-muted">Windows ADB & Fastboot connectivity for all Samsung phones</span>
                        </div>
                        <a href="{{ url('/drivers/samsung-usb-driver') }}" class="btn btn-sm btn-outline-primary rounded-pill">Download</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest News and Repair Tutorials (PUB-007) -->
<section class="py-5">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-success fw-bold text-uppercase small">Knowledge Base</span>
                <h2 class="h3 fw-bold mb-1">Guides, Fixes & Tech News</h2>
                <p class="text-muted mb-0 small">Curated step-by-step guides for flashing, driver troubleshooting, and mobile repairs</p>
            </div>
            <a href="{{ url('/tutorials') }}" class="btn btn-sm btn-outline-success text-body rounded-pill px-3">
                Browse Guides &rarr;
            </a>
        </div>

        <div class="row g-4">
            @foreach($latestGuides as $guide)
            <div class="col-12 col-md-6">
                <div class="mrh-card p-4">
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <span class="mrh-badge mrh-badge-success">{{ $guide['category'] }}</span>
                        <span class="small text-muted">&bull; {{ $guide['date'] }}</span>
                        <span class="small text-muted">&bull; {{ $guide['read_time'] }}</span>
                    </div>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ url('/tutorials/' . $guide['slug']) }}" class="text-body text-decoration-none">
                            {{ $guide['title'] }}
                        </a>
                    </h5>
                    <p class="text-muted small mb-3">
                        Comprehensive step-by-step tutorial with checksum verifications, backup prerequisites, and troubleshooting for common error codes.
                    </p>
                    <a href="{{ url('/tutorials/' . $guide['slug']) }}" class="text-primary fw-semibold small text-decoration-none">
                        Read Walkthrough &rarr;
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
