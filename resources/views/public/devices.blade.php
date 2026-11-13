@extends('layouts.public')

@section('title', 'Mobile Phones Directory — Mobile Resource Hub')
@section('meta_description', 'Browse the complete smartphone catalog with technical specifications, model variants, screen metrics, chipsets, and compatible firmware.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mobile Phones</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                    <span>Filters</span>
                    @if($search || $brandFilter)
                    <a href="{{ url('/mobile-phones') }}" class="small text-danger fw-normal text-decoration-none">Reset</a>
                    @endif
                </h5>

                <form action="{{ url('/mobile-phones') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Filter by Keyword</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Model or name..." value="{{ $search }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Brand</label>
                        <select name="brand" class="form-select form-select-sm">
                            <option value="">All Brands</option>
                            <option value="samsung" {{ $brandFilter === 'samsung' ? 'selected' : '' }}>Samsung</option>
                            <option value="apple" {{ $brandFilter === 'apple' ? 'selected' : '' }}>Apple</option>
                            <option value="xiaomi" {{ $brandFilter === 'xiaomi' ? 'selected' : '' }}>Xiaomi</option>
                            <option value="google" {{ $brandFilter === 'google' ? 'selected' : '' }}>Google</option>
                            <option value="oneplus" {{ $brandFilter === 'oneplus' ? 'selected' : '' }}>OnePlus</option>
                            <option value="motorola" {{ $brandFilter === 'motorola' ? 'selected' : '' }}>Motorola</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Release Year</label>
                        <select class="form-select form-select-sm">
                            <option value="">All Years</option>
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">Apply Filters</button>
                </form>
            </div>
        </div>

        <!-- Devices Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 fw-bold mb-0">Smartphone Catalog</h1>
                <span class="text-muted small">Showing {{ count($devices) }} devices</span>
            </div>

            @if(count($devices) > 0)
            <div class="row g-3">
                @foreach($devices as $device)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="mrh-card">
                        <div class="p-3 bg-body text-center border-bottom">
                            <div class="brand-icon-box mx-auto mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-phone"></i>
                            </div>
                            <span class="small text-muted text-uppercase fw-semibold">{{ $device['brand'] }}</span>
                            <h6 class="fw-bold mb-1 text-truncate">{{ $device['name'] }}</h6>
                            <code class="small text-primary">{{ $device['model'] }}</code>
                        </div>
                        <div class="p-3 small flex-grow-1">
                            <div class="mb-1 text-muted text-truncate"><i class="bi bi-display me-1 text-primary"></i> {{ $device['screen'] }}</div>
                            <div class="mb-1 text-muted text-truncate"><i class="bi bi-cpu me-1 text-primary"></i> {{ $device['chipset'] }}</div>
                            <div class="mb-1 text-muted text-truncate"><i class="bi bi-battery-charging me-1 text-primary"></i> {{ $device['battery'] }}</div>
                            <div class="text-muted text-truncate"><i class="bi bi-calendar3 me-1 text-primary"></i> Released: {{ $device['release_year'] }}</div>
                        </div>
                        <div class="p-3 pt-0">
                            <a href="{{ url('/mobile-phones/' . $device['slug']) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                View Specs & Resources &rarr;
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="mrh-card p-5 text-center text-muted">
                <i class="bi bi-search fs-1 mb-2"></i>
                <h5>No devices found matching your criteria</h5>
                <p class="small">Try clearing your filters or searching for another model or brand.</p>
                <a href="{{ url('/mobile-phones') }}" class="btn btn-primary btn-sm rounded-pill">Clear All Filters</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
