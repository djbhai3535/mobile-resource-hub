@extends('layouts.public')

@section('title', 'Qualcomm HS-USB QDLoader 9008 Driver Download — Mobile Resource Hub')
@section('meta_description', 'Download certified Qualcomm HS-USB QDLoader 9008 driver for Windows 11/10/8/7 (32-bit and 64-bit). Fix EDL mode device detection.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/drivers') }}">USB Drivers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Qualcomm QDLoader 9008 Driver</li>
        </ol>
    </nav>

    <div class="mrh-card p-4">
        <span class="mrh-badge mrh-badge-primary mb-2">Qualcomm Driver</span>
        <h1 class="h3 fw-bold mb-1">Qualcomm HS-USB QDLoader 9008 Driver</h1>
        <p class="text-muted small">Developer: Qualcomm Technologies, Inc. &bull; License: Official Vendor Driver</p>

        <hr class="my-3">

        <h4 class="h5 fw-bold mb-3">Driver Details</h4>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped small">
                <tbody>
                    <tr>
                        <th style="width: 30%;">Driver Version</th>
                        <td>2.1.0.5 (WHQL Tested)</td>
                    </tr>
                    <tr>
                        <th>Supported OS</th>
                        <td>Windows 11, Windows 10, Windows 8.1, Windows 7 (x86 & x64)</td>
                    </tr>
                    <tr>
                        <th>Purpose</th>
                        <td>Emergency Download (EDL) Mode communication for QFIL and QPST</td>
                    </tr>
                    <tr>
                        <th>Archive Size</th>
                        <td>19.8 MB</td>
                    </tr>
                    <tr>
                        <th>SHA-256 Checksum</th>
                        <td><code>e8b417c82e2129e12089f64bf5017e882a9d20c548a3138380e22ea1ba1a9420</code></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a href="https://example.com/download-placeholder" target="_blank" rel="noopener nofollow" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-download me-1"></i> Download Driver Setup
        </a>
    </div>
</div>
@endsection
