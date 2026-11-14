<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DownloadMirror;
use App\Models\DownloadRequest;
use App\Services\FileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Process and initiate a download request from an authorized mirror.
     */
    public function downloadMirror(Request $request, DownloadMirror $mirror): StreamedResponse|RedirectResponse
    {
        // 1. Verify mirror is active
        if (!$mirror->is_active) {
            abort(404, 'This download mirror has been deactivated or retired.');
        }

        // 2. Verify parent resource is published
        $resource = $mirror->mirrorable;
        if (!$resource || (method_exists($resource, 'isPublished') && !$resource->isPublished())) {
            abort(404, 'The requested resource is not currently available for public download.');
        }

        // 3. Log download request initiation (noting: initiation does not guarantee completed file transfer)
        DownloadRequest::create([
            'downloadable_type' => get_class($resource),
            'downloadable_id' => $resource->id,
            'mirror_id' => $mirror->id,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit($request->userAgent() ?? '', 500),
            'status' => $mirror->isLocal() ? 'streamed' : 'redirected',
            'created_at' => now(),
        ]);

        // 4. Increment download request counters
        $mirror->increment('download_requests_count');
        if (isset($resource->download_requests_count)) {
            $resource->increment('download_requests_count');
        }

        // 5. Handle Local Storage Stream
        if ($mirror->isLocal()) {
            $downloadName = $resource->title ?? $resource->name ?? 'download';
            $extension = pathinfo($mirror->file_path, PATHINFO_EXTENSION);
            if ($extension && !str_ends_with(strtolower($downloadName), '.' . strtolower($extension))) {
                $downloadName .= '.' . $extension;
            }

            return FileStorageService::streamDownload($mirror->file_path, $downloadName, $mirror->disk);
        }

        // 6. Handle External Mirror Redirect with Open Redirect / SSRF Validation
        if (!FileStorageService::isSafeExternalUrl($mirror->url)) {
            abort(400, 'Security rejection: The configured mirror destination failed protocol and destination safety verification.');
        }

        return redirect()->away($mirror->url);
    }
}
