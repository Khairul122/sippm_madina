<?php
// SCRIPT SEMENTARA — jangan biarkan menetap di public_html.
// Cara pakai ada di deploy-tools/README.md di repo ini.

$token = 'uwVW5Kx3Xfmv';

if (!hash_equals($token, $_GET['token'] ?? '')) {
    http_response_code(403);
    exit('Forbidden');
}

$vendorAutoload = __DIR__.'/../laravel_app/vendor/autoload.php';
$bootstrapApp = __DIR__.'/../laravel_app/bootstrap/app.php';

if (!file_exists($vendorAutoload)) {
    http_response_code(500);
    exit('vendor/autoload.php tidak ditemukan di: ' . $vendorAutoload . ' — pastikan folder vendor/ sudah diupload ke laravel_app/.');
}

if (!file_exists($bootstrapApp)) {
    http_response_code(500);
    exit('bootstrap/app.php tidak ditemukan di: ' . $bootstrapApp . ' — cek apakah path laravel_app/ sudah benar.');
}

require $vendorAutoload;

$app = require_once $bootstrapApp;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$allowedCommands = [
    'key'     => ['key:generate', ['--force' => true]],
    'migrate' => ['migrate', ['--force' => true]],
    'link'    => ['storage:link', []],
    'config'  => ['config:cache', []],
    'route'   => ['route:cache', []],
    'view'    => ['view:cache', []],
    'clear'   => ['config:clear', []],
];

$cmd = $_GET['cmd'] ?? '';

if (!isset($allowedCommands[$cmd])) {
    echo '<pre>Command tidak dikenal. Pilihan: ' . implode(', ', array_keys($allowedCommands)) . '</pre>';
    exit;
}

[$commandName, $parameters] = $allowedCommands[$cmd];

try {
    $status = $kernel->call($commandName, $parameters);
    echo '<pre>';
    echo htmlspecialchars($kernel->output());
    echo "\nExit code: {$status}";
    echo '</pre>';
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<pre>';
    echo 'Error: ' . htmlspecialchars($e->getMessage()) . "\n\n";
    echo htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
}
