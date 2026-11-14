@extends('layouts.admin')

@section('title', 'Specification Groups & Fields')
@section('page_title', 'Dynamic Specifications Builder')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dynamic Specifications Schema</h4>
        <p class="text-muted small mb-0">Configure specification groups and technical parameter fields without modifying database migrations or code</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#newGroupModal">
        <i class="bi bi-folder-plus me-1"></i> Add Spec Group
    </button>
</div>

<!-- Specification Groups List -->
<div class="row g-4">
    @foreach($groups as $group)
    <div class="col-12">
        <div class="mrh-card p-0 overflow-hidden mb-3">
            <div class="p-3 bg-body-tertiary border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary-subtle text-secondary border">#{{ $group->sort_order }}</span>
                    <h5 class="fw-bold mb-0 text-body">{{ $group->name }}</h5>
                    <span class="small text-muted font-monospace">({{ $group->slug }})</span>
                    @if(!$group->is_active)
                        <span class="badge bg-warning-subtle text-warning border">Inactive</span>
                    @endif
                    <span class="badge bg-primary-subtle text-primary border rounded-pill ms-2">
                        {{ $group->fields->count() }} fields
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFieldModal-{{ $group->id }}">
                        <i class="bi bi-plus-circle me-1"></i> Add Field
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editGroupModal-{{ $group->id }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('admin.specifications.groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Delete group {{ $group->name }}? All fields in it must be deleted first.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Group">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="p-0">
                @if($group->fields->isEmpty())
                    <div class="text-center py-4 text-muted small">No fields in this group. Click "Add Field" to create one.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 25%;">Field Label</th>
                                    <th style="width: 20%;">Key / Attribute</th>
                                    <th style="width: 15%;">Type</th>
                                    <th style="width: 15%;">Visibility</th>
                                    <th style="width: 10%;">Order</th>
                                    <th class="text-end pe-4" style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->fields as $field)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-body">{{ $field->label }}</div>
                                        @if($field->help_text)
                                            <small class="text-muted">{{ $field->help_text }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="text-secondary small">{{ $field->key }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-body-secondary text-body border">{{ $field->field_type }}</span>
                                        @if($field->unit)
                                            <small class="text-muted ms-1">({{ $field->unit }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($field->show_on_public)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Public</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border">Hidden</span>
                                        @endif
                                        @if(!$field->is_active)
                                            <span class="badge bg-warning-subtle text-warning border">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ $field->sort_order }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-1">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editFieldModal-{{ $field->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.specifications.fields.destroy', $field) }}" method="POST" onsubmit="return confirm('Delete specification field {{ $field->label }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Field Modal -->
                                <div class="modal fade" id="editFieldModal-{{ $field->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.specifications.fields.update', $field) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="group_id" value="{{ $group->id }}">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Edit Field: {{ $field->label }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Label <span class="text-danger">*</span></label>
                                                        <input type="text" name="label" class="form-control" value="{{ $field->label }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Key</label>
                                                        <input type="text" name="key" class="form-control" value="{{ $field->key }}">
                                                    </div>
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Field Type</label>
                                                            <select name="field_type" class="form-select">
                                                                <option value="text" {{ $field->field_type === 'text' ? 'selected' : '' }}>Text</option>
                                                                <option value="textarea" {{ $field->field_type === 'textarea' ? 'selected' : '' }}>Textarea</option>
                                                                <option value="number" {{ $field->field_type === 'number' ? 'selected' : '' }}>Number</option>
                                                                <option value="boolean" {{ $field->field_type === 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                                                                <option value="select" {{ $field->field_type === 'select' ? 'selected' : '' }}>Select</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Unit (e.g. mAh, mm, MP)</label>
                                                            <input type="text" name="unit" class="form-control" value="{{ $field->unit }}">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Help Text / Description</label>
                                                        <input type="text" name="help_text" class="form-control" value="{{ $field->help_text }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Sort Order</label>
                                                        <input type="number" name="sort_order" class="form-control" value="{{ $field->sort_order }}" min="0">
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="show_on_public" value="1" id="editPublic-{{ $field->id }}" {{ $field->show_on_public ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="editPublic-{{ $field->id }}">Show on Public Specification Sheet</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActive-{{ $field->id }}" {{ $field->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="editActive-{{ $field->id }}">Active</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Field</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Field Modal for Group -->
    <div class="modal fade" id="addFieldModal-{{ $group->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.specifications.fields.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Field to: {{ $group->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Field Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" class="form-control" placeholder="e.g. Refresh Rate, Battery Capacity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Key / Attribute</label>
                            <input type="text" name="key" class="form-control" placeholder="auto-generated or e.g. display_refresh_rate">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Field Type</label>
                                <select name="field_type" class="form-select">
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea (Multiline)</option>
                                    <option value="number">Number</option>
                                    <option value="boolean">Boolean (Yes/No)</option>
                                    <option value="select">Select</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Unit (Optional)</label>
                                <input type="text" name="unit" class="form-control" placeholder="e.g. mAh, Hz, g">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Help Text (Optional)</label>
                            <input type="text" name="help_text" class="form-control" placeholder="Guidance displayed to editors">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="show_on_public" value="1" id="newPublic-{{ $group->id }}" checked>
                            <label class="form-check-label" for="newPublic-{{ $group->id }}">Show on Public Specification Sheet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newActive-{{ $group->id }}" checked>
                            <label class="form-check-label" for="newActive-{{ $group->id }}">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Field</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Group Modal -->
    <div class="modal fade" id="editGroupModal-{{ $group->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.specifications.groups.update', $group) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Group: {{ $group->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Group Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $group->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug</label>
                            <input type="text" name="slug" class="form-control" value="{{ $group->slug }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ $group->sort_order }}" min="0">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editGroupActive-{{ $group->id }}" {{ $group->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="editGroupActive-{{ $group->id }}">Active Group</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- New Group Modal -->
<div class="modal fade" id="newGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.specifications.groups.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Specification Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Group Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Connectivity, Audio, Thermal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug (Optional)</label>
                        <input type="text" name="slug" class="form-control" placeholder="auto-generated-from-name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newGroupActive" checked>
                        <label class="form-check-label" for="newGroupActive">Active Group</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
