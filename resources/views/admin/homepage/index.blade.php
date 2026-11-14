@extends('layouts.admin')

@section('title', 'Homepage Manager')
@section('page_title', 'Homepage Section & Layout Manager')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Homepage Section & Layout Manager</h4>
        <p class="text-muted small mb-0">Control homepage section ordering, visibility, titles, item volumes, and display styles.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-box-arrow-up-right me-1"></i> View Live Homepage
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="mrh-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="sectionsTable">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;" class="text-center">Order</th>
                    <th>Section Name & Key</th>
                    <th>Section Title & Subtitle</th>
                    <th class="text-center">Items</th>
                    <th class="text-center">Display Style</th>
                    <th class="text-center">Background</th>
                    <th class="text-center" style="width: 100px;">Status</th>
                    <th class="text-end" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sections as $index => $sec)
                    <tr data-id="{{ $sec->id }}">
                        <td class="text-center">
                            <span class="badge text-bg-light border fw-bold">{{ $sec->sort_order }}</span>
                        </td>
                        <td>
                            <strong class="d-block">{{ ucwords(str_replace('_', ' ', $sec->section_key)) }}</strong>
                            <code class="small text-muted">{{ $sec->section_key }}</code>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $sec->title }}</div>
                            @if($sec->subtitle)
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 280px;">{{ $sec->subtitle }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge text-bg-secondary">{{ $sec->item_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge text-bg-light border text-capitalize">{{ $sec->display_style }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge text-bg-light border text-capitalize">{{ $sec->background_style }}</span>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input toggle-section-btn" type="checkbox" data-id="{{ $sec->id }}" {{ $sec->is_enabled ? 'checked' : '' }} role="switch">
                            </div>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $sec->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Section Modal -->
                    <div class="modal fade" id="editModal{{ $sec->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $sec->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.homepage.update', $sec->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="modalLabel{{ $sec->id }}">Edit: {{ ucwords(str_replace('_', ' ', $sec->section_key)) }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Section Heading Title</label>
                                            <input type="text" name="title" class="form-control" value="{{ $sec->title }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Subtitle / Description</label>
                                            <textarea name="subtitle" class="form-control" rows="2">{{ $sec->subtitle }}</textarea>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Max Item Count</label>
                                                <input type="number" name="item_count" class="form-control" value="{{ $sec->item_count }}" min="1" max="24" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Display Style</label>
                                                <select name="display_style" class="form-select">
                                                    <option value="grid" {{ $sec->display_style === 'grid' ? 'selected' : '' }}>Grid Layout</option>
                                                    <option value="carousel" {{ $sec->display_style === 'carousel' ? 'selected' : '' }}>Carousel / Slider</option>
                                                    <option value="list" {{ $sec->display_style === 'list' ? 'selected' : '' }}>Compact List</option>
                                                    <option value="default" {{ $sec->display_style === 'default' ? 'selected' : '' }}>Default</option>
                                                    <option value="ad" {{ $sec->display_style === 'ad' ? 'selected' : '' }}>Ad Placement</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Background Style</label>
                                                <select name="background_style" class="form-select">
                                                    <option value="default" {{ $sec->background_style === 'default' ? 'selected' : '' }}>Default App Background</option>
                                                    <option value="subtle" {{ $sec->background_style === 'subtle' ? 'selected' : '' }}>Subtle Contrast</option>
                                                    <option value="glass" {{ $sec->background_style === 'glass' ? 'selected' : '' }}>Glassmorphism</option>
                                                    <option value="dark" {{ $sec->background_style === 'dark' ? 'selected' : '' }}>Dark Surface</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Category Filter (Optional)</label>
                                                <input type="text" name="category_filter" class="form-control" placeholder="e.g. tutorials" value="{{ $sec->category_filter }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Quick Toggle switches
    document.querySelectorAll('.toggle-section-btn').forEach(btn => {
        btn.addEventListener('change', function () {
            const id = this.getAttribute('data-id');
            fetch(`/admin/homepage/sections/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                // Success feedback
            })
            .catch(() => {
                alert('Failed to toggle section status.');
                this.checked = !this.checked;
            });
        });
    });
});
</script>
@endpush
