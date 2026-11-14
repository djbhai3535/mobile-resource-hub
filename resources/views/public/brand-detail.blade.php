@extends('layouts.public')

@section('title', $brandName . ' Phones, Specifications & Resources — Mobile Resource Hub')
@section('meta_description', 'Discover all ' . $brandName . ' smartphone specifications, official model numbers, firmware downloads, and hardware details.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/brands') }}">Brands</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $brandName }}</li>
        </ol>
    </nav>

    <!-- Brand Header -->
    <div class="mrh-card p-4 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-4">
            @if(isset($brand) && $brand->logo)
                <div class="p-2 bg-white rounded border d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <img src="{{ $brand->logo_url }}" alt="{{ $brandName }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" loading="lazy">
                </div>
            @else
                <div class="brand-icon-box" style="width: 72px; height: 72px; font-size: 2.5rem;">
                    <i class="bi bi-phone"></i>
                </div>
            @endif
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h1 class="h2 fw-bold mb-0">{{ $brandName }} Smartphones & Resources</h1>
                    @if(isset($brand) && $brand->country_of_origin)
                        <span class="badge bg-secondary-subtle text-secondary border">{{ $brand->country_of_origin }}</span>
                    @endif
                </div>
                <p class="text-muted small mb-3">
                    {{ isset($brand) && $brand->short_description ? $brand->short_description : 'Verified technical device database and download archives for ' . $brandName . '.' }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url('/mobile-phones?brand=' . $brandSlug) }}" class="btn btn-sm btn-primary rounded-pill">
                        <i class="bi bi-phone me-1"></i> Browse {{ $brandName }} Phones
                    </a>
                    @if(isset($brand) && $brand->website_url)
                        <a href="{{ $brand->website_url }}" target="_blank" rel="noopener nofollow" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="bi bi-globe me-1"></i> Official Website
                        </a>
                    @endif
                    <a href="{{ url('/firmware?search=' . $brandSlug) }}" class="btn btn-sm btn-outline-warning text-body rounded-pill">
                        <i class="bi bi-cpu me-1"></i> Firmware Archive
                    </a>
                </div>
            </div>
        </div>

        @if(isset($brand) && $brand->full_description)
            <div class="mt-4 pt-3 border-top small text-muted">
                {{ $brand->full_description }}
            </div>
        @endif
    </div>

    <!-- Published Devices for Brand -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h5 fw-bold mb-0">Registered {{ $brandName }} Models</h3>
        <span class="small text-muted">{{ $devices->total() }} Devices Cataloged</span>
    </div>

    @if($devices->isEmpty())
    <div class="mrh-card p-5 text-center text-muted">
        <i class="bi bi-phone fs-1 mb-2 opacity-50"></i>
        <h6>No devices currently cataloged under {{ $brandName }}</h6>
        <p class="small mb-0">Check back soon as new verified device specifications are added.</p>
    </div>
    @else
    <div class="row g-3">
        @foreach($devices as $dev)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="mrh-card p-3 h-100 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-2">
                    @if($dev->featured_image)
                        <img src="{{ asset('storage/' . $dev->featured_image) }}" alt="{{ $dev->name }}" class="rounded border p-1 bg-white" style="width: 48px; height: 48px; object-fit: contain;" loading="lazy">
                    @else
                        <div class="rounded border d-flex align-items-center justify-content-center bg-body-tertiary text-muted" style="width: 48px; height: 48px;">
                            <i class="bi bi-phone"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1 overflow-hidden">
                        <span class="small text-muted text-uppercase fw-semibold">{{ $brandName }}</span>
                        <h6 class="fw-bold mb-0 text-truncate">
                            <a href="{{ url('/mobile-phones/' . $dev->slug) }}" class="text-body text-decoration-none">
                                {{ $dev->name }}
                            </a>
                        </h6>
                        <code class="small text-primary">
                            {{ $dev->modelNumbers->pluck('model_number')->take(2)->implode(', ') ?: 'Standard' }}
                        </code>
                    </div>
                </div>

                @if($dev->short_description)
                    <p class="small text-muted line-clamp-2 mb-3 flex-grow-1">{{ $dev->short_description }}</p>
                @endif

                <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted text-capitalize">{{ $dev->market_status }}</span>
                    <a href="{{ url('/mobile-phones/' . $dev->slug) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        View Specs &rarr;
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $devices->links() }}
    </div>
    @endif
</div>
@endsection
