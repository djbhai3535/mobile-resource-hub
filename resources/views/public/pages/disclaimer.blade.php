@extends('layouts.public')

@section('title', 'Disclaimer — Mobile Resource Hub')
@section('meta_description', 'Mobile Resource Hub Technical Disclaimer: Hardware risk notices, firmware flashing prerequisites, trademark attributions, and warranty terms.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disclaimer</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="mrh-card p-4 p-md-5">
                <h1 class="h2 fw-bold mb-2">Technical Disclaimer & Risk Disclosure</h1>
                <div class="text-muted small mb-4">Effective Date: 18 November 2026</div>

                <div class="content-body" style="line-height: 1.8;">
                    @if(isset($page) && $page->content)
                        {!! $page->content !!}
                    @else
                    <p class="lead text-muted">
                        The resources, firmware files, USB drivers, flashing utilities, tutorials, and hardware specifications indexed on <strong>Mobile Resource Hub</strong> are compiled for technical reference, educational study, diagnostic recovery, and device maintenance purposes only.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">1. Firmware Flashing & Hardware Risks</h2>
                    <p>
                        Modifying smartphone firmware, installing custom or stock ROMs, flashing bootloader partitions, unlocking bootloaders, or invoking emergency download modes (such as Qualcomm EDL 9008 or MediaTek VCOM) carries inherent technical risks. Improper execution, power loss during writes, or mismatched model numbers can lead to temporary or permanent device bricking, voided vendor warranties, or complete data loss.
                    </p>
                    <p>
                        Users are strongly advised to back up all personal data, verify battery charge levels (minimum 60%), and check cryptographic hashes (MD5 and SHA-256) before attempting any flashing operation.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">2. Limitation of Liability</h2>
                    <p>
                        Mobile Resource Hub, its authors, and contributors expressly disclaim all warranties, express or implied. Under no circumstances shall Mobile Resource Hub be liable for any direct, indirect, incidental, special, or consequential damages resulting from the use or inability to use the files, instructions, or tools published on this website.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">3. Trademark Attributions</h2>
                    <p>
                        All product names, logos, manufacturer names, and registered trademarks (such as Samsung, Apple, Xiaomi, Google, Qualcomm, MediaTek, Odin, SP Flash Tool) mentioned on this website belong to their respective copyright and trademark holders. Reference to these brands does not constitute or imply endorsement, sponsorship, or affiliation.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">4. Third-Party Links & Mirrors</h2>
                    <p>
                        While we strive to index clean, authentic, and verified binaries, external mirrors and developer servers are operated independently. We encourage visitors to report any inactive or questionable download links through our <a href="{{ url('/contact-us') }}">Contact Us</a> portal.
                    </p>
                    @endif
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
