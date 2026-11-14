@extends('layouts.admin')

@section('title', 'Inquiries')
@section('page_title', 'Contact Inquiries')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Contact Inquiries & Feedback</h4>
        <p class="text-muted small mb-0">Review user feedback, firmware requests, and DMCA inquiries</p>
    </div>
</div>

<!-- Filters Bar -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.messages.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search by name, email, or subject..." value="{{ request('q') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread ({{ $unreadCount }})</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
</div>

<!-- Messages Table -->
<div class="mrh-card p-0 overflow-hidden">
    @if($messages->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        <h5>No inquiries found</h5>
        <p class="small mb-0">Inquiries submitted via the public contact form will appear here.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Status</th>
                    <th>Sender Name</th>
                    <th>Email Address</th>
                    <th>Subject</th>
                    <th>Received At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($messages as $msg)
                <tr class="{{ $msg->status === 'unread' ? 'fw-semibold' : '' }}">
                    <td>
                        @if($msg->status === 'unread')
                        <span class="badge bg-danger">Unread</span>
                        @elseif($msg->status === 'read')
                        <span class="badge bg-secondary-subtle text-secondary">Read</span>
                        @else
                        <span class="badge bg-success-subtle text-success">{{ ucfirst($msg->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $msg->name }}</td>
                    <td class="font-monospace small text-muted">{{ $msg->email }}</td>
                    <td>
                        <a href="{{ route('admin.messages.show', $msg) }}" class="text-body text-decoration-none">
                            {{ $msg->subject }}
                        </a>
                    </td>
                    <td class="small text-muted font-monospace">
                        {{ $msg->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.messages.show', $msg) }}" class="btn btn-outline-primary" title="View Message">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('admin.messages.toggle-read', $msg) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary" title="{{ $msg->status === 'unread' ? 'Mark as Read' : 'Mark as Unread' }}">
                                    <i class="bi {{ $msg->status === 'unread' ? 'bi-check2' : 'bi-envelope' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.messages.destroy', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete inquiry message?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($messages->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">
        {{ $messages->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
