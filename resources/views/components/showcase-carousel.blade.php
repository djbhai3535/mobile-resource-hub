@if(isset($items) && $items->isNotEmpty())
<!-- 3D Mobile Showcase Carousel Section -->
<section class="showcase-3d-section py-4 py-lg-5 position-relative overflow-hidden" id="showcase3dSection" aria-label="3D Smartphone Showcase">
    <div class="container-xl">
        <div class="showcase-carousel-wrapper position-relative">
            <!-- Stage Header Badge -->
            <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-semibold small">
                        <i class="bi bi-badge-3d me-1"></i> Interactive 3D Showcase
                    </span>
                    <span class="text-muted small d-none d-sm-inline">Hardware &amp; Design Showcase</span>
                </div>
                
                @if($items->count() > 1)
                <!-- Navigation Buttons -->
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="showcase-nav-btn" id="showcasePrevBtn" aria-label="Previous Showcase Video">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="showcase-nav-btn" id="showcaseNextBtn" aria-label="Next Showcase Video">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                @endif
            </div>

            <!-- Carousel Slides Track -->
            <div class="showcase-track-container rounded-4 overflow-hidden shadow-lg border" id="showcaseTrackContainer">
                <div class="showcase-slides-track" id="showcaseSlidesTrack">
                    @foreach($items as $index => $item)
                    <div class="showcase-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" data-autoplay="{{ $item->autoplay ? 'true' : 'false' }}">
                        <div class="showcase-media-frame position-relative">
                            <video 
                                class="showcase-video" 
                                id="showcaseVideo-{{ $index }}"
                                src="{{ $item->video_url }}" 
                                @if($item->poster_url) poster="{{ $item->poster_url }}" @endif
                                {{ $item->muted ? 'muted' : '' }}
                                {{ $item->loop ? 'loop' : '' }}
                                playsinline 
                                preload="metadata"
                                aria-label="{{ $item->title }}"
                            ></video>

                            <!-- Gradient Scrim & Info Overlay -->
                            <div class="showcase-info-scrim">
                                <div class="showcase-info-content p-4 p-md-5">
                                    @if($item->brand_name)
                                        <span class="badge bg-white-subtle text-white border border-white-50 px-3 py-1 rounded-pill mb-2 small fw-semibold">
                                            {{ $item->brand_name }}
                                        </span>
                                    @endif
                                    <h3 class="h2 fw-extrabold text-white mb-1">{{ $item->title }}</h3>
                                    @if($item->subtitle)
                                        <p class="text-white-50 lead fs-6 mb-0" style="max-width: 580px;">{{ $item->subtitle }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- 3D Realistic Phone Glare & Bezel Highlights -->
                            <div class="showcase-frame-glare" aria-hidden="true"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($items->count() > 1)
            <!-- Dots Indicators -->
            <div class="showcase-indicators d-flex justify-content-center align-items-center gap-2 mt-3" id="showcaseIndicators" aria-label="Showcase Navigation Indicators">
                @foreach($items as $index => $item)
                <button 
                    type="button" 
                    class="showcase-dot {{ $index === 0 ? 'active' : '' }}" 
                    data-slide-to="{{ $index }}" 
                    aria-label="Go to showcase slide {{ $index + 1 }}"
                ></button>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const section = document.getElementById('showcase3dSection');
    if (!section) return;

    const track = document.getElementById('showcaseSlidesTrack');
    const slides = Array.from(document.querySelectorAll('.showcase-slide'));
    const dots = Array.from(document.querySelectorAll('.showcase-dot'));
    const prevBtn = document.getElementById('showcasePrevBtn');
    const nextBtn = document.getElementById('showcaseNextBtn');
    
    if (slides.length <= 1) {
        // Single slide initialization
        initSingleSlide(slides[0]);
        return;
    }

    let currentIndex = 0;
    let autoRotateTimer = null;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let isVisible = false;

    function initSingleSlide(slide) {
        const video = slide.querySelector('video');
        if (!video) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (slide.dataset.autoplay === 'true') {
                        video.play().catch(() => {});
                    }
                } else {
                    video.pause();
                }
            });
        }, { threshold: 0.25 });

        observer.observe(slide);
    }

    function goToSlide(index) {
        if (index < 0) {
            index = slides.length - 1;
        } else if (index >= slides.length) {
            index = 0;
        }

        // Pause previous video
        const prevVideo = slides[currentIndex]?.querySelector('video');
        if (prevVideo) {
            prevVideo.pause();
        }

        slides[currentIndex]?.classList.remove('active');
        dots[currentIndex]?.classList.remove('active');

        currentIndex = index;

        slides[currentIndex]?.classList.add('active');
        dots[currentIndex]?.classList.add('active');

        // Play new video if in view and autoplay enabled
        const currentVideo = slides[currentIndex]?.querySelector('video');
        if (currentVideo && isVisible && slides[currentIndex].dataset.autoplay === 'true') {
            currentVideo.play().catch(() => {});
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            goToSlide(currentIndex - 1);
            resetAutoRotate();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            goToSlide(currentIndex + 1);
            resetAutoRotate();
        });
    }

    dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => {
            goToSlide(idx);
            resetAutoRotate();
        });
    });

    // Auto rotate every 8 seconds if not reduced motion and when in view
    function startAutoRotate() {
        if (reducedMotion) return;
        stopAutoRotate();
        autoRotateTimer = setInterval(() => {
            if (isVisible) {
                goToSlide(currentIndex + 1);
            }
        }, 8000);
    }

    function stopAutoRotate() {
        if (autoRotateTimer) {
            clearInterval(autoRotateTimer);
            autoRotateTimer = null;
        }
    }

    function resetAutoRotate() {
        stopAutoRotate();
        startAutoRotate();
    }

    // IntersectionObserver to pause playback and rotation when outside viewport
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            isVisible = entry.isIntersecting;
            const currentVideo = slides[currentIndex]?.querySelector('video');
            if (isVisible) {
                if (currentVideo && slides[currentIndex].dataset.autoplay === 'true') {
                    currentVideo.play().catch(() => {});
                }
                startAutoRotate();
            } else {
                if (currentVideo) {
                    currentVideo.pause();
                }
                stopAutoRotate();
            }
        });
    }, { threshold: 0.2 });

    sectionObserver.observe(section);
});
</script>
@endpush
@endif
