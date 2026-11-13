@extends('layouts.public')

@section('title', 'Contact Us — Mobile Resource Hub')
@section('meta_description', 'Contact the Mobile Resource Hub team for feedback, data corrections, firmware requests, or DMCA inquiries.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mrh-card p-4 p-md-5">
                <h1 class="h3 fw-bold mb-2">Get in Touch with Us</h1>
                <p class="text-muted small mb-4">
                    Have questions, suggestions, or need to report a broken download link? Complete the form below and our technical team will review your inquiry.
                </p>

                @if(session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
                @endif

                <form action="{{ url('/contact-us') }}" method="POST">
                    @csrf
                    
                    <!-- Honeypot protection field (hidden from genuine users) -->
                    <div style="display: none;" aria-hidden="true">
                        <input type="text" name="mrh_website_hp" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. John Doe" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" placeholder="Brief summary of your inquiry..." value="{{ old('subject') }}" required>
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Your Message <span class="text-danger">*</span></label>
                            <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror" placeholder="Provide details, device model, or URL if reporting an issue..." required>{{ old('message') }}</textarea>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                <i class="bi bi-send me-1"></i> Send Message
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <div class="row g-3 text-muted small">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock text-primary"></i>
                            <span>Response Time: Typically 24-48 business hours</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-lock text-primary"></i>
                            <span>Privacy: Your email is never shared or published</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
