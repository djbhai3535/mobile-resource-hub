@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Operational Overview')

@section('content')
<!-- Catalog & Content KPI Row -->
<div class="row g-4 mb-4">
    <!-- Stat 1: Mobile Brands -->
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Mobile Brands</span>
                <div class="brand-icon-box" style="width: 40px; height: 40px; font-size: 1.2rem; background: rgba(59, 130, 246, 0.15); color: #3B82F6;">
                    <i class="bi bi-award-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="h1 fw-extrabold mb-0">{{ $metrics['total_brands'] }}</h2>
                <span class="text-muted small">Brands Active</span>
            </div>
            <div class="small text-muted mt-2">
                <a href="{{ route('admin.brands.index') }}" class="text-decoration-none">Manage manufacturers &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Stat 2: Mobile Devices -->
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Devices & Specs</span>
                <div class="brand-icon-box" style="width: 40px; height: 40px; font-size: 1.2rem; background: rgba(16, 185, 129, 0.15); color: #10B981;">
                    <i class="bi bi-phone-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="h1 fw-extrabold mb-0">{{ $metrics['total_devices'] }}</h2>
                <span class="badge bg-success-subtle text-success border border-success-subtle small">
                    {{ $metrics['published_devices'] }} Live
                </span>
            </div>
            <div class="small text-muted mt-2">
                <span class="text-warning fw-semibold">{{ $metrics['draft_devices'] }}</span> in draft &bull; <a href="{{ route('admin.devices.index') }}" class="text-decoration-none">Catalog &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Stat 3: Articles & Guides -->
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Articles & Guides</span>
                <div class="brand-icon-box" style="width: 40px; height: 40px; font-size: 1.2rem; background: rgba(139, 92, 246, 0.15); color: #8B5CF6;">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="h1 fw-extrabold mb-0">{{ $metrics['total_posts'] }}</h2>
                <span class="badge bg-success-subtle text-success border border-success-subtle small">
                    {{ $metrics['published_posts'] }} Live
                </span>
            </div>
            <div class="small text-muted mt-2">
                <span class="text-warning fw-semibold">{{ $metrics['draft_posts'] }}</span> in draft &bull; <a href="{{ route('admin.posts.index') }}" class="text-decoration-none">Articles &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Stat 4: Contact Inquiries -->
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Inquiries</span>
                <div class="brand-icon-box" style="width: 40px; height: 40px; font-size: 1.2rem; background: rgba(239, 68, 68, 0.15); color: #EF4444;">
                    <i class="bi bi-envelope-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="h1 fw-extrabold mb-0">{{ $metrics['unread_messages'] }}</h2>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">Unread</span>
            </div>
            <div class="small text-muted mt-2">
                <span class="fw-semibold text-body">{{ $metrics['total_messages'] }}</span> lifetime contact submissions
            </div>
        </div>
    </div>
</div>

<!-- Phase 4 Firmware & Downloads Metrics -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Stat 5: Firmware ROMs -->
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Firmware ROMs</span>
                <div class="brand-icon-box" style="width: 40px; height: 40px; font-size: 1.2rem; background: rgba(14, 165, 233, 0.15); color: #0ea5e9;">
                    <i class="bi bi-hdd-network-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="h1 fw-extrabold mb-0">{{ $metrics['total_firmware'] }}</h2>
                <span class="badge bg-info-subtle text-info border border-info-subtle small">
                    {{ $metrics['published_firmware'] }} Published
                </span>
            </div>
            <div class="small text-muted mt-2">
                <a href="{{ route('admin.firmware.index') }}" class="text-decoration-none">Manage Firmware &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Stat 6: Flash Files -->
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Flash Files</span>
                <div class="brand-icon-box" style="width: 40px; height: 40px; font-size: 1.2rem; background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                    <i class="bi bi-file-earmark-zip-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="h1 fw-extrabold mb-0">{{ $metrics['total_flash_files'] }}</h2>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle small">
                    {{ $metrics['published_flash_files'] }} Live
                </span>
            </div>
            <div class="small text-muted mt-2">
                <a href="{{ route('admin.flash-files.index') }}" class="text-decoration-none">Manage Flash Files &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Stat 7: Software Tools -->
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Flashing Tools</span>
                <div class="brand-icon-box" style="width: 40px; height: 40px; font-size: 1.2rem; background: rgba(99, 102, 241, 0.15); color: #6366f1;">
                    <i class="bi bi-tools"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="h1 fw-extrabold mb-0">{{ $metrics['total_software'] }}</h2>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                    {{ $metrics['published_software'] }} Tools
                </span>
            </div>
            <div class="small text-muted mt-2">
                <a href="{{ route('admin.software.index') }}" class="text-decoration-none">Manage Utilities &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Stat 8: USB Drivers & Downloads -->
    <div class="col-sm-6 col-xl-3">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Drivers & DL Hits</span>
                <div class="brand-icon-box" style="width: 40px; height: 40px; font-size: 1.2rem; background: rgba(34, 197, 94, 0.15); color: #22c55e;">
                    <i class="bi bi-cloud-arrow-down-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="h1 fw-extrabold mb-0">{{ $metrics['total_download_requests'] }}</h2>
                <span class="badge bg-success-subtle text-success border border-success-subtle small">
                    {{ $metrics['total_drivers'] }} Drivers
                </span>
            </div>
            <div class="small text-muted mt-2">
                <span>{{ $metrics['total_mirrors'] }} mirrors</span> &bull; <a href="{{ route('admin.drivers.index') }}" class="text-decoration-none">Drivers &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Row -->
<div class="mrh-card p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h5 class="fw-bold mb-1">Quick Administrative Actions</h5>
            <p class="text-muted small mb-0">Direct shortcuts to add devices, firmware, tools, articles, or manage taxonomy</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.devices.create') }}" class="btn btn-primary rounded-pill btn-sm px-3">
                <i class="bi bi-phone me-1"></i> New Device
            </a>
            <a href="{{ route('admin.firmware.create') }}" class="btn btn-outline-info rounded-pill btn-sm px-3">
                <i class="bi bi-hdd-network me-1"></i> New Firmware
            </a>
            <a href="{{ route('admin.flash-files.create') }}" class="btn btn-outline-warning rounded-pill btn-sm px-3">
                <i class="bi bi-file-earmark-zip me-1"></i> New Flash File
            </a>
            <a href="{{ route('admin.software.create') }}" class="btn btn-outline-primary rounded-pill btn-sm px-3">
                <i class="bi bi-tools me-1"></i> New Tool
            </a>
            <a href="{{ route('admin.drivers.create') }}" class="btn btn-outline-success rounded-pill btn-sm px-3">
                <i class="bi bi-usb-symbol me-1"></i> New Driver
            </a>
            <a href="{{ route('admin.brands.create') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="bi bi-award me-1"></i> New Brand
            </a>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="bi bi-plus-circle me-1"></i> New Article
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Devices Section -->
    <div class="col-lg-6">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Recent Devices Added</h5>
                <a href="{{ route('admin.devices.index') }}" class="small text-decoration-none">View All Devices &rarr;</a>
            </div>

            @if($recentDevices->isEmpty())
                <div class="text-center py-4 text-muted small">No devices recorded in the database yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Device</th>
                                <th class="border-0">Brand</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDevices as $device)
                            <tr>
                                <td>
                                    <div class="fw-bold text-body">{{ $device->name }}</div>
                                    <small class="text-muted">{{ $device->device_category }} &bull; {{ $device->os }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border">{{ $device->brand->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($device->status === 'published')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                                    @elseif($device->status === 'draft')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Draft</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border">Archived</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-outline-secondary" title="Edit Device">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Posts Section -->
    <div class="col-lg-6">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Recent Articles & Content</h5>
                <a href="{{ route('admin.posts.index') }}" class="small text-decoration-none">View All Posts &rarr;</a>
            </div>

            @if($recentPosts->isEmpty())
                <div class="text-center py-4 text-muted small">No editorial posts created yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Title</th>
                                <th class="border-0">Category</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPosts as $post)
                            <tr>
                                <td>
                                    <div class="fw-bold text-body text-truncate" style="max-width: 220px;">{{ $post->title }}</div>
                                    <small class="text-muted">{{ $post->author->name ?? 'Author' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border">{{ $post->category->name ?? 'Uncategorized' }}</span>
                                </td>
                                <td>
                                    @if($post->status === 'published')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                                    @elseif($post->status === 'draft')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Draft</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border">Archived</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-secondary" title="Edit Post">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-lg-6">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Administrative Activity Audit</h5>
                <a href="{{ route('admin.activity.index') }}" class="small text-decoration-none">Audit Log &rarr;</a>
            </div>

            @if($recentActivities->isEmpty())
                <div class="text-center py-4 text-muted small">No administrative activities logged yet.</div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($recentActivities as $activity)
                    <div class="list-group-item px-0 py-2 d-flex align-items-start gap-2 border-0">
                        <div class="mt-1">
                            @if($activity->action === 'create')
                                <i class="bi bi-plus-circle text-success"></i>
                            @elseif($activity->action === 'update' || $activity->action === 'publish')
                                <i class="bi bi-pencil-square text-primary"></i>
                            @elseif($activity->action === 'delete')
                                <i class="bi bi-trash text-danger"></i>
                            @else
                                <i class="bi bi-dot text-muted"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1 small">
                            <div class="text-body fw-semibold">{{ $activity->description }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                {{ $activity->user->name ?? 'System' }} &bull; {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="col-lg-6">
        <div class="mrh-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Recent Contact Inquiries</h5>
                <a href="{{ route('admin.messages.index') }}" class="small text-decoration-none">All Messages &rarr;</a>
            </div>

            @if($recentMessages->isEmpty())
                <div class="text-center py-4 text-muted small">No messages received yet.</div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($recentMessages as $msg)
                    <a href="{{ route('admin.messages.show', $msg) }}" class="list-group-item list-group-item-action px-0 py-2 border-0">
                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <span class="fw-semibold text-body small {{ !$msg->is_read ? 'text-primary' : '' }}">{{ $msg->name }}</span>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-muted small text-truncate">{{ $msg->subject ?? $msg->message }}</div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
