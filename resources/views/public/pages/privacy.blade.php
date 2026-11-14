@extends('layouts.public')

@section('title', 'Privacy Policy — Mobile Resource Hub')
@section('meta_description', 'Mobile Resource Hub Privacy Policy: Read about our data protection standards, cookie policies, and visitor confidentiality.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="mrh-card p-4 p-md-5">
                <h1 class="h2 fw-bold mb-2">Privacy Policy</h1>
                <div class="text-muted small mb-4">Effective Date: 13 November 2026</div>

                <div class="content-body" style="line-height: 1.8;">
                    @if(isset($page) && $page->content)
                        {!! $page->content !!}
                    @else
                    <p>
                        At <strong>Mobile Resource Hub</strong>, visitor privacy is a paramount concern. This Privacy Policy outlines what information we collect, how it is handled, and your rights concerning your personal data.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">1. No Public User Accounts</h2>
                    <p>
                        Public visitors do not create user accounts, profiles, or public passwords. You can browse all public device specifications, tutorials, and download listings freely without registering.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">2. Information We Collect</h2>
                    <ul>
                        <li><strong>Contact Inquiries:</strong> When you submit a message through our Contact Us form, we collect your name, email address, subject, and message content solely to respond to your inquiry.</li>
                        <li><strong>Log & Analytics Data:</strong> In accordance with standard web server practices, our servers log anonymous request data including page URLs, timestamps, browser types, and truncated, privacy-safe IP hashes to prevent abuse and measure catalog popularity.</li>
                    </ul>

                    <h2 class="h5 fw-bold mt-4 mb-2">3. Cookies & Local Storage</h2>
                    <p>
                        We use minimal local storage exclusively to remember your preferred UI appearance (Light or Dark theme) so you enjoy a consistent reading experience across sessions.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">4. Third-Party Links</h2>
                    <p>
                        Our website contains outbound links to official device manufacturers, software publishers, and external download mirrors. We do not control or endorse the privacy practices of external websites.
                    </p>

                    <h2 class="h5 fw-bold mt-4 mb-2">5. Contact Information</h2>
                    <p>
                        If you have questions regarding this Privacy Policy or wish to request data deletion, please reach out via our <a href="{{ url('/contact-us') }}">Contact Page</a>.
                    </p>
                    @endif
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
