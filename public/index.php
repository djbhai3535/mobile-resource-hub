<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Register early fatal error handler to catch boot crashes in PHP-FPM / Nginx
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_USER_ERROR])) {
        error_log(sprintf('[MRH SHUTDOWN FATAL] %s in %s:%d', $error['message'], $error['file'], $error['line']));
    }
});

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

try {
    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // 1. Always log to PHP system error log (which Nginx & PHP-FPM record into /var/log/)
    error_log(sprintf('[MRH ENTRYPOINT EXCEPTION] %s: %s in %s:%d', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
    error_log($e->getTraceAsString());

    // 2. Safe debug display if requested via secure key or if APP_DEBUG=true
    if (($_GET['debug_key'] ?? '') === 'mrh_inspect_500' || (getenv('APP_DEBUG') === 'true')) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
        }
        echo "=== LARAVEL ENTRYPOINT EXCEPTION ===\n";
        echo get_class($e) . ': ' . $e->getMessage() . "\n";
        echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
        echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
        exit;
    }

    // 3. Render clean self-contained HTML 500 error page if headers not sent
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>500 - Server Error</title>';
    echo '<style>body{font-family:system-ui,-apple-system,sans-serif;background:#0b1220;color:#e2e8f0;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px}';
    echo '.box{background:rgba(20,30,48,0.9);border:1px solid rgba(255,255,255,0.1);padding:40px;border-radius:16px;max-width:500px;text-align:center}';
    echo 'h1{color:#ef4444;margin-top:0}a{color:#3b82f6;text-decoration:none}</style></head>';
    echo '<body><div class="box"><h1>500 - Server Error</h1><p>The server encountered an unexpected error. Our technical team is working on resolving it.</p>';
    echo '<p><a href="/" onclick="location.reload();return false;">&#x21bb; Try Again</a></p></div></body></html>';
}
