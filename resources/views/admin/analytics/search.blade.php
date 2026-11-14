@extends('layouts.admin')

@section('title', 'Search Analytics')
@section('page_title', 'Search Engine Analytics & Indexing')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Search Engine Analytics & Indexing</h4>
        <p class="text-muted small mb-0">Monitor catalog search queries, zero-result trends, user intent, and search index health.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <form action="{{ route('admin.search-analytics.rebuild') }}" method="POST" onsubmit="return confirm('Rebuild entire search index? This synchronizes all published models and terms.');">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-arrow-repeat me-1"></i> Rebuild Search Index
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Overview KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-3 h-100">
            <span class="text-muted small text-uppercase fw-semibold">Total Searches ({{ $days }}d)</span>
            <h3 class="fw-bold mt-2 mb-1">{{ number_format($overview['total_searches']) }}</h3>
            <span class="small text-muted"><i class="bi bi-search text-primary me-1"></i> Recorded user requests</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-3 h-100">
            <span class="text-muted small text-uppercase fw-semibold">Unique Queries</span>
            <h3 class="fw-bold mt-2 mb-1">{{ number_format($overview['unique_queries']) }}</h3>
            <span class="small text-muted"><i class="bi bi-chat-left-dots text-info me-1"></i> Distinct search terms</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-3 h-100">
            <span class="text-muted small text-uppercase fw-semibold">Zero-Result Searches</span>
            <h3 class="fw-bold mt-2 mb-1 {{ $overview['zero_result_count'] > 0 ? 'text-warning' : 'text-success' }}">
                {{ number_format($overview['zero_result_count']) }}
            </h3>
            <span class="small text-muted">
                {{ $overview['zero_result_rate'] }}% miss rate
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-3 h-100">
            <span class="text-muted small text-uppercase fw-semibold">Origin Breakdown</span>
            <div class="mt-2 small">
                @forelse($overview['origin_breakdown'] as $origin => $cnt)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-capitalize">{{ $origin }}:</span>
                        <span class="fw-bold">{{ number_format($cnt) }}</span>
                    </div>
                @empty
                    <span class="text-muted">No search traffic yet</span>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Top Searches & Zero-Result Searches -->
<div class="row g-3 mb-4">
    <!-- Top Queries -->
    <div class="col-lg-6">
        <div class="mrh-card p-3 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-fire text-danger me-2"></i>Top Search Queries</h6>
            @if($overview['top_queries']->isEmpty())
                <p class="text-muted small mb-0">No query data recorded in this period.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Query</th>
                                <th class="text-center">Count</th>
                                <th class="text-center">Avg Results</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overview['top_queries'] as $row)
                                <tr>
                                    <td class="fw-semibold">
                                        <a href="{{ url('/search?q=' . urlencode($row->normalized_query)) }}" target="_blank" class="text-decoration-none">
                                            {{ $row->normalized_query }} <i class="bi bi-box-arrow-up-right small text-muted"></i>
                                        </a>
                                    </td>
                                    <td class="text-center"><span class="badge text-bg-primary rounded-pill">{{ $row->search_count }}</span></td>
                                    <td class="text-center small text-muted">{{ round($row->avg_results, 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Zero-Result Queries -->
    <div class="col-lg-6">
        <div class="mrh-card p-3 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Top Missing Content (Zero Results)</h6>
            @if($overview['zero_result_queries']->isEmpty())
                <p class="text-muted small mb-0">Great job! No zero-result queries recorded.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Missing Term</th>
                                <th class="text-center">Missed Inquiries</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overview['zero_result_queries'] as $row)
                                <tr>
                                    <td class="fw-semibold text-danger">
                                        {{ $row->normalized_query }}
                                    </td>
                                    <td class="text-center"><span class="badge text-bg-warning text-dark rounded-pill">{{ $row->zero_count }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Search Query Log Table -->
<div class="mrh-card p-3 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Search Requests Log</h6>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('admin.search-analytics.index') }}" method="GET" class="row g-2 align-items-center mb-3">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control" placeholder="Filter queries..." value="{{ $queryFilter }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select form-select-sm">
                <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>All Categories</option>
                <option value="device" {{ $typeFilter === 'device' ? 'selected' : '' }}>Devices</option>
                <option value="firmware" {{ $typeFilter === 'firmware' ? 'selected' : '' }}>Firmware</option>
                <option value="flash_file" {{ $typeFilter === 'flash_file' ? 'selected' : '' }}>Flash Files</option>
                <option value="software" {{ $typeFilter === 'software' ? 'selected' : '' }}>Software Tools</option>
                <option value="driver" {{ $typeFilter === 'driver' ? 'selected' : '' }}>USB Drivers</option>
                <option value="post" {{ $typeFilter === 'post' ? 'selected' : '' }}>Posts & Guides</option>
            </select>
        </div>
        <div class="col-md-2">
            <div class="form-check form-check-inline mt-1">
                <input class="form-check-input" type="checkbox" name="zero_only" id="zero_only" value="1" {{ $zeroOnly ? 'checked' : '' }}>
                <label class="form-check-label small" for="zero_only">Zero results</label>
            </div>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Filter</button>
            <a href="{{ route('admin.search-analytics.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Timestamp</th>
                    <th>Query</th>
                    <th>Category</th>
                    <th>Origin</th>
                    <th>Results Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="small text-muted">{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        <td class="fw-semibold">
                            <a href="{{ url('/search?q=' . urlencode($log->query) . '&type=' . $log->content_type) }}" target="_blank" class="text-decoration-none">
                                {{ $log->query }}
                            </a>
                        </td>
                        <td><span class="badge text-bg-light border text-capitalize">{{ $log->content_type }}</span></td>
                        <td><span class="badge text-bg-secondary">{{ $log->origin }}</span></td>
                        <td>
                            @if($log->results_count > 0)
                                <span class="badge text-bg-success">{{ $log->results_count }}</span>
                            @else
                                <span class="badge text-bg-danger">0</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No search query logs found matching criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
