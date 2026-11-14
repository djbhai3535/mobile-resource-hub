@extends('layouts.admin')

@section('title', 'Tags')
@section('page_title', 'Tag Taxonomy')

@section('content')
<div class="row g-4">
    <!-- Add Tag Form -->
    <div class="col-lg-4">
        <div class="mrh-card p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-tag me-1"></i> Add New Tag</h5>

            <form action="{{ route('admin.tags.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="name">Tag Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Snapdragon 8 Gen 3" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="slug">URL Slug <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" name="slug" id="slug" class="form-control font-monospace @error('slug') is-invalid @enderror" placeholder="e.g. snapdragon-8-gen-3" value="{{ old('slug') }}">
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Add Tag
                </button>
            </form>
        </div>
    </div>

    <!-- Tags List -->
    <div class="col-lg-8">
        <div class="mrh-card p-0 overflow-hidden">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Active Tags ({{ $tags->total() }})</h5>
            </div>

            @if($tags->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-tags fs-1 d-block mb-2"></i>
                <p class="small mb-0">No tags created yet.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Tagged Articles</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                        <tr>
                            <td>
                                <span class="fw-bold text-body">#{{ $tag->name }}</span>
                            </td>
                            <td>
                                <span class="small text-muted font-monospace">{{ $tag->slug }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $tag->posts_count }}</span>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete tag #{{ $tag->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Tag">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tags->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $tags->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection
