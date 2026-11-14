@extends('layouts.admin')

@section('title', 'Inquiry: ' . $message->subject)
@section('page_title', 'Inquiry Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.messages.index') }}" class="small text-muted text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Back to Inquiries
    </a>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-2">
        <h3 class="fw-bold mb-0">{{ $message->subject }}</h3>
        <div class="d-flex gap-2">
            <a href="mailto:{{ $message->email }}?subject=Re: {{ rawurlencode($message->subject) }}" class="btn btn-primary rounded-pill btn-sm px-3">
                <i class="bi bi-reply-fill me-1"></i> Reply via Email Client
            </a>
            <form action="{{ route('admin.messages.toggle-read', $message) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                    <i class="bi {{ $message->status === 'unread' ? 'bi-check2' : 'bi-envelope' }} me-1"></i>
                    {{ $message->status === 'unread' ? 'Mark as Read' : 'Mark as Unread' }}
                </button>
            </form>
            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Delete message?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-pill btn-sm px-3">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="mrh-card p-4 p-md-5">
            <div class="border-bottom pb-3 mb-4">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <span class="text-muted small">From:</span>
                        <div class="fw-bold text-body fs-5">{{ $message->name }}</div>
                        <a href="mailto:{{ $message->email }}" class="small font-monospace text-decoration-none">{{ $message->email }}</a>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <span class="text-muted small">Received:</span>
                        <div class="font-monospace text-body">{{ $message->created_at->format('l, d F Y - H:i:s') }}</div>
                        <span class="badge bg-secondary-subtle text-secondary small">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <div class="content-body" style="line-height: 1.8; white-space: pre-line;">
                {{ $message->message }}
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="mrh-card p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-shield-check me-1"></i> Security & Metadata</h5>

            <div class="small mb-3">
                <div class="text-muted">Status:</div>
                <div class="fw-bold text-uppercase">{{ $message->status }}</div>
            </div>

            <div class="small mb-3">
                <div class="text-muted">Honeypot Verification:</div>
                <div class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Passed (Legitimate Visitor)</div>
            </div>

            <div class="small mb-3">
                <div class="text-muted">Privacy-Salted IP Hash:</div>
                <div class="font-monospace text-muted text-break" style="font-size: 0.72rem;">{{ $message->ip_hash ?? 'N/A' }}</div>
            </div>

            <div class="alert alert-info small mb-0">
                <i class="bi bi-info-circle me-1"></i> In Phase 2, outgoing transactional email SMTP is not yet configured. Use the "Reply via Email Client" link above to correspond directly from your email app.
            </div>
        </div>
    </div>
</div>
@endsection
