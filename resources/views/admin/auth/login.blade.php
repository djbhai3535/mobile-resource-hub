<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Administrative Sign In — Mobile Resource Hub</title>

    <!-- Theme Initialization Script (Anti-FOUT) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('mrh_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-5" style="background: var(--bg-color);">
    <div class="container" style="max-width: 440px;">
        <!-- Brand Header -->
        <div class="text-center mb-4">
            <div class="brand-icon-box mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.8rem;">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h1 class="h3 fw-bold mb-1">Mobile<span class="text-primary">Hub</span></h1>
            <p class="text-muted small">Authorized Administration Access Portal</p>
        </div>

        <!-- Glassmorphism Login Card -->
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

            <form action="{{ route('admin.login.submit') }}" method="POST" autocomplete="off">
                @csrf

                <div class="mb-3">
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

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-bold mb-0" for="password">Password</label>
                        <a href="{{ route('admin.password.request') }}" class="small text-decoration-none text-muted">Forgot password?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-key"></i></span>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control border-start-0 @error('password') is-invalid @enderror" 
                            placeholder="••••••••••••" 
                            required
                        >
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label small" for="remember">Keep session active</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Admin
                </button>
            </form>
        </div>

        <div class="text-center mt-4">
            <a href="{{ url('/') }}" class="small text-muted text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Return to Public Site
            </a>
        </div>
    </div>
</body>
</html>
