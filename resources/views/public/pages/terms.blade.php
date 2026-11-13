@extends('layouts.public')

@section('title', 'Terms and Conditions — Mobile Resource Hub')
@section('meta_description', 'Mobile Resource Hub Terms and Conditions: Acceptable use, firmware disclaimers, and intellectual property.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Terms and Conditions</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="mrh-card p-4 p-md-5">
                <h1 class="h2 fw-bold mb-2">Terms and Conditions</h1>
                <div class="text-muted small mb-4">Effective Date: 13 November 2026</div>

                <div class="content-body" style="line-height: 1.8;">
                    <p>
                        Welcome to <strong>Mobile Resource Hub</strong>. By accessing or using our website, you agree to comply with and be bound by the following terms of use.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">1. Technical Information & Hardware Disclaimer</h2>
                    <p>
                        All specifications, technical sheets, firmware guides, and driver references are provided for educational and repair reference purposes only. Flashing firmware, unlocking bootloaders, or modifying device partitions carries inherent hardware risk. Mobile Resource Hub is not liable for data loss, device malfunctions, or voided warranties resulting from your use of the resources indexed here.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">2. Intellectual Property & Trademarks</h2>
                    <p>
                        All company names, logos, brand names, and product trademarks mentioned on this website (such as Samsung, Apple, Xiaomi, Qualcomm, MediaTek) belong to their respective registered owners. Their mention on this site is purely descriptive to identify hardware compatibility and does not imply affiliation or endorsement.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">3. Acceptable Use</h2>
                    <p>
                        You agree not to engage in malicious activities against this website, including denial-of-service attacks, automated high-frequency scraping that degrades server performance, or injecting malicious scripts.
                    </p>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
