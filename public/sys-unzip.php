<?php
// Bridge script to execute vendor-unzip.php from public_html
// Will be deployed to /public_html/sys-unzip.php

$scriptInApp = __DIR__ . '/../laravel_app/deploy-tools/vendor-unzip.php';
$scriptLocal = __DIR__ . '/../deploy-tools/vendor-unzip.php';

if (file_exists($scriptInApp)) {
    require $scriptInApp;
} elseif (file_exists($scriptLocal)) {
    require $scriptLocal;
} else {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Script vendor-unzip.php tidak ditemukan di deploy-tools.'
    ]);
}
