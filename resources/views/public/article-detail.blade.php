@extends('layouts.public')

@section('title', 'Samsung Officially Begins One UI 7.0 Android 15 Rollout — Mobile Resource Hub')
@section('meta_description', 'Samsung has begun rolling out the stable One UI 7 Android 15 update for the Galaxy S24 lineup across international markets.')

@section('content')
<div class="container-xl py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/news') }}">News</a></li>
            <li class="breadcrumb-item active" aria-current="page">One UI 7 Rollout</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="mrh-card p-4 p-md-5">
                <div class="d-flex gap-2 align-items-center mb-3">
                    <span class="mrh-badge mrh-badge-success">Android 15</span>
                    <span class="small text-muted">&bull; 12 Nov 2026</span>
                    <span class="small text-muted">&bull; By Editorial Team</span>
                </div>

                <h1 class="h2 fw-extrabold mb-4">Samsung Officially Begins One UI 7.0 Android 15 Rollout for Galaxy S24 Series</h1>

                <div class="lead text-muted mb-4">
                    The stable build brings redesigned fluid animations, an updated quick settings interface, and integrated AI notification summaries.
                </div>

                <hr class="my-4">

                <div class="content-body text-body" style="line-height: 1.8;">
                    <p>
                        Samsung Electronics has officially commenced the global deployment of its flagship software interface, <strong>One UI 7.0</strong>, based on Android 15. The rollout is beginning with the Galaxy S24 Ultra, Galaxy S24+, and Galaxy S24 across European and Asian markets.
                    </p>

                    <h2 class="h4 fw-bold mt-4 mb-3">Key Highlights of One UI 7</h2>
                    <ul>
                        <li><strong>Independent Notification and Control Center:</strong> Users can swipe from the top-left for notifications and top-right for quick toggles.</li>
                        <li><strong>Revamped Camera Experience:</strong> More accessible zoom shortcuts and manual controls optimized for one-hand operation.</li>
                        <li><strong>Enhanced Battery Intelligence:</strong> Advanced charging protection profiles to prolong Li-ion longevity over multi-year cycles.</li>
                    </ul>

                    <p class="mt-4">
                        Users who prefer to flash the official stock firmware manually rather than waiting for over-the-air (OTA) updates can download the verified firmware build directly from our <a href="{{ url('/firmware/galaxy-s24-ultra-sm-s928b-oneui7') }}" class="fw-bold">Firmware Hub</a>.
                    </p>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
