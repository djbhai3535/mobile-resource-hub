@extends('layouts.public')

@section('title', 'Mobile Technology News & Industry Updates — Mobile Resource Hub')
@section('meta_description', 'Latest mobile technology news, Android updates, security patch rollouts, chipset releases, and manufacturer announcements.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">News</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Mobile Technology News</h1>
        <p class="text-muted small mb-0">Editorial coverage on firmware updates, security advisories, and mobile hardware</p>
    </div>

    <div class="row g-4">
        @if(isset($posts) && $posts->count() > 0)
            @foreach($posts as $post)
            <div class="col-12 col-md-6">
                <div class="mrh-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <span class="mrh-badge mrh-badge-success">{{ $post->category?->name ?? 'Update' }}</span>
                        <span class="small text-muted">&bull; {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}</span>
                    </div>
                    <h4 class="fw-bold mb-2">
                        <a href="{{ url('/news/' . $post->slug) }}" class="text-body text-decoration-none">
                            {{ $post->title }}
                        </a>
                    </h4>
                    <p class="small text-muted mb-3 flex-grow-1">
                        {{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 140) }}
                    </p>
                    <a href="{{ url('/news/' . $post->slug) }}" class="text-primary fw-semibold small text-decoration-none mt-auto">
                        Read Full Story &rarr;
                    </a>
                </div>
            </div>
            @endforeach
        @else
        <div class="col-12 col-md-6">
            <div class="mrh-card p-4">
                <div class="d-flex gap-2 align-items-center mb-2">
                    <span class="mrh-badge mrh-badge-success">Android 15</span>
                    <span class="small text-muted">&bull; 12 Nov 2026</span>
                </div>
                <h4 class="fw-bold mb-2">
                    <a href="{{ url('/news/samsung-oneui-7-global-rollout-schedule') }}" class="text-body text-decoration-none">
                        Samsung Officially Begins One UI 7.0 Android 15 Rollout for Galaxy S24 Series
                    </a>
                </h4>
                <p class="small text-muted mb-3">
                    Samsung has initiated the stable deployment of One UI 7 based on Android 15, featuring redesigned system notifications and revamped camera controls.
                </p>
                <a href="{{ url('/news/samsung-oneui-7-global-rollout-schedule') }}" class="text-primary fw-semibold small text-decoration-none">
                    Read Full Story &rarr;
                </a>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="mrh-card p-4">
                <div class="d-flex gap-2 align-items-center mb-2">
                    <span class="mrh-badge mrh-badge-primary">Security</span>
                    <span class="small text-muted">&bull; 10 Nov 2026</span>
                </div>
                <h4 class="fw-bold mb-2">
                    <a href="{{ url('/news/google-november-2026-android-security-bulletin') }}" class="text-body text-decoration-none">
                        Google Releases November 2026 Android Security Bulletin Addressing Critical Flaws
                    </a>
                </h4>
                <p class="small text-muted mb-3">
                    The latest security patch resolves vulnerabilities in Qualcomm and MediaTek vendor components, preventing remote arbitrary code execution.
                </p>
                <a href="{{ url('/news/google-november-2026-android-security-bulletin') }}" class="text-primary fw-semibold small text-decoration-none">
                    Read Full Story &rarr;
                </a>
            </div>
        </div>
        @endif
    </div>

    @if(isset($posts) && $posts->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
