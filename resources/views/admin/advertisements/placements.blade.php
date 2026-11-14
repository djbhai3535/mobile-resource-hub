@extends('layouts.admin')

@section('title', 'Ad Placements Guide')
@section('page_title', 'Advertisement Placements Guide')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Advertisement Placements Guide</h4>
        <p class="text-muted small mb-0">Overview of all 17 registered advertising slots across website templates.</p>
    </div>
    <div>
        <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Advertisement
        </a>
    </div>
</div>

<div class="row g-3">
    @foreach($placements as $key => $p)
        @php
            $counts = $placementCounts->get($key);
            $total = $counts ? $counts->total : 0;
            $active = $counts ? $counts->active_count : 0;
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="mrh-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0 text-body">{{ $p['label'] }}</h6>
                    @if($active > 0)
                        <span class="badge text-bg-success">{{ $active }} Active</span>
                    @else
                        <span class="badge text-bg-secondary">Empty Slot</span>
                    @endif
                </div>
                <code class="small text-primary d-block mb-2">{{ $key }}</code>
                <p class="small text-muted mb-3">{{ $p['description'] }}</p>
                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top small">
                    <span class="text-muted"><i class="bi bi-aspect-ratio me-1"></i> {{ $p['recommended_size'] }}</span>
                    <a href="{{ route('admin.advertisements.create') }}?placement={{ $key }}" class="text-primary text-decoration-none fw-semibold">
                        Assign Ad &rarr;
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
