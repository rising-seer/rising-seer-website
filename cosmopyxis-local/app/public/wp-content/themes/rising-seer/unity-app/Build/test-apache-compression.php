<?php
// Test if Apache is properly compressing uncompressed Unity files
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Apache Compression Test for Unity WebGL</title>
    <style>
        body { font-family: monospace; padding: 20px; line-height: 1.6; }
        .test { margin: 20px 0; padding: 15px; border: 2px solid #ccc; border-radius: 5px; }
        .success { background: #e8f5e9; border-color: #4caf50; }
        .error { background: #ffebee; border-color: #f44336; }
        .warning { background: #fff3cd; border-color: #ff9800; }
        .info { background: #e3f2fd; border-color: #2196f3; }
        pre { white-space: pre-wrap; margin: 0; }
        h3 { margin-top: 0; }
        .summary { font-size: 1.2em; font-weight: bold; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Apache Compression Test for Unity WebGL</h1>
    <p>This tests if Apache is properly serving uncompressed files with on-the-fly gzip compression.</p>
    
    <div class="test info">
        <h3>What We're Testing</h3>
        <pre>✓ Files on disk: unity-app.wasm (NO .gz extension)
✓ Apache should compress when serving
✓ Headers should be:
  - Content-Type: application/wasm (NOT application/x-gzip)
  - Content-Encoding: gzip (if compression is working)
  - Transfer-Encoding: chunked or Content-Length present</pre>
    </div>
    
    <div class="test" id="browserTest">
        <h3>Browser Info</h3>
        <pre id="browserInfo"></pre>
    </div>
    
    <div class="test" id="wasmTest">
        <h3>WASM File Test (unity-app.wasm)</h3>
        <pre id="wasmResult"></pre>
    </div>
    
    <div class="test" id="dataTest">
        <h3>Data File Test (unity-app.data)</h3>
        <pre id="dataResult"></pre>
    </div>
    
    <div class="test" id="jsTest">
        <h3>JavaScript File Test (unity-app.framework.js)</h3>
        <pre id="jsResult"></pre>
    </div>
    
    <div class="test" id="safariTest">
        <h3>Safari WebAssembly Streaming Test</h3>
        <pre id="safariResult"></pre>
    </div>
    
    <div class="test info" id="summary">
        <h3>Summary</h3>
        <div id="summaryContent"></div>
    </div>
    
    <script>
        // Browser detection
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        document.getElementById('browserInfo').textContent = 
            `User Agent: ${navigator.userAgent}\n` +
            `Is Safari: ${isSafari}\n` +
            `Platform: ${navigator.platform}\n` +
            `Accepts Gzip: ${navigator.userAgent.includes('gzip') ? 'Unknown (check network tab)' : 'Yes'}`;
        
        async function testFile(filename, expectedType, elementId) {
            const element = document.getElementById(elementId);
            const testDiv = document.getElementById(elementId.replace('Result', 'Test'));
            
            try {
                // Test both with and without Accept-Encoding to see difference
                const tests = [
                    { headers: { 'Accept-Encoding': 'gzip, deflate, br' }, label: 'With compression' },
                    { headers: { 'Accept-Encoding': 'identity' }, label: 'Without compression' }
                ];
                
                let output = '';
                let success = true;
                
                for (const test of tests) {
                    const response = await fetch(filename, { 
                        method: 'HEAD',
                        headers: test.headers
                    });
                    
                    const contentType = response.headers.get('Content-Type') || 'not set';
                    const contentEncoding = response.headers.get('Content-Encoding') || 'not set';
                    const contentLength = response.headers.get('Content-Length') || 'not set';
                    
                    output += `${test.label}:\n`;
                    output += `  Status: ${response.status}\n`;
                    output += `  Content-Type: ${contentType}\n`;
                    output += `  Content-Encoding: ${contentEncoding}\n`;
                    output += `  Content-Length: ${contentLength}\n\n`;
                    
                    // Check if it's correct for Safari
                    if (test.label === 'With compression') {
                        const correctType = contentType.includes(expectedType);
                        const hasCompression = contentEncoding === 'gzip';
                        
                        if (!correctType) {
                            output += `  ❌ Wrong Content-Type! Expected: ${expectedType}\n`;
                            success = false;
                        }
                        if (!hasCompression && test.headers['Accept-Encoding'].includes('gzip')) {
                            output += `  ⚠️  No gzip compression detected\n`;
                        }
                    }
                }
                
                element.textContent = output;
                testDiv.className = `test ${success ? 'success' : 'error'}`;
                return success;
                
            } catch (e) {
                element.textContent = `Error: ${e.message}`;
                testDiv.className = 'test error';
                return false;
            }
        }
        
        async function testSafariWebAssembly() {
            const element = document.getElementById('safariResult');
            const testDiv = document.getElementById('safariTest');
            
            try {
                element.textContent = 'Fetching unity-app.wasm...\n';
                const response = await fetch('unity-app.wasm');
                
                element.textContent += `Content-Type: ${response.headers.get('Content-Type')}\n`;
                element.textContent += `Content-Encoding: ${response.headers.get('Content-Encoding')}\n\n`;
                
                element.textContent += 'Testing WebAssembly.instantiateStreaming...\n';
                await WebAssembly.compileStreaming(response.clone());
                
                element.textContent += '✅ SUCCESS! Safari can stream compile this WASM file!\n';
                element.textContent += 'This means the Content-Type is correct.';
                testDiv.className = 'test success';
                return true;
                
            } catch (e) {
                element.textContent += `❌ FAILED: ${e.message}\n\n`;
                
                // Try non-streaming as fallback
                try {
                    element.textContent += 'Trying non-streaming fallback...\n';
                    const response = await fetch('unity-app.wasm');
                    const buffer = await response.arrayBuffer();
                    await WebAssembly.compile(buffer);
                    element.textContent += '✅ Non-streaming compile works (but Safari streaming failed)\n';
                    element.textContent += 'This indicates a Content-Type issue.';
                    testDiv.className = 'test warning';
                } catch (e2) {
                    element.textContent += `❌ Even fallback failed: ${e2.message}`;
                    testDiv.className = 'test error';
                }
                return false;
            }
        }
        
        async function runAllTests() {
            const results = await Promise.all([
                testFile('unity-app.wasm', 'application/wasm', 'wasmResult'),
                testFile('unity-app.data', 'application/octet-stream', 'dataResult'),
                testFile('unity-app.framework.js', 'application/javascript', 'jsResult'),
                testSafariWebAssembly()
            ]);
            
            // Summary
            const allPassed = results.every(r => r);
            const summaryDiv = document.getElementById('summary');
            const summaryContent = document.getElementById('summaryContent');
            
            if (allPassed) {
                summaryDiv.className = 'test success';
                summaryContent.innerHTML = '<p class="summary">✅ All tests passed! Apache is correctly configured for Unity WebGL.</p>';
            } else {
                summaryDiv.className = 'test error';
                summaryContent.innerHTML = '<p class="summary">❌ Some tests failed. Check the results above.</p>' +
                    '<p>Common issues:</p>' +
                    '<ul>' +
                    '<li>Apache gzip module not enabled (check mod_deflate)</li>' +
                    '<li>MIME types not configured correctly</li>' +
                    '<li>.htaccess rules not being applied</li>' +
                    '</ul>';
            }
        }
        
        // Run tests
        runAllTests();
    </script>
</body>
</html>