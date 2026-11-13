@extends('layouts.public')

@section('title', 'Mobile Flash Files & Scatter Firmware — Mobile Resource Hub')
@section('meta_description', 'Download verified scatter files, raw program files, emergency EDL flash files, and pac files for MediaTek, Qualcomm, and Spreadtrum devices.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Flash Files</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Flash File Resource Hub</h1>
            <p class="text-muted small mb-0">Unbrick packages, rawprogram XMLs, MTK scatter files, and service pac files</p>
        </div>
        <form action="{{ url('/flash-files') }}" method="GET" class="d-flex gap-2" style="max-width: 320px;">
            <input type="search" name="search" class="form-control form-control-sm" placeholder="Chipset or device..." value="{{ $search }}">
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="mrh-card p-4">
                <span class="mrh-badge mrh-badge-warning mb-2"><i class="bi bi-file-earmark-zip"></i> MTK Scatter File</span>
                <h5 class="fw-bold mb-2">
                    <a href="{{ url('/flash-files/redmi-note-12-4g-scatter-rom') }}" class="text-body text-decoration-none">
                        Redmi Note 12 4G (topaz) Scatter ROM
                    </a>
                </h5>
                <p class="small text-muted mb-3">
                    MediaTek MT6785 scatter partition XML tested for SP Flash Tool unbricking.
                </p>
                <div class="d-flex justify-content-between align-items-center border-top pt-3 small text-muted">
                    <span>Size: 4.8 GB</span>
                    <a href="{{ url('/flash-files/redmi-note-12-4g-scatter-rom') }}" class="btn btn-sm btn-outline-primary rounded-pill">Details &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="mrh-card p-4">
                <span class="mrh-badge mrh-badge-warning mb-2"><i class="bi bi-file-earmark-zip"></i> Qualcomm EDL File</span>
                <h5 class="fw-bold mb-2">
                    <a href="{{ url('/flash-files/poco-f5-qualcomm-edl-unbrick') }}" class="text-body text-decoration-none">
                        POCO F5 (marble) EDL 9008 Flash File
                    </a>
                </h5>
                <p class="small text-muted mb-3">
                    Qualcomm SM7475 rawprogram0.xml and patch0.xml for QFIL flashing.
                </p>
                <div class="d-flex justify-content-between align-items-center border-top pt-3 small text-muted">
                    <span>Size: 6.2 GB</span>
                    <a href="{{ url('/flash-files/poco-f5-qualcomm-edl-unbrick') }}" class="btn btn-sm btn-outline-primary rounded-pill">Details &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
