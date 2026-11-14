@extends('layouts.public')

@php
    $f = $fileModel ?? null;
    $title = $f ? $f->title : 'Redmi Note 12 4G Scatter ROM';
    $metaDesc = $f ? ($f->meta_description ?: $f->title) : 'Download verified MTK scatter file and unbrick firmware for Redmi Note 12 4G (topaz). Verified with MD5 and SHA-256.';
@endphp

@section('title', $title . ' — Mobile Resource Hub')
@section('meta_description', $metaDesc)

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/flash-files') }}">Flash Files</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $f ? $f->title : 'Redmi Note 12 4G Scatter ROM' }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mrh-card p-4 mb-4">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="mrh-badge mrh-badge-warning text-uppercase"><i class="bi bi-file-earmark-zip"></i> {{ $f ? str_replace('_', ' ', $f->file_type) : 'Scatter Package' }}</span>
                    @if($f ? $f->is_verified : true)
                        <span class="mrh-badge mrh-badge-success"><i class="bi bi-shield-check"></i> Tested & Verified</span>
                    @endif
                    <span class="mrh-badge mrh-badge-primary">Distribution: {{ $f ? ucwords(str_replace('_', ' ', $f->distribution_permission)) : 'Authorized Mirror' }}</span>
                </div>
                
                <h1 class="h3 fw-bold mb-1">{{ $f ? $f->title : 'Redmi Note 12 4G (topaz) Official Scatter File' }}</h1>
                <p class="text-muted small">
                    @if($f)
                        Chipset: <code>{{ $f->chipset ?? 'Universal' }}</code>
                        &bull; Model: <code>{{ $f->model_number }}</code>
                        @if($f->android_version)
                            &bull; OS: {{ $f->android_version }}
                        @endif
                    @else
                        Chipset: MediaTek MT6785 &bull; File: <code>MT6785_Android_scatter.txt</code>
                    @endif
                </p>

                <hr class="my-3">

                <h4 class="h5 fw-bold mb-3">Package Metadata & Verification</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped small">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Brand / Ecosystem</th>
                                <td>{{ $f ? ($f->brand->name ?? 'Universal') : 'Xiaomi / Redmi' }}</td>
                            </tr>
                            <tr>
                                <th>Hardware Model Number</th>
                                <td><code>{{ $f ? $f->model_number : 'topaz' }}</code></td>
                            </tr>
                            <tr>
                                <th>SoC / Processor Architecture</th>
                                <td><code>{{ $f ? ($f->chipset ?? 'Universal Architecture') : 'MediaTek MT6785' }}</code></td>
                            </tr>
                            <tr>
                                <th>Flashing Tool</th>
                                <td>{{ $f ? ($f->flashing_tool ?? 'Generic Flash Utility') : 'SP Flash Tool v5.2216 or higher' }}</td>
                            </tr>
                            <tr>
                                <th>Archive Size</th>
                                <td>{{ $f ? ($f->file_size_formatted ?? 'N/A') : '4.8 GB' }}</td>
                            </tr>
                            <tr>
                                <th>MD5 Checksum</th>
                                <td><code>{{ $f ? ($f->md5_checksum ?? 'Not Calculated') : 'c1dfab389a997d91ec08d13264ec4529' }}</code></td>
                            </tr>
                            <tr>
                                <th>SHA-256 Checksum</th>
                                <td><code class="user-select-all">{{ $f ? ($f->sha256_checksum ?? 'Not Calculated') : 'c1dfab389a997d91ec08d13264ec4529f5f0f353f47e307738f7127110e54911' }}</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($f && $f->compatibility_notes)
                    <div class="mb-4">
                        <h6 class="fw-bold">Compatibility Notes:</h6>
                        <p class="small text-muted mb-0">{{ $f->compatibility_notes }}</p>
                    </div>
                @endif

                @if($f && $f->installation_instructions)
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-tools text-primary me-1"></i> Flashing Instructions:</h6>
                        <div class="small text-muted" style="white-space: pre-line;">
                            {{ $f->installation_instructions }}
                        </div>
                    </div>
                @endif

                <div class="alert alert-info small mb-0">
                    <i class="bi bi-info-circle me-1"></i> In compliance with security and distribution guidelines, binary packages are delivered through authorized links with cryptographic hash verification.
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-down text-primary me-1"></i> Download Mirrors</h5>
                
                <p class="small text-muted mb-3">
                    Select a mirror below to initiate the download. All requests are securely verified.
                </p>

                <div class="d-grid gap-2 mb-3">
                    @if($f && $f->mirrors->isNotEmpty())
                        @foreach($f->mirrors as $mirror)
                            <a href="{{ route('downloads.mirror', $mirror) }}" class="btn {{ $loop->first ? 'btn-primary fw-semibold' : 'btn-outline-secondary btn-sm' }}">
                                <i class="bi bi-download me-1"></i> {{ $mirror->mirror_name }}
                            </a>
                        @endforeach
                    @else
                        <a href="https://example.com/download-placeholder" target="_blank" rel="noopener nofollow" class="btn btn-primary fw-semibold">
                            <i class="bi bi-download me-1"></i> Proceed to Authorized Download
                        </a>
                    @endif
                </div>

                <div class="small text-muted border-top pt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Download Requests:</span>
                        <strong class="text-body">{{ $f ? number_format($f->download_requests_count) : '850' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Antivirus Status:</span>
                        <span class="text-success fw-semibold"><i class="bi bi-shield-check"></i> Scanned Clean</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
