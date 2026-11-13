@extends('layouts.public')

@section('title', 'Mobile Flashing Software & Service Tools — Mobile Resource Hub')
@section('meta_description', 'Download verified smartphone repair software, Odin, SP Flash Tool, Qualcomm QFIL, Mi Flash Tool, and Fastboot utilities.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Software</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Mobile Service Software & Utilities</h1>
            <p class="text-muted small mb-0">Official flashing utilities, EDL repair tools, and ADB toolkits</p>
        </div>
        <form action="{{ url('/software') }}" method="GET" class="d-flex gap-2" style="max-width: 320px;">
            <input type="search" name="search" class="form-control form-control-sm" placeholder="Tool name..." value="{{ $search }}">
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
        </form>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="mrh-card p-4">
                <span class="mrh-badge mrh-badge-primary mb-2">Samsung Utility</span>
                <h5 class="fw-bold mb-2">
                    <a href="{{ url('/software/odin-3-14') }}" class="text-body text-decoration-none">
                        Samsung Odin 3.14.4
                    </a>
                </h5>
                <p class="small text-muted mb-3">Official Windows flashing utility for Samsung Galaxy devices with support for Android 10 through 15.</p>
                <div class="d-flex justify-content-between align-items-center border-top pt-3 small">
                    <span class="text-muted">OS: Windows 11/10/8/7</span>
                    <a href="{{ url('/software/odin-3-14') }}" class="btn btn-sm btn-outline-primary rounded-pill">Details &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="mrh-card p-4">
                <span class="mrh-badge mrh-badge-primary mb-2">MediaTek Tool</span>
                <h5 class="fw-bold mb-2">
                    <a href="{{ url('/software/sp-flash-tool') }}" class="text-body text-decoration-none">
                        SP Flash Tool (v5.2216)
                    </a>
                </h5>
                <p class="small text-muted mb-3">MediaTek smartphone firmware flashing utility with scatter file loading and memory read/write.</p>
                <div class="d-flex justify-content-between align-items-center border-top pt-3 small">
                    <span class="text-muted">OS: Windows & Linux</span>
                    <a href="{{ url('/software/sp-flash-tool') }}" class="btn btn-sm btn-outline-primary rounded-pill">Details &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="mrh-card p-4">
                <span class="mrh-badge mrh-badge-primary mb-2">Qualcomm Utility</span>
                <h5 class="fw-bold mb-2">
                    <a href="{{ url('/software/qfil-tool') }}" class="text-body text-decoration-none">
                        Qualcomm Flash Image Loader (QFIL)
                    </a>
                </h5>
                <p class="small text-muted mb-3">Qualcomm QDLoader 9008 emergency download mode unbrick and firmware restoration tool.</p>
                <div class="d-flex justify-content-between align-items-center border-top pt-3 small">
                    <span class="text-muted">OS: Windows 11/10</span>
                    <a href="{{ url('/software/qfil-tool') }}" class="btn btn-sm btn-outline-primary rounded-pill">Details &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
