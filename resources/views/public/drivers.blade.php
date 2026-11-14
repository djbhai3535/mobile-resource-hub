@extends('layouts.public')

@section('title', 'Official Mobile USB Drivers Directory — Mobile Resource Hub')
@section('meta_description', 'Download official USB drivers for Samsung, Qualcomm, MediaTek MTK, Google ADB, Xiaomi, and Apple devices.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">USB Drivers</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Official Mobile USB Drivers</h1>
            <p class="text-muted small mb-0">Certified Windows drivers for ADB, Fastboot, Emergency Download (EDL), and MTK Preloader modes</p>
        </div>
        <form action="{{ url('/drivers') }}" method="GET" class="d-flex gap-2" style="max-width: 340px;">
            <input type="search" name="search" class="form-control form-control-sm" placeholder="Driver name, chipset..." value="{{ $search }}">
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            @if($search)
                <a href="{{ url('/drivers') }}" class="btn btn-outline-secondary btn-sm" title="Clear"><i class="bi bi-x"></i></a>
            @endif
        </form>
    </div>

    <div class="row g-4">
        @if(isset($drivers) && $drivers->isNotEmpty())
            @foreach($drivers as $drv)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="mrh-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="mrh-badge mrh-badge-primary">{{ $drv->brand ? $drv->brand->name : 'Universal / Chipset' }}</span>
                        @if($drv->is_verified)
                            <span class="badge bg-success-subtle text-success small"><i class="bi bi-patch-check-fill"></i> Signed</span>
                        @endif
                    </div>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ route('drivers.show', $drv->slug) }}" class="text-body text-decoration-none">
                            {{ $drv->name }}
                        </a>
                    </h5>
                    <p class="small text-muted mb-3 flex-grow-1">
                        {{ $drv->supported_devices ?: 'Enables PC communication with device bootloader, recovery, and download modes.' }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 small mt-auto">
                        <span class="text-muted font-monospace">{{ $drv->driver_version ?? 'Latest' }}</span>
                        <a href="{{ route('drivers.show', $drv->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill">Download &rarr;</a>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="col-12 mt-4 d-flex justify-content-center">
                {{ $drivers->links() }}
            </div>
        @else
            <!-- Static Fallback Records -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="mrh-card p-4">
                    <span class="mrh-badge mrh-badge-primary mb-2">Qualcomm</span>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ url('/drivers/qualcomm-usb-driver') }}" class="text-body text-decoration-none">
                            Qualcomm HS-USB QDLoader 9008 Driver
                        </a>
                    </h5>
                    <p class="small text-muted mb-3">Certified Windows 32/64-bit driver for Qualcomm Emergency Download (EDL) Mode recovery.</p>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 small">
                        <span class="text-muted">Version: 2.1.0.5</span>
                        <a href="{{ url('/drivers/qualcomm-usb-driver') }}" class="btn btn-sm btn-outline-primary rounded-pill">Download &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="mrh-card p-4">
                    <span class="mrh-badge mrh-badge-primary mb-2">Samsung</span>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ url('/drivers/samsung-usb-driver') }}" class="text-body text-decoration-none">
                            Samsung Android USB Driver
                        </a>
                    </h5>
                    <p class="small text-muted mb-3">Official Samsung driver package enabling seamless Odin, SmartSwitch, and ADB connections.</p>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 small">
                        <span class="text-muted">Version: 1.7.59</span>
                        <a href="{{ url('/drivers/samsung-usb-driver') }}" class="btn btn-sm btn-outline-primary rounded-pill">Download &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="mrh-card p-4">
                    <span class="mrh-badge mrh-badge-primary mb-2">MediaTek</span>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ url('/drivers/mediatek-vcom-driver') }}" class="text-body text-decoration-none">
                            MediaTek MTK USB VCOM Driver
                        </a>
                    </h5>
                    <p class="small text-muted mb-3">Preloader VCOM driver for flashing MTK devices with SP Flash Tool on Windows 11/10.</p>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 small">
                        <span class="text-muted">Version: 3.0.1</span>
                        <a href="{{ url('/drivers/mediatek-vcom-driver') }}" class="btn btn-sm btn-outline-primary rounded-pill">Download &rarr;</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
