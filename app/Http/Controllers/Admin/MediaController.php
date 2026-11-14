<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use App\Models\Media;
use App\Models\Post;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    public function index(Request $request): View
    {
        $query = Media::with('uploader');

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('file_name', 'like', $keyword)
                    ->orWhere('alt_text', 'like', $keyword);
            });
        }

        $media = $query->latest()->paginate(24)->withQueryString();

        return view('admin.media.index', compact('media'));
    }

    public function upload(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:5120', // 5 MB max
                'mimes:jpeg,png,webp,gif',
            ],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('file');

        // SVG explicit prohibition: strictly disabled unless dedicated XML sanitizer installed
        $clientExt = strtolower($file->getClientOriginalExtension());
        if ($clientExt === 'svg' || strtolower($file->getClientMimeType()) === 'image/svg+xml') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'SVG uploads are strictly disabled for security reasons.'], 422);
            }
            return back()->withErrors(['file' => 'SVG uploads are strictly disabled for security reasons. Please upload PNG, JPEG, WEBP, or GIF.']);
        }

        // Explicit server-side MIME type verification using finfo
        $realMime = $file->getMimeType();
        if (!in_array($realMime, $this->allowedMimeTypes, true)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Invalid file format. Only approved image types are allowed.'], 422);
            }
            return back()->withErrors(['file' => 'Security check failed: Disallowed image MIME type detected.']);
        }

        // Store with cryptographic random hash filename to prevent path traversal or script execution
        $extension = $file->getClientOriginalExtension();
        $safeFileName = Str::random(32) . '.' . strtolower($extension);
        $path = $file->storeAs('media', $safeFileName, 'public');

        $media = Media::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_name' => $safeFileName,
            'file_path' => $path,
            'disk' => 'public',
            'mime_type' => $realMime,
            'file_size' => $file->getSize(),
            'alt_text' => $request->alt_text ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'uploaded_by' => Auth::id(),
        ]);

        ActivityLogger::log('create', $media, ['file_name' => $safeFileName, 'size' => $media->file_size]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->url,
                'alt_text' => $media->alt_text,
            ]);
        }

        return back()->with('success', "Image '{$file->getClientOriginalName()}' uploaded successfully.");
    }

    public function update(Request $request, Media $media): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $media->update($validated);

        ActivityLogger::log('update', $media, ['name' => $media->name]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Media details updated.');
    }

    public function destroy(Media $media): RedirectResponse
    {
        // Reference-aware protection: Check if file is in use by Posts, Brands, or Devices
        $path = $media->file_path;
        $url = $media->url;

        $isReferencedInPosts = Post::where('featured_image', $path)
            ->orWhere('featured_image', $url)
            ->orWhere('og_image', $path)
            ->orWhere('og_image', $url)
            ->exists();

        $isReferencedInBrands = Brand::where('logo', $path)
            ->orWhere('logo', $url)
            ->orWhere('og_image', $path)
            ->orWhere('og_image', $url)
            ->exists();

        $isReferencedInDevices = Device::where('featured_image', $path)
            ->orWhere('featured_image', $url)
            ->orWhere('og_image', $path)
            ->orWhere('og_image', $url)
            ->orWhereJsonContains('image_gallery', $path)
            ->orWhereJsonContains('image_gallery', $url)
            ->exists();

        if ($isReferencedInPosts || $isReferencedInBrands || $isReferencedInDevices) {
            return back()->with('error', "Cannot delete '{$media->name}': it is currently referenced by published or draft content (articles, brands, or devices).");
        }

        // Delete actual physical file from storage
        if (Storage::disk($media->disk)->exists($media->file_path)) {
            Storage::disk($media->disk)->delete($media->file_path);
        }

        $name = $media->name;
        $media->delete();

        ActivityLogger::log('delete', $media, ['name' => $name]);

        return back()->with('success', "Media item '{$name}' deleted.");
    }
}
