@extends('layouts.public')

@section('title', ($device['name'] ?? 'Device') . ' Full Specifications & Resources — Mobile Resource Hub')
@section('meta_description', 'Full technical specifications for ' . ($device['name'] ?? 'Device') . ' (' . ($device['model'] ?? '') . '), including display, chipset, camera, battery, compatible firmware, and USB drivers.')

@push('scripts_head')
<script type="application/ld+json">
{
    "{{ '@' }}context": "https://schema.org",
    "@type": "Product",
    "name": "{{ addslashes($device['name'] ?? 'Device') }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ addslashes($device['brand'] ?? 'Mobile') }}"
    },
    "model": "{{ addslashes($device['model'] ?? '') }}",
    "description": "{{ addslashes($device['meta_description'] ?? (($device['name'] ?? 'Smartphone') . ' full technical specifications, firmware, and USB drivers.')) }}",
    "url": "{{ url()->current() }}",
    "category": "Smartphone"
}
</script>
@endpush

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/mobile-phones') }}">Mobile Phones</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/brands/' . strtolower($device['brand'])) }}">{{ $device['brand'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $device['name'] }}</li>
        </ol>
    </nav>

    <!-- Device Header Banner -->
    <div class="mrh-card p-4 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-md-3 text-center">
                @if(!empty($device['featured_image']))
                    <div class="p-3 bg-white rounded border d-inline-block">
                        <img src="{{ asset('storage/' . $device['featured_image']) }}" alt="{{ $device['name'] }}" style="max-height: 180px; max-width: 100%; object-fit: contain;" loading="lazy">
                    </div>
                @else
                    <div class="brand-icon-box mx-auto" style="width: 100px; height: 100px; font-size: 3.5rem;">
                        <i class="bi bi-phone"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-9">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <a href="{{ url('/brands/' . strtolower($device['brand'])) }}" class="mrh-badge mrh-badge-primary text-decoration-none">
                        {{ $device['brand'] }}
                    </a>
                    <span class="mrh-badge mrh-badge-success"><i class="bi bi-patch-check-fill"></i> Verified Specs</span>
                    <span class="badge bg-secondary-subtle text-secondary border">Status: {{ $device['status'] }}</span>
                </div>
                <h1 class="h2 fw-bold mb-1">{{ $device['name'] }}</h1>
                <p class="text-muted mb-3">Model Identifiers: <code>{{ $device['model'] }}</code></p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="#specs-section" class="btn btn-sm btn-primary rounded-pill">
                        <i class="bi bi-sliders2 me-1"></i> Technical Specs
                    </a>
                    <a href="#firmware-section" class="btn btn-sm btn-outline-warning text-body rounded-pill">
                        <i class="bi bi-cpu me-1"></i> Firmware & ROMs
                    </a>
                    <a href="#drivers-section" class="btn btn-sm btn-outline-primary rounded-pill">
                        <i class="bi bi-usb-symbol me-1"></i> USB Drivers
                    </a>
                    <a href="#tutorials-section" class="btn btn-sm btn-outline-success text-body rounded-pill">
                        <i class="bi bi-journal-code me-1"></i> Guides
                    </a>
                </div>
            </div>
        </div>

        @if(!empty($device['image_gallery']) && count($device['image_gallery']) > 0)
        <div class="mt-4 pt-3 border-top">
            <h6 class="small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.05em;">Device Gallery</h6>
            <div class="d-flex flex-wrap gap-2">
                @foreach($device['image_gallery'] as $galleryImg)
                <div class="p-1 border rounded bg-white">
                    <img src="{{ asset('storage/' . $galleryImg) }}" alt="gallery render" style="height: 60px; object-fit: contain;" loading="lazy">
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="row g-4" id="specs-section">
        <!-- Main Specifications Column -->
        <div class="col-lg-8">
            <!-- Hardware Variants & Model Numbers if available -->
            @if(isset($deviceModel))
                @if($deviceModel->variants->isNotEmpty())
                <div class="mrh-card p-4 mb-4">
                    <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-memory text-primary"></i> Available Hardware Configurations
                    </h3>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Configuration</th>
                                    <th>RAM</th>
                                    <th>Storage</th>
                                    <th>Color Option</th>
                                    <th>Reference Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deviceModel->variants as $variant)
                                <tr>
                                    <td class="fw-semibold">{{ $variant->name }}</td>
                                    <td>{{ $variant->ram ?: '—' }}</td>
                                    <td>{{ $variant->internal_storage ?: '—' }}</td>
                                    <td>{{ $variant->color ?: '—' }}</td>
                                    <td class="small text-muted">{{ $variant->notes ?: '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endif

            <!-- Dynamic Relational Technical Specifications Sheet -->
            <div class="mrh-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h2 class="h4 fw-bold mb-0">Technical Specifications</h2>
                    <span class="badge bg-primary-subtle text-primary border small">Verified Data</span>
                </div>

                @if(isset($groupedSpecs) && !empty($groupedSpecs))
                    @foreach($groupedSpecs as $groupItem)
                    <div class="spec-group-header">
                        <i class="bi bi-check-circle text-primary me-1"></i> {{ $groupItem['group']->name }}
                    </div>
                    <table class="spec-table">
                        @foreach($groupItem['fields'] as $fItem)
                        <tr>
                            <td class="spec-label" style="width: 32%;">
                                {{ $fItem['field']->label }}
                                @if($fItem['field']->unit)
                                    <span class="text-muted fw-normal small">({{ $fItem['field']->unit }})</span>
                                @endif
                            </td>
                            <td class="spec-value">
                                {!! nl2br(e($fItem['value'])) !!}
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    @endforeach
                @else
                    <!-- Fallback Standard Static Sections -->
                    <!-- General -->
                    <div class="spec-group-header">
                        <i class="bi bi-info-circle text-primary"></i> General Information
                    </div>
                    <table class="spec-table">
                        <tr>
                            <td class="spec-label">Commercial Name</td>
                            <td class="spec-value"><strong>{{ $device['name'] }}</strong></td>
                        </tr>
                        <tr>
                            <td class="spec-label">Brand</td>
                            <td class="spec-value">{{ $device['brand'] }}</td>
                        </tr>
                        <tr>
                            <td class="spec-label">Model Numbers</td>
                            <td class="spec-value"><code>{{ $device['model'] }}</code></td>
                        </tr>
                        <tr>
                            <td class="spec-label">Announced Date</td>
                            <td class="spec-value">{{ $device['announced'] }}</td>
                        </tr>
                    </table>

                    <!-- Body -->
                    <div class="spec-group-header">
                        <i class="bi bi-aspect-ratio text-primary"></i> Body & Build
                    </div>
                    <table class="spec-table">
                        <tr>
                            <td class="spec-label">Dimensions</td>
                            <td class="spec-value">{{ $device['dimensions'] }}</td>
                        </tr>
                        <tr>
                            <td class="spec-label">Weight</td>
                            <td class="spec-value">{{ $device['weight'] }}</td>
                        </tr>
                        <tr>
                            <td class="spec-label">Materials</td>
                            <td class="spec-value">{{ $device['build'] }}</td>
                        </tr>
                        <tr>
                            <td class="spec-label">Ingress Protection</td>
                            <td class="spec-value"><span class="badge bg-success-subtle text-success">{{ $device['ip_rating'] }}</span></td>
                        </tr>
                    </table>

                    <!-- Display -->
                    <div class="spec-group-header">
                        <i class="bi bi-display text-primary"></i> Display
                    </div>
                    <table class="spec-table">
                        <tr>
                            <td class="spec-label">Display Technology</td>
                            <td class="spec-value">{{ $device['display_type'] }}</td>
                        </tr>
                        <tr>
                            <td class="spec-label">Diagonal Size</td>
                            <td class="spec-value">{{ $device['display_size'] }}</td>
                        </tr>
                        <tr>
                            <td class="spec-label">Resolution</td>
                            <td class="spec-value">{{ $device['resolution'] }}</td>
                        </tr>
                    </table>

                    <!-- Platform -->
                    <div class="spec-group-header">
                        <i class="bi bi-cpu text-primary"></i> Platform & Hardware
                    </div>
                    <table class="spec-table">
                        <tr>
                            <td class="spec-label">Operating System</td>
                            <td class="spec-value">{{ $device['os'] }}</td>
                        </tr>
                        <tr>
                            <td class="spec-label">Chipset (SoC)</td>
                            <td class="spec-value"><strong>{{ $device['chipset'] }}</strong></td>
                        </tr>
                    </table>

                    <!-- Battery -->
                    <div class="spec-group-header">
                        <i class="bi bi-battery-charging text-primary"></i> Battery & Power
                    </div>
                    <table class="spec-table">
                        <tr>
                            <td class="spec-label">Capacity & Charging</td>
                            <td class="spec-value">{{ $device['battery'] }}</td>
                        </tr>
                    </table>
                @endif
            </div>
        </div>

        <!-- Right Column: Compatible Resources & Related Devices -->
        <div class="col-lg-4">
            <!-- Related Devices from Brand -->
            @if(isset($relatedDevices) && $relatedDevices->isNotEmpty())
            <div class="mrh-card p-4 mb-4">
                <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-phone text-primary"></i>
                    <span>Other {{ $device['brand'] }} Devices</span>
                </h3>
                <div class="list-group list-group-flush">
                    @foreach($relatedDevices as $relDev)
                    <a href="{{ url('/mobile-phones/' . $relDev->slug) }}" class="list-group-item list-group-item-action px-0 py-2 border-0 d-flex align-items-center gap-3">
                        @if($relDev->featured_image)
                            <img src="{{ asset('storage/' . $relDev->featured_image) }}" alt="{{ $relDev->name }}" style="width: 36px; height: 36px; object-fit: contain;" loading="lazy">
                        @else
                            <div class="rounded border d-flex align-items-center justify-content-center bg-body-tertiary" style="width: 36px; height: 36px;">
                                <i class="bi bi-phone small text-muted"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-body small text-truncate">{{ $relDev->name }}</div>
                            <small class="text-muted text-capitalize">{{ $relDev->device_category }} &bull; {{ $relDev->market_status }}</small>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Compatible Firmware -->
            <div class="mrh-card p-4 mb-4" id="firmware-section">
                <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-cpu text-warning"></i>
                    <span>Compatible Firmware</span>
                </h3>
                @if(isset($relatedFirmwares) && $relatedFirmwares->isNotEmpty())
                    @foreach($relatedFirmwares as $rfw)
                        <div class="p-3 border rounded-3 mb-2 bg-body-tertiary">
                            <h6 class="fw-bold mb-1 small">
                                <a href="{{ route('firmware.show', $rfw->slug) }}" class="text-decoration-none">
                                    {{ $rfw->title }}
                                </a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="badge bg-warning-subtle text-warning font-monospace" style="font-size: 0.7rem;">{{ $rfw->model_number }} &bull; {{ $rfw->file_size_formatted }}</span>
                                <a href="{{ route('firmware.show', $rfw->slug) }}" class="small fw-semibold">View &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-3 border rounded-3 mb-2 bg-body-tertiary">
                        <h6 class="fw-bold mb-1 small">
                            <a href="{{ url('/firmware/galaxy-s24-ultra-sm-s928b-oneui7') }}" class="text-decoration-none">
                                {{ $device['brand'] }} Stock ROM Archive
                            </a>
                        </h6>
                        <span class="badge bg-warning-subtle text-warning">Official Builds</span>
                        <div class="mt-2 text-end">
                            <a href="{{ url('/firmware?search=' . urlencode($device['brand'])) }}" class="small fw-semibold">View ROMs &rarr;</a>
                        </div>
                    </div>
                @endif
                <a href="{{ url('/firmware?search=' . urlencode($device['brand'])) }}" class="btn btn-outline-warning text-body btn-sm w-100 rounded-pill">
                    Find All {{ $device['brand'] }} Firmware
                </a>
            </div>

            <!-- Compatible Flash Files & Service ROMs -->
            @if(isset($relatedFlashFiles) && $relatedFlashFiles->isNotEmpty())
            <div class="mrh-card p-4 mb-4" id="flash-files-section">
                <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-zip text-warning"></i>
                    <span>Flash Files & Repair Packages</span>
                </h3>
                @foreach($relatedFlashFiles as $rff)
                    <div class="p-3 border rounded-3 mb-2 bg-body-tertiary">
                        <h6 class="fw-bold mb-1 small">
                            <a href="{{ route('flash-files.show', $rff->slug) }}" class="text-decoration-none">
                                {{ $rff->title }}
                            </a>
                        </h6>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.7rem;">{{ $rff->model_number }}</span>
                            <a href="{{ route('flash-files.show', $rff->slug) }}" class="small fw-semibold">View &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Compatible USB Drivers -->
            <div class="mrh-card p-4 mb-4" id="drivers-section">
                <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-usb-symbol text-primary"></i>
                    <span>Official USB Drivers</span>
                </h3>
                @if(isset($relatedDrivers) && $relatedDrivers->isNotEmpty())
                    @foreach($relatedDrivers as $rdrv)
                        <div class="p-3 border rounded-3 mb-2 bg-body-tertiary">
                            <h6 class="fw-bold mb-1 small">
                                <a href="{{ route('drivers.show', $rdrv->slug) }}" class="text-decoration-none">
                                    {{ $rdrv->name }}
                                </a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="small text-muted font-monospace">{{ $rdrv->driver_version ?? 'Latest' }}</span>
                                <a href="{{ route('drivers.show', $rdrv->slug) }}" class="small fw-semibold">Download &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-3 border rounded-3 mb-2 bg-body-tertiary">
                        <h6 class="fw-bold mb-1 small">{{ $device['brand'] }} Mobile USB Driver</h6>
                        <span class="small text-muted">Windows ADB & Fastboot certified</span>
                        <div class="mt-2 text-end">
                            <a href="{{ url('/drivers') }}" class="small fw-semibold">Download Driver &rarr;</a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Repair & Flashing Guides -->
            <div class="mrh-card p-4" id="tutorials-section">
                <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-journal-code text-success"></i>
                    <span>Device Guides</span>
                </h3>
                <div class="p-3 border rounded-3 mb-2 bg-body-tertiary">
                    <h6 class="fw-bold mb-1 small">
                        <a href="{{ url('/tutorials/how-to-flash-samsung-firmware-using-odin') }}" class="text-decoration-none">
                            Firmware Flashing & Unbrick Tutorial
                        </a>
                    </h6>
                    <span class="small text-muted">Step-by-step flashing walkthrough</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
