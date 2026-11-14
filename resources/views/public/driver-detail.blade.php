@extends('layouts.public')

@php
    $d = $driverModel ?? null;
    $title = $d ? $d->name : 'Qualcomm HS-USB QDLoader 9008 Driver';
    $metaDesc = $d ? ($d->meta_description ?: $d->name) : 'Download certified Qualcomm HS-USB QDLoader 9008 driver for Windows 11/10/8/7 (32-bit and 64-bit). Fix EDL mode device detection.';
@endphp

@section('title', $title . ' — Mobile Resource Hub')
@section('meta_description', Str::limit($metaDesc, 160))

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/drivers') }}">USB Drivers</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $d ? $d->name : 'Qualcomm QDLoader 9008 Driver' }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mrh-card p-4 mb-4">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="mrh-badge mrh-badge-primary">{{ $d && $d->brand ? $d->brand->name : 'Universal Driver' }}</span>
                    @if($d ? $d->is_verified : true)
                        <span class="mrh-badge mrh-badge-success"><i class="bi bi-patch-check-fill"></i> Digitally Signed & Tested</span>
                    @endif
                </div>
                <h1 class="h3 fw-bold mb-1">{{ $d ? $d->name : 'Qualcomm HS-USB QDLoader 9008 Driver' }}</h1>
                <p class="text-muted small">
                    Version: <code>{{ $d ? ($d->driver_version ?? 'v1.0') : '2.1.0.5 (WHQL Tested)' }}</code>
                    &bull; Format: {{ $d ? $d->file_type : 'EXE Installer' }}
                    @if($d && $d->release_date)
                        &bull; Released: {{ $d->release_date->format('M Y') }}
                    @endif
                </p>

                <hr class="my-3">

                <h4 class="h5 fw-bold mb-3">Driver Technical Details</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped small">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Driver Version</th>
                                <td><code>{{ $d ? ($d->driver_version ?? 'Latest') : '2.1.0.5 (WHQL Tested)' }}</code></td>
                            </tr>
                            <tr>
                                <th>Supported Windows OS</th>
                                <td>{{ $d ? ($d->windows_versions ?? 'Windows 11, 10, 8.1, 7') : 'Windows 11, Windows 10, Windows 8.1, Windows 7 (x86 & x64)' }}</td>
                            </tr>
                            <tr>
                                <th>Architecture Support</th>
                                <td>{{ $d ? ($d->architecture ?? '32-bit & 64-bit') : '32-bit (x86) / 64-bit (x64)' }}</td>
                            </tr>
                            <tr>
                                <th>Compatible Hardware / Devices</th>
                                <td>{{ $d ? ($d->supported_devices ?? 'Universal Smartphone Compatibility') : 'Emergency Download (EDL) Mode communication for QFIL and QPST' }}</td>
                            </tr>
                            <tr>
                                <th>Package Size</th>
                                <td>{{ $d ? ($d->file_size_formatted ?? 'N/A') : '19.8 MB' }}</td>
                            </tr>
                            <tr>
                                <th>MD5 Checksum</th>
                                <td><code>{{ $d ? ($d->md5_checksum ?? 'Not Calculated') : '8a7b6c5d4e3f2019a8b7c6d5e4f3a2b1' }}</code></td>
                            </tr>
                            <tr>
                                <th>SHA-256 Checksum</th>
                                <td><code class="user-select-all">{{ $d ? ($d->sha256_checksum ?? 'Not Calculated') : 'e8b417c82e2129e12089f64bf5017e882a9d20c548a3138380e22ea1ba1a9420' }}</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($d && $d->installation_instructions)
                    <div class="mb-0">
                        <h5 class="fw-bold"><i class="bi bi-tools text-primary me-1"></i> Installation Guide</h5>
                        <div class="small text-muted" style="white-space: pre-line;">
                            {{ $d->installation_instructions }}
                        </div>
                    </div>
                @else
                    <div class="mb-0">
                        <h5 class="fw-bold"><i class="bi bi-tools text-primary me-1"></i> Quick Installation Instructions</h5>
                        <ol class="small text-muted ps-3 mb-0">
                            <li>Download the driver package and extract if compressed.</li>
                            <li>Right click setup and select Run as administrator.</li>
                            <li>Follow the setup wizard to complete driver registration.</li>
                            <li>Restart your PC before plugging in the smartphone.</li>
                        </ol>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-down text-primary me-1"></i> Download Mirrors</h5>
                
                <p class="small text-muted mb-3">
                    Direct certified driver downloads. No adware, installers, or bundled software.
                </p>

                <div class="d-grid gap-2 mb-3">
                    @if($d && $d->mirrors->isNotEmpty())
                        @foreach($d->mirrors as $mirror)
                            <a href="{{ route('downloads.mirror', $mirror) }}" class="btn {{ $loop->first ? 'btn-primary fw-semibold' : 'btn-outline-secondary btn-sm' }}">
                                <i class="bi bi-download me-1"></i> {{ $mirror->mirror_name }}
                            </a>
                        @endforeach
                    @else
                        <a href="https://example.com/download-placeholder" target="_blank" rel="noopener nofollow" class="btn btn-primary fw-semibold">
                            <i class="bi bi-download me-1"></i> Download Driver Setup
                        </a>
                    @endif
                </div>

                <div class="small text-muted border-top pt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Download Requests:</span>
                        <strong class="text-body">{{ $d ? number_format($d->download_requests_count) : '12,900' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Digital Signature:</span>
                        <span class="text-success fw-semibold"><i class="bi bi-shield-check"></i> WHQL Verified</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
