@extends('layouts.admin')

@section('title', 'Categories')
@section('page_title', 'Category Taxonomy')

@section('content')
<div class="row g-4">
    <!-- Add Category Form -->
    <div class="col-lg-4">
        <div class="mrh-card p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-folder-plus me-1"></i> Add Category</h5>

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="name">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Smartphone News" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="slug">URL Slug <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" name="slug" id="slug" class="form-control font-monospace @error('slug') is-invalid @enderror" placeholder="e.g. smartphone-news" value="{{ old('slug') }}">
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="parent_id">Parent Category</label>
                    <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                        <option value="">None (Top-Level Category)</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="description">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Brief summary of what this category encompasses...">{{ old('description') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="status">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Create Category
                </button>
            </form>
        </div>
    </div>

    <!-- Categories List -->
    <div class="col-lg-8">
        <div class="mrh-card p-0 overflow-hidden">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Existing Categories ({{ $categories->count() }})</h5>
            </div>

            @if($categories->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                <p class="small mb-0">No categories created yet.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name & Slug</th>
                            <th>Parent</th>
                            <th>Articles</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>
                                <div class="fw-bold text-body">{{ $category->name }}</div>
                                <span class="small text-muted font-monospace">{{ $category->slug }}</span>
                                @if($category->description)
                                <div class="small text-muted text-truncate" style="max-width: 250px;">{{ $category->description }}</div>
                                @endif
                            </td>
                            <td>
                                @if($category->parent)
                                <span class="badge bg-body-tertiary text-body border">{{ $category->parent->name }}</span>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">{{ $category->posts_count }}</span>
                            </td>
                            <td>
                                @if($category->status === 'active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCatModal_{{ $category->id }}" title="Edit Category">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete category? Associated articles will remain uncategorized.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete Category">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade text-start" id="editCatModal_{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content mrh-card border-0">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title fw-bold">Edit Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Name</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Slug</label>
                                                        <input type="text" name="slug" class="form-control font-monospace" value="{{ $category->slug }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Parent Category</label>
                                                        <select name="parent_id" class="form-select">
                                                            <option value="">None</option>
                                                            @foreach($categories as $parentCandidate)
                                                            @if($parentCandidate->id !== $category->id)
                                                            <option value="{{ $parentCandidate->id }}" {{ $category->parent_id == $parentCandidate->id ? 'selected' : '' }}>
                                                                {{ $parentCandidate->name }}
                                                            </option>
                                                            @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Description</label>
                                                        <textarea name="description" rows="3" class="form-control">{{ $category->description }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $category->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $category->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
