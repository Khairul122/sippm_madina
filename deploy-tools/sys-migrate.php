<?php
/**
 * Standalone Migration Script untuk Hosting CWP / Shared Hosting tanpa SSH
 * Letakkan file ini di public_html/sys-migrate.php di server CWP Anda.
 */

$token = 'uwVW5Kx3Xfmv';

if (($_GET['token'] ?? '') !== $token) {
    http_response_code(403);
    exit('<pre style="font-family: monospace; color: red;">Forbidden: Token tidak valid.</pre>');
}

// Cari letak vendor/autoload.php di struktur CWP (/home/sipapam/laravel_app/vendor/autoload.php)
$possibleAutoloads = [
    __DIR__ . '/../laravel_app/vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
];

$vendorAutoload = null;
foreach ($possibleAutoloads as $path) {
    if (file_exists($path)) {
        $vendorAutoload = $path;
        break;
    }
}

if (!$vendorAutoload) {
    http_response_code(500);
    exit('<pre style="font-family: monospace; color: red;">Error: vendor/autoload.php tidak ditemukan di server.</pre>');
}

$bootstrapApp = str_replace('/vendor/autoload.php', '/bootstrap/app.php', $vendorAutoload);

require $vendorAutoload;
$app = require_once $bootstrapApp;

$publicPath = realpath(__DIR__);
if ($publicPath) {
    $app->usePublicPath($publicPath);
}

// Eksekusi migrasi via Artisan Console Kernel (Bypass HTTP Request & Session Middleware)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    $status = $kernel->call('migrate', ['--force' => true]);
    $migrateOutput = $kernel->output();

    $seedOutput = '';
    if (isset($_GET['seed']) || isset($_GET['with_seed'])) {
        $kernel->call('db:seed', ['--class' => 'SilapgaWebSqlDataSeeder', '--force' => true]);
        $seedOutput = "\n\n=== SEEDER SILAPGA WEB DATA BERHASIL ===\n" . $kernel->output();
    }

    echo '<pre style="background: #111827; color: #10B981; padding: 24px; font-family: monospace; border-radius: 8px; font-size: 14px;">';
    echo "=== MIGRASI DATABASE SIPAPA MADINA BERHASIL ===\n\n";
    echo htmlspecialchars($migrateOutput);
    echo htmlspecialchars($seedOutput);
    echo "\nStatus Code: {$status} (Success)";
    echo '</pre>';
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<pre style="background: #111827; color: #EF4444; padding: 24px; font-family: monospace; border-radius: 8px; font-size: 14px;">';
    echo "=== ERROR MIGRASI DATABASE ===\n\n";
    echo 'Pesan Error: ' . htmlspecialchars($e->getMessage()) . "\n\n";
    echo htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
}
