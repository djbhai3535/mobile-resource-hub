@extends('layouts.public')

@section('title', 'Mobile Flashing & Repair Tutorials — Mobile Resource Hub')
@section('meta_description', 'Step-by-step guides for flashing Samsung Odin firmware, unbricking Qualcomm devices via EDL 9008, and repairing MediaTek devices.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tutorials</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Flashing & Repair Tutorials</h1>
        <p class="text-muted small mb-0">Expert repair walkthroughs, safety disclosures, and error resolutions</p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-6">
            <div class="mrh-card p-4">
                <span class="mrh-badge mrh-badge-success mb-2">Samsung Flashing</span>
                <h4 class="fw-bold mb-2">
                    <a href="{{ url('/tutorials/how-to-flash-samsung-firmware-using-odin') }}" class="text-body text-decoration-none">
                        How to Flash Official Samsung Firmware Using Odin 3.14
                    </a>
                </h4>
                <p class="small text-muted mb-3">
                    A comprehensive beginner-to-advanced walkthrough on extracting AP, BL, CP, and CSC binaries and executing a clean factory flash.
                </p>
                <a href="{{ url('/tutorials/how-to-flash-samsung-firmware-using-odin') }}" class="text-primary fw-semibold small text-decoration-none">
                    Read Tutorial &rarr;
                </a>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="mrh-card p-4">
                <span class="mrh-badge mrh-badge-warning mb-2">Qualcomm Unbrick</span>
                <h4 class="fw-bold mb-2">
                    <a href="{{ url('/tutorials/fix-qualcomm-hs-usb-qdloader-9008-edl-brick') }}" class="text-body text-decoration-none">
                        Fix Qualcomm HS-USB QDLoader 9008 EDL Brick Step-by-Step
                    </a>
                </h4>
                <p class="small text-muted mb-3">
                    Recover completely hard-bricked devices stuck in 9008 mode using QFIL, test points, and verified rawprogram XML files.
                </p>
                <a href="{{ url('/tutorials/fix-qualcomm-hs-usb-qdloader-9008-edl-brick') }}" class="text-primary fw-semibold small text-decoration-none">
                    Read Tutorial &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
