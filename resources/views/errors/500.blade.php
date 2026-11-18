<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server Error — Mobile Resource Hub</title>
    <style>
        :root {
            --bg-color: #0b1220;
            --surface-color: rgba(20, 30, 48, 0.85);
            --border-color: rgba(255, 255, 255, 0.1);
            --text-color: #f1f5f9;
            --text-muted: #94a3b8;
            --primary-color: #3b82f6;
            --error-color: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .error-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 48px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
            backdrop-filter: blur(12px);
        }
        .error-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 24px;
        }
        h1 { font-size: 28px; font-weight: 700; margin-bottom: 12px; }
        p { color: var(--text-muted); font-size: 15px; line-height: 1.6; margin-bottom: 28px; }
        .btn {
            display: inline-block;
            background: var(--primary-color);
            color: #fff;
            padding: 12px 28px;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.9; }
        .meta { margin-top: 24px; font-size: 12px; color: var(--text-muted); opacity: 0.6; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">&#9888;</div>
        <h1>500 — Server Error</h1>
        <p>The server encountered an internal issue and was unable to complete your request. Our technical team has been notified and is addressing it.</p>
        <a href="/" class="btn" onclick="location.reload();return false;">&#x21bb; Reload Page</a>
        <div class="meta">Mobile Resource Hub &bull; Reference Code: HTTP-500</div>
    </div>
</body>
</html>
