<?php
/**
 * Mobile Resource Hub — Standalone Production Diagnostic Endpoint
 * 
 * This file runs outside Laravel's HTTP Kernel to diagnose server-level,
 * permission, environment, and boot issues directly on the production host.
 */

// Allow only with secret key
$secretKey = 'mrh_audit_2026';
if (($_GET['key'] ?? '') !== $secretKey) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    return;
}

header('Content-Type: application/json');

$results = [
    'timestamp' => date('c'),
    'php_version' => PHP_VERSION,
    'php_sapi' => PHP_SAPI,
    'user' => function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : get_current_user(),
    'memory_limit' => ini_get('memory_limit'),
    'display_errors' => ini_get('display_errors'),
    'checks' => [],
];

// Check 1: Extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'curl', 'fileinfo'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}
$results['checks']['extensions'] = [
    'ok' => empty($missingExtensions),
    'missing' => $missingExtensions,
];

// Check 2: Writable directories
$dirs = [
    'storage' => __DIR__ . '/../storage',
    'storage/logs' => __DIR__ . '/../storage/logs',
    'storage/framework' => __DIR__ . '/../storage/framework',
    'storage/framework/views' => __DIR__ . '/../storage/framework/views',
    'storage/framework/sessions' => __DIR__ . '/../storage/framework/sessions',
    'storage/framework/cache' => __DIR__ . '/../storage/framework/cache',
    'bootstrap/cache' => __DIR__ . '/../bootstrap/cache',
];

$dirResults = [];
foreach ($dirs as $name => $path) {
    $exists = file_exists($path);
    $writable = $exists && is_writable($path);
    
    // Test actual file write
    $canCreateFile = false;
    if ($exists) {
        $testFile = $path . '/.write_test_' . uniqid();
        $written = @file_put_contents($testFile, 'test');
        if ($written !== false) {
            $canCreateFile = true;
            @unlink($testFile);
        }
    }

    $dirResults[$name] = [
        'exists' => $exists,
        'is_writable' => $writable,
        'can_create_file' => $canCreateFile,
    ];
}
$results['checks']['writable_directories'] = $dirResults;

// Check 3: .env file
$envPath = __DIR__ . '/../.env';
$envExists = file_exists($envPath);
$envReadable = $envExists && is_readable($envPath);
$appKey = '';
$dbHost = '';
$dbName = '';
$dbUser = '';
$dbPass = '';

if ($envReadable) {
    $envContent = file_get_contents($envPath);
    if (preg_match('/^APP_KEY=(.*)$/m', $envContent, $m)) $appKey = trim($m[1]);
    if (preg_match('/^DB_HOST=(.*)$/m', $envContent, $m)) $dbHost = trim($m[1]);
    if (preg_match('/^DB_DATABASE=(.*)$/m', $envContent, $m)) $dbName = trim($m[1]);
    if (preg_match('/^DB_USERNAME=(.*)$/m', $envContent, $m)) $dbUser = trim($m[1]);
    if (preg_match('/^DB_PASSWORD=(.*)$/m', $envContent, $m)) $dbPass = trim($m[1]);
}

$results['checks']['env'] = [
    'exists' => $envExists,
    'readable' => $envReadable,
    'has_app_key' => !empty($appKey),
    'app_key_length' => strlen($appKey),
];

// Check 4: MySQL Connection
$dbCheck = ['ok' => false, 'error' => null];
if (!empty($dbHost) && !empty($dbName)) {
    try {
        $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $dbCheck['ok'] = true;
        
        // Count tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $dbCheck['table_count'] = count($tables);
        $dbCheck['has_firmwares'] = in_array('firmwares', $tables);
        $dbCheck['has_homepage_sections'] = in_array('homepage_sections', $tables);
        $dbCheck['has_site_settings'] = in_array('site_settings', $tables);
        $dbCheck['has_sessions'] = in_array('sessions', $tables);
        $dbCheck['has_cache'] = in_array('cache', $tables);
    } catch (\Throwable $e) {
        $dbCheck['error'] = $e->getMessage();
    }
} else {
    $dbCheck['error'] = 'DB credentials missing in .env';
}
$results['checks']['database'] = $dbCheck;

// Check 5: Laravel Boot Test
$laravelBoot = ['ok' => false, 'error' => null, 'trace' => null];
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    
    // Simulate a request to /
    $request = \Illuminate\Http\Request::create('/', 'GET');
    $response = $kernel->handle($request);
    
    $laravelBoot['ok'] = true;
    $laravelBoot['status_code'] = $response->getStatusCode();
    $laravelBoot['content_length'] = strlen($response->getContent());
} catch (\Throwable $e) {
    $laravelBoot['error'] = get_class($e) . ': ' . $e->getMessage();
    $laravelBoot['file'] = $e->getFile() . ':' . $e->getLine();
    $laravelBoot['trace'] = explode("\n", $e->getTraceAsString());
}
$results['checks']['laravel_boot'] = $laravelBoot;

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
