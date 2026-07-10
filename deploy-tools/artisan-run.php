<?php
// SCRIPT SEMENTARA — jangan biarkan menetap di public_html.
// Cara pakai ada di deploy-tools/README.md di repo ini.

$token = 'uwVW5Kx3Xfmv';

if (!hash_equals($token, $_GET['token'] ?? '')) {
    http_response_code(403);
    exit('Forbidden');
}

require __DIR__.'/../laravel_app/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$allowedCommands = [
    'key'     => 'key:generate --force',
    'migrate' => 'migrate --force',
    'link'    => 'storage:link',
    'config'  => 'config:cache',
    'route'   => 'route:cache',
    'view'    => 'view:cache',
    'clear'   => 'config:clear',
];

$cmd = $_GET['cmd'] ?? '';

if (!isset($allowedCommands[$cmd])) {
    echo '<pre>Command tidak dikenal. Pilihan: ' . implode(', ', array_keys($allowedCommands)) . '</pre>';
    exit;
}

$status = $kernel->call($allowedCommands[$cmd]);

echo '<pre>';
echo htmlspecialchars($kernel->output());
echo "\nExit code: {$status}";
echo '</pre>';
