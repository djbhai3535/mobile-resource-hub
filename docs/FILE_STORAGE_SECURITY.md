# File Storage Security & Threat Mitigation — Mobile Resource Hub

## 1. Threat Model & Mitigation Strategy

| Threat Vector | Attack Scenario | Implemented Mitigation |
| :--- | :--- | :--- |
| **Direct Web Execution** | Attacker uploads malicious `.php` / `.exe` file into web root and triggers execution via URL. | Files are stored strictly outside the document root in private disk directories (`storage/app/private_downloads/`). Web server denies direct HTTP access. |
| **Open Redirect Abuse** | Malicious administrator or payload injects phishing URL into download mirror. | `FileStorageService::isSafeExternalUrl()` strictly validates protocol schemes (HTTP/HTTPS only) and parses host validity. |
| **Server-Side Request Forgery (SSRF)** | Attacker inputs `http://127.0.0.1:8000/admin` or `http://192.168.1.1` to access internal services. | DNS resolution check inspects target IP address, explicitly rejecting loopback, RFC1918 private subnets, and link-local addresses. |
| **PHP Memory Exhaustion** | Large multi-gigabyte firmware archive read with `file_get_contents()` crashes PHP process. | Checksums computed via stream `hash_file('sha256', ...)`. Downloads served in 64 KB chunked streams via `FileStorageService::streamDownload()`. |
| **Arbitrary File Extension Injection** | Attacker attempts to upload `.phtml`, `.phar`, or shell script disguised as archive. | Strict extension allowlist enforces approved archive and binary formats (`zip`, `tar`, `gz`, `7z`, `rar`, `img`, `bin`, `md5`, `pac`, `exe`, `msi`). |

## 2. Safe External URL Validation Algorithm

```php
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
    if (in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0'], true) 
        || str_ends_with($host, '.local') 
        || str_ends_with($host, '.internal')) {
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
```

## 3. Streaming Download Implementation
Downloads of local packages are never buffered into RAM. The response streams 64 KB chunks directly to output buffer while monitoring client connection status:

```php
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
```
