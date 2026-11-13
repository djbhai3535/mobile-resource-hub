@extends('layouts.public')

@section('title', $brandName . ' Phones, Firmware & Resources — Mobile Resource Hub')
@section('meta_description', 'Discover all ' . $brandName . ' smartphone specifications, official stock firmware ROMs, USB drivers, and flashing tools.')

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
        <div class="d-flex align-items-center gap-4">
            <div class="brand-icon-box" style="width: 72px; height: 72px; font-size: 2.5rem;">
                <i class="bi bi-phone"></i>
            </div>
            <div>
                <h1 class="h2 fw-bold mb-1">{{ $brandName }} Smartphones & Resources</h1>
                <p class="text-muted small mb-2">Verified technical device database and download archives for {{ $brandName }}.</p>
                <div class="d-flex gap-2">
                    <a href="{{ url('/mobile-phones?brand=' . $brandSlug) }}" class="btn btn-sm btn-primary rounded-pill">
                        Browse {{ $brandName }} Phones
                    </a>
                    <a href="{{ url('/firmware?search=' . $brandSlug) }}" class="btn btn-sm btn-outline-warning text-body rounded-pill">
                        Browse Firmware
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Published Devices for Brand -->
    <h3 class="h5 fw-bold mb-3">Popular {{ $brandName }} Models</h3>
    <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="mrh-card p-3">
                <span class="small text-muted text-uppercase fw-semibold">{{ $brandName }}</span>
                <h6 class="fw-bold mb-1">
                    <a href="{{ url('/mobile-phones/' . $brandSlug . '-flagship') }}" class="text-body text-decoration-none">
                        {{ $brandName }} Flagship Pro
                    </a>
                </h6>
                <code class="small text-primary">SM-9000 Series</code>
                <div class="mt-3">
                    <a href="{{ url('/mobile-phones/' . $brandSlug . '-flagship') }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                        View Specs &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
