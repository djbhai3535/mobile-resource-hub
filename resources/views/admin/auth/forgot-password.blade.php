<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reset Administrative Password — Mobile Resource Hub</title>

    <!-- Theme Initialization Script (Anti-FOUT) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('mrh_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-5" style="background: var(--bg-color);">
    <div class="container" style="max-width: 440px;">
        <div class="text-center mb-4">
            <div class="brand-icon-box mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.8rem;">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <h1 class="h3 fw-bold mb-1">Password Recovery</h1>
            <p class="text-muted small">Enter your administrative email to request a reset link</p>
        </div>

        <div class="mrh-card p-4 p-md-5">
            @if(session('status'))
            <div class="alert alert-info alert-dismissible fade show small mb-4" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('admin.password.email') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label small fw-bold" for="email">Administrative Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-envelope"></i></span>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="form-control border-start-0 @error('email') is-invalid @enderror" 
                            placeholder="admin@mobilehub.local" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill mb-3">
                    <i class="bi bi-envelope-arrow-up me-1"></i> Send Recovery Request
                </button>

                <div class="text-center">
                    <a href="{{ route('admin.login') }}" class="small text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Back to Sign In
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
