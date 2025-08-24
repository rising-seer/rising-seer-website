<?php
// Test script to check how the server handles .gz files
header('Content-Type: text/plain');

echo "Testing Unity .gz file headers on production server\n";
echo "==================================================\n\n";

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$buildPath = dirname($_SERVER['REQUEST_URI']);

$files = [
    'unity-app.wasm.gz',
    'unity-app.data.gz',
    'unity-app.framework.js.gz'
];

foreach ($files as $file) {
    $url = $baseUrl . $buildPath . '/' . $file;
    echo "Testing: $file\n";
    echo "URL: $url\n";
    
    // Use HEAD request to get headers without downloading the file
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'header' => "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36\r\n"
        ]
    ]);
    
    $headers = @get_headers($url, 1, $context);
    
    if ($headers) {
        echo "Response Headers:\n";
        foreach ($headers as $key => $value) {
            if (is_array($value)) {
                echo "  $key: " . implode(', ', $value) . "\n";
            } else {
                echo "  $key: $value\n";
            }
        }
    } else {
        echo "  ERROR: Could not fetch headers\n";
    }
    
    echo "\n---\n\n";
}

// Also test what headers THIS script sees when served
echo "Current script headers (what the server sets by default):\n";
$currentHeaders = headers_list();
foreach ($currentHeaders as $header) {
    echo "  $header\n";
}

echo "\n\nServer Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "PHP Version: " . phpversion() . "\n";

// Check if mod_headers is enabled
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "\nApache Modules:\n";
    echo "  mod_headers: " . (in_array('mod_headers', $modules) ? 'ENABLED' : 'DISABLED') . "\n";
    echo "  mod_mime: " . (in_array('mod_mime', $modules) ? 'ENABLED' : 'DISABLED') . "\n";
    echo "  mod_deflate: " . (in_array('mod_deflate', $modules) ? 'ENABLED' : 'DISABLED') . "\n";
}
?>