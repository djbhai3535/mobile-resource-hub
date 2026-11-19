@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<!-- Page Header & Filter Controls (Lumzy Reference Design) -->
<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold text-body mb-1">Analytics &amp; Overview</h1>
        <p class="text-muted small mb-0">Platform resource metrics, recent catalog additions, and operational controls.</p>
    </div>

    <!-- Header Action Controls (Matching Lumzy Reference) -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- Date Range Filter Pill -->
        <div class="dropdown">
            <button class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 text-muted fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.82rem; background: #fff;">
                <i class="bi bi-calendar3 text-primary"></i>
                <span class="text-body">Last 30 Days</span>
                <i class="bi bi-chevron-down small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="border-radius: 12px; font-size: 0.85rem;">
                <li><a class="dropdown-item active" href="#">Last 30 Days</a></li>
                <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                <li><a class="dropdown-item" href="#">Last 90 Days</a></li>
                <li><a class="dropdown-item" href="#">All Time</a></li>
            </ul>
        </div>

        <!-- Filter Action Button -->
        <button class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 text-muted fw-semibold" type="button" style="font-size: 0.82rem; background: #fff;" onclick="window.location.reload();" title="Refresh Live Data">
            <i class="bi bi-funnel"></i>
            <span class="text-body">Filter</span>
        </button>

        <!-- Quick Create Item Button -->
        <div class="dropdown">
            <button class="btn btn-sm btn-primary rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.82rem;">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Quick Action</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="border-radius: 12px; font-size: 0.85rem; min-width: 210px;">
                <li><h6 class="dropdown-header text-uppercase small text-muted" style="font-size: 0.7rem;">Catalog Additions</h6></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.devices.create') }}"><i class="bi bi-phone me-2 text-primary"></i> Add New Mobile</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.brands.create') }}"><i class="bi bi-award me-2 text-primary"></i> Add New Brand</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.firmware.create') }}"><i class="bi bi-hdd-network me-2 text-info"></i> Add New Firmware</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.flash-files.create') }}"><i class="bi bi-file-earmark-zip me-2 text-warning"></i> Add New Flash File</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.posts.create') }}"><i class="bi bi-file-earmark-text me-2 text-success"></i> Add New Article</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.advertisements.create') }}"><i class="bi bi-badge-ad me-2 text-danger"></i> Add New Advertisement</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Primary KPI Cards Row (Matching Lumzy Reference Design) -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Card 1: Mobile Devices -->
    <div class="col-sm-6 col-xl-3">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small" style="font-size: 0.82rem;">Total Mobile Devices</span>
                <div class="lumzy-kpi-icon-box" style="background: rgba(37, 99, 235, 0.1); color: #2563EB;">
                    <i class="bi bi-phone"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-2">
                <h2 class="h2 fw-extrabold mb-0 text-body">{{ number_format($metrics['total_devices']) }}</h2>
            </div>
            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <span class="lumzy-kpi-pill-success">
                    <i class="bi bi-check-circle me-1"></i>{{ $metrics['published_devices'] }} Live
                </span>
                <a href="{{ route('admin.devices.index') }}" class="small text-muted text-decoration-none">Catalog &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Card 2: Firmware Releases -->
    <div class="col-sm-6 col-xl-3">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small" style="font-size: 0.82rem;">Firmware / Stock ROMs</span>
                <div class="lumzy-kpi-icon-box" style="background: rgba(14, 165, 233, 0.1); color: #0EA5E9;">
                    <i class="bi bi-hdd-network"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-2">
                <h2 class="h2 fw-extrabold mb-0 text-body">{{ number_format($metrics['total_firmware']) }}</h2>
            </div>
            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <span class="lumzy-kpi-pill-primary">
                    <i class="bi bi-shield-check me-1"></i>{{ $metrics['published_firmware'] }} Published
                </span>
                <a href="{{ route('admin.firmware.index') }}" class="small text-muted text-decoration-none">Manage &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Card 3: Flash Files -->
    <div class="col-sm-6 col-xl-3">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small" style="font-size: 0.82rem;">Tested Flash Files</span>
                <div class="lumzy-kpi-icon-box" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                    <i class="bi bi-file-earmark-zip"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-2">
                <h2 class="h2 fw-extrabold mb-0 text-body">{{ number_format($metrics['total_flash_files']) }}</h2>
            </div>
            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <span class="lumzy-kpi-pill-warning">
                    <i class="bi bi-cpu me-1"></i>{{ $metrics['published_flash_files'] }} Verified
                </span>
                <a href="{{ route('admin.flash-files.index') }}" class="small text-muted text-decoration-none">Files &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Card 4: Software & USB Drivers -->
    <div class="col-sm-6 col-xl-3">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small" style="font-size: 0.82rem;">Software &amp; Drivers</span>
                <div class="lumzy-kpi-icon-box" style="background: rgba(124, 58, 237, 0.1); color: #7C3AED;">
                    <i class="bi bi-usb-symbol"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-2">
                <h2 class="h2 fw-extrabold mb-0 text-body">{{ number_format($metrics['total_software'] + $metrics['total_drivers']) }}</h2>
            </div>
            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <span class="lumzy-kpi-pill-primary" style="background: #F5F3FF; color: #7C3AED; border-color: #DDD6FE;">
                    {{ $metrics['total_drivers'] }} Drivers &bull; {{ $metrics['total_software'] }} Tools
                </span>
                <a href="{{ route('admin.drivers.index') }}" class="small text-muted text-decoration-none">Utilities &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Secondary KPI Cards Row -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Card 5: Mobile Brands -->
    <div class="col-sm-6 col-xl-3">
        <div class="lumzy-kpi-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-medium">Active Manufacturers</span>
                <i class="bi bi-award text-primary fs-5"></i>
            </div>
            <div class="h4 fw-bold mb-1 text-body">{{ number_format($metrics['total_brands']) }} Brands</div>
            <a href="{{ route('admin.brands.index') }}" class="small text-primary text-decoration-none">Manage Brands &rarr;</a>
        </div>
    </div>

    <!-- Card 6: Articles & News -->
    <div class="col-sm-6 col-xl-3">
        <div class="lumzy-kpi-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-medium">Published Articles</span>
                <i class="bi bi-newspaper text-info fs-5"></i>
            </div>
            <div class="h4 fw-bold mb-1 text-body">{{ number_format($metrics['published_posts']) }} Posts</div>
            <a href="{{ route('admin.posts.index') }}" class="small text-primary text-decoration-none">Editorial CMS &rarr;</a>
        </div>
    </div>

    <!-- Card 7: Contact Messages -->
    <div class="col-sm-6 col-xl-3">
        <div class="lumzy-kpi-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-medium">Visitor Inquiries</span>
                <i class="bi bi-envelope text-danger fs-5"></i>
            </div>
            <div class="h4 fw-bold mb-1 text-body">
                {{ number_format($metrics['total_messages']) }} Total
                @if($metrics['unread_messages'] > 0)
                <span class="badge bg-danger rounded-pill fs-6 ms-1">{{ $metrics['unread_messages'] }} New</span>
                @endif
            </div>
            <a href="{{ route('admin.messages.index') }}" class="small text-primary text-decoration-none">Open Inbox &rarr;</a>
        </div>
    </div>

    <!-- Card 8: Advertisements Active -->
    <div class="col-sm-6 col-xl-3">
        <div class="lumzy-kpi-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-medium">Active Campaigns</span>
                <i class="bi bi-badge-ad text-success fs-5"></i>
            </div>
            <div class="h4 fw-bold mb-1 text-body">{{ number_format($metrics['active_ads'] ?? 0) }} Campaigns</div>
            <a href="{{ route('admin.advertisements.index') }}" class="small text-primary text-decoration-none">Ad Placements &rarr;</a>
        </div>
    </div>
</div>

<!-- Main 2-Column Dashboard Layout (Matching Lumzy Reference Screenshot) -->
<div class="row g-4 mb-4">
    <!-- LEFT COLUMN: Main Chart & Top Content Table (approx 66% width) -->
    <div class="col-lg-8">
        <!-- Website Traffic & Platform Activity Card -->
        <div class="lumzy-card p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                <div>
                    <h5 class="fw-bold mb-1 text-body">Website Traffic &amp; Live Analytics</h5>
                    <p class="text-muted small mb-0">Real visitor metrics, page views, and engagement growth.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small">
                        <span class="spinner-grow spinner-grow-sm me-1 text-success" role="status" style="width: 8px; height: 8px;"></span>
                        {{ $liveStats['live_visitors'] ?? 1 }} Live Online
                    </span>
                    <a href="{{ route('admin.analytics.visitors') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                        Detailed Insights &rarr;
                    </a>
                </div>
            </div>

            <!-- Visual Bar Chart with Real Analytics -->
            <div class="p-3 rounded-3 mb-3" style="background: #F8FAFC; border: 1px solid #EAECF0;">
                @if(isset($visitorAnalytics['daily_trend']) && count($visitorAnalytics['daily_trend']) > 0)
                    @php
                        $trendSlice = array_slice($visitorAnalytics['daily_trend'], -14);
                        $maxVal = max(array_merge(
                            array_column($trendSlice, 'views'),
                            array_column($trendSlice, 'visitors'),
                            [5]
                        ));
                    @endphp
                    <div class="d-flex justify-content-between align-items-end" style="height: 160px; padding: 0 0.5rem; border-bottom: 2px solid #E2E8F0;">
                        @foreach($trendSlice as $tDay)
                            @php
                                $vH = ($tDay['views'] / $maxVal) * 140;
                                $uH = ($tDay['visitors'] / $maxVal) * 140;
                            @endphp
                            <div class="d-flex flex-column align-items-center flex-grow-1" style="min-width: 20px;" title="{{ $tDay['date'] }}: {{ $tDay['views'] }} views, {{ $tDay['visitors'] }} visitors">
                                <div class="d-flex align-items-end gap-1 justify-content-center" style="height: 140px;">
                                    <div class="rounded-top" style="width: 8px; height: {{ max($vH, 4) }}px; background: #2563EB;"></div>
                                    <div class="rounded-top" style="width: 8px; height: {{ max($uH, 4) }}px; background: #10B981;"></div>
                                </div>
                                <span class="text-muted mt-2 text-center" style="font-size: 0.65rem;">{{ $tDay['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted small">No traffic recorded yet.</div>
                @endif
                <div class="d-flex justify-content-center align-items-center gap-4 mt-3 small text-muted">
                    <div class="d-flex align-items-center gap-1">
                        <span style="width: 10px; height: 10px; background: #2563EB; border-radius: 2px;"></span>
                        <span>Page Views</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span style="width: 10px; height: 10px; background: #10B981; border-radius: 2px;"></span>
                        <span>Unique Visitors</span>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 small text-muted pt-2 border-top">
                <span>Today: <strong>{{ number_format($liveStats['today_visitors'] ?? 1) }}</strong> Visitors &bull; Month: <strong>{{ number_format($liveStats['monthly_visitors'] ?? 1) }}</strong> Unique &bull; Total: <strong>{{ number_format($liveStats['total_visitors'] ?? 1) }}</strong></span>
                <a href="{{ route('admin.analytics.visitors') }}" class="text-primary fw-medium text-decoration-none">
                    <i class="bi bi-graph-up me-1"></i> Open Visitor Analytics
                </a>
            </div>
        </div>

        <!-- Top Pages & Popular Catalog Table (Matching Lumzy "Top Pages") -->
        <div class="lumzy-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-body">Top Catalog Items &amp; Resources</h5>
                    <p class="text-muted small mb-0">Latest smartphones and technical downloads active in the public catalog.</p>
                </div>
                <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.8rem;">
                    View All &rarr;
                </a>
            </div>

            @if($recentDevices->isEmpty() && $recentFirmwares->isEmpty())
                <div class="text-center py-4 text-muted small">No items recorded yet. Use the Quick Action button above to add devices.</div>
            @else
                <div class="table-responsive">
                    <table class="table lumzy-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" class="form-check-input" aria-label="Select all"></th>
                                <th>Resource / Item</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date Added</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDevices as $device)
                            <tr>
                                <td><input type="checkbox" class="form-check-input" aria-label="Select device"></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="brand-badge-lumzy" style="width: 32px; height: 32px; font-size: 0.95rem; border-radius: 8px;">
                                            <i class="bi bi-phone"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-body">{{ $device->name }}</div>
                                            <small class="text-muted">{{ $device->brand->name ?? 'Brand' }} &bull; {{ $device->os ?? 'Android' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">Mobile Device</span></td>
                                <td>
                                    @if($device->status === 'published')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Live</span>
                                    @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Draft</span>
                                    @endif
                                </td>
                                <td><span class="text-muted small">{{ $device->created_at ? $device->created_at->format('M d, Y') : 'Recent' }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1" title="Edit">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach

                            @foreach($recentFirmwares as $fw)
                            <tr>
                                <td><input type="checkbox" class="form-check-input" aria-label="Select firmware"></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="brand-badge-lumzy" style="width: 32px; height: 32px; font-size: 0.95rem; border-radius: 8px; background: linear-gradient(135deg, #0EA5E9 0%, #2563EB 100%);">
                                            <i class="bi bi-hdd-network"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-body text-truncate" style="max-width: 200px;">{{ $fw->name }}</div>
                                            <small class="text-muted">{{ $fw->brand->name ?? 'Manufacturer' }} &bull; {{ $fw->version ?? 'Stock ROM' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-info-subtle text-info border border-info-subtle">Firmware</span></td>
                                <td>
                                    @if($fw->status === 'published')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Live</span>
                                    @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Pending</span>
                                    @endif
                                </td>
                                <td><span class="text-muted small">{{ $fw->created_at ? $fw->created_at->format('M d, Y') : 'Recent' }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.firmware.edit', $fw) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1" title="Edit">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- RIGHT COLUMN: Distribution Donut + Actions + Activity (approx 34% width) -->
    <div class="col-lg-4">
        <!-- Resource Catalog Distribution (Matching Lumzy "Visibility share" Donut Chart) -->
        <div class="lumzy-card p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0 text-body">Catalog Distribution</h5>
                <span class="badge bg-light text-muted border">Live Proportions</span>
            </div>
            
            @php
                $totalResources = max(1, $metrics['total_devices'] + $metrics['total_firmware'] + $metrics['total_flash_files'] + $metrics['total_software'] + $metrics['total_drivers']);
                $pctDevices = round(($metrics['total_devices'] / $totalResources) * 100);
                $pctFirmware = round(($metrics['total_firmware'] / $totalResources) * 100);
                $pctFlash = round(($metrics['total_flash_files'] / $totalResources) * 100);
                $pctTools = round((($metrics['total_software'] + $metrics['total_drivers']) / $totalResources) * 100);
            @endphp

            <!-- SVG Donut Representation (Lightweight & 100% Reliable) -->
            <div class="text-center py-2">
                <svg width="150" height="150" viewBox="0 0 42 42" class="donut mb-2">
                    <circle class="donut-ring" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#F1F5F9" stroke-width="6"></circle>
                    <circle class="donut-segment" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#2563EB" stroke-width="6" stroke-dasharray="{{ $pctDevices }} {{ 100 - $pctDevices }}" stroke-dashoffset="25"></circle>
                    <circle class="donut-segment" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#0EA5E9" stroke-width="6" stroke-dasharray="{{ $pctFirmware }} {{ 100 - $pctFirmware }}" stroke-dashoffset="{{ 125 - $pctDevices }}"></circle>
                    <circle class="donut-segment" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#F59E0B" stroke-width="6" stroke-dasharray="{{ $pctFlash }} {{ 100 - $pctFlash }}" stroke-dashoffset="{{ 125 - $pctDevices - $pctFirmware }}"></circle>
                    <g class="donut-text">
                        <text x="50%" y="48%" class="fw-bold" text-anchor="middle" font-size="0.45em" fill="#0F172A" font-weight="700">{{ $totalResources }}</text>
                        <text x="50%" y="62%" class="text-muted" text-anchor="middle" font-size="0.25em" fill="#64748B">Resources</text>
                    </g>
                </svg>
            </div>

            <!-- Legend with Real Database Proportions -->
            <div class="row g-2 pt-2 border-top small">
                <div class="col-6 d-flex align-items-center justify-content-between">
                    <span class="d-flex align-items-center gap-1 text-muted"><span style="width: 8px; height: 8px; background: #2563EB; border-radius: 50%;"></span> Mobile Phones</span>
                    <span class="fw-bold text-body">{{ $pctDevices }}%</span>
                </div>
                <div class="col-6 d-flex align-items-center justify-content-between">
                    <span class="d-flex align-items-center gap-1 text-muted"><span style="width: 8px; height: 8px; background: #0EA5E9; border-radius: 50%;"></span> Firmware ROMs</span>
                    <span class="fw-bold text-body">{{ $pctFirmware }}%</span>
                </div>
                <div class="col-6 d-flex align-items-center justify-content-between">
                    <span class="d-flex align-items-center gap-1 text-muted"><span style="width: 8px; height: 8px; background: #F59E0B; border-radius: 50%;"></span> Flash Files</span>
                    <span class="fw-bold text-body">{{ $pctFlash }}%</span>
                </div>
                <div class="col-6 d-flex align-items-center justify-content-between">
                    <span class="d-flex align-items-center gap-1 text-muted"><span style="width: 8px; height: 8px; background: #10B981; border-radius: 50%;"></span> Drivers &amp; Tools</span>
                    <span class="fw-bold text-body">{{ $pctTools }}%</span>
                </div>
            </div>
        </div>

        <!-- Quick Administrative Shortcuts Card (Matching Lumzy "User Engagement") -->
        <div class="lumzy-card p-4 mb-4">
            <h5 class="fw-bold mb-3 text-body">Quick Administrative Actions</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.devices.create') }}" class="btn btn-sm btn-light border text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                    <span class="d-flex align-items-center gap-2 text-body"><i class="bi bi-plus-circle text-primary"></i> Add New Mobile Device</span>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('admin.firmware.create') }}" class="btn btn-sm btn-light border text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                    <span class="d-flex align-items-center gap-2 text-body"><i class="bi bi-plus-circle text-info"></i> Add New Firmware ROM</span>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('admin.flash-files.create') }}" class="btn btn-sm btn-light border text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                    <span class="d-flex align-items-center gap-2 text-body"><i class="bi bi-plus-circle text-warning"></i> Add New Flash File</span>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('admin.posts.create') }}" class="btn btn-sm btn-light border text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                    <span class="d-flex align-items-center gap-2 text-body"><i class="bi bi-plus-circle text-success"></i> Add New Article / Tutorial</span>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('admin.advertisements.create') }}" class="btn btn-sm btn-light border text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                    <span class="d-flex align-items-center gap-2 text-body"><i class="bi bi-plus-circle text-danger"></i> Add New Advertisement</span>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('admin.theme.index') }}" class="btn btn-sm btn-light border text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                    <span class="d-flex align-items-center gap-2 text-body"><i class="bi bi-palette2 text-primary"></i> Theme Customizer Studio</span>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('admin.settings.whatsapp') }}" class="btn btn-sm btn-light border text-start d-flex align-items-center justify-content-between p-2 rounded-3">
                    <span class="d-flex align-items-center gap-2 text-body"><i class="bi bi-whatsapp text-success"></i> WhatsApp Floating Button</span>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
            </div>
        </div>

        <!-- Recent Activity Audit Card -->
        <div class="lumzy-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0 text-body">Recent Activity</h5>
                <a href="{{ route('admin.activity.index') }}" class="small text-primary text-decoration-none">Audit &rarr;</a>
            </div>

            @if($recentActivities->isEmpty())
                <div class="text-center py-3 text-muted small">No recent activity logged.</div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($recentActivities as $act)
                    <div class="d-flex align-items-start gap-2">
                        <div class="badge bg-primary-subtle text-primary rounded-circle p-2 mt-1" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="small fw-semibold text-body text-truncate">{{ $act->description ?? ucwords(str_replace('_', ' ', $act->action ?? 'Event')) }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                {{ $act->user->name ?? 'System' }} &bull; {{ $act->created_at ? $act->created_at->diffForHumans() : 'Recently' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
