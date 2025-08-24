<?php
header('Content-Type: text/plain');

// Test if .htaccess is being processed
echo "Testing .htaccess processing in Local by Flywheel\n";
echo "================================================\n\n";

// Check if mod_rewrite is enabled
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "Apache Modules:\n";
    echo "- mod_rewrite: " . (in_array('mod_rewrite', $modules) ? 'ENABLED' : 'DISABLED') . "\n";
    echo "- mod_mime: " . (in_array('mod_mime', $modules) ? 'ENABLED' : 'DISABLED') . "\n";
    echo "- mod_headers: " . (in_array('mod_headers', $modules) ? 'ENABLED' : 'DISABLED') . "\n\n";
}

// Check server software
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n\n";

// Test if this script sees the custom header we set
$headers = getallheaders();
echo "Request Headers:\n";
foreach ($headers as $key => $value) {
    echo "- $key: $value\n";
}

echo "\n\nTo test .htaccess:\n";
echo "1. Check response headers for 'X-Test' header on test.txt\n";
echo "2. If missing, .htaccess is not being processed\n";
?>