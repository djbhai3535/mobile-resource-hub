@extends('layouts.admin')

@section('title', 'Logo & Branding Settings')
@section('page_title', 'Logo & Branding Customization')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="mrh-card p-4 p-md-5 mb-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="brand-badge-lumzy" style="background: rgba(37, 99, 235, 0.12); color: #2563EB; font-size: 1.5rem; width: 48px; height: 48px;">
                    <i class="bi bi-image"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Website Logo &amp; Identity</h4>
                    <p class="text-muted small mb-0">Upload and configure your custom brand logo, dark mode variant, and browser favicon</p>
                </div>
            </div>

            <form action="{{ route('admin.settings.branding.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Section 1: Website Name -->
                <div class="mb-4 pb-3 border-bottom">
                    <label class="form-label fw-bold" for="site_name">
                        Website Brand Name <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="site_name" 
                        id="site_name" 
                        class="form-control @error('site_name') is-invalid @enderror" 
                        value="{{ old('site_name', $siteName) }}"
                        placeholder="Mobile Resource Hub"
                        required
                    >
                    <div class="form-text text-muted small">
                        Displayed in header brand mark when an image logo is not configured, and used for system emails and browser titles.
                    </div>
                    @error('site_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Section 2: Primary Website Logo -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-sun text-warning"></i> Primary Website Logo (Light Mode &amp; Header)
                    </h5>
                    <p class="text-muted small mb-3">
                        The primary logo appears in the public website header navbar, mobile drawer, and footer. Recommended format: transparent PNG, WebP, or SVG.
                    </p>

                    <!-- Preview Area -->
                    <div class="card bg-body-tertiary border p-3 mb-3">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div>
                                <span class="text-muted small fw-semibold d-block mb-1">Current Active Logo:</span>
                                <div class="p-2 bg-white rounded border d-inline-flex align-items-center justify-content-center shadow-sm" style="min-width: 160px; min-height: 52px;">
                                    @if($siteLogoUrl)
                                        <img src="{{ $siteLogoUrl }}" id="primaryLogoPreview" alt="{{ $siteName }}" style="max-height: {{ $logoHeight }}px; max-width: {{ $logoWidth }}px; object-fit: contain;">
                                    @else
                                        <div class="d-flex align-items-center gap-2 text-muted small" id="primaryLogoPreviewFallback">
                                            <i class="bi bi-phone-flip text-primary fs-5"></i>
                                            <span class="fw-bold">Mobile<span class="text-primary">Hub</span></span>
                                            <span class="badge bg-secondary-subtle text-secondary ms-1">Default</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($siteLogo)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_logo" id="remove_logo" value="1">
                                <label class="form-check-label text-danger small fw-semibold" for="remove_logo">
                                    <i class="bi bi-trash3 me-1"></i> Remove Custom Logo
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- File Input -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small" for="site_logo">Upload New Primary Logo</label>
                        <input 
                            type="file" 
                            name="site_logo" 
                            id="site_logo" 
                            class="form-control @error('site_logo') is-invalid @enderror" 
                            accept=".png,.jpg,.jpeg,.webp,.svg"
                            onchange="previewImage(this, 'primaryLogoPreview')"
                        >
                        <div class="form-text text-muted small">
                            Max size: 4MB. Formats: PNG, SVG, WebP, JPG.
                        </div>
                        @error('site_logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dimension Controls -->
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small" for="site_logo_width">Max Display Width (px)</label>
                            <div class="input-group input-group-sm">
                                <input 
                                    type="number" 
                                    name="site_logo_width" 
                                    id="site_logo_width" 
                                    class="form-control" 
                                    min="20" 
                                    max="400" 
                                    value="{{ old('site_logo_width', $logoWidth) }}"
                                >
                                <span class="input-group-text">px</span>
                            </div>
                            <div class="form-text text-muted small">Default: 140px (range: 20–400px)</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small" for="site_logo_height">Max Display Height (px)</label>
                            <div class="input-group input-group-sm">
                                <input 
                                    type="number" 
                                    name="site_logo_height" 
                                    id="site_logo_height" 
                                    class="form-control" 
                                    min="16" 
                                    max="120" 
                                    value="{{ old('site_logo_height', $logoHeight) }}"
                                >
                                <span class="input-group-text">px</span>
                            </div>
                            <div class="form-text text-muted small">Default: 36px (range: 16–120px)</div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Dark Mode Variant Logo (Optional) -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-moon-stars text-primary"></i> Dark Mode Variant Logo <span class="badge bg-secondary-subtle text-secondary small fw-normal ms-2">Optional</span>
                    </h5>
                    <p class="text-muted small mb-3">
                        Optional variant optimized for dark backgrounds. If omitted, the primary logo is used on all themes.
                    </p>

                    <!-- Dark Preview Area -->
                    <div class="card bg-dark border p-3 mb-3 text-white">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div>
                                <span class="text-white-50 small fw-semibold d-block mb-1">Dark Mode Preview:</span>
                                <div class="p-2 bg-dark rounded border border-secondary d-inline-flex align-items-center justify-content-center" style="min-width: 160px; min-height: 52px;">
                                    @if($siteLogoDarkUrl)
                                        <img src="{{ $siteLogoDarkUrl }}" id="darkLogoPreview" alt="{{ $siteName }} (Dark)" style="max-height: {{ $logoHeight }}px; max-width: {{ $logoWidth }}px; object-fit: contain;">
                                    @else
                                        <div class="d-flex align-items-center gap-2 text-white-50 small">
                                            <i class="bi bi-phone-flip text-primary fs-5"></i>
                                            <span class="fw-bold text-white">Mobile<span class="text-primary">Hub</span></span>
                                            <span class="badge bg-dark-subtle text-white-50 ms-1">Fallback</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($siteLogoDark)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_logo_dark" id="remove_logo_dark" value="1">
                                <label class="form-check-label text-danger small fw-semibold" for="remove_logo_dark">
                                    <i class="bi bi-trash3 me-1"></i> Remove Dark Variant
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Dark File Input -->
                    <div>
                        <label class="form-label fw-semibold small" for="site_logo_dark">Upload Dark Variant Logo</label>
                        <input 
                            type="file" 
                            name="site_logo_dark" 
                            id="site_logo_dark" 
                            class="form-control @error('site_logo_dark') is-invalid @enderror" 
                            accept=".png,.jpg,.jpeg,.webp,.svg"
                        >
                        <div class="form-text text-muted small">Max size: 4MB. Transparent PNG or SVG recommended.</div>
                        @error('site_logo_dark')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Section 4: Browser Favicon -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-browser-chrome text-success"></i> Browser Favicon
                    </h5>
                    <p class="text-muted small mb-3">
                        The icon displayed in browser tabs and bookmarks. Recommended format: ICO, 32x32 PNG, or SVG.
                    </p>

                    <div class="card bg-body-tertiary border p-3 mb-3">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 bg-white rounded border d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                                    <img src="{{ $siteFaviconUrl }}" id="faviconPreview" alt="Favicon" style="width: 32px; height: 32px; object-fit: contain;">
                                </div>
                                <div>
                                    <span class="fw-semibold small d-block">Active Tab Favicon</span>
                                    <span class="text-muted small">Displayed on all public and administration pages</span>
                                </div>
                            </div>
                            @if($siteFavicon)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_favicon" id="remove_favicon" value="1">
                                <label class="form-check-label text-danger small fw-semibold" for="remove_favicon">
                                    <i class="bi bi-trash3 me-1"></i> Reset to Default
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold small" for="site_favicon">Upload New Favicon</label>
                        <input 
                            type="file" 
                            name="site_favicon" 
                            id="site_favicon" 
                            class="form-control @error('site_favicon') is-invalid @enderror" 
                            accept=".ico,.png,.svg"
                        >
                        <div class="form-text text-muted small">Max size: 2MB. Formats: ICO, PNG (32x32 / 64x64), SVG.</div>
                        @error('site_favicon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex align-items-center justify-content-between pt-2">
                    <span class="text-muted small">
                        <i class="bi bi-shield-lock me-1"></i> Changes take effect immediately across all public and admin pages.
                    </span>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Save Branding Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let img = document.getElementById(previewId);
                if (img) {
                    img.src = e.target.result;
                } else {
                    const fallback = document.getElementById(previewId + 'Fallback');
                    if (fallback) {
                        fallback.innerHTML = `<img src="${e.target.result}" id="${previewId}" style="max-height: 40px; max-width: 140px; object-fit: contain;">`;
                    }
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
