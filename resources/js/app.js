import 'bootstrap';

// ==========================================================================
// Theme Management (Light / Dark)
// ==========================================================================
const THEME_STORAGE_KEY = 'mrh-theme';

function getPreferredTheme() {
    const storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
    if (storedTheme) {
        return storedTheme;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    const themeIcon = document.getElementById('theme-toggle-icon');
    if (themeIcon) {
        if (theme === 'dark') {
            themeIcon.className = 'bi bi-sun-fill text-warning';
        } else {
            themeIcon.className = 'bi bi-moon-stars-fill text-primary';
        }
    }
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
    const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
    localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
    applyTheme(nextTheme);
}

// ==========================================================================
// Global Live Search & Suggestions
// ==========================================================================
function initGlobalSearch() {
    const searchInputs = document.querySelectorAll('.mrh-search-input');
    
    searchInputs.forEach(input => {
        const container = input.closest('.search-wrapper') || input.parentElement;
        const panel = container.querySelector('.search-suggestions-panel');
        let activeIndex = -1;
        let debounceTimer = null;

        if (!panel) return;

        input.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                panel.style.display = 'none';
                panel.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetchSuggestions(query, panel);
            }, 250);
        });

        input.addEventListener('keydown', (e) => {
            const items = panel.querySelectorAll('.suggestion-item');
            if (panel.style.display === 'none' || items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                updateActiveSuggestion(items, activeIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                updateActiveSuggestion(items, activeIndex);
            } else if (e.key === 'Enter') {
                if (activeIndex > -1 && items[activeIndex]) {
                    e.preventDefault();
                    items[activeIndex].click();
                }
            } else if (e.key === 'Escape') {
                panel.style.display = 'none';
                activeIndex = -1;
            }
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                panel.style.display = 'none';
                activeIndex = -1;
            }
        });
    });
}

function updateActiveSuggestion(items, index) {
    items.forEach((item, i) => {
        if (i === index) {
            item.classList.add('active');
            item.scrollIntoView({ block: 'nearest' });
        } else {
            item.classList.remove('active');
        }
    });
}

function fetchSuggestions(query, panel) {
    // Call live search suggestion endpoint
    fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            renderSuggestions(data, panel, query);
        })
        .catch(() => {
            // Graceful fallback with quick links if API not reachable
            renderFallbackSuggestions(query, panel);
        });
}

function renderSuggestions(data, panel, query) {
    if (!data || (!data.brands?.length && !data.devices?.length && !data.firmware?.length && !data.flash_files?.length && !data.software?.length && !data.drivers?.length && !data.posts?.length)) {
        panel.innerHTML = `
            <div class="p-3 text-center text-muted">
                <i class="bi bi-search me-1"></i> No immediate matches for "${escapeHtml(query)}"
                <div class="mt-2">
                    <a href="/search?q=${encodeURIComponent(query)}" class="btn btn-sm btn-outline-primary">
                        Search full catalog
                    </a>
                </div>
            </div>`;
        panel.style.display = 'block';
        return;
    }

    let html = '';

    if (data.brands?.length) {
        html += `<div class="suggestion-group-title"><i class="bi bi-award me-1"></i> Brands</div>`;
        data.brands.forEach(item => {
            html += `
                <a href="${item.url}" class="suggestion-item text-decoration-none">
                    <span><strong>${escapeHtml(item.name)}</strong></span>
                    <span class="mrh-badge mrh-badge-secondary">Brand</span>
                </a>`;
        });
    }

    if (data.devices?.length) {
        html += `<div class="suggestion-group-title"><i class="bi bi-phone me-1"></i> Devices</div>`;
        data.devices.forEach(item => {
            html += `
                <a href="${item.url}" class="suggestion-item text-decoration-none">
                    <span><strong>${escapeHtml(item.name)}</strong> <small class="text-muted ms-1">${escapeHtml(item.model_number || '')}</small></span>
                    <span class="mrh-badge mrh-badge-primary">Device</span>
                </a>`;
        });
    }

    if (data.firmware?.length) {
        html += `<div class="suggestion-group-title"><i class="bi bi-cpu me-1"></i> Stock Firmware</div>`;
        data.firmware.forEach(item => {
            html += `
                <a href="${item.url}" class="suggestion-item text-decoration-none">
                    <span>${escapeHtml(item.title || item.name)}</span>
                    <span class="mrh-badge mrh-badge-warning">Firmware</span>
                </a>`;
        });
    }

    if (data.flash_files?.length) {
        html += `<div class="suggestion-group-title"><i class="bi bi-file-earmark-zip me-1"></i> Flash Files</div>`;
        data.flash_files.forEach(item => {
            html += `
                <a href="${item.url}" class="suggestion-item text-decoration-none">
                    <span>${escapeHtml(item.title || item.name)}</span>
                    <span class="mrh-badge mrh-badge-warning">Flash ROM</span>
                </a>`;
        });
    }

    if (data.software?.length) {
        html += `<div class="suggestion-group-title"><i class="bi bi-tools me-1"></i> Software Tools</div>`;
        data.software.forEach(item => {
            html += `
                <a href="${item.url}" class="suggestion-item text-decoration-none">
                    <span>${escapeHtml(item.title || item.name)}</span>
                    <span class="mrh-badge mrh-badge-primary">Tool</span>
                </a>`;
        });
    }

    if (data.drivers?.length) {
        html += `<div class="suggestion-group-title"><i class="bi bi-usb-symbol me-1"></i> USB Drivers</div>`;
        data.drivers.forEach(item => {
            html += `
                <a href="${item.url}" class="suggestion-item text-decoration-none">
                    <span>${escapeHtml(item.title || item.name)}</span>
                    <span class="mrh-badge mrh-badge-secondary">Driver</span>
                </a>`;
        });
    }

    if (data.posts?.length) {
        html += `<div class="suggestion-group-title"><i class="bi bi-journal-text me-1"></i> Guides & News</div>`;
        data.posts.forEach(item => {
            html += `
                <a href="${item.url}" class="suggestion-item text-decoration-none">
                    <span>${escapeHtml(item.title)}</span>
                    <span class="mrh-badge mrh-badge-success">Guide</span>
                </a>`;
        });
    }

    html += `
        <div class="border-top mt-2 pt-2 text-center">
            <a href="/search?q=${encodeURIComponent(query)}" class="small text-primary fw-bold text-decoration-none">
                View all results for "${escapeHtml(query)}" &rarr;
            </a>
        </div>`;

    panel.innerHTML = html;
    panel.style.display = 'block';
}

function renderFallbackSuggestions(query, panel) {
    panel.innerHTML = `
        <div class="p-2">
            <a href="/search?q=${encodeURIComponent(query)}" class="suggestion-item text-decoration-none">
                <span><i class="bi bi-search me-2 text-primary"></i> Search for "<strong>${escapeHtml(query)}</strong>"</span>
                <span class="mrh-badge mrh-badge-primary">All</span>
            </a>
            <a href="/mobile-phones?search=${encodeURIComponent(query)}" class="suggestion-item text-decoration-none">
                <span><i class="bi bi-phone me-2 text-primary"></i> Search in Mobile Phones</span>
            </a>
            <a href="/firmware?search=${encodeURIComponent(query)}" class="suggestion-item text-decoration-none">
                <span><i class="bi bi-cpu me-2 text-warning"></i> Search in Firmware</span>
            </a>
        </div>`;
    panel.style.display = 'block';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

// ==========================================================================
// Initialization on DOMContentLoaded
// ==========================================================================
document.addEventListener('DOMContentLoaded', () => {
    // Sync theme UI button
    const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
    applyTheme(currentTheme);

    const toggleBtn = document.getElementById('theme-toggle-btn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleTheme);
    }

    initGlobalSearch();
});
