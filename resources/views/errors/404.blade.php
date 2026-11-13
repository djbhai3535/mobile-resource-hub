@extends('layouts.public')

@section('title', '404 - Resource Not Found — Mobile Resource Hub')
@section('meta_description', 'The requested mobile specification, firmware, or guide could not be located on Mobile Resource Hub.')

@section('content')
<div class="container-xl py-5 text-center">
    <div class="py-5">
        <div class="brand-icon-box mx-auto mb-4" style="width: 80px; height: 80px; font-size: 2.8rem; background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
            <i class="bi bi-exclamation-octagon"></i>
        </div>
        <h1 class="display-5 fw-extrabold mb-2">404 - Page Not Found</h1>
        <p class="lead text-muted mx-auto mb-4" style="max-width: 520px;">
            The mobile device model, firmware release, or tutorial you are looking for may have been moved, renamed, or is currently unavailable.
        </p>

        <!-- Search Bar on 404 Page -->
        <div class="search-wrapper search-wrapper-hero mb-4">
            <form action="{{ url('/search') }}" method="GET">
                <i class="bi bi-search search-prefix-icon"></i>
                <input 
                    type="search" 
                    name="q" 
                    class="search-input-glass mrh-search-input" 
                    placeholder="Search catalog by model (e.g. SM-S928B), brand, or firmware..." 
                    autocomplete="off" 
                    required
                >
                <button type="submit" class="btn btn-primary search-action-btn">
                    <span>Search</span>
                </button>
            </form>
            <div class="search-suggestions-panel text-start"></div>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-house-door me-1"></i> Return to Homepage
            </a>
            <a href="{{ url('/mobile-phones') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-phone me-1"></i> Browse All Phones
            </a>
            <a href="{{ url('/firmware') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-cpu me-1"></i> Browse Firmware Hub
            </a>
        </div>
    </div>
</div>
@endsection
