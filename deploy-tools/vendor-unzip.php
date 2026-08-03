<?php
/**
 * Fast Vendor Unzipper Tool for SIPAPA Madina
 * Standalone PHP script to extract vendor.zip on server without requiring Composer or SSH.
 */

// Secret Token Authentication
$expectedToken = 'uwVW5Kx3Xfmv';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (!hash_equals($expectedToken, (string) $token)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Forbidden: Invalid authentication token.'
    ]);
    exit;
}

header('Content-Type: application/json');

// Check ZipArchive extension
if (!class_exists('ZipArchive')) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'PHP extension ZipArchive is not installed on this server.'
    ]);
    exit;
}

// Candidate paths for vendor.zip and target vendor folder
$baseDirs = [
    realpath(__DIR__ . '/..'),              // standard app root
    realpath(__DIR__ . '/../laravel_app'),  // CWP hosting layout from public_html
    __DIR__,
];

$zipPath = null;
$targetDir = null;

foreach ($baseDirs as $dir) {
    if ($dir && file_exists($dir . '/vendor.zip')) {
        $zipPath = $dir . '/vendor.zip';
        $targetDir = $dir . '/vendor';
        break;
    }
}

if (!$zipPath) {
    // Also check if target dir can be determined even if zip not found yet
    $appRoot = realpath(__DIR__ . '/..');
    if (file_exists($appRoot . '/bootstrap')) {
        $targetDir = $appRoot . '/vendor';
    } else {
        $cwpRoot = realpath(__DIR__ . '/../laravel_app');
        if ($cwpRoot && file_exists($cwpRoot . '/bootstrap')) {
            $targetDir = $cwpRoot . '/vendor';
        }
    }

    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'File vendor.zip tidak ditemukan.',
        'checked_directories' => array_values(array_filter($baseDirs))
    ]);
    exit;
}

$startTime = microtime(true);

$zip = new ZipArchive();
$res = $zip->open($zipPath);

if ($res !== true) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal membuka vendor.zip. Error code: ' . $res
    ]);
    exit;
}

// Target parent directory where vendor/ will be extracted
$parentDir = dirname($targetDir);

// Extract zip contents
$numFiles = $zip->numFiles;
$extracted = $zip->extractTo($parentDir);
$zip->close();

if (!$extracted) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal meng-ekstrak vendor.zip ke ' . $parentDir
    ]);
    exit;
}

// Remove vendor.zip after successful extraction
@unlink($zipPath);

$duration = round(microtime(true) - $startTime, 2);

echo json_encode([
    'status' => 'success',
    'message' => 'Berhasil meng-ekstrak vendor (' . number_format($numFiles) . ' file) dalam ' . $duration . ' detik.',
    'num_files' => $numFiles,
    'duration_seconds' => $duration,
    'target_dir' => $targetDir,
    'zip_removed' => !file_exists($zipPath)
], JSON_PRETTY_PRINT);
