@extends('layouts.admin')

@section('title', 'Navigation Menus')
@section('page_title', 'Navigation Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Navigation Menus Management</h4>
        <p class="text-muted small mb-0">Organize and sequence menu items for both desktop/mobile header and footer links</p>
    </div>
</div>

<div class="row g-4">
    @foreach($menus as $menu)
    <div class="col-lg-6">
        <div class="mrh-card p-4 h-100 d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0 text-capitalize">{{ $menu->location }} Navigation</h5>
                    <span class="text-muted small">{{ $menu->name }}</span>
                </div>
                <span class="badge bg-primary text-uppercase" style="font-size: 0.65rem;">Location: {{ $menu->location }}</span>
            </div>

            <!-- Existing Menu Items Table -->
            <div class="table-responsive flex-grow-1 mb-4">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">Order</th>
                            <th>Label</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menu->items as $item)
                        <tr>
                            <td class="font-monospace small fw-bold">{{ $item->order }}</td>
                            <td class="fw-semibold">{{ $item->label }}</td>
                            <td><span class="font-monospace small text-muted">{{ $item->url }}</span></td>
                            <td>
                                @if($item->is_active)
                                <span class="badge bg-success-subtle text-success small">Active</span>
                                @else
                                <span class="badge bg-secondary-subtle text-secondary small">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editItem_{{ $item->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.menus.items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove menu link?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Edit Item Modal -->
                                <div class="modal fade text-start" id="editItem_{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content mrh-card border-0">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title fw-bold">Edit Menu Item</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.menus.items.update', $item) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Label</label>
                                                        <input type="text" name="label" class="form-control" value="{{ $item->label }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">URL (Internal or External)</label>
                                                        <input type="text" name="url" class="form-control font-monospace" value="{{ $item->url }}" required>
                                                    </div>
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">Order Position</label>
                                                            <input type="number" name="order" class="form-control" value="{{ $item->order }}" required>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">Target</label>
                                                            <select name="target" class="form-select">
                                                                <option value="_self" {{ $item->target === '_self' ? 'selected' : '' }}>Same Tab (_self)</option>
                                                                <option value="_blank" {{ $item->target === '_blank' ? 'selected' : '' }}>New Tab (_blank)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck_{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="activeCheck_{{ $item->id }}">Display in navigation</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Update Link</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No menu links created yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Add Item Accordion / Form -->
            <div class="border-top pt-3 mt-auto">
                <h6 class="fw-bold small mb-2"><i class="bi bi-plus-circle me-1"></i> Add Link to {{ ucfirst($menu->location) }}</h6>
                <form action="{{ route('admin.menus.items.store', $menu) }}" method="POST" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-sm-4">
                        <label class="form-label small mb-1">Label</label>
                        <input type="text" name="label" class="form-control form-control-sm" placeholder="e.g. Firmware Hub" required>
                    </div>
                    <div class="col-sm-5">
                        <label class="form-label small mb-1">URL</label>
                        <input type="text" name="url" class="form-control form-control-sm font-monospace" placeholder="/firmware" required>
                    </div>
                    <div class="col-sm-3">
                        <input type="hidden" name="target" value="_self">
                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill">
                            <i class="bi bi-plus-lg"></i> Add Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
