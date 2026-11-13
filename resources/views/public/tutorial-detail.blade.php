@extends('layouts.public')

@section('title', 'How to Flash Official Samsung Firmware Using Odin 3.14 — Mobile Resource Hub')
@section('meta_description', 'Complete step-by-step tutorial on flashing stock Samsung firmware using Odin 3.14.4 without tripping KNOX or bootlooping.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/tutorials') }}">Tutorials</a></li>
            <li class="breadcrumb-item active" aria-current="page">Flash Samsung Firmware with Odin</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="mrh-card p-4 p-md-5">
                <span class="mrh-badge mrh-badge-success mb-2">Repair Guide</span>
                <h1 class="h2 fw-extrabold mb-3">How to Flash Official Samsung Firmware Using Odin 3.14</h1>
                <div class="text-muted small mb-4">
                    Updated: 10 Nov 2026 &bull; Reading Time: 6 min read &bull; Complexity: Intermediate
                </div>

                <div class="alert alert-warning small mb-4">
                    <i class="bi bi-shield-exclamation me-1"></i> <strong>Important:</strong> Flashing with <code>CSC_...</code> will perform a factory wipe. To retain personal data, flash with <code>HOME_CSC_...</code> instead.
                </div>

                <div class="content-body" style="line-height: 1.8;">
                    <h2 class="h4 fw-bold mt-4 mb-3">1. Prerequisites</h2>
                    <ul>
                        <li>PC running Windows 11, 10, or 7.</li>
                        <li>Original high-speed USB Type-C cable.</li>
                        <li>Installed <a href="{{ url('/drivers/samsung-usb-driver') }}" class="fw-bold">Samsung USB Drivers</a>.</li>
                        <li>Latest <a href="{{ url('/software/odin-3-14') }}" class="fw-bold">Odin 3.14.4 utility</a>.</li>
                        <li>Official stock firmware zip for your exact model number from the <a href="{{ url('/firmware') }}" class="fw-bold">Firmware Hub</a>.</li>
                    </ul>

                    <h2 class="h4 fw-bold mt-4 mb-3">2. Step-by-Step Procedure</h2>
                    <p>Follow each step carefully to ensure successful flashing:</p>
                    <ol>
                        <li>Extract the firmware zip package on your computer. You will see files starting with <code>AP_</code>, <code>BL_</code>, <code>CP_</code>, and <code>CSC_</code>.</li>
                        <li>Run <strong>Odin3 v3.14.4.exe</strong> as Administrator.</li>
                        <li>Click on the <strong>BL</strong> button and select the <code>BL_...</code> file.</li>
                        <li>Click on the <strong>AP</strong> button and select the <code>AP_...</code> file (allow a minute for verification).</li>
                        <li>Click on the <strong>CP</strong> button and select the <code>CP_...</code> file.</li>
                        <li>Click on the <strong>CSC</strong> button and choose <code>CSC_...</code> for clean install or <code>HOME_CSC_...</code> to preserve user files.</li>
                        <li>Put the phone into <strong>Download Mode</strong> (power off, hold Volume Up + Volume Down and connect to PC). Press Volume Up to confirm.</li>
                        <li>When Odin detects the device, the <code>ID:COM</code> box lights up blue.</li>
                        <li>Click <strong>Start</strong>. The flash will take approximately 3-5 minutes.</li>
                    </ol>

                    <div class="alert alert-success mt-4">
                        <i class="bi bi-check-circle-fill me-1"></i> Once completed, Odin will display a green <strong>PASS!</strong> box, and the phone will automatically reboot into the freshly installed system.
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
