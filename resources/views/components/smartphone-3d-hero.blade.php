{{-- 
    Mobile Resource Hub — Reusable 3D Smartphone Showcase Component
    Supports:
    1. Realistic CSS 3D Depth / WebGL Smartphone Placeholder with Floating & Gyroscope Tilt
    2. Dynamic GLTF / GLB 3D model loading via <model-viewer> when custom models are provided
    3. Smooth lighting, subtle shadows, and responsive scaling
    4. Accessibility: respects prefers-reduced-motion
--}}
@props([
    'modelUrl' => null, // Pass URL to .glb/.gltf file when provided by owner
    'alt' => 'Interactive 3D Flagship Smartphone',
])

<div class="smartphone-3d-container position-relative" id="smartphone3dContainer" aria-label="{{ $alt }}">
    @if($modelUrl)
        <!-- GLTF/GLB 3D Model Renderer via Model-Viewer -->
        <model-viewer
            src="{{ $modelUrl }}"
            alt="{{ $alt }}"
            auto-rotate
            camera-controls
            rotation-per-second="15deg"
            shadow-intensity="1.2"
            exposure="1"
            loading="lazy"
            poster="{{ asset('images/smartphone-placeholder.png') }}"
            style="width: 100%; height: 420px; outline: none;"
        >
            <div slot="poster" class="d-flex align-items-center justify-content-center h-100">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading 3D Smartphone...</span>
                </div>
            </div>
        </model-viewer>
    @else
        <!-- Premium Dual-Phone 3D Showcase (Floating, Gentle Tilt, Realistic Metallic Bezel) -->
        <div class="smartphone-showcase-stage" id="phoneStage">
            <!-- Primary Smartphone (Front Perspective with Dynamic Glowing Screen) -->
            <div class="phone-3d-wrapper phone-primary">
                <div class="phone-3d-body">
                    <!-- Glass Reflection Layer -->
                    <div class="phone-screen-glare"></div>
                    
                    <!-- Dynamic Island / Speaker -->
                    <div class="phone-notch">
                        <span class="camera-lens"></span>
                        <span class="speaker-grill"></span>
                    </div>

                    <!-- Illuminated OLED Screen -->
                    <div class="phone-screen-display">
                        <div class="screen-header">
                            <span class="time-widget">9:41</span>
                            <div class="status-icons">
                                <i class="bi bi-wifi"></i>
                                <i class="bi bi-battery-full"></i>
                            </div>
                        </div>

                        <!-- Live Hub Brand Widget -->
                        <div class="screen-brand-widget">
                            <div class="brand-avatar">
                                <i class="bi bi-cpu-fill"></i>
                            </div>
                            <div class="brand-title">MobileHub</div>
                            <div class="brand-subtitle">5G Ultra Connected</div>
                        </div>

                        <!-- Mini Floating Tech Cards on Screen -->
                        <div class="screen-card-row">
                            <div class="screen-mini-card">
                                <i class="bi bi-shield-check text-success"></i>
                                <span>Verified</span>
                            </div>
                            <div class="screen-mini-card">
                                <i class="bi bi-hdd-network text-primary"></i>
                                <span>ROM 14</span>
                            </div>
                        </div>
                    </div>

                    <!-- Metallic Bezel Sides & Antenna -->
                    <div class="phone-bezel-left"></div>
                    <div class="phone-bezel-right"></div>
                    <div class="phone-bezel-bottom"></div>
                </div>
                <!-- Soft Dynamic Ground Shadow -->
                <div class="phone-3d-shadow"></div>
            </div>

            <!-- Secondary Smartphone (Angled Rear Perspective with Quad-Camera Module) -->
            <div class="phone-3d-wrapper phone-secondary d-none d-sm-block">
                <div class="phone-3d-body rear-body">
                    <!-- Camera Island with Realistic Lenses -->
                    <div class="camera-island-3d">
                        <div class="camera-lens-ring lens-main">
                            <div class="lens-glass"></div>
                        </div>
                        <div class="camera-lens-ring lens-tele">
                            <div class="lens-glass"></div>
                        </div>
                        <div class="camera-lens-ring lens-ultra">
                            <div class="lens-glass"></div>
                        </div>
                        <div class="camera-flash"></div>
                        <div class="camera-lidar"></div>
                    </div>
                    <!-- Matte Titanium Rear Finish -->
                    <div class="rear-brand-mark">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                </div>
                <!-- Secondary Ground Shadow -->
                <div class="phone-3d-shadow shadow-secondary"></div>
            </div>
        </div>

        <!-- 3D Model Ready Notice (Subtle Developer Pill for Owner) -->
        <div class="d-none">
            <span class="badge bg-secondary-subtle text-muted" style="font-size: 0.65rem;">
                Ready for custom GLTF/GLB drop-in
            </span>
        </div>
    @endif
</div>
