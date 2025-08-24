<?php
// Enhanced Safari Unity diagnostic with production-ready tests
?>
<!DOCTYPE html>
<html>
<head>
    <title>Safari Unity Diagnostic v2</title>
    <style>
        body { font-family: monospace; padding: 20px; line-height: 1.5; }
        .test { margin: 20px 0; padding: 15px; border: 2px solid #ccc; border-radius: 5px; }
        .success { background: #e8f5e9; border-color: #4caf50; }
        .error { background: #ffebee; border-color: #f44336; }
        .warning { background: #fff3cd; border-color: #ff9800; }
        .info { background: #e3f2fd; border-color: #2196f3; }
        pre { white-space: pre-wrap; margin: 0; }
        h3 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .good { color: green; font-weight: bold; }
        .bad { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Safari Unity WebGL Diagnostic v2</h1>
    
    <div class="test info">
        <h3>Server Environment</h3>
        <table>
            <tr><th>Server Software</th><td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td></tr>
            <tr><th>PHP Version</th><td><?php echo phpversion(); ?></td></tr>
            <tr><th>Document Root</th><td><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></td></tr>
            <tr><th>Script Path</th><td><?php echo __FILE__; ?></td></tr>
            <tr><th>Apache Modules</th><td><?php 
                if (function_exists('apache_get_modules')) {
                    $modules = apache_get_modules();
                    echo in_array('mod_rewrite', $modules) ? '<span class="good">✓ mod_rewrite</span> ' : '<span class="bad">✗ mod_rewrite</span> ';
                    echo in_array('mod_headers', $modules) ? '<span class="good">✓ mod_headers</span> ' : '<span class="bad">✗ mod_headers</span> ';
                    echo in_array('mod_mime', $modules) ? '<span class="good">✓ mod_mime</span>' : '<span class="bad">✗ mod_mime</span>';
                } else {
                    echo 'Not Apache or function disabled';
                }
            ?></td></tr>
        </table>
    </div>
    
    <div class="test info">
        <h3>File Check</h3>
        <?php
        $files = [
            'unity-app.wasm.gz',
            'unity-app.data.gz',
            'unity-app.framework.js.gz',
            'serve-gz.php',
            '.htaccess'
        ];
        echo "<table>";
        echo "<tr><th>File</th><th>Exists</th><th>Size</th></tr>";
        foreach ($files as $file) {
            $path = __DIR__ . '/' . $file;
            $exists = file_exists($path);
            echo "<tr>";
            echo "<td>$file</td>";
            echo "<td>" . ($exists ? '<span class="good">✓</span>' : '<span class="bad">✗</span>') . "</td>";
            echo "<td>" . ($exists ? number_format(filesize($path)) . ' bytes' : '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        ?>
    </div>
    
    <div class="test" id="browserTest">
        <h3>Browser Detection</h3>
        <pre id="browserInfo"></pre>
    </div>
    
    <div class="test" id="headerTest">
        <h3>Unity File Headers</h3>
        <pre id="headerResult"></pre>
    </div>
    
    <div class="test" id="streamTest">
        <h3>WebAssembly Streaming Test</h3>
        <pre id="streamResult"></pre>
    </div>
    
    <div class="test info">
        <h3>Quick Tests</h3>
        <ul>
            <li><a href="unity-app.wasm.gz" target="_blank">Direct: unity-app.wasm.gz</a></li>
            <li><a href="serve-gz.php?file=unity-app.wasm.gz" target="_blank">PHP Proxy: unity-app.wasm.gz</a></li>
            <li><a href="../" target="_blank">Unity App</a></li>
        </ul>
    </div>
    
    <script>
        // Browser info
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        document.getElementById('browserInfo').textContent = 
            `User Agent: ${navigator.userAgent}\n` +
            `Is Safari: ${isSafari ? 'YES' : 'NO'}\n` +
            `Platform: ${navigator.platform}`;
        document.getElementById('browserTest').className = `test ${isSafari ? 'warning' : 'info'}`;
        
        // Test headers
        async function testHeaders() {
            const result = document.getElementById('headerResult');
            const testDiv = document.getElementById('headerTest');
            let output = '';
            let allGood = true;
            
            const files = ['unity-app.wasm.gz', 'unity-app.data.gz', 'unity-app.framework.js.gz'];
            
            for (const file of files) {
                try {
                    const response = await fetch(file, { method: 'HEAD' });
                    const contentType = response.headers.get('Content-Type') || 'not set';
                    const contentEncoding = response.headers.get('Content-Encoding') || 'not set';
                    
                    output += `${file}:\n`;
                    output += `  Status: ${response.status}\n`;
                    output += `  Content-Type: ${contentType}`;
                    
                    // Check if correct for Safari
                    let expectedType = 'application/octet-stream';
                    if (file.includes('.wasm.')) expectedType = 'application/wasm';
                    if (file.includes('.js.')) expectedType = 'application/javascript';
                    
                    if (contentType.includes(expectedType)) {
                        output += ' ✓\n';
                    } else {
                        output += ` ✗ (expected ${expectedType})\n`;
                        allGood = false;
                    }
                    
                    output += `  Content-Encoding: ${contentEncoding}`;
                    if (contentEncoding === 'gzip') {
                        output += ' ✓\n';
                    } else {
                        output += ' ✗ (expected gzip)\n';
                        allGood = false;
                    }
                    
                    output += '\n';
                } catch (e) {
                    output += `${file}: ERROR - ${e.message}\n\n`;
                    allGood = false;
                }
            }
            
            result.textContent = output;
            testDiv.className = `test ${allGood ? 'success' : 'error'}`;
        }
        
        // Test WebAssembly streaming
        async function testStreaming() {
            const result = document.getElementById('streamResult');
            const testDiv = document.getElementById('streamTest');
            
            try {
                result.textContent = 'Fetching unity-app.wasm.gz...\n';
                const response = await fetch('unity-app.wasm.gz');
                
                const contentType = response.headers.get('Content-Type') || 'not set';
                const contentEncoding = response.headers.get('Content-Encoding') || 'not set';
                
                result.textContent += `Content-Type: ${contentType}\n`;
                result.textContent += `Content-Encoding: ${contentEncoding}\n\n`;
                
                result.textContent += 'Testing WebAssembly.instantiateStreaming...\n';
                
                try {
                    await WebAssembly.compileStreaming(response.clone());
                    result.textContent += '✅ SUCCESS! Safari can stream this WASM file!\n';
                    result.textContent += 'The headers are configured correctly.';
                    testDiv.className = 'test success';
                } catch (streamError) {
                    result.textContent += `❌ Streaming failed: ${streamError.message}\n\n`;
                    
                    // Try fallback
                    result.textContent += 'Testing non-streaming fallback...\n';
                    try {
                        const buffer = await response.arrayBuffer();
                        await WebAssembly.compile(buffer);
                        result.textContent += '✅ Fallback works (file is valid WASM)\n';
                        result.textContent += '⚠️  But Safari streaming is broken due to headers';
                        testDiv.className = 'test warning';
                    } catch (fallbackError) {
                        result.textContent += `❌ Even fallback failed: ${fallbackError.message}\n`;
                        result.textContent += 'File might be corrupted or double-compressed';
                        testDiv.className = 'test error';
                    }
                }
            } catch (e) {
                result.textContent = `Network error: ${e.message}`;
                testDiv.className = 'test error';
            }
        }
        
        // Run tests
        testHeaders();
        testStreaming();
    </script>
</body>
</html>