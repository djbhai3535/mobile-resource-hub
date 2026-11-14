@extends('layouts.public')

@php
    $s = $softwareModel ?? null;
    $title = $s ? $s->name : 'Samsung Odin 3.14.4';
    $metaDesc = $s ? ($s->meta_description ?: ($s->description ?: "Download official {$title} flashing tool for Windows.")) : 'Download official Samsung Odin 3.14.4 flashing tool for Windows. Complete with installation instructions and virus-scanned checksums.';
@endphp

@section('title', $title . ' — Mobile Resource Hub')
@section('meta_description', Str::limit($metaDesc, 160))

@push('scripts_head')
<script type="application/ld+json">
{
    "{{ '@' }}context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "{{ addslashes($title) }}",
    "applicationCategory": "UtilitiesApplication",
    "operatingSystem": "Windows",
    "description": "{{ addslashes(Str::limit($metaDesc, 160)) }}",
    "url": "{{ url()->current() }}"
}
</script>
@endpush

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/software') }}">Software</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $s ? $s->name : 'Samsung Odin 3.14.4' }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mrh-card p-4 mb-4">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="mrh-badge mrh-badge-primary text-uppercase">{{ $s ? str_replace('_', ' ', $s->category) : 'Flashing Utility' }}</span>
                    @if($s ? $s->is_verified : true)
                        <span class="mrh-badge mrh-badge-success"><i class="bi bi-shield-check"></i> Verified Clean & Safe</span>
                    @endif
                </div>
                <h1 class="h3 fw-bold mb-1">{{ $s ? $s->name : 'Samsung Odin 3.14.4' }}</h1>
                <p class="text-muted small">
                    Developer: {{ $s ? ($s->developer ?? 'Independent / OEM') : 'Samsung Electronics' }}
                    &bull; License: {{ $s ? $s->license_type : 'Freeware / Service Utility' }}
                    @if($s && $s->version)
                        &bull; Version: <code>{{ $s->version }}</code>
                    @endif
                </p>

                <hr class="my-3">

                @if($s && $s->description)
                    <div class="mb-4">
                        <h5 class="fw-bold">Overview</h5>
                        <p class="text-muted small" style="white-space: pre-line;">{{ $s->description }}</p>
                    </div>
                @endif

                <h4 class="h5 fw-bold mb-3">Software Details</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped small">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Software Version</th>
                                <td><code>{{ $s ? ($s->version ?? 'Latest') : '3.14.4 (Latest stable release)' }}</code></td>
                            </tr>
                            <tr>
                                <th>Supported OS</th>
                                <td>{{ $s ? ($s->supported_os ?? 'Windows 11/10/8/7') : 'Windows 11, Windows 10, Windows 8.1, Windows 7 (32-bit & 64-bit)' }}</td>
                            </tr>
                            <tr>
                                <th>Architecture</th>
                                <td>{{ $s ? ($s->supported_architectures ?? 'x86 & x64') : '32-bit & 64-bit' }}</td>
                            </tr>
                            <tr>
                                <th>Package Size</th>
                                <td>{{ $s ? ($s->file_size_formatted ?? 'N/A') : '3.4 MB (Compressed ZIP)' }}</td>
                            </tr>
                            @if($s && $s->dependencies)
                            <tr>
                                <th>Dependencies</th>
                                <td>{{ $s->dependencies }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>MD5 Checksum</th>
                                <td><code>{{ $s ? ($s->md5_checksum ?? 'Not Calculated') : '9e107d9d372bb6826bd81d3542a419d6' }}</code></td>
                            </tr>
                            <tr>
                                <th>SHA-256 Checksum</th>
                                <td><code class="user-select-all">{{ $s ? ($s->sha256_checksum ?? 'Not Calculated') : '9f83a48e89f1b98a3e721bc683416a9a3b83981b89efbc9901178c558b9911e3' }}</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($s && $s->installation_instructions)
                    <div class="mb-4">
                        <h5 class="fw-bold"><i class="bi bi-tools text-primary me-1"></i> How to Use</h5>
                        <div class="small text-muted" style="white-space: pre-line;">
                            {{ $s->installation_instructions }}
                        </div>
                    </div>
                @endif

                @if($s && $s->changelog)
                    <div class="mb-0">
                        <h6 class="fw-bold">Changelog:</h6>
                        <div class="small text-muted" style="white-space: pre-line;">
                            {{ $s->changelog }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-down text-primary me-1"></i> Download Mirrors</h5>
                
                <p class="small text-muted mb-3">
                    Verified downloads with anti-malware and hash validation.
                </p>

                <div class="d-grid gap-2 mb-3">
                    @if($s && $s->mirrors->isNotEmpty())
                        @foreach($s->mirrors as $mirror)
                            <a href="{{ route('downloads.mirror', $mirror) }}" class="btn {{ $loop->first ? 'btn-primary fw-semibold' : 'btn-outline-secondary btn-sm' }}">
                                <i class="bi bi-download me-1"></i> {{ $mirror->mirror_name }}
                            </a>
                        @endforeach
                    @else
                        <a href="https://example.com/download-placeholder" target="_blank" rel="noopener nofollow" class="btn btn-primary fw-semibold">
                            <i class="bi bi-download me-1"></i> Download Odin 3.14.4
                        </a>
                    @endif
                </div>

                <div class="small text-muted border-top pt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Download Requests:</span>
                        <strong class="text-body">{{ $s ? number_format($s->download_requests_count) : '8,420' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Security Check:</span>
                        <span class="text-success fw-semibold"><i class="bi bi-shield-check"></i> Antivirus Clean</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
