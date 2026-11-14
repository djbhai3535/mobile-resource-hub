@extends('layouts.admin')

@section('title', 'Theme Customizer')
@section('page_title', 'Theme Customizer & Design Studio')

@push('styles')
<style>
    .customizer-container {
        display: flex;
        gap: 1.5rem;
        height: calc(100vh - 160px);
        min-height: 650px;
    }
    .customizer-controls {
        flex: 0 0 460px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }
    .customizer-preview {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        background: #0f172a;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .preview-toolbar {
        padding: 0.75rem 1rem;
        background: #1e293b;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .preview-viewport-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #090d16;
        padding: 1rem;
        overflow: auto;
    }
    .preview-iframe {
        border: none;
        border-radius: 8px;
        background: #fff;
        height: 100%;
        transition: width 0.3s ease, max-width 0.3s ease;
    }
    .preview-desktop { width: 100%; max-width: 100%; }
    .preview-tablet { width: 768px; max-width: 768px; }
    .preview-mobile { width: 375px; max-width: 375px; }

    .color-picker-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .color-picker-input {
        width: 44px;
        height: 38px;
        padding: 2px;
        border-radius: 6px;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h4 class="fw-bold mb-1">Theme Customizer & Design Studio</h4>
        <p class="text-muted small mb-0">Active Theme: <strong class="text-primary">{{ $theme->name }}</strong> (v{{ $theme->version }})</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.theme.history') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-clock-history me-1"></i> Version History
        </a>
        <form action="{{ route('admin.theme.reset') }}" method="POST" onsubmit="return confirm('Reset draft changes back to the currently published theme?');">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Draft
            </button>
        </form>
        <button type="button" class="btn btn-primary btn-sm" id="saveDraftBtn">
            <i class="bi bi-save me-1"></i> Save Draft
        </button>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#publishModal">
            <i class="bi bi-cloud-arrow-up me-1"></i> Publish Theme
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="customizer-container">
    <!-- Left: Settings Control Form -->
    <div class="customizer-controls">
        <form id="themeCustomizerForm">
            @csrf

            <!-- Navigation Accordion -->
            <div class="accordion" id="themeAccordion">
                
                <!-- 1. Branding -->
                <div class="accordion-item mb-2 border rounded">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBranding">
                            <i class="bi bi-badge-ad me-2 text-primary"></i> Branding & Identity
                        </button>
                    </h2>
                    <div id="collapseBranding" class="accordion-collapse collapse show" data-bs-parent="#themeAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Website Name</label>
                                <input type="text" name="branding[site_name]" class="form-control form-control-sm" value="{{ $draftSettings['branding']['site_name'] ?? 'Mobile Resource Hub' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Tagline</label>
                                <input type="text" name="branding[tagline]" class="form-control form-control-sm" value="{{ $draftSettings['branding']['tagline'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Main Logo URL</label>
                                <input type="text" name="branding[logo_url]" class="form-control form-control-sm" placeholder="/images/logo.png" value="{{ $draftSettings['branding']['logo_url'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Favicon URL</label>
                                <input type="text" name="branding[favicon_url]" class="form-control form-control-sm" placeholder="/favicon.ico" value="{{ $draftSettings['branding']['favicon_url'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Global Colors -->
                <div class="accordion-item mb-2 border rounded">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseColors">
                            <i class="bi bi-palette me-2 text-info"></i> Global Colors
                        </button>
                    </h2>
                    <div id="collapseColors" class="accordion-collapse collapse" data-bs-parent="#themeAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Primary Color</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" id="primaryColorPicker" value="{{ $draftSettings['global_colors']['primary'] ?? '#2563EB' }}" onchange="document.getElementById('primaryColorText').value = this.value">
                                    <input type="text" name="global_colors[primary]" id="primaryColorText" class="form-control form-control-sm" value="{{ $draftSettings['global_colors']['primary'] ?? '#2563EB' }}" oninput="document.getElementById('primaryColorPicker').value = this.value">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Primary Hover</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" id="primaryHoverPicker" value="{{ $draftSettings['global_colors']['primary_hover'] ?? '#1D4ED8' }}" onchange="document.getElementById('primaryHoverText').value = this.value">
                                    <input type="text" name="global_colors[primary_hover]" id="primaryHoverText" class="form-control form-control-sm" value="{{ $draftSettings['global_colors']['primary_hover'] ?? '#1D4ED8' }}" oninput="document.getElementById('primaryHoverPicker').value = this.value">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Secondary Color</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" id="secondaryColorPicker" value="{{ $draftSettings['global_colors']['secondary'] ?? '#475569' }}" onchange="document.getElementById('secondaryColorText').value = this.value">
                                    <input type="text" name="global_colors[secondary]" id="secondaryColorText" class="form-control form-control-sm" value="{{ $draftSettings['global_colors']['secondary'] ?? '#475569' }}" oninput="document.getElementById('secondaryColorPicker').value = this.value">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Accent Color</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" value="{{ $draftSettings['global_colors']['accent'] ?? '#3B82F6' }}" onchange="this.nextElementSibling.value = this.value">
                                    <input type="text" name="global_colors[accent]" class="form-control form-control-sm" value="{{ $draftSettings['global_colors']['accent'] ?? '#3B82F6' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Light Theme Palette -->
                <div class="accordion-item mb-2 border rounded">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLight">
                            <i class="bi bi-sun me-2 text-warning"></i> Light Theme Palette
                        </button>
                    </h2>
                    <div id="collapseLight" class="accordion-collapse collapse" data-bs-parent="#themeAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Page Background</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" value="{{ $draftSettings['light_palette']['bg_app'] ?? '#F5F7FB' }}" onchange="this.nextElementSibling.value = this.value">
                                    <input type="text" name="light_palette[bg_app]" class="form-control form-control-sm" value="{{ $draftSettings['light_palette']['bg_app'] ?? '#F5F7FB' }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Card Surface Background</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" value="{{ $draftSettings['light_palette']['surface'] ?? '#FFFFFF' }}" onchange="this.nextElementSibling.value = this.value">
                                    <input type="text" name="light_palette[surface]" class="form-control form-control-sm" value="{{ $draftSettings['light_palette']['surface'] ?? '#FFFFFF' }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Text Main</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" value="{{ $draftSettings['light_palette']['text_main'] ?? '#172033' }}" onchange="this.nextElementSibling.value = this.value">
                                    <input type="text" name="light_palette[text_main]" class="form-control form-control-sm" value="{{ $draftSettings['light_palette']['text_main'] ?? '#172033' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Dark Theme Palette -->
                <div class="accordion-item mb-2 border rounded">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDark">
                            <i class="bi bi-moon-stars me-2 text-info"></i> Dark Theme Palette
                        </button>
                    </h2>
                    <div id="collapseDark" class="accordion-collapse collapse" data-bs-parent="#themeAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Dark Background</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" value="{{ $draftSettings['dark_palette']['bg_app'] ?? '#0B1220' }}" onchange="this.nextElementSibling.value = this.value">
                                    <input type="text" name="dark_palette[bg_app]" class="form-control form-control-sm" value="{{ $draftSettings['dark_palette']['bg_app'] ?? '#0B1220' }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Dark Card Surface</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" value="{{ $draftSettings['dark_palette']['surface'] ?? '#141E30' }}" onchange="this.nextElementSibling.value = this.value">
                                    <input type="text" name="dark_palette[surface]" class="form-control form-control-sm" value="{{ $draftSettings['dark_palette']['surface'] ?? '#141E30' }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Dark Primary Accent</label>
                                <div class="color-picker-group">
                                    <input type="color" class="color-picker-input" value="{{ $draftSettings['dark_palette']['primary'] ?? '#60A5FA' }}" onchange="this.nextElementSibling.value = this.value">
                                    <input type="text" name="dark_palette[primary]" class="form-control form-control-sm" value="{{ $draftSettings['dark_palette']['primary'] ?? '#60A5FA' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Typography -->
                <div class="accordion-item mb-2 border rounded">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTypography">
                            <i class="bi bi-fonts me-2 text-success"></i> Typography
                        </button>
                    </h2>
                    <div id="collapseTypography" class="accordion-collapse collapse" data-bs-parent="#themeAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Font Family</label>
                                <select name="typography[font_family]" class="form-select form-select-sm">
                                    <option value="'Inter', system-ui, sans-serif" {{ ($draftSettings['typography']['font_family'] ?? '') === "'Inter', system-ui, sans-serif" ? 'selected' : '' }}>Inter (Default Modern)</option>
                                    <option value="'Roboto', system-ui, sans-serif" {{ ($draftSettings['typography']['font_family'] ?? '') === "'Roboto', system-ui, sans-serif" ? 'selected' : '' }}>Roboto</option>
                                    <option value="'Open Sans', system-ui, sans-serif" {{ ($draftSettings['typography']['font_family'] ?? '') === "'Open Sans', system-ui, sans-serif" ? 'selected' : '' }}>Open Sans</option>
                                    <option value="'Poppins', system-ui, sans-serif" {{ ($draftSettings['typography']['font_family'] ?? '') === "'Poppins', system-ui, sans-serif" ? 'selected' : '' }}>Poppins</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Base Font Size</label>
                                <select name="typography[base_font_size]" class="form-select form-select-sm">
                                    <option value="14px" {{ ($draftSettings['typography']['base_font_size'] ?? '') === '14px' ? 'selected' : '' }}>14px (Compact)</option>
                                    <option value="15px" {{ ($draftSettings['typography']['base_font_size'] ?? '15px') === '15px' ? 'selected' : '' }}>15px (Recommended)</option>
                                    <option value="16px" {{ ($draftSettings['typography']['base_font_size'] ?? '') === '16px' ? 'selected' : '' }}>16px (Spacious)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Layout & Glassmorphism -->
                <div class="accordion-item mb-2 border rounded">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGlass">
                            <i class="bi bi-droplet-half me-2 text-primary"></i> Layout & Glassmorphism
                        </button>
                    </h2>
                    <div id="collapseGlass" class="accordion-collapse collapse" data-bs-parent="#themeAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Glass Blur Intensity</label>
                                <select name="glassmorphism[blur_intensity]" class="form-select form-select-sm">
                                    <option value="8px" {{ ($draftSettings['glassmorphism']['blur_intensity'] ?? '') === '8px' ? 'selected' : '' }}>Subtle (8px)</option>
                                    <option value="16px" {{ ($draftSettings['glassmorphism']['blur_intensity'] ?? '16px') === '16px' ? 'selected' : '' }}>Standard (16px)</option>
                                    <option value="24px" {{ ($draftSettings['glassmorphism']['blur_intensity'] ?? '') === '24px' ? 'selected' : '' }}>Heavy Frost (24px)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Card Border Radius</label>
                                <select name="layout[border_radius_md]" class="form-select form-select-sm">
                                    <option value="8px" {{ ($draftSettings['layout']['border_radius_md'] ?? '') === '8px' ? 'selected' : '' }}>Crisp (8px)</option>
                                    <option value="12px" {{ ($draftSettings['layout']['border_radius_md'] ?? '12px') === '12px' ? 'selected' : '' }}>Smooth (12px)</option>
                                    <option value="16px" {{ ($draftSettings['layout']['border_radius_md'] ?? '') === '16px' ? 'selected' : '' }}>Curved (16px)</option>
                                    <option value="20px" {{ ($draftSettings['layout']['border_radius_md'] ?? '') === '20px' ? 'selected' : '' }}>Round (20px)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Right: Live Website Preview with Viewport Toggles -->
    <div class="customizer-preview">
        <div class="preview-toolbar">
            <div class="d-flex align-items-center gap-2">
                <span class="badge text-bg-dark border small">Device View:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-light active" id="viewDesktopBtn" title="Desktop View">
                        <i class="bi bi-display"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light" id="viewTabletBtn" title="Tablet (768px)">
                        <i class="bi bi-tablet"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light" id="viewMobileBtn" title="Mobile (375px)">
                        <i class="bi bi-phone"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-light" id="togglePreviewThemeBtn">
                    <i class="bi bi-circle-half me-1"></i> Toggle Dark/Light
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="refreshPreviewBtn" title="Reload Preview">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <div class="preview-viewport-wrapper">
            <iframe src="{{ route('admin.theme.preview') }}" id="themePreviewIframe" class="preview-iframe preview-desktop" title="Theme Preview"></iframe>
        </div>
    </div>
</div>

<!-- Publish Confirmation Modal -->
<div class="modal fade" id="publishModal" tabindex="-1" aria-labelledby="publishModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.theme.publish') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="publishModalLabel">Publish Live Theme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Publishing will update the live public website design and increment the theme version to <strong>v{{ $theme->version + 1 }}</strong>. A historical snapshot will be saved.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Version Change Notes</label>
                        <input type="text" name="change_summary" class="form-control" placeholder="e.g. Updated primary brand colors and card rounding" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm & Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const iframe = document.getElementById('themePreviewIframe');
    const saveBtn = document.getElementById('saveDraftBtn');
    const form = document.getElementById('themeCustomizerForm');

    // Responsive Viewport Toggles
    const viewDesktop = document.getElementById('viewDesktopBtn');
    const viewTablet = document.getElementById('viewTabletBtn');
    const viewMobile = document.getElementById('viewMobileBtn');

    function setActiveViewport(btn, className) {
        [viewDesktop, viewTablet, viewMobile].forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        iframe.className = 'preview-iframe ' + className;
    }

    viewDesktop.addEventListener('click', () => setActiveViewport(viewDesktop, 'preview-desktop'));
    viewTablet.addEventListener('click', () => setActiveViewport(viewTablet, 'preview-tablet'));
    viewMobile.addEventListener('click', () => setActiveViewport(viewMobile, 'preview-mobile'));

    // Dark/Light Theme Toggle inside preview
    document.getElementById('togglePreviewThemeBtn').addEventListener('click', function () {
        try {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            const current = doc.documentElement.getAttribute('data-bs-theme');
            doc.documentElement.setAttribute('data-bs-theme', current === 'dark' ? 'light' : 'dark');
        } catch (e) {}
    });

    // Refresh Preview
    document.getElementById('refreshPreviewBtn').addEventListener('click', function () {
        iframe.src = iframe.src;
    });

    // Save Draft via AJAX and update preview
    saveBtn.addEventListener('click', function () {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const formData = new FormData(form);

        fetch('{{ route("admin.theme.draft") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Saved!';
            setTimeout(() => {
                saveBtn.innerHTML = '<i class="bi bi-save me-1"></i> Save Draft';
            }, 2000);

            // Reload iframe with fresh draft CSS
            iframe.src = iframe.src;
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-save me-1"></i> Save Draft';
            alert('Failed to save draft settings. Please check your inputs.');
        });
    });
});
</script>
@endpush
