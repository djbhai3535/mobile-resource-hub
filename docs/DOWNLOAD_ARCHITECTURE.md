# Download Architecture & Mirror Gateway — Mobile Resource Hub

## 1. Architectural Principles
The Mobile Resource Hub download architecture addresses three core technical challenges:
1. **Hostinger Shared Hosting Constraints:** File transfers must not exhaust PHP memory (`memory_limit`) or exceed execution time limits (`max_execution_time`).
2. **Metric Integrity:** The system records **Download Requests / Initiations**, never falsely claiming verified file completion when redirecting to external hosts or beginning a stream.
3. **Security Isolation:** Protection against Open Redirect attacks, Server-Side Request Forgery (SSRF), and unauthorized web-root executable downloads.

## 2. Gateway Flow (`/downloads/mirror/{mirror}`)

```mermaid
flowchart TD
    User([Public Visitor]) -->|GET /downloads/mirror/{id}| GW[DownloadController Gateway]
    GW --> RL{Rate Limiter: 60/min?}
    RL -- Exceeded --> 429[429 Too Many Requests]
    RL -- OK --> ActiveCheck{Mirror Active & Resource Published?}
    ActiveCheck -- No --> 404[404 Not Found]
    ActiveCheck -- Yes --> LogReq[Log DownloadRequest & Increment Counters]
    
    LogReq --> TypeCheck{Is Local Private File?}
    TypeCheck -- Yes --> Stream[FileStorageService::streamDownload]
    Stream -->|64 KB Chunk Streaming| User
    
    TypeCheck -- No --> SafeCheck{FileStorageService::isSafeExternalUrl?}
    SafeCheck -- Failed SSRF/Localhost --> 400[400 Security Rejection]
    SafeCheck -- Valid HTTP/S --> 302[302 Redirect to External CDN/Cloud]
    302 --> User
```

## 3. Polymorphic Mirror Model (`download_mirrors`)
Any downloadable resource (`Firmware`, `FlashFile`, `SoftwareTool`, `Driver`) can have multiple download mirrors:
- `mirrorable_type`: Fully qualified class name.
- `mirrorable_id`: Model primary key.
- `mirror_name`: Descriptive display label (e.g. `Official Samsung CDN`, `Fast Cloud Mirror`).
- `url`: External download URL.
- `disk`: Storage disk (`local`).
- `file_path`: Relative path in private storage.
- `file_size_bytes`: Integer size.
- `is_local`: Boolean flag differentiating server storage from external redirect.
- `is_official`: Boolean flag denoting first-party OEM mirrors.
- `is_active`: Operational toggle to disable defunct mirrors without deletion.
- `download_requests_count`: Integer counter of requests initiated through this specific mirror.

## 4. Download Request Logging (`download_requests`)
Every download initiation is logged into a dedicated audit table:
- `downloadable_type` & `downloadable_id`
- `mirror_id`
- `ip_address`: Captured for analytics and abuse tracking.
- `user_agent`: Truncated browser identity.
- `status`: `streamed` (local disk) or `redirected` (external CDN).
- `created_at`: Timestamp.

> [!IMPORTANT]
> In strict accordance with the PRD specification, download counts are presented in public and administrative interfaces as **Download Requests** or **Downloads Initiated**, acknowledging that external redirects and browser clicks do not verify whether the client completed the download transfer.
