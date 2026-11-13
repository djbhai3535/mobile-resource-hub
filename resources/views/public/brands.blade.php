@extends('layouts.public')

@section('title', 'Mobile Phone Brands Directory — Mobile Resource Hub')
@section('meta_description', 'Explore verified smartphone manufacturer profiles, official models, firmware repositories, and driver archives.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Brands</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Mobile Brand Directory</h1>
            <p class="text-muted small mb-0">Browse smartphones and resources indexed by hardware manufacturer</p>
        </div>
        <span class="badge text-bg-light border px-3 py-2 rounded-pill">{{ count($brands) }} Brands Indexed</span>
    </div>

    <div class="row g-4">
        @foreach($brands as $brand)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="mrh-card p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="brand-icon-box" style="width: 48px; height: 48px;">
                        <i class="bi bi-phone"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">
                            <a href="{{ url('/brands/' . $brand['slug']) }}" class="text-body text-decoration-none">
                                {{ $brand['name'] }}
                            </a>
                        </h5>
                        <span class="small text-muted">{{ $brand['origin'] }} &bull; Est. {{ $brand['founded'] }}</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="small text-muted"><strong>{{ $brand['count'] }}</strong> Models Registered</span>
                    <a href="{{ url('/brands/' . $brand['slug']) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                        View Devices &rarr;
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
