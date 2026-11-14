@extends('layouts.admin')

@section('title', 'Theme Version History')
@section('page_title', 'Theme Version History & Restoration')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Theme Version History</h4>
        <p class="text-muted small mb-0">Historical snapshots and audit trail for <strong class="text-primary">{{ $theme->name }}</strong>.</p>
    </div>
    <div>
        <a href="{{ route('admin.theme.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Customizer
        </a>
    </div>
</div>

<div class="mrh-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Version</th>
                    <th>Change Summary</th>
                    <th>Published By</th>
                    <th>Published Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($versions as $v)
                    <tr>
                        <td class="fw-bold">
                            <span class="badge text-bg-primary rounded-pill px-3">v{{ $v->version_number }}</span>
                            @if($v->version_number === $theme->version)
                                <span class="badge text-bg-success ms-1">Active</span>
                            @endif
                        </td>
                        <td>{{ $v->change_summary ?: 'Theme modification' }}</td>
                        <td>{{ $v->publisher?->name ?? 'System' }}</td>
                        <td class="small text-muted">{{ $v->published_at ? $v->published_at->format('M d, Y H:i') : $v->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-end">
                            @if($v->version_number !== $theme->version)
                                <form action="{{ route('admin.theme.restore', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Restore Version {{ $v->version_number }} as the active live theme?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Restore This Version
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">Current Version</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history fs-2 d-block mb-2"></i>
                            No historical snapshots published yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($versions->hasPages())
    <div class="p-3 border-top">
        {{ $versions->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
