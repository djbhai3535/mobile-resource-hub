@extends('layouts.public')

@section('title', 'Redmi Note 12 4G Scatter ROM — Mobile Resource Hub')
@section('meta_description', 'Download verified MTK scatter file and unbrick firmware for Redmi Note 12 4G (topaz). Verified with MD5 and SHA-256.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/flash-files') }}">Flash Files</a></li>
            <li class="breadcrumb-item active" aria-current="page">Redmi Note 12 4G Scatter ROM</li>
        </ol>
    </nav>

    <div class="mrh-card p-4">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="mrh-badge mrh-badge-warning"><i class="bi bi-file-earmark-zip"></i> Scatter Package</span>
            <span class="mrh-badge mrh-badge-success"><i class="bi bi-shield-check"></i> Verified MediaTek Partition</span>
        </div>
        <h1 class="h3 fw-bold mb-1">Redmi Note 12 4G (topaz) Official Scatter File</h1>
        <p class="text-muted small">Chipset: MediaTek MT6785 &bull; File: <code>MT6785_Android_scatter.txt</code></p>

        <hr class="my-3">

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped small">
                <tbody>
                    <tr>
                        <th style="width: 30%;">Archive File Name</th>
                        <td><code>Redmi_Note_12_4G_topaz_MT6785_Scatter.zip</code></td>
                    </tr>
                    <tr>
                        <th>Archive Size</th>
                        <td>4,831,838,208 bytes (approx. 4.8 GB)</td>
                    </tr>
                    <tr>
                        <th>Flashing Tool</th>
                        <td>SP Flash Tool v5.2216 or higher</td>
                    </tr>
                    <tr>
                        <th>Driver Requirement</th>
                        <td>MediaTek MTK Preloader USB VCOM Driver</td>
                    </tr>
                    <tr>
                        <th>SHA-256 Checksum</th>
                        <td><code>c1dfab389a997d91ec08d13264ec4529f5f0f353f47e307738f7127110e54911</code></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info small">
            <i class="bi bi-info-circle me-1"></i> In compliance with distribution guidelines, binary packages are delivered through authorized links with checksum matching.
        </div>

        <a href="https://example.com/download-placeholder" target="_blank" rel="noopener nofollow" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-download me-1"></i> Proceed to Authorized Download
        </a>
    </div>
</div>
@endsection
