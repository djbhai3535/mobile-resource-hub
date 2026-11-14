@extends('layouts.public')

@section('title', 'Official Mobile Firmware & Stock ROM Directory — Mobile Resource Hub')
@section('meta_description', 'Search and download verified official stock firmware ROMs, multi-CSC packages, security patch updates, and service binaries.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Firmware</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Firmware & Stock ROM Hub</h1>
            <p class="text-muted small mb-0">Verified manufacturer packages with cryptographic checksum verifications</p>
        </div>
        <form action="{{ url('/firmware') }}" method="GET" class="d-flex gap-2" style="max-width: 340px;">
            <input type="search" name="search" class="form-control form-control-sm" placeholder="Model (e.g. SM-S928B, CSC)..." value="{{ $search }}">
            <button type="submit" class="btn btn-warning btn-sm">Search</button>
            @if($search)
                <a href="{{ url('/firmware') }}" class="btn btn-outline-secondary btn-sm" title="Clear"><i class="bi bi-x"></i></a>
            @endif
        </form>
    </div>

    <div class="row g-3">
        @if(isset($firmwares) && $firmwares->isNotEmpty())
            @foreach($firmwares as $fw)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="mrh-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="mrh-badge mrh-badge-warning"><i class="bi bi-cpu"></i> {{ $fw->file_type }}</span>
                        <span class="small text-muted">{{ $fw->release_date?->format('M Y') ?? 'Latest' }}</span>
                    </div>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ route('firmware.show', $fw->slug) }}" class="text-body text-decoration-none">
                            {{ $fw->title }}
                        </a>
                    </h5>
                    <div class="small text-muted mb-3 flex-grow-1">
                        <div><strong>Model:</strong> <code>{{ $fw->model_number }}</code></div>
                        <div><strong>Brand:</strong> {{ $fw->brand->name ?? 'Universal' }}</div>
                        <div><strong>Region / CSC:</strong> {{ $fw->region ?? 'Global' }} {{ $fw->csc ? '(' . $fw->csc . ')' : '' }}</div>
                        <div><strong>Build Number:</strong> <code>{{ $fw->build_number ?? 'N/A' }}</code></div>
                        @if($fw->security_patch_date)
                            <div><strong>Security Patch:</strong> {{ $fw->security_patch_date->format('Y-m-d') }}</div>
                        @endif
                        <div><strong>Archive Size:</strong> {{ $fw->file_size_formatted ?? 'N/A' }}</div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('firmware.show', $fw->slug) }}" class="btn btn-sm btn-outline-warning text-body w-100 rounded-pill fw-semibold">
                            View Build & Checksum &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="col-12 mt-4 d-flex justify-content-center">
                {{ $firmwares->links() }}
            </div>
        @else
            <!-- Static Fallback Records for Demo Continuity -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="mrh-card p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="mrh-badge mrh-badge-warning"><i class="bi bi-cpu"></i> Official Firmware</span>
                        <span class="small text-muted">Nov 2026</span>
                    </div>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ url('/firmware/galaxy-s24-ultra-sm-s928b-oneui7') }}" class="text-body text-decoration-none">
                            Galaxy S24 Ultra Android 15 OneUI 7.0
                        </a>
                    </h5>
                    <div class="small text-muted mb-3">
                        <div><strong>Model:</strong> <code>SM-S928B</code></div>
                        <div><strong>Region / CSC:</strong> OXM Multi-CSC (Global)</div>
                        <div><strong>Build Number:</strong> <code>S928BXXU3BXK2</code></div>
                        <div><strong>Security Patch:</strong> 2026-11-01</div>
                        <div><strong>Archive Size:</strong> 9.2 GB</div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ url('/firmware/galaxy-s24-ultra-sm-s928b-oneui7') }}" class="btn btn-sm btn-outline-warning text-body w-100 rounded-pill fw-semibold">
                            View Build & Checksum &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="mrh-card p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="mrh-badge mrh-badge-warning"><i class="bi bi-cpu"></i> HyperOS ROM</span>
                        <span class="small text-muted">Nov 2026</span>
                    </div>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ url('/firmware/redmi-note-13-pro-5g-hyperos2-global') }}" class="text-body text-decoration-none">
                            Redmi Note 13 Pro 5G HyperOS 2.0
                        </a>
                    </h5>
                    <div class="small text-muted mb-3">
                        <div><strong>Codename:</strong> <code>garnet</code></div>
                        <div><strong>Region:</strong> Global (MI)</div>
                        <div><strong>Build Number:</strong> <code>OS2.0.1.0.UNRMIXM</code></div>
                        <div><strong>Security Patch:</strong> 2026-10-01</div>
                        <div><strong>Archive Size:</strong> 5.6 GB</div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ url('/firmware/redmi-note-13-pro-5g-hyperos2-global') }}" class="btn btn-sm btn-outline-warning text-body w-100 rounded-pill fw-semibold">
                            View Build & Checksum &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="mrh-card p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="mrh-badge mrh-badge-warning"><i class="bi bi-cpu"></i> Factory Image</span>
                        <span class="small text-muted">Nov 2026</span>
                    </div>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ url('/firmware/pixel-9-pro-factory-image-android15') }}" class="text-body text-decoration-none">
                            Pixel 9 Pro Factory Image Android 15
                        </a>
                    </h5>
                    <div class="small text-muted mb-3">
                        <div><strong>Codename:</strong> <code>caiman</code></div>
                        <div><strong>Region:</strong> Global Factory</div>
                        <div><strong>Build Number:</strong> <code>AP4A.241105.008</code></div>
                        <div><strong>Security Patch:</strong> 2026-11-05</div>
                        <div><strong>Archive Size:</strong> 3.8 GB</div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ url('/firmware/pixel-9-pro-factory-image-android15') }}" class="btn btn-sm btn-outline-warning text-body w-100 rounded-pill fw-semibold">
                            View Build & Checksum &rarr;
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
