@extends('layouts.admin')

@section('title', 'Edit Advertisement')
@section('page_title', 'Edit Advertisement Campaign')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Edit Advertisement Campaign</h4>
        <p class="text-muted small mb-0">Updating: <strong class="text-primary">{{ $advertisement->name }}</strong></p>
    </div>
    <div>
        <a href="{{ route('admin.advertisements.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Advertisements
        </a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the errors below.
    <ul class="mb-0 mt-2 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('admin.advertisements.update', $advertisement->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3">General Campaign Details</h5>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Advertisement Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $advertisement->name) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Placement Slot <span class="text-danger">*</span></label>
                        <select name="placement" class="form-select" required>
                            @foreach($placements as $key => $p)
                                <option value="{{ $key }}" {{ old('placement', $advertisement->placement) === $key ? 'selected' : '' }}>
                                    {{ $p['label'] }} ({{ $p['recommended_size'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Advertisement Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="image" {{ old('type', $advertisement->type) === 'image' ? 'selected' : '' }}>Image Banner (Standard)</option>
                            <option value="url" {{ old('type', $advertisement->type) === 'url' ? 'selected' : '' }}>External Sponsored Link</option>
                            <option value="network" {{ old('type', $advertisement->type) === 'network' ? 'selected' : '' }}>Ad Network Script</option>
                            @if(auth()->user()->isSuperAdmin())
                                <option value="custom_html" {{ old('type', $advertisement->type) === 'custom_html' ? 'selected' : '' }}>Custom HTML / JS Code (Super Admin)</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Creative Image</label>
                    @if($advertisement->image_path)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $advertisement->image_path) }}" alt="" class="rounded border" style="max-height: 100px;">
                        </div>
                    @endif
                    <input type="file" name="creative_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                    <small class="text-muted">Upload a new image to replace current creative.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Creative Alt Text</label>
                    <input type="text" name="image_alt" class="form-control" value="{{ old('image_alt', $advertisement->image_alt) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Destination URL</label>
                    <input type="url" name="destination_url" class="form-control" value="{{ old('destination_url', $advertisement->destination_url) }}">
                    <small class="text-muted">Must be a secure HTTPS link.</small>
                </div>

                @if(auth()->user()->isSuperAdmin())
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-danger">Custom HTML / Script Snippet (Super Admin Restricted)</label>
                    <textarea name="custom_html" class="form-control font-monospace" rows="4">{{ old('custom_html', $advertisement->custom_html) }}</textarea>
                </div>
                @endif
            </div>

            <!-- Schedule & Targeting -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3">Schedule & Rotation Rules</h5>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Start Date (Optional)</label>
                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', $advertisement->start_date ? $advertisement->start_date->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">End Date (Optional)</label>
                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', $advertisement->end_date ? $advertisement->end_date->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Rotation Weight (1-100)</label>
                        <input type="number" name="rotation_weight" class="form-control" value="{{ old('rotation_weight', $advertisement->rotation_weight) }}" min="1" max="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Priority Level (1-10)</label>
                        <input type="number" name="priority" class="form-control" value="{{ old('priority', $advertisement->priority) }}" min="1" max="10">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Campaign Group / Advertiser Name</label>
                    <input type="text" name="campaign_name" class="form-control" value="{{ old('campaign_name', $advertisement->campaign_name) }}">
                </div>
            </div>
        </div>

        <!-- Right Side Settings -->
        <div class="col-lg-4">
            <!-- Publishing & Status -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3">Status</h5>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $advertisement->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Campaign Active</label>
                </div>

                <div class="p-3 bg-body-tertiary rounded mb-3 small">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Impressions:</span>
                        <strong>{{ number_format($advertisement->impressions_count) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Clicks:</span>
                        <strong>{{ number_format($advertisement->clicks_count) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>CTR:</span>
                        <strong class="text-success">{{ $advertisement->ctr }}%</strong>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-1"></i> Update Advertisement
                </button>
            </div>

            <!-- Device Targeting -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3">Device Visibility</h5>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="show_desktop" id="show_desktop" value="1" {{ old('show_desktop', $advertisement->show_desktop) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="show_desktop"><i class="bi bi-display me-1"></i> Desktop Computers</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="show_tablet" id="show_tablet" value="1" {{ old('show_tablet', $advertisement->show_tablet) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="show_tablet"><i class="bi bi-tablet me-1"></i> Tablet Devices</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="show_mobile" id="show_mobile" value="1" {{ old('show_mobile', $advertisement->show_mobile) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="show_mobile"><i class="bi bi-phone me-1"></i> Mobile Phones</label>
                </div>
            </div>

            <!-- Theme Visibility -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3">Theme Visibility</h5>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="show_light_theme" id="show_light_theme" value="1" {{ old('show_light_theme', $advertisement->show_light_theme) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="show_light_theme"><i class="bi bi-sun me-1"></i> Show on Light Theme</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="show_dark_theme" id="show_dark_theme" value="1" {{ old('show_dark_theme', $advertisement->show_dark_theme) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="show_dark_theme"><i class="bi bi-moon-stars me-1"></i> Show on Dark Theme</label>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
