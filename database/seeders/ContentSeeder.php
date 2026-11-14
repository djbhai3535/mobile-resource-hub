<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('email', 'admin@mobilehub.local')->first() ?? User::first();

        // 1. Categories
        $catNews = Category::firstOrCreate(
            ['slug' => 'smartphone-news'],
            [
                'name' => 'Smartphone News',
                'description' => 'Latest announcements, software updates, and smartphone industry news.',
                'status' => 'active',
            ]
        );

        $catGuides = Category::firstOrCreate(
            ['slug' => 'repair-guides'],
            [
                'name' => 'Repair & Flashing Guides',
                'description' => 'Comprehensive step-by-step firmware flashing, EDL mode, and unbricking guides.',
                'status' => 'active',
            ]
        );

        $catFirmware = Category::firstOrCreate(
            ['slug' => 'firmware-releases'],
            [
                'name' => 'Firmware & Stock ROMs',
                'description' => 'Official verified vendor ROM releases and security patch changelogs.',
                'status' => 'active',
            ]
        );

        // 2. Tags
        $tagSamsung = Tag::firstOrCreate(['slug' => 'samsung'], ['name' => 'Samsung']);
        $tagOneUi = Tag::firstOrCreate(['slug' => 'oneui-7'], ['name' => 'One UI 7.0']);
        $tagAndroid15 = Tag::firstOrCreate(['slug' => 'android-15'], ['name' => 'Android 15']);
        $tagOdin = Tag::firstOrCreate(['slug' => 'odin'], ['name' => 'Odin']);
        $tagQualcomm = Tag::firstOrCreate(['slug' => 'qualcomm'], ['name' => 'Qualcomm']);

        // 3. System CMS Pages
        Page::firstOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Mobile Resource Hub',
                'content' => '<p>Mobile Resource Hub is the definitive engineering reference and software archive for mobile technicians, firmware developers, and smartphone enthusiasts.</p><p>We provide cryptographic hashes, verified firmware checksums, WHQL-certified drivers, and peer-reviewed technical specifications for modern mobile hardware.</p>',
                'seo_title' => 'About Mobile Resource Hub — Specs & Firmware Authority',
                'meta_description' => 'Learn about Mobile Resource Hub, our testing methodology, and our cryptographic verification standards.',
                'is_system' => true,
                'system_key' => 'about',
                'status' => 'published',
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'content' => '<p>Mobile Resource Hub prioritizes visitor privacy. We do not require visitor registration, store personal identifying data from casual readers, or track users across external advertising networks.</p><p>Search queries and anonymous telemetry are sanitized and aggregated strictly to improve catalog accuracy and monitor system performance.</p>',
                'seo_title' => 'Privacy Policy — Mobile Resource Hub',
                'meta_description' => 'Understand how Mobile Resource Hub handles privacy, logs, and cookie preferences.',
                'is_system' => true,
                'system_key' => 'privacy',
                'status' => 'published',
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'terms-and-conditions'],
            [
                'title' => 'Terms and Conditions',
                'content' => '<p>By accessing Mobile Resource Hub, you agree to comply with our acceptable use standards.</p><p>Firmware flashing, bootloader unlocking, and USB driver installations carry inherent risks of data loss or device inoperability. All files are provided strictly for technical diagnostic and recovery purposes.</p>',
                'seo_title' => 'Terms and Conditions of Use — Mobile Resource Hub',
                'meta_description' => 'Terms of use, safety warnings, and liability disclaimers for Mobile Resource Hub.',
                'is_system' => true,
                'system_key' => 'terms',
                'status' => 'published',
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'dmca'],
            [
                'title' => 'DMCA & Copyright Policy',
                'content' => '<p>Mobile Resource Hub respects intellectual property rights. We index official publicly accessible firmware releases, vendor flash tools, and open-source repair utilities.</p><p>If you believe content hosted or linked on this portal infringes your copyright, submit a takedown request to our designated agent through our contact portal.</p>',
                'seo_title' => 'DMCA & Copyright Policy — Mobile Resource Hub',
                'meta_description' => 'DMCA takedown notice procedures and intellectual property protection guidelines.',
                'is_system' => true,
                'system_key' => 'dmca',
                'status' => 'published',
            ]
        );

        // 4. Initial Published Posts
        if ($superAdmin) {
            $post = Post::firstOrCreate(
                ['slug' => 'samsung-oneui-7-global-rollout-schedule'],
                [
                    'author_id' => $superAdmin->id,
                    'category_id' => $catNews->id,
                    'type' => 'news',
                    'title' => 'Samsung Officially Begins One UI 7.0 Android 15 Rollout',
                    'excerpt' => 'The long-awaited stable release of Samsung One UI 7.0 based on Android 15 has officially started deploying to Galaxy S24 series devices globally.',
                    'content' => '<p>The long-awaited stable release of Samsung One UI 7.0 based on Android 15 has officially started deploying to Galaxy S24 series devices globally.</p><p>Key improvements include redesigned system icons, fluid animation physics, advanced Galaxy AI on-device processing, and enhanced battery optimization.</p><h3>Official Rollout Wave 1</h3><ul><li>Galaxy S24 Ultra (SM-S928B / SM-S928U)</li><li>Galaxy S24+ (SM-S926B)</li><li>Galaxy S24 (SM-S921B)</li></ul>',
                    'seo_title' => 'Samsung One UI 7.0 Android 15 Rollout Schedule — Mobile Resource Hub',
                    'meta_description' => 'Complete release schedule and firmware download availability for Samsung One UI 7.0 on Android 15.',
                    'status' => 'published',
                    'published_at' => now()->subDays(2),
                ]
            );

            $post->tags()->syncWithoutDetaching([$tagSamsung->id, $tagOneUi->id, $tagAndroid15->id]);

            $guidePost = Post::firstOrCreate(
                ['slug' => 'how-to-flash-samsung-firmware-using-odin'],
                [
                    'author_id' => $superAdmin->id,
                    'category_id' => $catGuides->id,
                    'type' => 'tutorial',
                    'title' => 'How to Flash Official Samsung Firmware Using Odin 3.14',
                    'excerpt' => 'A complete, step-by-step unbricking and upgrade walkthrough for flashing multi-file Samsung stock ROMs safely.',
                    'content' => '<p>Follow this comprehensive guide to flash 4-file Samsung stock ROMs (BL, AP, CP, and CSC) using the official Samsung Odin 3.14.4 tool.</p><p>Ensure your device has at least 60% battery charge and you have installed the official Samsung USB drivers before proceeding.</p>',
                    'seo_title' => 'How to Flash Samsung Firmware with Odin 3.14 — Step by Step',
                    'meta_description' => 'Learn how to flash official Samsung firmware using Odin 3.14 with step-by-step instructions and safety tips.',
                    'status' => 'published',
                    'published_at' => now()->subDay(),
                ]
            );

            $guidePost->tags()->syncWithoutDetaching([$tagSamsung->id, $tagOdin->id]);
        }

        // 5. Navigation Menus
        $headerMenu = Menu::firstOrCreate(
            ['location' => 'header'],
            ['name' => 'Primary Header Navigation']
        );

        $headerItems = [
            ['label' => 'Phones', 'url' => '/mobile-phones', 'order' => 1],
            ['label' => 'Brands', 'url' => '/brands', 'order' => 2],
            ['label' => 'Firmware', 'url' => '/firmware', 'order' => 3],
            ['label' => 'Flash Files', 'url' => '/flash-files', 'order' => 4],
            ['label' => 'Software', 'url' => '/software', 'order' => 5],
            ['label' => 'Drivers', 'url' => '/drivers', 'order' => 6],
            ['label' => 'News', 'url' => '/news', 'order' => 7],
            ['label' => 'Tutorials', 'url' => '/tutorials', 'order' => 8],
        ];

        foreach ($headerItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $headerMenu->id, 'url' => $item['url']],
                [
                    'label' => $item['label'],
                    'target' => '_self',
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );
        }

        $footerMenu = Menu::firstOrCreate(
            ['location' => 'footer'],
            ['name' => 'Footer Legal & Company Links']
        );

        $footerItems = [
            ['label' => 'About Us', 'url' => '/about-us', 'order' => 1],
            ['label' => 'Contact Us', 'url' => '/contact-us', 'order' => 2],
            ['label' => 'Privacy Policy', 'url' => '/privacy-policy', 'order' => 3],
            ['label' => 'Terms of Service', 'url' => '/terms-and-conditions', 'order' => 4],
            ['label' => 'DMCA & Copyright', 'url' => '/dmca', 'order' => 5],
        ];

        foreach ($footerItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $footerMenu->id, 'url' => $item['url']],
                [
                    'label' => $item['label'],
                    'target' => '_self',
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );
        }

        // 6. Initial Seed Contact Message
        ContactMessage::firstOrCreate(
            ['email' => 'tech@sample-repair.com'],
            [
                'name' => 'Marcus Vance',
                'subject' => 'Request for SM-A546B Firmware Region INS',
                'message' => 'Hello Mobile Resource Hub team. Could you please mirror the recent A546BXXU5BXK1 build for Galaxy A54 5G? The current mirror is reporting slow throughput.',
                'status' => 'unread',
                'ip_hash' => hash('sha256', '127.0.0.1_mrh_salt'),
                'created_at' => now()->subHours(4),
            ]
        );
    }
}
