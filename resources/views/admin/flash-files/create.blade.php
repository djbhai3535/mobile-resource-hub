@extends('layouts.admin')

@section('title', 'Add Flash File')
@section('page_title', 'Create Flash File Record')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.flash-files.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Flash Files
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Validation Error!</strong> Please review the form fields below:
        <ul class="mb-0 mt-2 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.flash-files.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- 1. Identification & Hardware Target -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cpu me-2 text-primary"></i>Target Hardware & Chipset
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="brand_id" class="form-label fw-semibold">Mobile Brand <span class="text-danger">*</span></label>
                        <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror" required>
                            <option value="">-- Select Brand --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="device_id" class="form-label fw-semibold">Associated Device</label>
                        <select name="device_id" id="device_id" class="form-select @error('device_id') is-invalid @enderror">
                            <option value="">-- Optional: Link to Device Catalog --</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}" data-brand="{{ $device->brand_id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                    {{ $device->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('device_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label for="title" class="form-label fw-semibold">Flash File Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Xiaomi Redmi Note 13 Pro 5G Fastboot ROM (garnet)" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-semibold">URL Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="Leave blank to auto-generate">
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="model_number" class="form-label fw-semibold">Model Number / Hardware Code <span class="text-danger">*</span></label>
                        <input type="text" name="model_number" id="model_number" class="form-control font-monospace @error('model_number') is-invalid @enderror" value="{{ old('model_number') }}" placeholder="e.g. 2312DRA50G / garnet" required>
                        @error('model_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="file_type" class="form-label fw-semibold">File Type Architecture <span class="text-danger">*</span></label>
                        <select name="file_type" id="file_type" class="form-select @error('file_type') is-invalid @enderror" required>
                            <option value="stock_rom" {{ old('file_type') === 'stock_rom' ? 'selected' : '' }}>Stock ROM Package</option>
                            <option value="scatter_firmware" {{ old('file_type') === 'scatter_firmware' ? 'selected' : '' }}>Scatter Firmware (MediaTek MTK)</option>
                            <option value="pac_file" {{ old('file_type') === 'pac_file' ? 'selected' : '' }}>PAC File (Spreadtrum / Unisoc)</option>
                            <option value="qfil_package" {{ old('file_type') === 'qfil_package' ? 'selected' : '' }}>QFIL XML / MBN (Qualcomm EDL)</option>
                            <option value="da_file" {{ old('file_type') === 'da_file' ? 'selected' : '' }}>DA / Auth File (Download Agent)</option>
                            <option value="recovery_image" {{ old('file_type') === 'recovery_image' ? 'selected' : '' }}>Custom / Stock Recovery IMG</option>
                            <option value="boot_image" {{ old('file_type') === 'boot_image' ? 'selected' : '' }}>Patched Boot / Kernel IMG</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="chipset" class="form-label fw-semibold">SoC / Chipset</label>
                        <input type="text" name="chipset" id="chipset" class="form-control @error('chipset') is-invalid @enderror" value="{{ old('chipset') }}" placeholder="e.g. Snapdragon 7s Gen 2, MT6768, SC9863A">
                    </div>

                    <div class="col-md-4">
                        <label for="android_version" class="form-label fw-semibold">Android OS Version</label>
                        <input type="text" name="android_version" id="android_version" class="form-control @error('android_version') is-invalid @enderror" value="{{ old('android_version') }}" placeholder="e.g. Android 14 (HyperOS)">
                    </div>
                </div>
            </div>

            <!-- 2. Technical Details & Flashing Tool Requirements -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-tools me-2 text-primary"></i>Tooling & Verification Checksums
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="flashing_tool" class="form-label fw-semibold">Recommended Flashing Tool</label>
                        <input type="text" name="flashing_tool" id="flashing_tool" class="form-control @error('flashing_tool') is-invalid @enderror" value="{{ old('flashing_tool') }}" placeholder="e.g. Mi Flash Tool, SP Flash Tool v6, QFIL v2.0">
                    </div>

                    <div class="col-md-6">
                        <label for="file_size_formatted" class="form-label fw-semibold">Display File Size</label>
                        <input type="text" name="file_size_formatted" id="file_size_formatted" class="form-control @error('file_size_formatted') is-invalid @enderror" value="{{ old('file_size_formatted') }}" placeholder="e.g. 6.45 GB">
                    </div>

                    <div class="col-md-6">
                        <label for="md5_checksum" class="form-label fw-semibold">MD5 Checksum</label>
                        <input type="text" name="md5_checksum" id="md5_checksum" class="form-control font-monospace @error('md5_checksum') is-invalid @enderror" value="{{ old('md5_checksum') }}" placeholder="MD5 Hash">
                    </div>

                    <div class="col-md-6">
                        <label for="sha256_checksum" class="form-label fw-semibold">SHA-256 Checksum</label>
                        <input type="text" name="sha256_checksum" id="sha256_checksum" class="form-control font-monospace @error('sha256_checksum') is-invalid @enderror" value="{{ old('sha256_checksum') }}" placeholder="SHA-256 Hash">
                    </div>

                    <div class="col-12">
                        <label for="compatibility_notes" class="form-label fw-semibold">Compatibility Notes</label>
                        <textarea name="compatibility_notes" id="compatibility_notes" rows="2" class="form-control" placeholder="Compatible with all regional variants of 2312DRA50G. Ensure unlocked bootloader before flashing.">{{ old('compatibility_notes') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label for="installation_instructions" class="form-label fw-semibold">Flashing Instructions</label>
                        <textarea name="installation_instructions" id="installation_instructions" rows="3" class="form-control" placeholder="1. Boot device into Fastboot / EDL mode&#10;2. Connect via original USB cable&#10;3. Select clean install option in flash tool&#10;4. Click Flash and await green PASS">{{ old('installation_instructions') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 3. Local File Upload & Mirrors -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cloud-arrow-down me-2 text-primary"></i>Private Storage & Download Mirrors
                </h5>

                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    Upload limit on this server: <code>{{ $uploadLimits['upload_max_filesize'] }}</code>.
                    For large service ROMs, configure verified external mirror URLs below.
                </div>

                <div class="mb-4">
                    <label for="payload_file" class="form-label fw-semibold">Local File Upload (Private Storage)</label>
                    <input type="file" name="payload_file" id="payload_file" class="form-control @error('payload_file') is-invalid @enderror">
                    <div class="form-text small">Files are saved in secure private storage outside web root and streamed safely to users.</div>
                </div>

                <h6 class="fw-bold mb-2">Download Mirrors</h6>
                <div id="mirrorsContainer">
                    <div class="row g-2 align-items-center mb-2 mirror-row">
                        <div class="col-md-4">
                            <input type="text" name="mirrors[0][mirror_name]" class="form-control form-control-sm" placeholder="Mirror Name" value="Primary Server">
                        </div>
                        <div class="col-md-7">
                            <input type="url" name="mirrors[0][url]" class="form-control form-control-sm font-monospace" placeholder="https://...">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-mirror-btn" disabled><i class="bi bi-dash"></i></button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-2" id="addMirrorBtn">
                    <i class="bi bi-plus me-1"></i> Add Another Mirror
                </button>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Status & Verification -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Publishing Status</h5>
                
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" name="is_verified" value="1" id="is_verified" class="form-check-input" {{ old('is_verified', 1) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_verified">Verified Working File</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Save Flash File
                    </button>
                    <a href="{{ route('admin.flash-files.index') }}" class="btn btn-outline-secondary rounded-pill py-2">Cancel</a>
                </div>
            </div>

            <!-- Redistribution & Source -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-shield-check me-2 text-primary"></i>Source & Attribution
                </h5>

                <div class="mb-3">
                    <label for="distribution_permission" class="form-label fw-semibold">Distribution Mode <span class="text-danger">*</span></label>
                    <select name="distribution_permission" id="distribution_permission" class="form-select" required>
                        <option value="authorized_mirror" {{ old('distribution_permission', 'authorized_mirror') === 'authorized_mirror' ? 'selected' : '' }}>Authorized Mirror / CDN Link</option>
                        <option value="site_hosted" {{ old('distribution_permission') === 'site_hosted' ? 'selected' : '' }}>Site Hosted Mirror (Safe)</option>
                        <option value="official_link_only" {{ old('distribution_permission') === 'official_link_only' ? 'selected' : '' }}>Official OEM Link Only</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="official_download_url" class="form-label fw-semibold">Official Download URL</label>
                    <input type="url" name="official_download_url" id="official_download_url" class="form-control" value="{{ old('official_download_url') }}" placeholder="https://...">
                </div>

                <div class="mb-0">
                    <label for="source_website" class="form-label fw-semibold">Source Credits</label>
                    <input type="text" name="source_website" id="source_website" class="form-control" value="{{ old('source_website') }}" placeholder="e.g. Xiaomi Official Community, XDA Developers">
                </div>
            </div>

            <!-- SEO -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">SEO Settings</h5>
                
                <div class="mb-3">
                    <label for="seo_title" class="form-label fw-semibold">Meta Title</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title') }}">
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-semibold">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="form-control">{{ old('meta_description') }}</textarea>
                </div>

                <div class="mb-0">
                    <label for="canonical_url" class="form-label fw-semibold">Canonical URL</label>
                    <input type="url" name="canonical_url" id="canonical_url" class="form-control" value="{{ old('canonical_url') }}">
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let mirrorIndex = 1;
    const container = document.getElementById('mirrorsContainer');
    const addBtn = document.getElementById('addMirrorBtn');

    addBtn.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2 mirror-row';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="mirrors[${mirrorIndex}][mirror_name]" class="form-control form-control-sm" placeholder="Mirror Name" value="Mirror ${mirrorIndex + 1}">
            </div>
            <div class="col-md-7">
                <input type="url" name="mirrors[${mirrorIndex}][url]" class="form-control form-control-sm font-monospace" placeholder="https://...">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-mirror-btn"><i class="bi bi-dash"></i></button>
            </div>
        `;
        container.appendChild(row);
        mirrorIndex++;
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-mirror-btn')) {
            const row = e.target.closest('.mirror-row');
            if (row && container.children.length > 1) {
                row.remove();
            }
        }
    });
});
</script>
@endpush
@endsection
