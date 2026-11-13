<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class PublicPageController extends Controller
{
    /**
     * Display the Public Homepage.
     */
    public function home(): View
    {
        $popularBrands = [
            ['name' => 'Samsung', 'slug' => 'samsung', 'count' => 142, 'icon' => 'bi-phone'],
            ['name' => 'Apple', 'slug' => 'apple', 'count' => 58, 'icon' => 'bi-apple'],
            ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'count' => 110, 'icon' => 'bi-phone'],
            ['name' => 'Google', 'slug' => 'google', 'count' => 34, 'icon' => 'bi-google'],
            ['name' => 'OnePlus', 'slug' => 'oneplus', 'count' => 45, 'icon' => 'bi-phone'],
            ['name' => 'Motorola', 'slug' => 'motorola', 'count' => 62, 'icon' => 'bi-phone'],
        ];

        $latestDevices = [
            [
                'name' => 'Galaxy S24 Ultra',
                'brand' => 'Samsung',
                'model' => 'SM-S928B',
                'slug' => 'galaxy-s24-ultra',
                'screen' => '6.8" Dynamic AMOLED 2X 120Hz',
                'chipset' => 'Snapdragon 8 Gen 3 for Galaxy',
                'camera' => '200 MP Quad Camera',
                'battery' => '5000 mAh (45W)',
                'release_year' => '2024',
                'verified' => true,
            ],
            [
                'name' => 'iPhone 16 Pro Max',
                'brand' => 'Apple',
                'model' => 'A3296',
                'slug' => 'iphone-16-pro-max',
                'screen' => '6.9" Super Retina XDR OLED',
                'chipset' => 'Apple A18 Pro (3 nm)',
                'camera' => '48 MP Fusion + 5x Telephoto',
                'battery' => '4685 mAh',
                'release_year' => '2024',
                'verified' => true,
            ],
            [
                'name' => 'Pixel 9 Pro XL',
                'brand' => 'Google',
                'model' => 'GEC77',
                'slug' => 'pixel-9-pro-xl',
                'screen' => '6.8" LTPO OLED 120Hz',
                'chipset' => 'Google Tensor G4 (4 nm)',
                'camera' => '50 MP Triple Camera',
                'battery' => '5060 mAh (37W)',
                'release_year' => '2024',
                'verified' => true,
            ],
            [
                'name' => 'Xiaomi 14 Ultra',
                'brand' => 'Xiaomi',
                'model' => '24030PN60G',
                'slug' => 'xiaomi-14-ultra',
                'screen' => '6.73" LTPO AMOLED 120Hz',
                'chipset' => 'Snapdragon 8 Gen 3',
                'camera' => '50 MP Quad Leica Optics',
                'battery' => '5000 mAh (90W)',
                'release_year' => '2024',
                'verified' => true,
            ],
        ];

        $recentFirmware = [
            [
                'title' => 'Galaxy S24 Ultra Android 15 OneUI 7.0 (Global)',
                'slug' => 'galaxy-s24-ultra-sm-s928b-oneui7',
                'model_code' => 'SM-S928B',
                'region' => 'OXM (Multi-CSC)',
                'build' => 'S928BXXU3BXK2',
                'size' => '9.2 GB',
                'date' => 'Nov 2026',
            ],
            [
                'title' => 'Redmi Note 13 Pro 5G HyperOS 2.0 Global Stable',
                'slug' => 'redmi-note-13-pro-5g-hyperos2-global',
                'model_code' => 'garnet',
                'region' => 'Global (MI)',
                'build' => 'OS2.0.1.0.UNRMIXM',
                'size' => '5.6 GB',
                'date' => 'Nov 2026',
            ],
            [
                'title' => 'Pixel 9 Pro Factory Image Android 15 Stable',
                'slug' => 'pixel-9-pro-factory-image-android15',
                'model_code' => 'caiman',
                'region' => 'Global',
                'build' => 'AP4A.241105.008',
                'size' => '3.8 GB',
                'date' => 'Nov 2026',
            ],
        ];

        $latestGuides = [
            [
                'title' => 'How to Flash Official Samsung Firmware Using Odin 3.14',
                'slug' => 'how-to-flash-samsung-firmware-using-odin',
                'category' => 'Tutorials',
                'date' => '10 Nov 2026',
                'read_time' => '6 min read',
            ],
            [
                'title' => 'Fix Qualcomm HS-USB QDLoader 9008 Mode Brick Step-by-Step',
                'slug' => 'fix-qualcomm-hs-usb-qdloader-9008-edl-brick',
                'category' => 'Repair Guides',
                'date' => '08 Nov 2026',
                'read_time' => '9 min read',
            ],
        ];

        return view('public.home', compact('popularBrands', 'latestDevices', 'recentFirmware', 'latestGuides'));
    }

    /**
     * All Mobile Phones Directory.
     */
    public function devices(Request $request): View
    {
        $search = $request->query('search');
        $brandFilter = $request->query('brand');

        $devices = [
            [
                'name' => 'Galaxy S24 Ultra',
                'brand' => 'Samsung',
                'model' => 'SM-S928B',
                'slug' => 'galaxy-s24-ultra',
                'screen' => '6.8" Dynamic AMOLED 2X 120Hz',
                'chipset' => 'Snapdragon 8 Gen 3 for Galaxy',
                'camera' => '200 MP Quad Camera',
                'battery' => '5000 mAh (45W)',
                'release_year' => '2024',
                'os' => 'Android 14, upgradable to Android 15',
            ],
            [
                'name' => 'iPhone 16 Pro Max',
                'brand' => 'Apple',
                'model' => 'A3296',
                'slug' => 'iphone-16-pro-max',
                'screen' => '6.9" Super Retina XDR OLED',
                'chipset' => 'Apple A18 Pro (3 nm)',
                'camera' => '48 MP Fusion + 5x Telephoto',
                'battery' => '4685 mAh',
                'release_year' => '2024',
                'os' => 'iOS 18',
            ],
            [
                'name' => 'Pixel 9 Pro XL',
                'brand' => 'Google',
                'model' => 'GEC77',
                'slug' => 'pixel-9-pro-xl',
                'screen' => '6.8" LTPO OLED 120Hz',
                'chipset' => 'Google Tensor G4 (4 nm)',
                'camera' => '50 MP Triple Camera',
                'battery' => '5060 mAh (37W)',
                'release_year' => '2024',
                'os' => 'Android 14, upgradable to Android 15',
            ],
            [
                'name' => 'Xiaomi 14 Ultra',
                'brand' => 'Xiaomi',
                'model' => '24030PN60G',
                'slug' => 'xiaomi-14-ultra',
                'screen' => '6.73" LTPO AMOLED 120Hz',
                'chipset' => 'Snapdragon 8 Gen 3',
                'camera' => '50 MP Quad Leica Optics',
                'battery' => '5000 mAh (90W)',
                'release_year' => '2024',
                'os' => 'Android 14, HyperOS',
            ],
            [
                'name' => 'OnePlus 12',
                'brand' => 'OnePlus',
                'model' => 'CPH2581',
                'slug' => 'oneplus-12',
                'screen' => '6.82" LTPO AMOLED 120Hz',
                'chipset' => 'Snapdragon 8 Gen 3',
                'camera' => '50 MP Hasselblad Triple',
                'battery' => '5400 mAh (100W)',
                'release_year' => '2024',
                'os' => 'Android 14, OxygenOS 14',
            ],
            [
                'name' => 'Motorola Edge 50 Ultra',
                'brand' => 'Motorola',
                'model' => 'XT2401-1',
                'slug' => 'motorola-edge-50-ultra',
                'screen' => '6.7" P-OLED 144Hz',
                'chipset' => 'Snapdragon 8s Gen 3',
                'camera' => '50 MP Triple Camera',
                'battery' => '4500 mAh (125W)',
                'release_year' => '2024',
                'os' => 'Android 14',
            ],
        ];

        return view('public.devices', compact('devices', 'search', 'brandFilter'));
    }

    /**
     * Brand Directory.
     */
    public function brands(): View
    {
        $brands = [
            ['name' => 'Samsung', 'slug' => 'samsung', 'count' => 142, 'origin' => 'South Korea', 'founded' => 1938],
            ['name' => 'Apple', 'slug' => 'apple', 'count' => 58, 'origin' => 'United States', 'founded' => 1976],
            ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'count' => 110, 'origin' => 'China', 'founded' => 2010],
            ['name' => 'Google', 'slug' => 'google', 'count' => 34, 'origin' => 'United States', 'founded' => 1998],
            ['name' => 'OnePlus', 'slug' => 'oneplus', 'count' => 45, 'origin' => 'China', 'founded' => 2013],
            ['name' => 'Motorola', 'slug' => 'motorola', 'count' => 62, 'origin' => 'United States', 'founded' => 1928],
            ['name' => 'Realme', 'slug' => 'realme', 'count' => 78, 'origin' => 'China', 'founded' => 2018],
            ['name' => 'Oppo', 'slug' => 'oppo', 'count' => 95, 'origin' => 'China', 'founded' => 2004],
            ['name' => 'Vivo', 'slug' => 'vivo', 'count' => 88, 'origin' => 'China', 'founded' => 2009],
        ];

        return view('public.brands', compact('brands'));
    }

    /**
     * Individual Brand Detail Page.
     */
    public function brandDetail(string $brandSlug): View
    {
        $brandName = ucfirst($brandSlug);
        return view('public.brand-detail', compact('brandSlug', 'brandName'));
    }

    /**
     * Device Specification Detail Page.
     */
    public function deviceDetail(string $deviceSlug): View
    {
        $device = [
            'name' => 'Samsung Galaxy S24 Ultra',
            'brand' => 'Samsung',
            'model' => 'SM-S928B / SM-S928U',
            'slug' => $deviceSlug,
            'announced' => 'January 17, 2024',
            'status' => 'Available. Released January 24, 2024',
            'dimensions' => '162.3 x 79.0 x 8.6 mm (6.39 x 3.11 x 0.34 in)',
            'weight' => '232 g (8.18 oz)',
            'build' => 'Glass front (Corning Gorilla Armor), glass back, titanium frame (grade 2)',
            'ip_rating' => 'IP68 dust/water resistant (up to 1.5m for 30 min)',
            'display_type' => 'Dynamic LTPO AMOLED 2X, 120Hz, HDR10+, 2600 nits (peak)',
            'display_size' => '6.8 inches, 113.5 cm2 (~88.5% screen-to-body ratio)',
            'resolution' => '1440 x 3120 pixels, 19.5:9 ratio (~505 ppi density)',
            'os' => 'Android 14, upgradable to 7 major Android upgrades, One UI 7 ready',
            'chipset' => 'Qualcomm SM8650-AC Snapdragon 8 Gen 3 (4 nm) for Galaxy',
            'cpu' => '8-core (1x3.39GHz Cortex-X4 & 3x3.1GHz Cortex-A720 & 2x2.9GHz Cortex-A720 & 2x2.2GHz Cortex-A520)',
            'gpu' => 'Adreno 750 (1 GHz)',
            'internal_storage' => '256GB 12GB RAM, 512GB 12GB RAM, 1TB 12GB RAM (UFS 4.0)',
            'card_slot' => 'No',
            'main_camera' => '200 MP, f/1.7, 24mm (wide) + 50 MP, f/3.4, 111mm (periscope telephoto, 5x) + 10 MP, f/2.4, 67mm (telephoto, 3x) + 12 MP, f/2.2, 13mm (ultrawide)',
            'selfie_camera' => '12 MP, f/2.2, 26mm (wide), Dual Pixel PDAF',
            'battery' => '5000 mAh non-removable, 45W wired, 15W wireless, 4.5W reverse wireless',
            'colors' => 'Titanium Black, Titanium Gray, Titanium Violet, Titanium Yellow',
        ];

        return view('public.device-detail', compact('device'));
    }

    /**
     * Firmware Directory.
     */
    public function firmware(Request $request): View
    {
        $search = $request->query('search');
        return view('public.firmware', compact('search'));
    }

    /**
     * Firmware Detail Page.
     */
    public function firmwareDetail(string $firmwareSlug): View
    {
        return view('public.firmware-detail', compact('firmwareSlug'));
    }

    /**
     * Flash Files Directory.
     */
    public function flashFiles(Request $request): View
    {
        $search = $request->query('search');
        return view('public.flash-files', compact('search'));
    }

    /**
     * Flash File Detail Page.
     */
    public function flashFileDetail(string $fileSlug): View
    {
        return view('public.flash-file-detail', compact('fileSlug'));
    }

    /**
     * Software Tools Directory.
     */
    public function software(Request $request): View
    {
        $search = $request->query('search');
        return view('public.software', compact('search'));
    }

    /**
     * Software Detail Page.
     */
    public function softwareDetail(string $softwareSlug): View
    {
        return view('public.software-detail', compact('softwareSlug'));
    }

    /**
     * USB Drivers Directory.
     */
    public function drivers(Request $request): View
    {
        $search = $request->query('search');
        return view('public.drivers', compact('search'));
    }

    /**
     * USB Driver Detail Page.
     */
    public function driverDetail(string $driverSlug): View
    {
        return view('public.driver-detail', compact('driverSlug'));
    }

    /**
     * News Listing.
     */
    public function news(): View
    {
        return view('public.news');
    }

    /**
     * News Article Detail.
     */
    public function newsDetail(string $articleSlug): View
    {
        return view('public.article-detail', compact('articleSlug'));
    }

    /**
     * Tutorials Listing.
     */
    public function tutorials(): View
    {
        return view('public.tutorials');
    }

    /**
     * Tutorial Detail.
     */
    public function tutorialDetail(string $tutorialSlug): View
    {
        return view('public.tutorial-detail', compact('tutorialSlug'));
    }

    /**
     * Global Search Page.
     */
    public function search(Request $request): View
    {
        $query = trim($request->query('q', ''));
        return view('public.search', compact('query'));
    }

    /**
     * Live Search Suggestions JSON Endpoint.
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = strtolower(trim($request->query('q', '')));

        if (strlen($query) < 2) {
            return response()->json([
                'devices' => [],
                'firmware' => [],
                'software' => [],
                'posts' => [],
            ]);
        }

        // Search match logic across catalog
        $devices = [
            ['name' => 'Samsung Galaxy S24 Ultra', 'model_number' => 'SM-S928B', 'url' => url('/mobile-phones/galaxy-s24-ultra')],
            ['name' => 'Apple iPhone 16 Pro Max', 'model_number' => 'A3296', 'url' => url('/mobile-phones/iphone-16-pro-max')],
            ['name' => 'Google Pixel 9 Pro XL', 'model_number' => 'GEC77', 'url' => url('/mobile-phones/pixel-9-pro-xl')],
            ['name' => 'Xiaomi 14 Ultra', 'model_number' => '24030PN60G', 'url' => url('/mobile-phones/xiaomi-14-ultra')],
        ];

        $matchedDevices = array_filter($devices, function ($item) use ($query) {
            return str_contains(strtolower($item['name']), $query) || str_contains(strtolower($item['model_number']), $query);
        });

        $firmware = [
            ['title' => 'Samsung Galaxy S24 Ultra Android 15 OneUI 7.0 (Global)', 'url' => url('/firmware/galaxy-s24-ultra-sm-s928b-oneui7')],
            ['title' => 'Redmi Note 13 Pro 5G HyperOS 2.0 Global Stable', 'url' => url('/firmware/redmi-note-13-pro-5g-hyperos2-global')],
        ];

        $matchedFirmware = array_filter($firmware, function ($item) use ($query) {
            return str_contains(strtolower($item['title']), $query);
        });

        $posts = [
            ['title' => 'How to Flash Official Samsung Firmware Using Odin 3.14', 'url' => url('/tutorials/how-to-flash-samsung-firmware-using-odin')],
            ['title' => 'Fix Qualcomm HS-USB QDLoader 9008 EDL Brick', 'url' => url('/tutorials/fix-qualcomm-hs-usb-qdloader-9008-edl-brick')],
        ];

        $matchedPosts = array_filter($posts, function ($item) use ($query) {
            return str_contains(strtolower($item['title']), $query);
        });

        return response()->json([
            'devices' => array_values($matchedDevices),
            'firmware' => array_values($matchedFirmware),
            'software' => [],
            'posts' => array_values($matchedPosts),
        ]);
    }

    /**
     * Static Pages.
     */
    public function aboutUs(): View
    {
        return view('public.pages.about');
    }

    public function contactUs(): View
    {
        return view('public.pages.contact');
    }

    public function privacyPolicy(): View
    {
        return view('public.pages.privacy');
    }

    public function termsAndConditions(): View
    {
        return view('public.pages.terms');
    }

    public function dmca(): View
    {
        return view('public.pages.dmca');
    }
}
