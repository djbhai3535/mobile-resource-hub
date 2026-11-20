@props([
    'slug' => '',
    'name' => '',
    'logo' => null,
    'size' => 32,
])

@php
    $cleanSlug = \Illuminate\Support\Str::slug($slug ?: $name);
    $meta = \App\Models\Brand::getBrandColorMeta($cleanSlug);
@endphp

@if(!empty($logo))
    <img src="{{ $logo }}" alt="{{ $name }}" class="brand-vector-img" style="max-width: 100%; max-height: 100%; object-fit: contain;" loading="lazy">
@else
    @switch($cleanSlug)
        @case('apple')
            <!-- Apple Official Vector Silhouette -->
            <svg viewBox="0 0 170 170" width="{{ $size }}" height="{{ $size }}" fill="currentColor" aria-label="Apple">
                <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.69-3.04-7.69-7.85-12.01-14.42-6.19-9.35-11.07-20.15-14.65-32.4-3.58-12.25-5.37-23.79-5.37-34.61 0-14.15 3.73-25.96 11.19-35.43 7.46-9.47 16.92-14.35 28.38-14.65 4.36 0 9.29 1.12 14.79 3.36 5.5 2.24 9.17 3.42 11.01 3.54 1.63 0 5.48-1.22 11.55-3.66 6.07-2.44 11.14-3.52 15.21-3.23 11.43.66 20.61 4.77 27.53 12.33-10.02 6.09-14.9 14.69-14.65 25.8.25 8.71 3.71 16.03 10.38 21.96 6.67 5.93 14.59 9.38 23.77 10.35-2.07 6.32-4.57 12.87-7.51 19.65zM119.22 31.81c0-7.29 2.65-14.1 7.95-20.43 5.3-6.33 11.83-10.45 19.59-12.38.33 1.2.49 2.39.49 3.59 0 7.29-2.82 14.13-8.46 20.52-5.64 6.39-12.24 10.37-19.8 11.94-.1-.76-.17-1.46-.17-2.12-.02-.38-.02-.76-.02-1.12z"/>
            </svg>
            @break

        @case('samsung')
            <!-- Samsung Official Typography Wordmark Vector -->
            <svg viewBox="0 0 200 40" width="{{ (int)($size * 2.2) }}" height="{{ (int)($size * 0.55) }}" fill="#0c4da2" aria-label="Samsung">
                <text x="50%" y="65%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="28" letter-spacing="4">SAMSUNG</text>
            </svg>
            @break

        @case('xiaomi')
            <!-- Xiaomi Official MI Rounded Badge Vector -->
            <svg viewBox="0 0 100 100" width="{{ $size }}" height="{{ $size }}" aria-label="Xiaomi">
                <rect width="100" height="100" rx="26" fill="#ff6700"/>
                <path d="M28 32h14v22h14V32h14v36H56V44H42v24H28V32zm30 14h10v10H58V46z" fill="#ffffff"/>
            </svg>
            @break

        @case('google')
        @case('google-pixel')
            <!-- Google Multicolor G Emblem Vector -->
            <svg viewBox="0 0 48 48" width="{{ $size }}" height="{{ $size }}" aria-label="Google Pixel">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            @break

        @case('oneplus')
            <!-- OnePlus 1+ Badge Vector -->
            <svg viewBox="0 0 100 100" width="{{ $size }}" height="{{ $size }}" aria-label="OnePlus">
                <rect x="5" y="5" width="90" height="90" rx="14" fill="#eb0028"/>
                <!-- 1 glyph -->
                <path d="M42 28h8v44h-8V38l-6 4v-7l10-7h4z" fill="#ffffff"/>
                <!-- + glyph -->
                <path d="M68 46h14v8H68v14h-8V54H46v-8h14V32h8v14z" fill="#ffffff" transform="scale(0.8) translate(15, 12)"/>
            </svg>
            @break

        @case('motorola')
            <!-- Motorola Stylized Batwing M Emblem -->
            <svg viewBox="0 0 100 100" width="{{ $size }}" height="{{ $size }}" aria-label="Motorola">
                <circle cx="50" cy="50" r="46" fill="#001428"/>
                <path d="M28 66c6-14 14-26 22-26 4 0 7 4 10 11 3-7 6-11 10-11 8 0 16 12 22 26-6-6-13-10-19-10-5 0-9 4-13 13-4-9-8-13-13-13-6 0-13 4-19 10z" fill="#0081c9"/>
            </svg>
            @break

        @case('oppo')
            <!-- Oppo Authentic Stylized Logomark -->
            <svg viewBox="0 0 160 50" width="{{ (int)($size * 2) }}" height="{{ (int)($size * 0.6) }}" fill="#008a00" aria-label="Oppo">
                <text x="50%" y="65%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="34" letter-spacing="2">oppo</text>
            </svg>
            @break

        @case('vivo')
            <!-- Vivo Authentic Logotype -->
            <svg viewBox="0 0 160 50" width="{{ (int)($size * 1.8) }}" height="{{ (int)($size * 0.55) }}" fill="#415fff" aria-label="Vivo">
                <text x="50%" y="65%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="32" letter-spacing="2">vivo</text>
            </svg>
            @break

        @case('realme')
            <!-- Realme Authentic Lowercase Logomark -->
            <svg viewBox="0 0 180 50" width="{{ (int)($size * 2) }}" height="{{ (int)($size * 0.55) }}" fill="#ffb800" aria-label="Realme">
                <text x="50%" y="65%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="30" letter-spacing="1">realme</text>
            </svg>
            @break

        @case('huawei')
            <!-- Huawei Fan Petal Emblem -->
            <svg viewBox="0 0 100 100" width="{{ $size }}" height="{{ $size }}" fill="#cf0a2c" aria-label="Huawei">
                <circle cx="50" cy="50" r="14" fill="#cf0a2c"/>
                <path d="M50 8c-4 12-4 22 0 30 4-8 4-18 0-30zM22 20c3 12 9 20 17 25-1-9-6-17-17-25zM78 20c-11 8-16 16-17 25 8-5 14-13 17-25zM12 44c8 8 18 11 28 9-5-7-13-12-28-9zM88 44c-15-3-23 2-28 9 10 2 20-1 28-9z" opacity="0.95"/>
            </svg>
            @break

        @case('nokia')
            <!-- Nokia Authentic Bold Typography -->
            <svg viewBox="0 0 180 40" width="{{ (int)($size * 2) }}" height="{{ (int)($size * 0.5) }}" fill="#124191" aria-label="Nokia">
                <text x="50%" y="70%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="28" letter-spacing="3">NOKIA</text>
            </svg>
            @break

        @case('infinix')
            <!-- Infinix Geometric Tech Logomark -->
            <svg viewBox="0 0 180 40" width="{{ (int)($size * 2) }}" height="{{ (int)($size * 0.5) }}" fill="#00c49f" aria-label="Infinix">
                <text x="50%" y="70%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="26" letter-spacing="2">Infinix</text>
            </svg>
            @break

        @case('tecno')
            <!-- Tecno Bold Modern Logomark -->
            <svg viewBox="0 0 160 40" width="{{ (int)($size * 1.8) }}" height="{{ (int)($size * 0.5) }}" fill="#0066ff" aria-label="Tecno">
                <text x="50%" y="70%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="28" letter-spacing="3">TECNO</text>
            </svg>
            @break

        @case('sony')
            <!-- Sony Classic Typography -->
            <svg viewBox="0 0 160 40" width="{{ (int)($size * 1.8) }}" height="{{ (int)($size * 0.45) }}" fill="currentColor" aria-label="Sony">
                <text x="50%" y="70%" text-anchor="middle" font-family="'Inter', serif" font-weight="900" font-size="28" letter-spacing="4">SONY</text>
            </svg>
            @break

        @case('asus')
            <!-- Asus Bold Emblem -->
            <svg viewBox="0 0 160 40" width="{{ (int)($size * 1.8) }}" height="{{ (int)($size * 0.5) }}" fill="#00539b" aria-label="Asus">
                <text x="50%" y="70%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="28" letter-spacing="3">ASUS</text>
            </svg>
            @break

        @case('nothing')
            <!-- Nothing Stylized Dot-Matrix Emblem -->
            <svg viewBox="0 0 100 100" width="{{ $size }}" height="{{ $size }}" aria-label="Nothing">
                <circle cx="50" cy="50" r="44" fill="#111111"/>
                <circle cx="50" cy="50" r="12" fill="#d71920"/>
                <circle cx="30" cy="30" r="4" fill="#ffffff"/>
                <circle cx="70" cy="30" r="4" fill="#ffffff"/>
                <circle cx="30" cy="70" r="4" fill="#ffffff"/>
                <circle cx="70" cy="70" r="4" fill="#ffffff"/>
            </svg>
            @break

        @case('poco')
            <!-- POCO Bold Yellow & Black -->
            <svg viewBox="0 0 140 40" width="{{ (int)($size * 1.6) }}" height="{{ (int)($size * 0.5) }}" aria-label="POCO">
                <rect width="140" height="40" rx="8" fill="#ffd200"/>
                <text x="50%" y="72%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="24" fill="#111111" letter-spacing="3">POCO</text>
            </svg>
            @break

        @case('redmi')
            <!-- Redmi Modern Logomark -->
            <svg viewBox="0 0 160 40" width="{{ (int)($size * 1.8) }}" height="{{ (int)($size * 0.5) }}" fill="#e53935" aria-label="Redmi">
                <text x="50%" y="70%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="26" letter-spacing="1">Redmi</text>
            </svg>
            @break

        @case('honor')
            <!-- Honor Modern Cyan-Blue -->
            <svg viewBox="0 0 160 40" width="{{ (int)($size * 1.8) }}" height="{{ (int)($size * 0.5) }}" fill="#00a4e4" aria-label="Honor">
                <text x="50%" y="70%" text-anchor="middle" font-family="'Inter', sans-serif" font-weight="900" font-size="26" letter-spacing="2">HONOR</text>
            </svg>
            @break

        @default
            <!-- Clean Typographic Brand Monogram Fallback -->
            <div class="brand-monogram-badge d-flex align-items-center justify-content-center fw-bold" style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ (int)($size * 0.42) }}px; color: {{ $meta['color'] }}; background: {{ $meta['bg'] }}; border-radius: 8px;">
                {{ strtoupper(substr($name ?: $slug, 0, 2)) }}
            </div>
    @endswitch
@endif
