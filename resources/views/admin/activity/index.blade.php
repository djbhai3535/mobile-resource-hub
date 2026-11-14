@extends('layouts.admin')

@section('title', 'Activity Audit')
@section('page_title', 'Administrative Activity Audit')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Administrative Activity Audit</h4>
        <p class="text-muted small mb-0">Immutable audit log of all administrative actions, logins, publication events, and modifications</p>
    </div>
</div>

<!-- Filters -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.activity.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search by action, entity, or IP..." value="{{ request('q') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="action" class="form-select">
                <option value="">All Actions</option>
                <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create</option>
                <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update</option>
                <option value="publish" {{ request('action') === 'publish' ? 'selected' : '' }}>Publish</option>
                <option value="unpublish" {{ request('action') === 'unpublish' ? 'selected' : '' }}>Unpublish</option>
                <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete</option>
                <option value="restore" {{ request('action') === 'restore' ? 'selected' : '' }}>Restore</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.activity.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="mrh-card p-0 overflow-hidden">
    @if($logs->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-shield-check fs-1 d-block mb-2"></i>
        <p class="small mb-0">No activity logs recorded matching criteria.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Timestamp</th>
                    <th>Actor (Administrator)</th>
                    <th>Action</th>
                    <th>Target Entity</th>
                    <th>IP Address & Client</th>
                    <th class="text-end">Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td class="font-monospace small text-muted">
                        <div>{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                        <span class="text-muted" style="font-size: 0.72rem;">{{ $log->created_at->diffForHumans() }}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-body small">{{ $log->user?->name ?? 'System Guest' }}</div>
                        <span class="small text-muted">{{ $log->user?->email ?? 'N/A' }}</span>
                    </td>
                    <td>
                        @php
                            $badgeColor = match($log->action) {
                                'login' => 'info',
                                'logout' => 'secondary',
                                'create' => 'success',
                                'update' => 'primary',
                                'publish' => 'success',
                                'unpublish' => 'warning',
                                'delete' => 'danger',
                                'restore' => 'teal',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} border border-{{ $badgeColor }}-subtle text-uppercase" style="font-size: 0.7rem;">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="small">
                        @if($log->subject_type)
                        <div class="font-monospace">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</div>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="small">
                        <div class="font-monospace text-body">{{ $log->ip_address ?? '127.0.0.1' }}</div>
                        <div class="text-muted text-truncate" style="max-width: 220px; font-size: 0.72rem;" title="{{ $log->user_agent }}">
                            {{ $log->user_agent }}
                        </div>
                    </td>
                    <td class="text-end">
                        @if(!empty($log->properties))
                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" data-bs-toggle="modal" data-bs-target="#propModal_{{ $log->id }}">
                            <i class="bi bi-code-slash"></i> JSON
                        </button>

                        <!-- Modal -->
                        <div class="modal fade text-start" id="propModal_{{ $log->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content mrh-card border-0">
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title fw-bold small">Audit Payload #{{ $log->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <pre class="bg-body-tertiary p-3 rounded font-monospace small mb-0 text-break">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
