@extends('layouts.public')

@section('title', '[Preview] ' . $post->title . ' — Mobile Resource Hub')
@section('meta_description', $post->meta_description ?? $post->excerpt)

@section('content')
<div class="container-xl py-4">
    <!-- Admin Preview Notification Banner -->
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-eye-fill fs-5"></i>
            <div>
                <strong>Administrative Preview Mode:</strong> This post is currently 
                <span class="badge bg-dark text-uppercase">{{ $post->status }}</span>.
                @if(!$post->isPublished())
                It is <em>not</em> accessible to public visitors.
                @endif
            </div>
        </div>
        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-dark">
            <i class="bi bi-pencil me-1"></i> Edit Post
        </a>
    </div>

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/news') }}">News</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="mrh-card p-4 p-md-5">
                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                    @if($post->category)
                    <span class="mrh-badge mrh-badge-success">{{ $post->category->name }}</span>
                    @endif
                    <span class="small text-muted">&bull; {{ $post->published_at ? $post->published_at->format('d M Y') : 'Draft Date' }}</span>
                    <span class="small text-muted">&bull; By {{ $post->author?->name ?? 'Editorial Team' }}</span>
                </div>

                <h1 class="h2 fw-extrabold mb-3">{{ $post->title }}</h1>

                @if($post->excerpt)
                <div class="lead text-muted mb-4">
                    {{ $post->excerpt }}
                </div>
                @endif

                @if($post->featured_image)
                <div class="mb-4 text-center">
                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="img-fluid rounded-3 border w-100" style="max-height: 400px; object-fit: cover;">
                </div>
                @endif

                <hr class="my-4">

                <div class="content-body text-body" style="line-height: 1.8;">
                    {!! $post->content !!}
                </div>

                @if($post->tags->isNotEmpty())
                <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                    <span class="small text-muted fw-bold me-1">Tags:</span>
                    @foreach($post->tags as $tag)
                    <span class="badge bg-body-tertiary text-body border">#{{ $tag->name }}</span>
                    @endforeach
                </div>
                @endif
            </article>
        </div>
    </div>
</div>
@endsection
