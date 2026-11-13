@extends('layouts.public')

@section('title', $device['name'] . ' Full Specifications & Resources — Mobile Resource Hub')
@section('meta_description', 'Full technical specifications for ' . $device['name'] . ' (' . $device['model'] . '), including display, chipset, camera, battery, compatible firmware, and USB drivers.')

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
                <div class="brand-icon-box mx-auto" style="width: 100px; height: 100px; font-size: 3.5rem;">
                    <i class="bi bi-phone"></i>
                </div>
            </div>
            <div class="col-md-9">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="mrh-badge mrh-badge-primary">{{ $device['brand'] }}</span>
                    <span class="mrh-badge mrh-badge-success"><i class="bi bi-patch-check-fill"></i> Verified Specs</span>
                    <span class="text-muted small">Status: {{ $device['status'] }}</span>
                </div>
                <h1 class="h2 fw-bold mb-1">{{ $device['name'] }}</h1>
                <p class="text-muted mb-3">Model Code: <code>{{ $device['model'] }}</code></p>
                
                <div class="d-flex flex-wrap gap-2">
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
    </div>

    <div class="row g-4">
        <!-- Main Specifications Table (PRD Section 7.3) -->
        <div class="col-lg-8">
            <div class="mrh-card p-4">
                <h2 class="h4 fw-bold mb-3 border-bottom pb-2">Technical Specifications</h2>

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
                    <tr>
                        <td class="spec-label">CPU Architecture</td>
                        <td class="spec-value">{{ $device['cpu'] }}</td>
                    </tr>
                    <tr>
                        <td class="spec-label">GPU</td>
                        <td class="spec-value">{{ $device['gpu'] }}</td>
                    </tr>
                </table>

                <!-- Memory -->
                <div class="spec-group-header">
                    <i class="bi bi-memory text-primary"></i> Memory & Storage
                </div>
                <table class="spec-table">
                    <tr>
                        <td class="spec-label">Internal Storage Variants</td>
                        <td class="spec-value">{{ $device['internal_storage'] }}</td>
                    </tr>
                    <tr>
                        <td class="spec-label">Card Slot (Expandable)</td>
                        <td class="spec-value">{{ $device['card_slot'] }}</td>
                    </tr>
                </table>

                <!-- Camera -->
                <div class="spec-group-header">
                    <i class="bi bi-camera text-primary"></i> Camera Optics
                </div>
                <table class="spec-table">
                    <tr>
                        <td class="spec-label">Main Camera Modules</td>
                        <td class="spec-value">{{ $device['main_camera'] }}</td>
                    </tr>
                    <tr>
                        <td class="spec-label">Selfie Camera</td>
                        <td class="spec-value">{{ $device['selfie_camera'] }}</td>
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
            </div>
        </div>

        <!-- Right Column: Compatible Resources -->
        <div class="col-lg-4">
            <!-- Compatible Firmware -->
            <div class="mrh-card p-4 mb-4" id="firmware-section">
                <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-cpu text-warning"></i>
                    <span>Compatible Firmware</span>
                </h3>
                <div class="p-3 border rounded-3 mb-2 bg-body-tertiary">
                    <h6 class="fw-bold mb-1 small">
                        <a href="{{ url('/firmware/galaxy-s24-ultra-sm-s928b-oneui7') }}" class="text-decoration-none">
                            Samsung Galaxy S24 Ultra Android 15 OneUI 7.0
                        </a>
                    </h6>
                    <span class="badge bg-warning-subtle text-warning">Build S928BXXU3BXK2</span>
                    <div class="mt-2 text-end">
                        <a href="{{ url('/firmware/galaxy-s24-ultra-sm-s928b-oneui7') }}" class="small fw-semibold">View ROM &rarr;</a>
                    </div>
                </div>
                <a href="{{ url('/firmware?search=' . urlencode($device['model'])) }}" class="btn btn-outline-warning text-body btn-sm w-100 rounded-pill">
                    Find All {{ $device['brand'] }} Firmware
                </a>
            </div>

            <!-- Compatible USB Drivers -->
            <div class="mrh-card p-4 mb-4" id="drivers-section">
                <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-usb-symbol text-primary"></i>
                    <span>Official USB Drivers</span>
                </h3>
                <div class="p-3 border rounded-3 mb-2 bg-body-tertiary">
                    <h6 class="fw-bold mb-1 small">Samsung Android USB Driver v1.7.59</h6>
                    <span class="small text-muted">Windows ADB & Fastboot certified</span>
                    <div class="mt-2 text-end">
                        <a href="{{ url('/drivers/samsung-usb-driver') }}" class="small fw-semibold">Download Driver &rarr;</a>
                    </div>
                </div>
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
                            How to Flash Official Samsung Firmware Using Odin 3.14
                        </a>
                    </h6>
                    <span class="small text-muted">Step-by-step flashing walkthrough</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
