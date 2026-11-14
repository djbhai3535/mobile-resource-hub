@extends('layouts.public')

@section('title', 'About Us — Mobile Resource Hub')
@section('meta_description', 'Learn about Mobile Resource Hub, our mission, technical verification standards, and mobile technology catalog.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">About Us</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="mrh-card p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="brand-icon-box" style="width: 54px; height: 54px; font-size: 1.8rem;">
                        <i class="bi bi-phone-flip"></i>
                    </div>
                    <div>
                        <h1 class="h2 fw-bold mb-0">About Mobile Resource Hub</h1>
                        <span class="text-muted small">Open, verified, and community-oriented mobile catalog</span>
                    </div>
                </div>

                <div class="content-body text-body" style="line-height: 1.8;">
                    @if(isset($page) && $page->content)
                        {!! $page->content !!}
                    @else
                    <p class="lead text-muted">
                        <strong>Mobile Resource Hub</strong> was founded with a single objective: to provide technicians, developers, and mobile technology enthusiasts with an accurate, searchable, and dependable database of smartphone hardware specifications, official stock firmware builds, USB drivers, and repair tutorials.
                    </p>

                    <h2 class="h4 fw-bold mt-4 mb-3">Our Core Principles</h2>
                    <ul>
                        <li><strong>Cryptographic Verification:</strong> Whenever possible, firmware and driver packages are published alongside MD5 and SHA-256 hashes to guarantee data integrity before flashing.</li>
                        <li><strong>Transparent Sources:</strong> We clearly distinguish between official vendor distributions, community mirrors, and third-party tools.</li>
                        <li><strong>Safe & Clean Delivery:</strong> We strictly avoid deceptive download links, bundled malware, and obstructive popups.</li>
                    </ul>

                    <h2 class="h4 fw-bold mt-4 mb-3">Editorial & Technical Standards</h2>
                    <p>
                        Our editorial team cross-references manufacturer technical whitepapers, FCC filings, and verified device benchmarks to ensure our specification sheets represent accurate real-world hardware rather than marketing assumptions.
                    </p>

                    <div class="mt-4 p-4 bg-body-tertiary border rounded-3">
                        <h5 class="fw-bold mb-2">Have questions or found an error?</h5>
                        <p class="small text-muted mb-3">
                            We actively maintain our catalog. If you spot an inaccuracy or wish to request a firmware archive, please contact our team.
                        </p>
                        <a href="{{ url('/contact-us') }}" class="btn btn-primary btn-sm rounded-pill px-4">Contact Our Team</a>
                    </div>
                    @endif
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
