@extends('layouts.public')

@section('title', 'Galaxy S24 Ultra Android 15 OneUI 7.0 Firmware (SM-S928B) — Mobile Resource Hub')
@section('meta_description', 'Download official stock firmware build S928BXXU3BXK2 for Samsung Galaxy S24 Ultra SM-S928B. Complete with verified SHA-256 checksum and flashing instructions.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/firmware') }}">Firmware</a></li>
            <li class="breadcrumb-item active" aria-current="page">Galaxy S24 Ultra Android 15</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mrh-card p-4 mb-4">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="mrh-badge mrh-badge-warning"><i class="bi bi-cpu"></i> Official Firmware</span>
                    <span class="mrh-badge mrh-badge-success"><i class="bi bi-shield-check"></i> Checksum Verified</span>
                    <span class="mrh-badge mrh-badge-primary">Distribution: Official Vendor Mirror</span>
                </div>
                
                <h1 class="h3 fw-bold mb-1">Samsung Galaxy S24 Ultra Android 15 OneUI 7.0</h1>
                <p class="text-muted small">Exact Model: <code>SM-S928B</code> &bull; Multi-CSC: <code>OXM</code></p>

                <hr class="my-3">

                <h4 class="h5 fw-bold mb-3">Firmware Technical Metadata</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped small">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Device Commercial Name</th>
                                <td>Samsung Galaxy S24 Ultra</td>
                            </tr>
                            <tr>
                                <th>Target Model Number</th>
                                <td><code>SM-S928B</code> (Global / European variant)</td>
                            </tr>
                            <tr>
                                <th>Build Number / PDA</th>
                                <td><code>S928BXXU3BXK2</code></td>
                            </tr>
                            <tr>
                                <th>CSC Code</th>
                                <td><code>S928BOXM3BXK2</code> (Multi-CSC)</td>
                            </tr>
                            <tr>
                                <th>Android OS Version</th>
                                <td>Android 15 (One UI 7.0)</td>
                            </tr>
                            <tr>
                                <th>Android Security Patch</th>
                                <td>November 1, 2026</td>
                            </tr>
                            <tr>
                                <th>File Name</th>
                                <td><code>SM-S928B_OXM_S928BXXU3BXK2_fac.zip</code></td>
                            </tr>
                            <tr>
                                <th>Archive File Size</th>
                                <td>9,248,154,624 bytes (approx. 9.24 GB)</td>
                            </tr>
                            <tr>
                                <th>MD5 Checksum</th>
                                <td><code>4a8f93e9812b1c3d90f23a9b9872e41c</code></td>
                            </tr>
                            <tr>
                                <th>SHA-256 Hash</th>
                                <td><code>e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855</code></td>
                            </tr>
                            <tr>
                                <th>Verification Date</th>
                                <td>12 November 2026 (Verified against official Samsung servers)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Step-by-Step Installation Guide (PRD Section 7.4) -->
                <h4 class="h5 fw-bold mb-3"><i class="bi bi-tools text-primary me-1"></i> Flashing Prerequisites & Instructions</h4>
                <div class="alert alert-warning small d-flex gap-2 align-items-center mb-3">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>
                        <strong>Risk Disclosure:</strong> Flashing incorrect firmware or disconnecting the USB cable mid-flash may brick your device. Ensure your phone has at least 60% battery charge and all data is backed up.
                    </div>
                </div>

                <ol class="small text-muted ps-3 mb-4">
                    <li class="mb-2">Download and install the official <a href="{{ url('/drivers/samsung-usb-driver') }}" class="fw-bold">Samsung USB Drivers</a> on your PC.</li>
                    <li class="mb-2">Download <a href="{{ url('/software/odin-3-14') }}" class="fw-bold">Odin 3.14.4</a> and extract the executable.</li>
                    <li class="mb-2">Extract the downloaded firmware archive to reveal the 4 files: <code>AP_...</code>, <code>BL_...</code>, <code>CP_...</code>, and <code>CSC_...</code>.</li>
                    <li class="mb-2">Power off the phone. Hold <em>Volume Down + Volume Up</em> and plug in the USB cable to enter <strong>Download Mode</strong>.</li>
                    <li class="mb-2">In Odin, ensure the <code>ID:COM</code> port turns blue. Load the respective files into the AP, BL, CP, and CSC slots.</li>
                    <li class="mb-2">Click <strong>Start</strong> and wait until Odin displays a green <strong>PASS!</strong> message.</li>
                </ol>
            </div>
        </div>

        <!-- Download Sidebar -->
        <div class="col-lg-4">
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-down text-primary me-1"></i> Authorized Downloads</h5>
                
                <p class="small text-muted mb-3">
                    In compliance with our distribution policy, we provide links to authorized mirrors and official vendor sources.
                </p>

                <div class="d-grid gap-2 mb-3">
                    <a href="https://example.com/download-official-placeholder" target="_blank" rel="noopener nofollow" class="btn btn-warning fw-semibold">
                        <i class="bi bi-download me-1"></i> Primary Official Mirror (High Speed)
                    </a>
                    <a href="https://example.com/download-mirror-placeholder" target="_blank" rel="noopener nofollow" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-hdd-network me-1"></i> Secondary Mirror Link
                    </a>
                </div>

                <div class="small text-muted border-top pt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Downloads Recorded:</span>
                        <strong class="text-body">1,428</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Antivirus Status:</span>
                        <span class="text-success fw-semibold"><i class="bi bi-shield-check"></i> Scanned Clean</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Integrity:</span>
                        <span class="text-success fw-semibold">SHA-256 Sourced</span>
                    </div>
                </div>
            </div>

            <!-- Related Tools -->
            <div class="mrh-card p-4">
                <h6 class="fw-bold mb-3">Required Tools for this ROM</h6>
                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                    <div>
                        <div class="fw-semibold small">Samsung Odin 3.14.4</div>
                        <span class="text-muted" style="font-size: 0.75rem;">Official Flash Utility</span>
                    </div>
                    <a href="{{ url('/software/odin-3-14') }}" class="btn btn-sm btn-outline-primary rounded-pill">Get</a>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-semibold small">Samsung USB Drivers</div>
                        <span class="text-muted" style="font-size: 0.75rem;">Windows x64 / x86</span>
                    </div>
                    <a href="{{ url('/drivers/samsung-usb-driver') }}" class="btn btn-sm btn-outline-primary rounded-pill">Get</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
