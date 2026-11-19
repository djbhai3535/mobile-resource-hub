@extends('layouts.admin')

@section('title', 'Visitor Analytics')
@section('page_title', 'Visitor Analytics & Traffic Insights')

@section('content')
<!-- Header Controls & Date Filters -->
<div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold text-body mb-1">Visitor Analytics &amp; Traffic Insights</h1>
        <p class="text-muted small mb-0">Genuine, privacy-conscious visitor metrics, page views, and daily growth trends.</p>
    </div>

    <!-- Date Range Filter Buttons -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('admin.analytics.visitors', ['range' => 'today']) }}" class="btn btn-sm {{ $range === 'today' ? 'btn-primary' : 'btn-white border text-muted' }} rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.82rem;">
            Today
        </a>
        <a href="{{ route('admin.analytics.visitors', ['range' => '7d']) }}" class="btn btn-sm {{ $range === '7d' ? 'btn-primary' : 'btn-white border text-muted' }} rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.82rem;">
            Last 7 Days
        </a>
        <a href="{{ route('admin.analytics.visitors', ['range' => '30d']) }}" class="btn btn-sm {{ $range === '30d' ? 'btn-primary' : 'btn-white border text-muted' }} rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.82rem;">
            Last 30 Days
        </a>
        <a href="{{ route('admin.analytics.visitors', ['range' => '90d']) }}" class="btn btn-sm {{ $range === '90d' ? 'btn-primary' : 'btn-white border text-muted' }} rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.82rem;">
            Last 90 Days
        </a>

        <!-- Custom Date Trigger Modal / Dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm {{ $range === 'custom' ? 'btn-primary' : 'btn-white border text-muted' }} rounded-pill px-3 py-1 fw-semibold dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.82rem;">
                <i class="bi bi-calendar-range me-1"></i> Custom Range
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-3" style="width: 280px; border-radius: 14px;">
                <form action="{{ route('admin.analytics.visitors') }}" method="GET">
                    <input type="hidden" name="range" value="custom">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Start Date</label>
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ $from ?? $analytics['start_date'] }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">End Date</label>
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ $to ?? $analytics['end_date'] }}" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">Apply Filter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Primary Real-Time Analytics Cards Row -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Active Visitors Right Now -->
    <div class="col-sm-6 col-xl-2">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small" style="font-size: 0.78rem;">Live Active</span>
                <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 10px; height: 10px;"></span>
            </div>
            <h3 class="fw-extrabold mb-1 text-success">{{ number_format($analytics['active_now']) }}</h3>
            <span class="small text-muted" style="font-size: 0.72rem;">Active in last 5m</span>
        </div>
    </div>

    <!-- Today's Unique Visitors -->
    <div class="col-sm-6 col-xl-2">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small" style="font-size: 0.78rem;">Today's Visitors</span>
                <i class="bi bi-person-check text-primary"></i>
            </div>
            <h3 class="fw-extrabold mb-1 text-body">{{ number_format($analytics['today_unique_visitors']) }}</h3>
            <span class="small text-muted" style="font-size: 0.72rem;">Unique human visits</span>
        </div>
    </div>

    <!-- Monthly Unique Visitors -->
    <div class="col-sm-6 col-xl-2">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small" style="font-size: 0.78rem;">Monthly Visitors</span>
                <i class="bi bi-calendar-month text-info"></i>
            </div>
            <h3 class="fw-extrabold mb-1 text-body">{{ number_format($analytics['monthly_unique_visitors']) }}</h3>
            <span class="small text-muted" style="font-size: 0.72rem;">This calendar month</span>
        </div>
    </div>

    <!-- Total Unique Visitors in Selected Range -->
    <div class="col-sm-6 col-xl-2">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small" style="font-size: 0.78rem;">Range Unique</span>
                <i class="bi bi-people-fill text-warning"></i>
            </div>
            <h3 class="fw-extrabold mb-1 text-body">{{ number_format($analytics['total_unique_visitors']) }}</h3>
            <span class="small text-muted" style="font-size: 0.72rem;">In selected period</span>
        </div>
    </div>

    <!-- Total Page Views in Range -->
    <div class="col-sm-6 col-xl-2">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small" style="font-size: 0.78rem;">Total Page Views</span>
                <i class="bi bi-eye text-primary"></i>
            </div>
            <h3 class="fw-extrabold mb-1 text-body">{{ number_format($analytics['total_page_views']) }}</h3>
            <span class="small text-muted" style="font-size: 0.72rem;">Raw content loads</span>
        </div>
    </div>

    <!-- Avg Pages per Visitor -->
    <div class="col-sm-6 col-xl-2">
        <div class="lumzy-kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small" style="font-size: 0.78rem;">Pages / Visitor</span>
                <i class="bi bi-file-earmark-bar-graph text-secondary"></i>
            </div>
            <h3 class="fw-extrabold mb-1 text-body">{{ $analytics['avg_views_per_visitor'] }}</h3>
            <span class="small text-muted" style="font-size: 0.72rem;">Engagement depth</span>
        </div>
    </div>
</div>

<!-- Main Row: Traffic Growth Chart & Device/Browser Breakdown -->
<div class="row g-4 mb-4">
    <!-- Traffic Trend Chart -->
    <div class="col-lg-8">
        <div class="lumzy-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-body">Daily Traffic &amp; Visitor Growth</h5>
                    <p class="text-muted small mb-0">{{ $analytics['start_date'] }} to {{ $analytics['end_date'] }}</p>
                </div>
                <div class="d-flex align-items-center gap-3 small">
                    <span class="d-flex align-items-center gap-1 text-muted">
                        <span class="rounded-circle" style="width: 10px; height: 10px; background-color: #2563EB;"></span> Page Views
                    </span>
                    <span class="d-flex align-items-center gap-1 text-muted">
                        <span class="rounded-circle" style="width: 10px; height: 10px; background-color: #10B981;"></span> Unique Visitors
                    </span>
                </div>
            </div>

            <!-- CSS Bar Chart Visualization -->
            <div class="pt-3 pb-2" style="overflow-x: auto;">
                <div class="d-flex align-items-end gap-2" style="height: 220px; min-width: {{ count($analytics['daily_trend']) * 32 }}px; border-bottom: 2px solid #EAECF0;">
                    @php
                        $maxVal = max(array_merge(
                            array_column($analytics['daily_trend'], 'views'),
                            array_column($analytics['daily_trend'], 'visitors'),
                            [10]
                        ));
                    @endphp

                    @foreach($analytics['daily_trend'] as $day)
                        @php
                            $vHeight = ($day['views'] / $maxVal) * 190;
                            $uHeight = ($day['visitors'] / $maxVal) * 190;
                        @endphp
                        <div class="flex-grow-1 d-flex flex-column align-items-center" style="min-width: 24px;" title="{{ $day['date'] }}: {{ $day['views'] }} views, {{ $day['visitors'] }} visitors">
                            <div class="d-flex align-items-end gap-1 w-100 justify-content-center" style="height: 190px;">
                                <div class="rounded-top" style="width: 9px; height: {{ max($vHeight, 4) }}px; background-color: #2563EB; transition: height 0.3s ease;"></div>
                                <div class="rounded-top" style="width: 9px; height: {{ max($uHeight, 4) }}px; background-color: #10B981; transition: height 0.3s ease;"></div>
                            </div>
                            <span class="text-muted mt-2 text-center" style="font-size: 0.65rem; white-space: nowrap;">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Client Breakdown Column -->
    <div class="col-lg-4">
        <div class="lumzy-card p-4 h-100 d-flex flex-column">
            <h5 class="fw-bold mb-3 text-body">Device &amp; Platform Breakdown</h5>

            <div class="mb-4">
                <h6 class="small fw-bold text-muted text-uppercase mb-2" style="font-size: 0.72rem;">Device Category</h6>
                @php
                    $totalDevs = array_sum($analytics['device_breakdown']) ?: 1;
                @endphp
                @foreach(['desktop' => 'Desktop Computer', 'mobile' => 'Mobile Smartphone', 'tablet' => 'Tablet Device'] as $dKey => $dLabel)
                    @php
                        $cnt = $analytics['device_breakdown'][$dKey] ?? 0;
                        $pct = round(($cnt / $totalDevs) * 100);
                    @endphp
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $dLabel }}</span>
                            <span class="fw-bold text-body">{{ $pct }}% ({{ $cnt }})</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 4px;">
                            <div class="progress-bar {{ $dKey === 'desktop' ? 'bg-primary' : ($dKey === 'mobile' ? 'bg-success' : 'bg-warning') }}" style="width: {{ $pct }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-auto">
                <h6 class="small fw-bold text-muted text-uppercase mb-2" style="font-size: 0.72rem;">Top Browsers</h6>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($analytics['browser_breakdown'] as $bName => $bCount)
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill" style="font-size: 0.78rem;">
                            {{ $bName }}: <strong>{{ $bCount }}</strong>
                        </span>
                    @empty
                        <span class="text-muted small">No browser data recorded yet.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: Top Viewed Pages Table -->
<div class="lumzy-card p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-bold mb-0 text-body">Top Viewed Platform Pages</h5>
            <p class="text-muted small mb-0">Highest engagement public routes in this period</p>
        </div>
        <span class="badge bg-primary-subtle text-primary">{{ count($analytics['top_pages']) }} Popular URLs</span>
    </div>

    @if(empty($analytics['top_pages']))
        <div class="text-center py-4 text-muted small">
            <i class="bi bi-clock-history fs-3 d-block mb-2 opacity-50"></i>
            No page view records logged in this date range.
        </div>
    @else
        <div class="table-responsive">
            <table class="table lumzy-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50%;">Page URL Path</th>
                        <th class="text-end">Total Views</th>
                        <th class="text-end">Unique Visitors</th>
                        <th class="text-end" style="width: 20%;">Share of Traffic</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analytics['top_pages'] as $pg)
                    <tr>
                        <td>
                            <a href="{{ url($pg['path']) }}" target="_blank" class="text-body fw-semibold text-decoration-none">
                                <code>{{ $pg['path'] }}</code>
                                <i class="bi bi-box-arrow-up-right ms-1 text-muted" style="font-size: 0.72rem;"></i>
                            </a>
                        </td>
                        <td class="text-end fw-bold text-body">{{ number_format($pg['views']) }}</td>
                        <td class="text-end text-muted">{{ number_format($pg['visitors']) }}</td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; border-radius: 4px; max-width: 80px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $pg['percentage'] }}%;"></div>
                                </div>
                                <span class="small fw-semibold text-muted" style="width: 40px;">{{ $pg['percentage'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
