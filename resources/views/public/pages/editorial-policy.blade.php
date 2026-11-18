@extends('layouts.public')

@section('title', 'Editorial & Content Policy — Mobile Resource Hub')
@section('meta_description', 'Mobile Resource Hub Editorial & Content Policy: Our verification methodology, source citations, corrections process, and independence standards.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editorial Policy</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="mrh-card p-4 p-md-5">
                <h1 class="h2 fw-bold mb-2">Editorial & Content Verification Policy</h1>
                <div class="text-muted small mb-4">Effective Date: 18 November 2026</div>

                <div class="content-body" style="line-height: 1.8;">
                    @if(isset($page) && $page->content)
                        {!! $page->content !!}
                    @else
                    <p class="lead text-muted">
                        At <strong>Mobile Resource Hub</strong>, we adhere to strict standards of technical accuracy, transparency, and integrity. Our mission is to provide technicians, developers, and mobile consumers with verified information.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">1. Specification Verification Standards</h2>
                    <p>
                        Every device specification page is verified against manufacturer technical whitepapers, regulatory filings (FCC, TENAA, BIS), and hands-on hardware testing. We do not publish speculative rumors without clearly labeling them as unconfirmed previews.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">2. Binary & Firmware Verification Policy</h2>
                    <p>
                        Firmware builds, scatter files, and USB driver archives undergo checksum auditing. We publish official MD5 and SHA-256 digests so users can independently verify the cryptographic hash of files prior to flashing. We explicitly reject modified, adulterated, or adware-injected binaries.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">3. Editorial Independence</h2>
                    <p>
                        Our evaluations, repair guides, and catalog entries are written independently. Commercial partnerships and advertisements do not dictate our editorial conclusions or safety evaluations. Sponsored placements, where present, are strictly delineated from technical editorial content.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">4. Corrections & Errata Reporting</h2>
                    <p>
                        We welcome feedback and corrections from the community. If you identify an erroneous specification field, an outdated build number, or a broken download mirror, please contact our team via the <a href="{{ url('/contact-us') }}">Contact Us</a> page. Corrections are audited and updated promptly.
                    </p>
                    @endif
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
