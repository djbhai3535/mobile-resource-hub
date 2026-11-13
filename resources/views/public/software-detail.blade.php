@extends('layouts.public')

@section('title', 'Samsung Odin 3.14.4 Download & Guide — Mobile Resource Hub')
@section('meta_description', 'Download official Samsung Odin 3.14.4 flashing tool for Windows. Complete with installation instructions and virus-scanned checksums.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/software') }}">Software</a></li>
            <li class="breadcrumb-item active" aria-current="page">Samsung Odin 3.14.4</li>
        </ol>
    </nav>

    <div class="mrh-card p-4">
        <span class="mrh-badge mrh-badge-primary mb-2">Flashing Utility</span>
        <h1 class="h3 fw-bold mb-1">Samsung Odin 3.14.4</h1>
        <p class="text-muted small">Developer: Samsung Electronics &bull; License: Freeware / Service Utility</p>

        <hr class="my-3">

        <h4 class="h5 fw-bold mb-3">Software Details</h4>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped small">
                <tbody>
                    <tr>
                        <th style="width: 30%;">Version</th>
                        <td>3.14.4 (Latest stable release)</td>
                    </tr>
                    <tr>
                        <th>Supported Operating Systems</th>
                        <td>Windows 11, Windows 10, Windows 8.1, Windows 7 (32-bit & 64-bit)</td>
                    </tr>
                    <tr>
                        <th>File Size</th>
                        <td>3.4 MB (Compressed ZIP)</td>
                    </tr>
                    <tr>
                        <th>SHA-256 Checksum</th>
                        <td><code>9f83a48e89f1b98a3e721bc683416a9a3b83981b89efbc9901178c558b9911e3</code></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a href="https://example.com/download-placeholder" target="_blank" rel="noopener nofollow" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-download me-1"></i> Download Odin 3.14.4
        </a>
    </div>
</div>
@endsection
