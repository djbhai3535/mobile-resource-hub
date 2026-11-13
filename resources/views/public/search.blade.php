@extends('layouts.public')

@section('title', ($query ? 'Search results for "' . e($query) . '"' : 'Global Search') . ' — Mobile Resource Hub')
@section('meta_description', 'Search the full database of mobile phones, model numbers, official firmware, USB drivers, and repair tutorials.')

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
                <button type="submit" class="btn btn-primary px-4 fw-semibold">Search</button>
            </div>
            <div class="search-suggestions-panel"></div>
        </form>
    </div>

    @if($query)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">
            Results for <span class="text-primary">"{{ $query }}"</span>
        </h2>
        <span class="text-muted small">Showing relevant matches</span>
    </div>

    <!-- Match Results -->
    <div class="row g-3">
        <!-- Sample Device Match -->
        <div class="col-12">
            <div class="mrh-card p-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="mrh-badge mrh-badge-primary">Device</span>
                    <small class="text-muted">High Relevance Match</small>
                </div>
                <h5 class="fw-bold mb-1">
                    <a href="{{ url('/mobile-phones/galaxy-s24-ultra') }}" class="text-body text-decoration-none">
                        Samsung Galaxy S24 Ultra (SM-S928B)
                    </a>
                </h5>
                <p class="small text-muted mb-2">
                    Commercial Name: Samsung Galaxy S24 Ultra &bull; Chipset: Snapdragon 8 Gen 3 for Galaxy &bull; Display: 6.8" Dynamic AMOLED 2X.
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ url('/mobile-phones/galaxy-s24-ultra') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                        View Specification &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Sample Firmware Match -->
        <div class="col-12">
            <div class="mrh-card p-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="mrh-badge mrh-badge-warning">Firmware</span>
                    <small class="text-muted">Exact Model Match</small>
                </div>
                <h5 class="fw-bold mb-1">
                    <a href="{{ url('/firmware/galaxy-s24-ultra-sm-s928b-oneui7') }}" class="text-body text-decoration-none">
                        Samsung Galaxy S24 Ultra Android 15 OneUI 7.0 (S928BXXU3BXK2)
                    </a>
                </h5>
                <p class="small text-muted mb-2">
                    Official stock firmware for model <code>SM-S928B</code> &bull; Region: OXM Multi-CSC &bull; SHA-256 Checksum Verified.
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ url('/firmware/galaxy-s24-ultra-sm-s928b-oneui7') }}" class="btn btn-sm btn-outline-warning text-body rounded-pill">
                        Download Details &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="mrh-card p-5 text-center text-muted">
        <i class="bi bi-search fs-1 mb-3 text-primary"></i>
        <h5>Enter a model number, brand, or technical keyword</h5>
        <p class="small text-muted mx-auto" style="max-width: 500px;">
            Search across our entire catalog including smartphone specifications, official ROMs, scatter files, USB drivers, and flashing tutorials.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
            <a href="{{ url('/search?q=Samsung') }}" class="badge text-bg-light border px-3 py-2 rounded-pill text-decoration-none">Samsung</a>
            <a href="{{ url('/search?q=SM-S928B') }}" class="badge text-bg-light border px-3 py-2 rounded-pill text-decoration-none">SM-S928B</a>
            <a href="{{ url('/search?q=Odin') }}" class="badge text-bg-light border px-3 py-2 rounded-pill text-decoration-none">Odin Tool</a>
            <a href="{{ url('/search?q=Qualcomm') }}" class="badge text-bg-light border px-3 py-2 rounded-pill text-decoration-none">Qualcomm 9008</a>
        </div>
    </div>
    @endif
</div>
@endsection
