<?php
// Diagnostic script for Safari Unity loading issues
?>
<!DOCTYPE html>
<html>
<head>
    <title>Safari Unity Diagnostic</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .test { margin: 20px 0; padding: 10px; border: 1px solid #ccc; }
        .success { background: #e8f5e9; }
        .error { background: #ffebee; }
        .info { background: #e3f2fd; }
        pre { white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Safari Unity WebGL Diagnostic</h1>
    
    <div class="test info">
        <h3>Browser Info</h3>
        <pre id="browserInfo"></pre>
    </div>
    
    <div class="test" id="wasmTest">
        <h3>1. Testing WASM File Direct Fetch</h3>
        <pre id="wasmResult"></pre>
    </div>
    
    <div class="test" id="headerTest">
        <h3>2. Testing Response Headers</h3>
        <pre id="headerResult"></pre>
    </div>
    
    <div class="test" id="mimeTest">
        <h3>3. Testing MIME Type Detection</h3>
        <pre id="mimeResult"></pre>
    </div>
    
    <div class="test info">
        <h3>Debug Links</h3>
        <p>Test these URLs directly:</p>
        <ul>
            <li><a href="serve-gz.php?file=unity-app.wasm.gz" target="_blank">PHP Proxy: unity-app.wasm.gz</a></li>
            <li><a href="unity-app.wasm.gz" target="_blank">Direct: unity-app.wasm.gz</a></li>
            <li><a href=".htaccess" target="_blank">View .htaccess</a></li>
        </ul>
    </div>
    
    <script>
        // Browser detection
        const browserInfo = document.getElementById('browserInfo');
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        browserInfo.textContent = `User Agent: ${navigator.userAgent}
Is Safari: ${isSafari}
Platform: ${navigator.platform}`;
        
        // Test 1: Direct fetch of WASM file
        async function testWasmFetch() {
            const result = document.getElementById('wasmResult');
            try {
                // First check if PHP proxy exists
                const proxyCheck = await fetch('serve-gz.php', { method: 'HEAD' });
                result.textContent = `PHP Proxy Check: ${proxyCheck.status}\n\n`;
                
                const response = await fetch('unity-app.wasm.gz', { method: 'HEAD' });
                result.textContent += `Direct Request URL: unity-app.wasm.gz
Final URL: ${response.url}
Status: ${response.status}
Content-Type: ${response.headers.get('Content-Type')}
Content-Encoding: ${response.headers.get('Content-Encoding')}
Content-Length: ${response.headers.get('Content-Length')}
Cache-Control: ${response.headers.get('Cache-Control')}
Server: ${response.headers.get('Server')}`;
                
                // Check if it went through PHP
                if (response.url.includes('serve-gz.php')) {
                    result.textContent += '\n\n✓ Request went through PHP proxy';
                } else {
                    result.textContent += '\n\n✗ Request did NOT go through PHP proxy';
                }
                
                if (response.headers.get('Content-Type') === 'application/wasm' && 
                    response.headers.get('Content-Encoding') === 'gzip') {
                    document.getElementById('wasmTest').classList.add('success');
                    result.textContent += '\n\n✅ CORRECT headers for Safari!';
                } else {
                    document.getElementById('wasmTest').classList.add('error');
                    result.textContent += '\n\nERROR: Incorrect headers for Safari!';
                }
            } catch (e) {
                result.textContent = `Error: ${e.message}`;
                document.getElementById('wasmTest').classList.add('error');
            }
        }
        
        // Test 2: Check all Unity file headers
        async function testAllHeaders() {
            const result = document.getElementById('headerResult');
            const files = ['unity-app.wasm.gz', 'unity-app.data.gz', 'unity-app.framework.js.gz'];
            let output = '';
            
            for (const file of files) {
                try {
                    const response = await fetch(file, { method: 'HEAD' });
                    output += `${file}:\n`;
                    output += `  Content-Type: ${response.headers.get('Content-Type')}\n`;
                    output += `  Content-Encoding: ${response.headers.get('Content-Encoding')}\n`;
                    output += `  Status: ${response.status}\n\n`;
                } catch (e) {
                    output += `${file}: ERROR - ${e.message}\n\n`;
                }
            }
            result.textContent = output;
        }
        
        // Test 3: WebAssembly instantiation
        async function testWasmInstantiation() {
            const result = document.getElementById('mimeResult');
            try {
                // Try to instantiate WebAssembly with streaming
                const wasmResponse = await fetch('unity-app.wasm.gz');
                result.textContent = `Fetch successful
Content-Type: ${wasmResponse.headers.get('Content-Type')}
Trying WebAssembly.instantiateStreaming...`;
                
                // This will fail if MIME type is wrong
                await WebAssembly.compileStreaming(wasmResponse);
                result.textContent += '\nSUCCESS: WebAssembly streaming compilation works!';
                document.getElementById('mimeTest').classList.add('success');
            } catch (e) {
                result.textContent += `\nERROR: ${e.message}`;
                document.getElementById('mimeTest').classList.add('error');
                
                // Try fallback
                try {
                    result.textContent += '\n\nTrying fallback (non-streaming)...';
                    const wasmResponse = await fetch('unity-app.wasm.gz');
                    const wasmBuffer = await wasmResponse.arrayBuffer();
                    await WebAssembly.compile(wasmBuffer);
                    result.textContent += '\nFallback successful - file is valid but streaming failed';
                } catch (e2) {
                    result.textContent += `\nFallback also failed: ${e2.message}`;
                }
            }
        }
        
        // Run all tests
        testWasmFetch();
        testAllHeaders();
        testWasmInstantiation();
    </script>
</body>
</html>