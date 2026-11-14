@extends('layouts.admin')

@section('title', 'Static Pages')
@section('page_title', 'Static Content Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Static Page Content Management</h4>
        <p class="text-muted small mb-0">Edit content, SEO metadata, and legal policies across all foundational public URLs</p>
    </div>
</div>

<div class="mrh-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Page Title</th>
                    <th>Public URL</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pages as $page)
                <tr>
                    <td>
                        <div class="fw-bold text-body">{{ $page->title }}</div>
                        @if($page->meta_description)
                        <div class="small text-muted text-truncate" style="max-width: 320px;">{{ $page->meta_description }}</div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ url('/' . $page->slug) }}" target="_blank" class="small font-monospace text-decoration-none">
                            /{{ $page->slug }} <i class="bi bi-box-arrow-up-right" style="font-size: 0.75rem;"></i>
                        </a>
                    </td>
                    <td>
                        @if($page->is_system)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Core System Page</span>
                        @else
                        <span class="badge bg-secondary-subtle text-secondary border">Custom Page</span>
                        @endif
                    </td>
                    <td>
                        @if($page->status === 'published')
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                        @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Draft</span>
                        @endif
                    </td>
                    <td class="small text-muted font-monospace">
                        {{ $page->updated_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary px-3">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
