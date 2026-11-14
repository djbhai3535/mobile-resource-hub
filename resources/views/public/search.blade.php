@extends('layouts.public')

@section('title', ($query ? 'Search results for "' . e($query) . '"' : 'Global Search') . ' — Mobile Resource Hub')
@section('meta_description', 'Search mobile phone specifications, official stock firmware, flash files, USB drivers, and step-by-step repair guides.')

@section('robots', 'noindex, follow')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Global Search</li>
        </ol>
    </nav>

    <!-- Prominent Search Bar (PRD Section 8) -->
    <div class="mrh-card p-4 mb-4">
        <h1 class="h4 fw-bold mb-3">Global Catalog Search</h1>
        <form action="{{ url('/search') }}" method="GET" class="search-wrapper">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-body"><i class="bi bi-search text-muted"></i></span>
                <input 
                    type="search" 
                    name="q" 
                    class="form-control mrh-search-input" 
                    placeholder="Search model number (e.g. SM-S928B), brand, chipset, firmware, driver..." 
                    value="{{ $query }}" 
                    required 
                    autocomplete="off"
                >
                @if($type && $type !== 'all')
                    <input type="hidden" name="type" value="{{ $type }}">
                @endif
                <button type="submit" class="btn btn-primary px-4 fw-semibold">Search</button>
            </div>
            <div class="search-suggestions-panel"></div>
        </form>
    </div>

    @if($query)
        <!-- Search Header with Query and Category Filter Tabs -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1">
                    Results for <span class="text-primary">"{{ $query }}"</span>
                </h2>
                <span class="text-muted small">
                    Found <strong>{{ number_format($totalResults) }}</strong> matching {{ Str::plural('resource', $totalResults) }}
                </span>
            </div>
        </div>

        <!-- Category Filter Tabs -->
        <div class="d-flex flex-nowrap overflow-x-auto pb-2 mb-4 gap-2">
            @php
                $tabs = [
                    'all' => ['label' => 'All Results', 'count' => $tabCounts['all'] ?? 0],
                    'device' => ['label' => 'Devices', 'count' => $tabCounts['device'] ?? 0],
                    'firmware' => ['label' => 'Firmware', 'count' => $tabCounts['firmware'] ?? 0],
                    'flash_file' => ['label' => 'Flash Files', 'count' => $tabCounts['flash_file'] ?? 0],
                    'software' => ['label' => 'Tools', 'count' => $tabCounts['software'] ?? 0],
                    'driver' => ['label' => 'Drivers', 'count' => $tabCounts['driver'] ?? 0],
                    'post' => ['label' => 'News', 'count' => $tabCounts['post'] ?? 0],
                    'tutorial' => ['label' => 'Tutorials', 'count' => $tabCounts['tutorial'] ?? 0],
                    'brand' => ['label' => 'Brands', 'count' => $tabCounts['brand'] ?? 0],
                    'page' => ['label' => 'Pages', 'count' => $tabCounts['page'] ?? 0],
                ];
            @endphp

            @foreach($tabs as $tabKey => $tab)
                @if($tab['count'] > 0 || $tabKey === 'all')
                    <a 
                        href="{{ url('/search') }}?q={{ urlencode($query) }}&type={{ $tabKey }}" 
                        class="btn btn-sm rounded-pill text-nowrap {{ ($type === $tabKey || ($type === 'all' && $tabKey === 'all')) ? 'btn-primary' : 'btn-outline-secondary' }}"
                    >
                        {{ $tab['label'] }}
                        <span class="badge ms-1 {{ ($type === $tabKey || ($type === 'all' && $tabKey === 'all')) ? 'bg-light text-primary' : 'bg-secondary' }}">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>

        <!-- Results Stream -->
        @if($results->count() > 0)
            <div class="row g-3">
                @foreach($results as $item)
                    @php
                        $badgeClasses = [
                            'device' => 'badge text-bg-primary',
                            'firmware' => 'badge text-bg-success',
                            'flash_file' => 'badge text-bg-warning',
                            'software' => 'badge text-bg-info text-dark',
                            'driver' => 'badge text-bg-secondary',
                            'post' => 'badge text-bg-dark',
                            'tutorial' => 'badge text-bg-danger',
                            'brand' => 'badge text-bg-secondary',
                            'page' => 'badge text-bg-light border',
                        ];
                        $badgeText = [
                            'device' => 'Smartphone Spec',
                            'firmware' => 'Stock Firmware',
                            'flash_file' => 'Flash File (ROM)',
                            'software' => 'Software Tool',
                            'driver' => 'USB Driver',
                            'post' => 'Editorial News',
                            'tutorial' => 'Repair Tutorial',
                            'brand' => 'Brand Portal',
                            'page' => 'Static Page',
                        ];
                        $badgeClass = $badgeClasses[$item->content_type] ?? 'badge text-bg-light border';
                        $typeLabel = $badgeText[$item->content_type] ?? ucfirst($item->content_type);
                    @endphp
                    <div class="col-12">
                        <div class="mrh-card p-3 p-md-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="{{ $badgeClass }}">{{ $typeLabel }}</span>
                                    @if($item->brand_name)
                                        <span class="badge text-bg-light border">{{ $item->brand_name }}</span>
                                    @endif
                                    @if($item->model_code)
                                        <span class="badge text-bg-light border font-monospace">{{ $item->model_code }}</span>
                                    @endif
                                </div>
                                @if($item->published_at)
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ $item->published_at->format('M d, Y') }}
                                    </small>
                                @endif
                            </div>

                            <h5 class="fw-bold mb-2">
                                <a href="{{ $item->searchable_url }}" class="text-body text-decoration-none">
                                    {!! $item->highlighted_title !!}
                                </a>
                            </h5>

                            <p class="small text-muted mb-3">
                                {!! $item->highlighted_snippet !!}
                            </p>

                            <div>
                                <a href="{{ $item->searchable_url }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    View Resource &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $results->appends(['q' => $query, 'type' => $type])->links('pagination::bootstrap-5') }}
            </div>
        @else
            <!-- Zero Results State -->
            <div class="mrh-card p-5 text-center text-muted">
                <i class="bi bi-search fs-1 mb-3 text-warning"></i>
                <h4 class="fw-bold text-body">No matching results found</h4>
                <p class="small text-muted mx-auto mb-4" style="max-width: 520px;">
                    We couldn't find any resources matching <strong class="text-body">"{{ $query }}"</strong> in this category. Try adjusting your query or searching across all categories.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-2">
                    @if($type !== 'all')
                        <a href="{{ url('/search') }}?q={{ urlencode($query) }}&type=all" class="btn btn-sm btn-primary rounded-pill px-3">
                            Search All Categories
                        </a>
                    @endif
                    <a href="{{ url('/devices') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        Browse Devices
                    </a>
                    <a href="{{ url('/firmware') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        Browse Firmware
                    </a>
                </div>
            </div>
        @endif
    @else
        <!-- Initial Blank State with Suggested Searches -->
        <div class="mrh-card p-5 text-center text-muted">
            <i class="bi bi-search fs-1 mb-3 text-primary"></i>
            <h5 class="fw-bold text-body">Enter a model number, brand, or technical keyword</h5>
            <p class="small text-muted mx-auto" style="max-width: 520px;">
                Search across our complete catalog of mobile phone specifications, official stock ROMs, scatter files, USB drivers, and flashing tutorials.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                <a href="{{ url('/search?q=Samsung') }}" class="badge text-bg-light border px-3 py-2 rounded-pill text-decoration-none">Samsung</a>
                <a href="{{ url('/search?q=SM-S928B') }}" class="badge text-bg-light border px-3 py-2 rounded-pill text-decoration-none">SM-S928B</a>
                <a href="{{ url('/search?q=Odin') }}" class="badge text-bg-light border px-3 py-2 rounded-pill text-decoration-none">Odin Tool</a>
                <a href="{{ url('/search?q=Qualcomm') }}" class="badge text-bg-light border px-3 py-2 rounded-pill text-decoration-none">Qualcomm 9008</a>
                <a href="{{ url('/search?q=MediaTek') }}" class="badge text-bg-light border px-3 py-2 rounded-pill text-decoration-none">MediaTek Scatter</a>
            </div>
        </div>
    @endif
</div>
@endsection
