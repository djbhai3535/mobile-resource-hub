<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageService
{
    /**
     * Explicitly approved archive, firmware, and software payload file extensions.
     */
    protected static array $approvedExtensions = [
        'zip',
        'rar',
        '7z',
        'tar',
        'gz',
        'img',
        'bin',
        'md5',
        'pac',
        'exe', // Allowed ONLY as download payload; NEVER executed
        'msi',
    ];

    /**
     * Validate an external URL ensuring protocol safety and preventing open redirect / SSRF.
     */
    public static function isSafeExternalUrl(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['scheme']) || !isset($parsed['host'])) {
            return false;
        }

        // 1. Enforce HTTP / HTTPS scheme only
        $scheme = strtolower($parsed['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower($parsed['host']);

        // 2. Reject localhost and local names
        if (in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0'], true) || str_ends_with($host, '.local') || str_ends_with($host, '.internal')) {
            return false;
        }

        // 3. Resolve IP and reject private / loopback / link-local IP addresses (SSRF defense)
        $ip = gethostbyname($host);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Store an uploaded resource file into private, non-executable storage.
     */
    public static function storePrivateFile(UploadedFile $file, string $subDirectory = 'firmware'): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::$approvedExtensions, true)) {
            throw new \InvalidArgumentException("Disallowed file format: .{$extension}. Only approved archives, images, or packages are accepted.");
        }

        $size = $file->getSize();
        $realPath = $file->getRealPath();

        // Memory-efficient streamed cryptographic checksums
        $md5 = hash_file('md5', $realPath);
        $sha256 = hash_file('sha256', $realPath);

        // Store with cryptographic random filename outside public root
        $safeFileName = Str::random(32) . '.' . $extension;
        $relativePath = $subDirectory . '/' . $safeFileName;

        // Store on local private disk (outside public_html)
        $storedPath = Storage::disk('local')->putFileAs('private_downloads/' . $subDirectory, $file, $safeFileName);

        return [
            'file_name' => $safeFileName,
            'original_name' => $file->getClientOriginalName(),
            'relative_path' => $storedPath,
            'disk' => 'local',
            'extension' => $extension,
            'size_bytes' => $size,
            'size_formatted' => self::formatBytes($size),
            'md5' => $md5,
            'sha256' => $sha256,
        ];
    }

    /**
     * Stream a private local file to the user without exhausting server memory.
     */
    public static function streamDownload(string $relativePath, string $downloadName, string $disk = 'local'): StreamedResponse
    {
        if (!Storage::disk($disk)->exists($relativePath)) {
            abort(404, 'The requested resource file is currently unavailable in server storage.');
        }

        $fileSize = Storage::disk($disk)->size($relativePath);
        $mimeType = Storage::disk($disk)->mimeType($relativePath) ?: 'application/octet-stream';

        return response()->streamDownload(function () use ($relativePath, $disk) {
            $stream = Storage::disk($disk)->readStream($relativePath);
            if ($stream) {
                while (!feof($stream) && connection_status() === 0) {
                    echo fread($stream, 1024 * 64); // 64 KB chunks
                    flush();
                }
                fclose($stream);
            }
        }, $downloadName, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Cache-Control' => 'no-cache, must-revalidate',
            'Content-Disposition' => 'attachment; filename="' . addslashes($downloadName) . '"',
        ]);
    }

    /**
     * Human readable byte formatting.
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get maximum upload size configured in PHP environment.
     */
    public static function getPhpUploadLimits(): array
    {
        return [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
        ];
    }
}
