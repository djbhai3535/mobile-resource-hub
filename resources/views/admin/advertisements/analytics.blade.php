@extends('layouts.admin')

@section('title', 'Advertisement Analytics')
@section('page_title', 'Advertisement Performance & Monetization Analytics')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Advertisement Performance & Analytics</h4>
        <p class="text-muted small mb-0">Genuine impression tracking, verified clicks, and CTR metrics across campaigns.</p>
    </div>
    <div>
        <a href="{{ route('admin.advertisements.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Advertisements
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-3 h-100">
            <span class="text-muted small text-uppercase fw-semibold">Ad Renders &bull; {{ $days }}d</span>
            <h3 class="fw-bold mt-2 mb-1 text-primary">{{ number_format($overview['total_impressions']) }}</h3>
            <span class="small text-muted"><i class="bi bi-eye text-primary me-1"></i> Server ad responses (deduplicated)</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-3 h-100">
            <span class="text-muted small text-uppercase fw-semibold">Total Clicks</span>
            <h3 class="fw-bold mt-2 mb-1 text-success">{{ number_format($overview['total_clicks']) }}</h3>
            <span class="small text-muted"><i class="bi bi-cursor text-success me-1"></i> Tracked outbound clicks</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-3 h-100">
            <span class="text-muted small text-uppercase fw-semibold">Average CTR</span>
            <h3 class="fw-bold mt-2 mb-1 {{ $overview['ctr'] > 0 ? 'text-info' : 'text-muted' }}">
                {{ $overview['ctr'] }}%
            </h3>
            <span class="small text-muted">(Clicks / Impressions) &times; 100</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-3 h-100">
            <span class="text-muted small text-uppercase fw-semibold">Campaign Status</span>
            <div class="mt-2 small">
                <div class="d-flex justify-content-between mb-1">
                    <span>Active Campaigns:</span>
                    <strong class="text-success">{{ $overview['active_campaigns'] }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Expired Campaigns:</span>
                    <strong class="text-danger">{{ $overview['expired_campaigns'] }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Placement Performance Breakdown -->
<div class="mrh-card p-3 mb-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2 text-primary"></i>Performance by Placement Slot</h6>
    @if(empty($overview['placement_stats']))
        <p class="text-muted small mb-0 py-3 text-center">No impression activity recorded across placements in this period.</p>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Placement</th>
                        <th>Slot Key</th>
                        <th class="text-center">Impressions</th>
                        <th class="text-center">Clicks</th>
                        <th class="text-center">CTR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overview['placement_stats'] as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row['label'] }}</td>
                            <td><code>{{ $row['key'] }}</code></td>
                            <td class="text-center"><span class="badge text-bg-light border">{{ number_format($row['impressions']) }}</span></td>
                            <td class="text-center"><span class="badge text-bg-primary">{{ number_format($row['clicks']) }}</span></td>
                            <td class="text-center"><span class="badge {{ $row['ctr'] > 0 ? 'text-bg-success' : 'text-bg-light border' }}">{{ $row['ctr'] }}%</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Top Performing Advertisements -->
<div class="mrh-card p-3">
    <h6 class="fw-bold mb-3"><i class="bi bi-trophy me-2 text-warning"></i>Top Performing Creatives</h6>
    @if($overview['top_ads']->isEmpty())
        <p class="text-muted small mb-0 py-3 text-center">No creative data available.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Creative</th>
                        <th>Placement</th>
                        <th class="text-center">Impressions</th>
                        <th class="text-center">Clicks</th>
                        <th class="text-center">CTR</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overview['top_ads'] as $ad)
                        <tr>
                            <td class="fw-bold">{{ $ad->name }}</td>
                            <td><span class="badge text-bg-light border">{{ $ad->placement }}</span></td>
                            <td class="text-center">{{ number_format($ad->impressions_count) }}</td>
                            <td class="text-center"><span class="badge text-bg-success">{{ number_format($ad->clicks_count) }}</span></td>
                            <td class="text-center"><strong>{{ $ad->ctr }}%</strong></td>
                            <td class="text-center">
                                @if($ad->is_active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
