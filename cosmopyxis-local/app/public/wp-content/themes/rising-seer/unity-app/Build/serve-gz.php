<?php
/**
 * PHP proxy to serve .gz files with correct headers for Safari
 * This is a fallback if .htaccess rules don't work
 */

// Get the requested file
$file = isset($_GET['file']) ? $_GET['file'] : '';

// Security: only allow specific Unity files
$allowed_files = [
    'unity-app.wasm.gz',
    'unity-app.data.gz',
    'unity-app.framework.js.gz',
    'unity-app.loader.js.gz'
];

if (!in_array($file, $allowed_files)) {
    http_response_code(404);
    die('File not found');
}

// Check if file exists
$filepath = __DIR__ . '/' . $file;
if (!file_exists($filepath)) {
    http_response_code(404);
    die('File not found');
}

// Determine content type based on original extension
$content_type = 'application/octet-stream';
if (strpos($file, '.wasm.gz') !== false) {
    $content_type = 'application/wasm';
} elseif (strpos($file, '.js.gz') !== false) {
    $content_type = 'application/javascript';
} elseif (strpos($file, '.data.gz') !== false) {
    $content_type = 'application/octet-stream';
}

// Send correct headers
header('Content-Type: ' . $content_type);
header('Content-Encoding: gzip');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: public, max-age=31536000');

// Output the file
readfile($filepath);
?>
