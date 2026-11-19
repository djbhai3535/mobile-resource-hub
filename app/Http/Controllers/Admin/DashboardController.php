<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Device;
use App\Models\DownloadMirror;
use App\Models\DownloadRequest;
use App\Models\Driver;
use App\Models\Firmware;
use App\Models\FlashFile;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\SoftwareTool;
use App\Models\SpecificationGroup;
use App\Models\Tag;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Actual database metrics
        $metrics = [
            'total_brands' => Brand::count(),
            'total_devices' => Device::count(),
            'published_devices' => Device::where('status', 'published')->count(),
            'draft_devices' => Device::where('status', 'draft')->count(),
            'total_spec_groups' => SpecificationGroup::count(),
            'total_posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'draft_posts' => Post::draft()->count(),
            'archived_posts' => Post::archived()->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
            'total_pages' => Page::count(),
            'total_media' => Media::count(),
            'media_storage_bytes' => Media::sum('file_size'),
            'unread_messages' => ContactMessage::unread()->count(),
            'total_messages' => ContactMessage::count(),
            'total_firmware' => Firmware::count(),
            'published_firmware' => Firmware::where('status', 'published')->count(),
            'total_flash_files' => FlashFile::count(),
            'published_flash_files' => FlashFile::where('status', 'published')->count(),
            'total_software' => SoftwareTool::count(),
            'published_software' => SoftwareTool::where('status', 'published')->count(),
            'total_drivers' => Driver::count(),
            'published_drivers' => Driver::where('status', 'published')->count(),
            'total_download_requests' => DownloadRequest::count(),
            'total_mirrors' => DownloadMirror::count(),
            'total_ads' => class_exists(\App\Models\Advertisement::class) ? \App\Models\Advertisement::count() : 0,
            'active_ads' => class_exists(\App\Models\Advertisement::class) ? \App\Models\Advertisement::where('is_active', true)->count() : 0,
        ];

        // Format storage bytes
        $bytes = $metrics['media_storage_bytes'] ?? 0;
        if ($bytes >= 1048576) {
            $metrics['media_storage_formatted'] = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $metrics['media_storage_formatted'] = number_format($bytes / 1024, 2) . ' KB';
        } else {
            $metrics['media_storage_formatted'] = $bytes . ' B';
        }

        // Recent devices
        $recentDevices = Device::with('brand')
            ->latest('id')
            ->take(6)
            ->get();

        // Recent firmware
        $recentFirmwares = Firmware::with(['brand', 'device'])
            ->latest('id')
            ->take(5)
            ->get();

        // Recent posts
        $recentPosts = Post::with(['author', 'category'])
            ->latest('id')
            ->take(6)
            ->get();

        // Recent administrative activities
        $recentActivities = ActivityLog::with('user')
            ->latest('created_at')
            ->take(8)
            ->get();

        // Recent inquiries
        $recentMessages = ContactMessage::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'metrics',
            'recentDevices',
            'recentFirmwares',
            'recentPosts',
            'recentActivities',
            'recentMessages'
        ));
    }
}
