@extends('layouts.admin')

@section('title', 'Add Mobile Device')
@section('page_title', 'New Device')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
        <i class="bi bi-arrow-left me-1"></i> Back to Devices
    </a>
    <h4 class="fw-bold mb-1">Create Mobile Device</h4>
    <p class="text-muted small mb-0">Record identity, model numbers, hardware variants, and dynamic technical specifications</p>
</div>

<form action="{{ route('admin.devices.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- 1. Identity Card -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-phone text-primary"></i> Basic Identity & Marketing
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="brand_id" class="form-label fw-semibold">Manufacturer / Brand <span class="text-danger">*</span></label>
                        <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror" required>
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="device_category" class="form-label fw-semibold">Device Category <span class="text-danger">*</span></label>
                        <select name="device_category" id="device_category" class="form-select" required>
                            <option value="smartphone" {{ old('device_category', 'smartphone') === 'smartphone' ? 'selected' : '' }}>Smartphone</option>
                            <option value="tablet" {{ old('device_category') === 'tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="smartwatch" {{ old('device_category') === 'smartwatch' ? 'selected' : '' }}>Smartwatch</option>
                            <option value="feature_phone" {{ old('device_category') === 'feature_phone' ? 'selected' : '' }}>Feature Phone</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Model / Device Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Galaxy S24 Ultra, iPhone 16 Pro" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="marketing_name" class="form-label fw-semibold">Full Marketing Designation</label>
                        <input type="text" name="marketing_name" id="marketing_name" class="form-control" value="{{ old('marketing_name') }}" placeholder="e.g. Samsung Galaxy S24 Ultra 5G">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label fw-semibold">URL Slug</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary">/mobile-phones/</span>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="auto-generated-from-name">
                    </div>
                    @error('slug')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="os" class="form-label fw-semibold">Operating System</label>
                        <input type="text" name="os" id="os" class="form-control" value="{{ old('os') }}" placeholder="e.g. Android, iOS, HarmonyOS">
                    </div>
                    <div class="col-md-6">
                        <label for="os_version" class="form-label fw-semibold">OS Version / Upgrade Policy</label>
                        <input type="text" name="os_version" id="os_version" class="form-control" value="{{ old('os_version') }}" placeholder="e.g. Android 14, One UI 6.1 (7 OS upgrades)">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label fw-semibold">Summary / Tagline</label>
                    <textarea name="short_description" id="short_description" rows="2" class="form-control" placeholder="Quick promotional highlight for cards...">{{ old('short_description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="product_description" class="form-label fw-semibold">Detailed Product Overview</label>
                    <textarea name="product_description" id="product_description" rows="4" class="form-control" placeholder="Editorial review, engineering breakthroughs, architecture highlights...">{{ old('product_description') }}</textarea>
                </div>

                <div class="mb-0">
                    <label for="official_product_url" class="form-label fw-semibold">Official Product Webpage</label>
                    <input type="url" name="official_product_url" id="official_product_url" class="form-control" value="{{ old('official_product_url') }}" placeholder="https://www.manufacturer.com/device">
                </div>
            </div>

            <!-- 2. Model Numbers & SKUs -->
            <div class="mrh-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-upc-scan text-primary"></i> Model Numbers & Regional Codes
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addModelBtn">
                        <i class="bi bi-plus-circle me-1"></i> Add Model Code
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0" id="modelNumbersTable">
                        <thead class="table-light">
                            <tr>
                                <th>Model Designation <span class="text-danger">*</span></th>
                                <th>Region / Carrier Target</th>
                                <th style="width: 100px;" class="text-center">Primary</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="model_numbers[0][model_number]" class="form-control form-control-sm" placeholder="e.g. SM-S928B" required></td>
                                <td><input type="text" name="model_numbers[0][region]" class="form-control form-control-sm" placeholder="e.g. Global, Europe"></td>
                                <td class="text-center"><input type="checkbox" name="model_numbers[0][is_primary]" value="1" checked class="form-check-input"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Hardware Variants -->
            <div class="mrh-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-memory text-primary"></i> Memory & Color Variants
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantBtn">
                        <i class="bi bi-plus-circle me-1"></i> Add Variant
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0" id="variantsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Variant Label <span class="text-danger">*</span></th>
                                <th>RAM</th>
                                <th>Storage</th>
                                <th>Color</th>
                                <th>Notes / Price</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="variants[0][name]" class="form-control form-control-sm" placeholder="e.g. 256GB / 12GB RAM" required></td>
                                <td><input type="text" name="variants[0][ram]" class="form-control form-control-sm" placeholder="12GB"></td>
                                <td><input type="text" name="variants[0][internal_storage]" class="form-control form-control-sm" placeholder="256GB"></td>
                                <td><input type="text" name="variants[0][color]" class="form-control form-control-sm" placeholder="Titanium Gray"></td>
                                <td><input type="text" name="variants[0][notes]" class="form-control form-control-sm" placeholder="$1,299"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Dynamic Specification Values -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-sliders2 text-primary"></i> Full Technical Specifications Sheet
                </h5>
                <p class="text-muted small mb-4">Dynamic relational specifications. Enter verified hardware and software values for this device.</p>

                <div class="accordion" id="specAccordion">
                    @foreach($specificationGroups as $index => $group)
                    <div class="accordion-item mb-2 border rounded overflow-hidden">
                        <h2 class="accordion-header" id="heading-{{ $group->id }}">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $group->id }}">
                                <span>{{ $group->name }}</span>
                                <span class="badge bg-secondary-subtle text-secondary border ms-2 small">{{ $group->fields->count() }} parameters</span>
                            </button>
                        </h2>
                        <div id="collapse-{{ $group->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#specAccordion">
                            <div class="accordion-body bg-body-tertiary">
                                <div class="row g-3">
                                    @foreach($group->fields as $field)
                                    <div class="{{ $field->field_type === 'textarea' ? 'col-12' : 'col-md-6' }}">
                                        <label for="spec-{{ $field->id }}" class="form-label small fw-semibold text-body">
                                            {{ $field->label }}
                                            @if($field->unit)<span class="text-muted fw-normal">({{ $field->unit }})</span>@endif
                                        </label>
                                        @if($field->field_type === 'textarea')
                                            <textarea name="specs[{{ $field->id }}]" id="spec-{{ $field->id }}" rows="3" class="form-control form-control-sm" placeholder="{{ $field->help_text ?: 'Enter ' . $field->label }}">{{ old('specs.' . $field->id) }}</textarea>
                                        @else
                                            <input type="text" name="specs[{{ $field->id }}]" id="spec-{{ $field->id }}" class="form-control form-control-sm" value="{{ old('specs.' . $field->id) }}" placeholder="{{ $field->help_text ?: 'e.g. value' }}">
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- 5. SEO & Metadata -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-search text-primary"></i> SEO & Search Visibility
                </h5>
                <div class="mb-3">
                    <label for="seo_title" class="form-label fw-semibold">SEO Title</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title') }}" placeholder="Custom meta title for Google (defaults to device name)">
                </div>
                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-semibold">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="2" class="form-control" placeholder="Search snippet description...">{{ old('meta_description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="canonical_url" class="form-label fw-semibold">Canonical URL</label>
                    <input type="url" name="canonical_url" id="canonical_url" class="form-control" value="{{ old('canonical_url') }}" placeholder="https://...">
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Publishing & Status -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3">Publishing Status</h5>

                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Website Publication Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live on site)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Admin only)</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="market_status" class="form-label fw-semibold">Commercial Market Status <span class="text-danger">*</span></label>
                    <select name="market_status" id="market_status" class="form-select @error('market_status') is-invalid @enderror" required>
                        <option value="available" {{ old('market_status', 'available') === 'available' ? 'selected' : '' }}>Available for Purchase</option>
                        <option value="upcoming" {{ old('market_status') === 'upcoming' ? 'selected' : '' }}>Upcoming / Rumored</option>
                        <option value="discontinued" {{ old('market_status') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                    <small class="text-muted">Independent of website publication visibility.</small>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="announced_date" class="form-label fw-semibold small">Announced Date</label>
                        <input type="date" name="announced_date" id="announced_date" class="form-control form-control-sm" value="{{ old('announced_date') }}">
                    </div>
                    <div class="col-6">
                        <label for="release_date" class="form-label fw-semibold small">Release Date</label>
                        <input type="date" name="release_date" id="release_date" class="form-control form-control-sm" value="{{ old('release_date') }}">
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_featured">Feature on Homepage</label>
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label fw-semibold small">Catalog Sort Priority</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control form-control-sm" value="{{ old('sort_order', 0) }}" min="0">
                </div>

                <hr>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill">
                        <i class="bi bi-check-lg me-1"></i> Save Device Record
                    </button>
                    <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-secondary rounded-pill">Cancel</a>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3">Featured Device Photo</h5>
                <input type="file" name="featured_image" id="featured_image" class="form-control @error('featured_image') is-invalid @enderror" accept="image/*">
                <small class="text-muted d-block mt-1">Recommended: Clean PNG/WEBP with transparent background. Max 3MB.</small>
                @error('featured_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Gallery Images -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3">Product Gallery Photos</h5>
                <input type="file" name="gallery_images[]" id="gallery_images" class="form-control" accept="image/*" multiple>
                <small class="text-muted d-block mt-1">Select multiple images: renders, lifestyle photos, camera angles.</small>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    let modelCount = 1;
    document.getElementById('addModelBtn')?.addEventListener('click', function() {
        const tbody = document.querySelector('#modelNumbersTable tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="model_numbers[${modelCount}][model_number]" class="form-control form-control-sm" placeholder="e.g. SM-S928U" required></td>
            <td><input type="text" name="model_numbers[${modelCount}][region]" class="form-control form-control-sm" placeholder="e.g. USA Unlocked"></td>
            <td class="text-center"><input type="checkbox" name="model_numbers[${modelCount}][is_primary]" value="1" class="form-check-input"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="this.closest('tr').remove()"><i class="bi bi-x-circle"></i></button></td>
        `;
        tbody.appendChild(row);
        modelCount++;
    });

    let variantCount = 1;
    document.getElementById('addVariantBtn')?.addEventListener('click', function() {
        const tbody = document.querySelector('#variantsTable tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="variants[${variantCount}][name]" class="form-control form-control-sm" placeholder="Variant name" required></td>
            <td><input type="text" name="variants[${variantCount}][ram]" class="form-control form-control-sm" placeholder="RAM"></td>
            <td><input type="text" name="variants[${variantCount}][internal_storage]" class="form-control form-control-sm" placeholder="Storage"></td>
            <td><input type="text" name="variants[${variantCount}][color]" class="form-control form-control-sm" placeholder="Color"></td>
            <td><input type="text" name="variants[${variantCount}][notes]" class="form-control form-control-sm" placeholder="Price"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="this.closest('tr').remove()"><i class="bi bi-x-circle"></i></button></td>
        `;
        tbody.appendChild(row);
        variantCount++;
    });
</script>
@endpush
@endsection
