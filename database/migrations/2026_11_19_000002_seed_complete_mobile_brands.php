<?php

use App\Models\Brand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip in testing environment so individual test cases can create their own test brands without slug collision
        if (app()->environment('testing')) {
            return;
        }

        $brands = [
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'country_of_origin' => 'South Korea',
                'short_description' => 'Global electronics powerhouse renowned for Galaxy smartphones, foldables, and displays.',
                'website_url' => 'https://www.samsung.com',
                'sort_order' => 1,
            ],
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'country_of_origin' => 'United States',
                'short_description' => 'Creator of iPhone, iOS, Apple Silicon, and premier consumer technology ecosystem.',
                'website_url' => 'https://www.apple.com',
                'sort_order' => 2,
            ],
            [
                'name' => 'Xiaomi',
                'slug' => 'xiaomi',
                'country_of_origin' => 'China',
                'short_description' => 'High-performance smartphones, hyper-charge innovations, and Leica co-engineered mobile cameras.',
                'website_url' => 'https://www.mi.com',
                'sort_order' => 3,
            ],
            [
                'name' => 'Redmi',
                'slug' => 'redmi',
                'country_of_origin' => 'China',
                'short_description' => 'Value-focused smartphone brand by Xiaomi delivering exceptional battery life and performance.',
                'website_url' => 'https://www.mi.com',
                'sort_order' => 4,
            ],
            [
                'name' => 'POCO',
                'slug' => 'poco',
                'country_of_origin' => 'China',
                'short_description' => 'Speed-focused performance smartphones engineered for gaming and power users.',
                'website_url' => 'https://www.po.co',
                'sort_order' => 5,
            ],
            [
                'name' => 'Infinix',
                'slug' => 'infinix',
                'country_of_origin' => 'Hong Kong',
                'short_description' => 'Trendy and innovative smartphones featuring fast charging and high refresh rate displays.',
                'website_url' => 'https://www.infinixmobility.com',
                'sort_order' => 6,
            ],
            [
                'name' => 'Tecno',
                'slug' => 'tecno',
                'country_of_origin' => 'Hong Kong',
                'short_description' => 'Pioneer in camera innovations, foldable devices, and modern mobile technology.',
                'website_url' => 'https://www.tecno-mobile.com',
                'sort_order' => 7,
            ],
            [
                'name' => 'Nokia',
                'slug' => 'nokia',
                'country_of_origin' => 'Finland',
                'short_description' => 'Legendary mobile pioneer famous for durable build quality, clean Android OS, and iconic design.',
                'website_url' => 'https://www.hmd.com',
                'sort_order' => 8,
            ],
            [
                'name' => 'Vivo',
                'slug' => 'vivo',
                'country_of_origin' => 'China',
                'short_description' => 'Leader in portrait photography, Zeiss optics co-engineering, and ultra-slim designs.',
                'website_url' => 'https://www.vivo.com',
                'sort_order' => 9,
            ],
            [
                'name' => 'Oppo',
                'slug' => 'oppo',
                'country_of_origin' => 'China',
                'short_description' => 'Imaging excellence, Hasselblad mobile camera systems, and industry-benchmark SuperVOOC charging.',
                'website_url' => 'https://www.oppo.com',
                'sort_order' => 10,
            ],
            [
                'name' => 'Realme',
                'slug' => 'realme',
                'country_of_origin' => 'China',
                'short_description' => 'Fast-growing tech lifestyle brand delivering trendsetting design and flagship performance.',
                'website_url' => 'https://www.realme.com',
                'sort_order' => 11,
            ],
            [
                'name' => 'Motorola',
                'slug' => 'motorola',
                'country_of_origin' => 'United States',
                'short_description' => 'Historic telecom pioneer known for Moto Razr foldables, Edge flagships, and clean MyUX software.',
                'website_url' => 'https://www.motorola.com',
                'sort_order' => 12,
            ],
            [
                'name' => 'Google Pixel',
                'slug' => 'google-pixel',
                'country_of_origin' => 'United States',
                'short_description' => 'Google Tensor silicon, pure Android experience, and industry-leading computational photography.',
                'website_url' => 'https://store.google.com',
                'sort_order' => 13,
            ],
            [
                'name' => 'OnePlus',
                'slug' => 'oneplus',
                'country_of_origin' => 'China',
                'short_description' => 'Never Settle performance flagships featuring fluid AMOLED displays and OxygenOS.',
                'website_url' => 'https://www.oneplus.com',
                'sort_order' => 14,
            ],
            [
                'name' => 'Huawei',
                'slug' => 'huawei',
                'country_of_origin' => 'China',
                'short_description' => 'Global technology leader renowned for XMAGE mobile imaging and HarmonyOS ecosystem.',
                'website_url' => 'https://consumer.huawei.com',
                'sort_order' => 15,
            ],
            [
                'name' => 'Honor',
                'slug' => 'honor',
                'country_of_origin' => 'China',
                'short_description' => 'Magic series flagship innovator featuring Silicon-Carbon batteries and ultra-thin foldable devices.',
                'website_url' => 'https://www.honor.com',
                'sort_order' => 16,
            ],
            [
                'name' => 'Sony',
                'slug' => 'sony',
                'country_of_origin' => 'Japan',
                'short_description' => 'Xperia smartphones powered by Alpha camera technology, Zeiss optics, and 4K OLED displays.',
                'website_url' => 'https://www.sony.com',
                'sort_order' => 17,
            ],
            [
                'name' => 'Asus',
                'slug' => 'asus',
                'country_of_origin' => 'Taiwan',
                'short_description' => 'ROG Phone mobile gaming benchmark and compact Zenfone performance flagships.',
                'website_url' => 'https://www.asus.com',
                'sort_order' => 18,
            ],
            [
                'name' => 'Lenovo',
                'slug' => 'lenovo',
                'country_of_origin' => 'China',
                'short_description' => 'Global personal computing giant developing Legion gaming smartphones and versatile smart devices.',
                'website_url' => 'https://www.lenovo.com',
                'sort_order' => 19,
            ],
            [
                'name' => 'ZTE',
                'slug' => 'zte',
                'country_of_origin' => 'China',
                'short_description' => 'Telecommunications pioneer and creator of Axon smartphones and Nubia photography devices.',
                'website_url' => 'https://www.ztedevices.com',
                'sort_order' => 20,
            ],
            [
                'name' => 'Nothing',
                'slug' => 'nothing',
                'country_of_origin' => 'United Kingdom',
                'short_description' => 'London-based design-led tech company famous for transparent hardware and the Glyph Interface.',
                'website_url' => 'https://nothing.tech',
                'sort_order' => 21,
            ],
            [
                'name' => 'iQOO',
                'slug' => 'iqoo',
                'country_of_origin' => 'China',
                'short_description' => 'High-performance sub-brand by Vivo focused on flagship gaming speeds and rapid charging.',
                'website_url' => 'https://www.iqoo.com',
                'sort_order' => 22,
            ],
            [
                'name' => 'Meizu',
                'slug' => 'meizu',
                'country_of_origin' => 'China',
                'short_description' => 'Premium smartphone maker known for Flyme AI OS, minimalist aesthetics, and audio heritage.',
                'website_url' => 'https://www.meizu.com',
                'sort_order' => 23,
            ],
            [
                'name' => 'Itel',
                'slug' => 'itel',
                'country_of_origin' => 'Hong Kong',
                'short_description' => 'Accessible, budget-friendly mobile devices offering reliable connectivity across emerging markets.',
                'website_url' => 'https://www.itel-life.com',
                'sort_order' => 24,
            ],
            [
                'name' => 'Lava',
                'slug' => 'lava',
                'country_of_origin' => 'India',
                'short_description' => 'Indian consumer electronics company producing Agni 5G smartphones with clean Android OS.',
                'website_url' => 'https://www.lavamobiles.com',
                'sort_order' => 25,
            ],
            [
                'name' => 'HTC',
                'slug' => 'htc',
                'country_of_origin' => 'Taiwan',
                'short_description' => 'Legendary smartphone trailblazer that shaped Android history, now focused on Viverse devices.',
                'website_url' => 'https://www.htc.com',
                'sort_order' => 26,
            ],
            [
                'name' => 'LG',
                'slug' => 'lg',
                'country_of_origin' => 'South Korea',
                'short_description' => 'Iconic Korean electronics giant with celebrated history of G-series and V-series multimedia smartphones.',
                'website_url' => 'https://www.lg.com',
                'sort_order' => 27,
            ],
        ];

        foreach ($brands as $data) {
            // Check by exact slug or exact name to prevent duplication
            $existing = Brand::where('slug', $data['slug'])
                ->orWhere('name', $data['name'])
                ->first();

            // Special case for 'Google' vs 'Google Pixel'
            if (!$existing && ($data['slug'] === 'google-pixel' || $data['name'] === 'Google Pixel')) {
                $existing = Brand::where('slug', 'google')->orWhere('name', 'Google')->first();
            }

            if (!$existing) {
                Brand::create([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'country_of_origin' => $data['country_of_origin'],
                    'short_description' => $data['short_description'],
                    'website_url' => $data['website_url'],
                    'status' => 'published',
                    'sort_order' => $data['sort_order'],
                    'seo_title' => $data['name'] . ' Phones, Specifications & Official Firmware',
                    'meta_description' => 'Discover verified ' . $data['name'] . ' smartphone specifications, official model numbers, firmware downloads, and hardware details.',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe forward-only migration: do not delete user-managed brands.
    }
};
