<?php
/*
Template Name: Unity App Page
*/

// Embed Unity directly for clean /cosmopyxis-web-app URL
// Skip WordPress header/footer for fullscreen Unity experience
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Rising Seer - Cosmic Insights</title>
    
    <!-- Preload Montserrat font to prevent FOUT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="shortcut icon" href="/wp-content/themes/rising-seer/unity-app/TemplateData/favicon.ico">
    <link rel="stylesheet" href="/wp-content/themes/rising-seer/unity-app/TemplateData/style.css">
    <script src="/wp-content/themes/rising-seer/unity-app/unity-touchend-fix.js"></script>
    
    <!-- Override Unity styles with our theme colors -->
    <style>
        body { 
            background: #290228 !important; /* Deep purple override */
            font-family: 'Montserrat', sans-serif !important;
        }
        #unity-container {
            background: #290228 !important; /* Deep purple override */
        }
        #unity-canvas { 
            background: #290228 !important; /* Deep purple override */
        }
        /* Force all elements to inherit purple background */
        html {
            background: #290228 !important;
        }
    </style>
    
    <!-- COMPREHENSIVE DEBUG LOGGING SYSTEM -->
    <script>
        // Debug logging system - BALANCED VERSION FOR BUG REPORTING
        // NOTE: In production builds, Unity's "Full" stack traces should be disabled
        // to significantly improve performance. This logging system will still work
        // but will be much faster without Unity's verbose stack trace generation.
        const debugLog = {
            logs: [],
            maxLogs: 1000, // Balanced limit for bug reporting while preventing memory issues
            originalConsole: {
                log: console.log,
                warn: console.warn,
                error: console.error,
                info: console.info,
                debug: console.debug
            },
            
            log: function(level, message, data = null, skipConsole = false) {
                const timestamp = new Date().toISOString();
                const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                
                const logEntry = {
                    timestamp,
                    level,
                    message,
                    data: data ? JSON.stringify(data) : null,
                    userAgent: navigator.userAgent,
                    url: window.location.href,
                    stack: null // Disabled stack traces - causes massive log spam in Unity WebGL
                };
                
                this.logs.push(logEntry);
                
                // Maintain max logs
                if (this.logs.length > this.maxLogs) {
                    this.logs.splice(0, this.logs.length - this.maxLogs);
                }
                
                // Console output with styling (only if not from console override)
                if (!skipConsole) {
                    const style = {
                        'ERROR': 'color: #ff6b6b; font-weight: bold;',
                        'WARN': 'color: #ffd93d; font-weight: bold;',
                        'INFO': 'color: #6bcf7f; font-weight: bold;',
                        'DEBUG': 'color: #4ecdc4;',
                        'LOG': 'color: #ffffff;'
                    };
                    
                    this.originalConsole.log(`%c[${level}] ${timestamp} - ${message}`, style[level] || '');
                    if (data) this.originalConsole.log('Data:', data);
                }
                
                // Update counter in real-time
                const counter = document.getElementById('log-counter');
                if (counter) {
                    counter.textContent = this.logs.length;
                }
                
                // Auto-save logs - less frequent on mobile
                const saveInterval = isMobile ? 50 : 10; // Save every 50 logs on mobile vs 10 on desktop
                if (this.logs.length % saveInterval === 0) {
                    this.saveToStorage();
                }
            },
            
            // Override all console methods to capture everything
            overrideConsole: function() {
                const self = this;
                
                // Helper to process arguments into a string
                const processArgs = (args) => {
                    return Array.from(args).map(arg => {
                        if (typeof arg === 'object') {
                            try {
                                return JSON.stringify(arg, null, 2);
                            } catch (e) {
                                return String(arg);
                            }
                        }
                        return String(arg);
                    }).join(' ');
                };
                
                // Override console.log
                console.log = function(...args) {
                    self.originalConsole.log.apply(console, args);
                    self.log('LOG', processArgs(args), null, true);
                };
                
                // Override console.warn
                console.warn = function(...args) {
                    self.originalConsole.warn.apply(console, args);
                    self.log('WARN', processArgs(args), null, true);
                };
                
                // Override console.error
                console.error = function(...args) {
                    self.originalConsole.error.apply(console, args);
                    self.log('ERROR', processArgs(args), null, true);
                };
                
                // Override console.info
                console.info = function(...args) {
                    self.originalConsole.info.apply(console, args);
                    self.log('INFO', processArgs(args), null, true);
                };
                
                // Override console.debug
                console.debug = function(...args) {
                    self.originalConsole.debug.apply(console, args);
                    self.log('DEBUG', processArgs(args), null, true);
                };
                
                this.log('INFO', 'Console override activated - capturing all console output');
            },
            
            saveToStorage: function() {
                const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                
                try {
                    // On mobile, only save the last 200 logs to prevent localStorage quota issues
                    const logsToSave = isMobile ? this.logs.slice(-200) : this.logs;
                    localStorage.setItem('cosmopyxis_debug_logs', JSON.stringify(logsToSave));
                } catch (e) {
                    console.error('Failed to save logs to localStorage:', e);
                }
            },
            
            downloadLogs: function() {
                // Create detailed log output with full stack traces
                const logText = this.logs.map(log => {
                    let entry = `[${log.timestamp}] ${log.level}: ${log.message}`;
                    
                    if (log.data) {
                        entry += `\nData: ${log.data}`;
                    }
                    
                    if (log.stack && (log.level === 'ERROR' || log.level === 'WARN')) {
                        entry += `\nStack Trace:\n${log.stack}`;
                    }
                    
                    entry += `\nUser Agent: ${log.userAgent}`;
                    entry += `\nURL: ${log.url}`;
                    entry += '\n' + '='.repeat(80);
                    
                    return entry;
                }).join('\n\n');
                
                // Add header information
                const header = `COSMOPYXIS WEBGL DEBUG LOG
Generated: ${new Date().toISOString()}
Total Entries: ${this.logs.length}
Browser: ${navigator.userAgent}
URL: ${window.location.href}
${'='.repeat(80)}\n\n`;
                
                const fullLog = header + logText;
                
                const blob = new Blob([fullLog], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `cosmopyxis_debug_${new Date().getTime()}.log`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                this.log('INFO', `Downloaded log file with ${this.logs.length} entries`);
            },
            
            downloadUnityLogs: function() {
                // Filter for Unity-specific logs
                const unityPatterns = [
                    /^\[Unity/,
                    /^\[WebGL/,
                    /^\[Cartomancer/,
                    /^\[Rising Seer/,
                    /UnityLoader/,
                    /Unity/,
                    /SendMessage/,
                    /Module\./,
                    /canvas/,
                    /WebGL.*Unity/,
                    /unity-app/
                ];
                
                const unityLogs = this.logs.filter(log => {
                    // Check if message matches Unity patterns
                    return unityPatterns.some(pattern => pattern.test(log.message));
                });
                
                // Create Unity-specific log output
                const logText = unityLogs.map(log => {
                    let entry = `[${log.timestamp}] ${log.level}: ${log.message}`;
                    
                    if (log.data) {
                        entry += `\nData: ${log.data}`;
                    }
                    
                    if (log.stack && (log.level === 'ERROR' || log.level === 'WARN')) {
                        entry += `\nStack Trace:\n${log.stack}`;
                    }
                    
                    entry += '\n' + '='.repeat(80);
                    
                    return entry;
                }).join('\n\n');
                
                // Add header information
                const header = `COSMOPYXIS UNITY-SPECIFIC DEBUG LOG
Generated: ${new Date().toISOString()}
Unity Entries: ${unityLogs.length} of ${this.logs.length} total
Browser: ${navigator.userAgent}
URL: ${window.location.href}
${'='.repeat(80)}\n\n`;
                
                const fullLog = header + logText;
                
                const blob = new Blob([fullLog], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `cosmopyxis_unity_debug_${new Date().getTime()}.log`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                this.log('INFO', `Downloaded Unity-specific log file with ${unityLogs.length} entries`);
            },
            
            clearLogs: function() {
                this.logs = [];
                localStorage.removeItem('cosmopyxis_debug_logs');
            }
        };

        // --- Rising Seer global capture-phase keyboard handler -------------
        (function() {
            function preEmptiveKeyBlocker(e) {
                if (window.__rs_bugModalOpen) {
                    // Stop Unity (capture phase) from swallowing the keystroke
                    e.stopImmediatePropagation();
                    // Do NOT preventDefault; textarea still needs the event
                }
            }
            ['keydown', 'keypress', 'keyup'].forEach(evt => {
                window.addEventListener(evt, preEmptiveKeyBlocker, true); // capture=true
            });
        })();
        // -------------------------------------------------------------------
        
        // Load existing logs from storage
        try {
            const savedLogs = localStorage.getItem('cosmopyxis_debug_logs');
            if (savedLogs) {
                debugLog.logs = JSON.parse(savedLogs);
                debugLog.log('INFO', `Loaded ${debugLog.logs.length} existing log entries`);
            }
        } catch (e) {
            debugLog.log('WARN', 'Failed to load existing logs', e.message);
        }
        
        // Global error handler
        window.addEventListener('error', (e) => {
            debugLog.log('ERROR', 'JavaScript Error', {
                message: e.message,
                filename: e.filename,
                lineno: e.lineno,
                colno: e.colno,
                stack: e.error ? e.error.stack : null
            });
        });
        
        // Unhandled promise rejections
        window.addEventListener('unhandledrejection', (e) => {
            debugLog.log('ERROR', 'Unhandled Promise Rejection', {
                reason: e.reason,
                stack: e.reason && e.reason.stack ? e.reason.stack : null
            });
        });
        
        // Make debug log available globally
        window.debugLog = debugLog;
        
        // Manual Unity input test function
        window.testUnityInput = function() {
            if (window.unityInstance) {
                const canvas = document.getElementById('unity-canvas');
                
                // Try to send a message directly to Unity
                try {
                    window.unityInstance.SendMessage('EventSystem', 'SetSelectedGameObject', '');
                    debugLog.log('INFO', 'Sent SetSelectedGameObject to EventSystem');
                } catch (e) {
                    debugLog.log('ERROR', 'Failed to send message to EventSystem', e.message);
                }
                
                // Try to activate the first button
                try {
                    window.unityInstance.SendMessage('LoginPanel', 'OnButtonClick', '');
                    debugLog.log('INFO', 'Sent OnButtonClick to LoginPanel');
                } catch (e) {
                    debugLog.log('ERROR', 'Failed to send message to LoginPanel', e.message);
                }
                
                // Force canvas focus
                canvas.focus();
                
                // Dispatch a Unity-friendly event
                const event = new PointerEvent('pointerdown', {
                    bubbles: true,
                    cancelable: true,
                    view: window,
                    detail: 1,
                    screenX: window.innerWidth / 2,
                    screenY: window.innerHeight / 2,
                    clientX: window.innerWidth / 2,
                    clientY: window.innerHeight / 2,
                    pointerId: 1,
                    pointerType: 'mouse',
                    isPrimary: true
                });
                canvas.dispatchEvent(event);
                
                debugLog.log('INFO', 'Manual Unity input test completed');
            } else {
                debugLog.log('ERROR', 'Unity instance not found');
            }
        };

        // BEGIN INSERT: WebGL input bridge fallback stubs (ensures env.NotifyInputField* are callable)
        if (typeof window._NotifyInputFieldFocused === 'undefined') {
            window._NotifyInputFieldFocused = function(fieldNamePtr, screenX, screenY, fieldHeight) {
                var name = (typeof UTF8ToString === 'function' ? UTF8ToString(fieldNamePtr) : '[ptr:' + fieldNamePtr + ']');
                console.debug('[Fallback _NotifyInputFieldFocused]', name, screenX, screenY, fieldHeight);
            };
        }
        if (typeof window._NotifyInputFieldBlurred === 'undefined') {
            window._NotifyInputFieldBlurred = function(fieldNamePtr) {
                var name = (typeof UTF8ToString === 'function' ? UTF8ToString(fieldNamePtr) : '[ptr:' + fieldNamePtr + ']');
                console.debug('[Fallback _NotifyInputFieldBlurred]', name);
            };
        }
        // END INSERT

        // Activate console override to capture ALL console output
        debugLog.overrideConsole();
        
        debugLog.log('INFO', 'Debug logging system initialized');
        debugLog.log('INFO', 'Page load started', {
            userAgent: navigator.userAgent,
            viewport: `${window.innerWidth}x${window.innerHeight}`,
            url: window.location.href
        });
        
        // Track which phase we're in
        debugLog.log('INFO', 'PHASE 1: Debug system ready, Firebase module loading next');
    </script>
    
    <!-- Firebase Web SDK removed - all auth now handled through Seer-Agent API -->
    <script>
        // Log that Firebase SDK has been removed
        if (window.debugLog) {
            window.debugLog.log('INFO', 'Firebase Web SDK removed - all auth handled through Seer-Agent API');
        }
        
        // CRITICAL: Prevent browser shortcuts that might interfere with Unity input
        document.addEventListener('keydown', function(event) {
            // Prevent browser refresh shortcuts that might interfere with Unity
            if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
                if (debugLog) {
                    debugLog.log('INFO', 'Prevented browser refresh (Ctrl/Cmd+R) to protect Unity input');
                }
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
            
            // Prevent other browser shortcuts during typing
            if (event.target && (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA')) {
                // Allow normal typing in input fields - don't block regular keys
                if (!event.ctrlKey && !event.metaKey && !event.altKey) {
                    return true;
                }
                
                // Block browser shortcuts when typing in Unity input fields
                if ((event.ctrlKey || event.metaKey) && (
                    event.key === 'r' || event.key === 'R' ||
                    event.key === 'l' || event.key === 'L' ||
                    event.key === 'f' || event.key === 'F' ||  // Find
                    event.key === 'h' || event.key === 'H'    // History
                )) {
                    if (debugLog) {
                        debugLog.log('INFO', `Prevented browser shortcut ${event.key} during Unity input`);
                    }
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }
        }, true); // Use capture phase to catch events early
    </script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            background: #290228; /* Rising Seer deep purple */
            color: white;
            font-family: 'Montserrat', sans-serif;
            font-weight: 400; /* Regular weight */
            overflow: hidden; /* Prevent scrollbars */
        }
        
        #unity-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #290228; /* Same deep purple background */
            overflow: hidden; /* Prevent any overflow */
        }
        
        #unity-canvas {
            background: #290228; /* Rising Seer deep purple */
            display: block;
            cursor: pointer !important; /* Ensure Unity can receive clicks */
            touch-action: none; /* Prevent touch interference */
            -webkit-tap-highlight-color: transparent; /* Remove tap highlight on mobile */
            /* Canvas will be sized by JavaScript for optimal fit */
            image-rendering: -webkit-optimize-contrast; /* Improve scaling quality */
            image-rendering: crisp-edges; /* Modern browsers */
            image-rendering: pixelated; /* Fallback for pixel art */
        }
        
        /* Loading UI */
        #unity-loading-bar {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            width: 300px;
            text-align: center;
        }
        
        #unity-progress-bar-empty {
            width: 300px;
            height: 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            overflow: hidden; /* Changed back to hidden for proper clipping */
            margin: 0 auto !important; /* Override Unity's 10px top margin */
            position: relative;
            box-sizing: border-box;
        }
        
        #unity-progress-bar-full {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
            transition: width 0.3s ease;
            border-radius: 10px;
            position: absolute;
            top: 0;
            left: 0;
            box-sizing: border-box;
            margin: 0 !important; /* Override Unity's 10px top margin */
        }
        
        .loading-text {
            text-align: center;
            color: #FFBD59; /* Rising Seer accent gold */
            font-size: 14px;
            margin-top: 10px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
        }
        
        #unity-warning {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1001;
            max-width: 80%;
            text-align: center;
        }
        
        /* Fix for Unity input handling */
        #unity-container * {
            pointer-events: auto !important;
        }
        
        /* Ensure Unity canvas can receive focus */
        #unity-canvas:focus {
            outline: none;
        }
        
        /* BUG REPORT BUTTON */
        #bug-report-button {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 10000;
            width: 40px;
            height: 40px;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0.6;
        }
        
        #bug-report-button:hover {
            opacity: 1;
            background: rgba(0,0,0,0.8);
            transform: scale(1.1);
        }
        
        /* BUG REPORT MODAL */
        #bug-report-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            pointer-events: auto !important;
        }
        
        .modal-content {
            background: #2a1a2e;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 90%;
            border: 2px solid #FFBD59;
            position: relative;
            z-index: 10002;
            pointer-events: auto !important;
        }
        
        .modal-content h3 {
            color: #FFBD59;
            margin-bottom: 20px;
            font-size: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        #bug-description {
            width: 100%;
            min-height: 100px;
            background: rgba(0,0,0,0.5);
            border: 1px solid #FFBD59;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            resize: vertical;
            position: relative;
            z-index: 10010;
            pointer-events: auto !important;
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
            font-size: 14px;
            /* Force standard input behavior */
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
            user-select: text !important;
            -webkit-touch-callout: text !important;
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .modal-buttons button {
            background: #FFBD59;
            color: #1A0019;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .modal-buttons button:hover {
            background: #FFA500;
        }
        
        #report-status {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }
        
        /* DEBUG CONTROLS */
        #debug-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 10000;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            max-width: 200px;
            pointer-events: auto;
        }
        
        #debug-controls button {
            background: #FFBD59;
            color: #1A0019;
            border: none;
            padding: 5px 10px;
            margin: 2px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
            font-weight: bold;
        }
        
        #debug-controls button:hover {
            background: #FFA500;
            transform: scale(1.05);
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            #unity-progress-bar-empty {
                width: 250px;
            }
            
            .loading-text {
                font-size: 12px;
            }
            
            #debug-controls {
                top: 5px;
                right: 5px;
                font-size: 10px;
                max-width: 150px;
            }
        }
        
        .error { color: #ff6b6b; }
        .success { color: #4ecdc4; }
    </style>
</head>
<body>
    <!-- BUG REPORT BUTTON -->
    <div id="bug-report-button" title="Report an issue">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 22H22L12 2Z" stroke="#FFBD59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 9V13" stroke="#FFBD59" stroke-width="2" stroke-linecap="round"/>
            <circle cx="12" cy="17" r="1" fill="#FFBD59"/>
        </svg>
    </div>
    
    <!-- DEBUG CONTROLS (Dev Mode) -->
    <div id="debug-controls" style="display: none;">
        <div>🔍 Debug Tools</div>
        <button onclick="debugLog.downloadLogs()">Download All Logs</button>
        <button onclick="debugLog.downloadUnityLogs()">Unity Logs Only</button>
        <button onclick="debugLog.clearLogs()">Clear Logs</button>
        <button onclick="console.log('Debug logs:', debugLog.logs); alert('Logs printed to console. Press F12 to view.')">Show Logs</button>
        <button onclick="showChromeExportInstructions()">Console Export Help</button>
        <button onclick="alert('Auth is now handled through Seer-Agent API')">Auth Info</button>
        <button onclick="clearAllCache()">Clear All Cache</button>
        <button onclick="downloadPlayerPrefs()">Download PlayerPrefs</button>
        <button onclick="toggleDevMode()">Hide Dev</button>
        <div id="log-count" style="font-size: 10px; margin-top: 5px;">
            Logs: <span id="log-counter">0</span>
        </div>
        <div style="font-size: 10px; margin-top: 5px; color: #6bcf7f;">
            ✅ Capturing ALL console output
        </div>
    </div>
    
    <!-- BUG REPORT MODAL -->
    <div id="bug-report-modal" style="display: none;">
        <div class="modal-content">
            <h3>Report an Issue</h3>
            <textarea id="bug-description" placeholder="Briefly describe what happened..." maxlength="500"></textarea>
            <div class="modal-buttons">
                <button onclick="sendBugReport()">Send Report</button>
                <button onclick="closeBugReport()">Cancel</button>
            </div>
            <div id="report-status"></div>
        </div>
    </div>

    <div id="unity-container">
        <canvas id="unity-canvas" tabindex="1"></canvas>
        
        <div id="unity-loading-bar">
            <div id="unity-progress-bar-empty">
                <div id="unity-progress-bar-full"></div>
            </div>
            <div class="loading-text" id="loading-text">Loading Rising Seer...</div>
        </div>
        
        <div id="unity-warning"></div>
    </div>

    <script>
        // Wait for DOM to be ready before using debugLog
        document.addEventListener('DOMContentLoaded', function() {
            debugLog.log('INFO', 'DOM loaded, starting Unity setup');
            
            // Force purple background on all elements
            document.documentElement.style.backgroundColor = '#290228';
            document.body.style.backgroundColor = '#290228';
            const container = document.getElementById('unity-container');
            if (container) container.style.backgroundColor = '#290228';
            const canvas = document.getElementById('unity-canvas');
            if (canvas) canvas.style.backgroundColor = '#290228';
        });
        
        // Dev mode toggle with hyper key combination - MOVED OUTSIDE to global scope
        window.devModeEnabled = false;
        
        window.toggleDevMode = function() {
            window.devModeEnabled = !window.devModeEnabled;
            const debugControls = document.getElementById('debug-controls');
            const bugButton = document.getElementById('bug-report-button');
            
            if (window.devModeEnabled) {
                debugControls.style.display = 'block';
                bugButton.style.display = 'none';
                if (debugLog) {
                    debugLog.log('INFO', 'Dev mode enabled');
                }
            } else {
                debugControls.style.display = 'none';
                bugButton.style.display = 'flex';
                if (debugLog) {
                    debugLog.log('INFO', 'Dev mode disabled');
                }
            }
        }
        
        // Download PlayerPrefs function - exports all Unity PlayerPrefs data
        window.downloadPlayerPrefs = function() {
            if (debugLog) {
                debugLog.log('INFO', 'Downloading PlayerPrefs data...');
            }
            
            try {
                // Get all PlayerPrefs-like data from localStorage
                const playerPrefsData = {};
                const sessionData = {};
                const allData = {};
                
                // Iterate through all localStorage keys
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key) {
                        const value = localStorage.getItem(key);
                        
                        // Categorize the data
                        if (key.includes('Unity') || key.includes('unity') || 
                            key.includes('PlayerPrefs') || key.includes('Screenmanager') ||
                            key.includes('Firebase') || key.includes('firebase')) {
                            playerPrefsData[key] = value;
                        } else if (key.includes('session') || key.includes('Session') ||
                                   key.includes('user') || key.includes('User') ||
                                   key.includes('onboarding') || key.includes('RisingSeer')) {
                            sessionData[key] = value;
                        }
                        
                        // Include everything in allData
                        allData[key] = value;
                    }
                }
                
                // Try to get Unity-specific PlayerPrefs if Unity provides access
                if (window.unityInstance) {
                    try {
                        // Request PlayerPrefs data from Unity
                        window.unityInstance.SendMessage('PlayerPrefsExporter', 'ExportAllPlayerPrefs', '');
                        if (debugLog) {
                            debugLog.log('DEBUG', 'Requested PlayerPrefs export from Unity');
                        }
                    } catch (e) {
                        if (debugLog) {
                            debugLog.log('DEBUG', 'Unity PlayerPrefsExporter not available', e);
                        }
                    }
                }
                
                // Get current authentication state from localStorage/sessionStorage
                const authData = {
                    seerAgentAuth: localStorage.getItem('risingSeer_authToken') || sessionStorage.getItem('risingSeer_authToken'),
                    userEmail: localStorage.getItem('risingSeer_userEmail') || sessionStorage.getItem('risingSeer_userEmail')
                };
                
                // Compile the export data
                const exportData = {
                    metadata: {
                        exportDate: new Date().toISOString(),
                        url: window.location.href,
                        userAgent: navigator.userAgent,
                        platform: 'WebGL',
                        unityVersion: 'WebGL Build',
                        playerPrefsCount: Object.keys(playerPrefsData).length,
                        sessionDataCount: Object.keys(sessionData).length,
                        totalLocalStorageItems: localStorage.length
                    },
                    playerPrefs: playerPrefsData,
                    sessionData: sessionData,
                    authData: authData,
                    allLocalStorage: allData
                };
                
                // Create formatted JSON
                const jsonContent = JSON.stringify(exportData, null, 2);
                
                // Create download
                const blob = new Blob([jsonContent], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `RisingSeer_PlayerPrefs_${new Date().getTime()}.json`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                if (debugLog) {
                    debugLog.log('INFO', `Downloaded PlayerPrefs with ${Object.keys(allData).length} total entries`);
                }
                
                // Show summary
                alert(`PlayerPrefs Export Complete!\n\n` +
                      `Total localStorage items: ${localStorage.length}\n` +
                      `Unity/PlayerPrefs items: ${Object.keys(playerPrefsData).length}\n` +
                      `Session/User data items: ${Object.keys(sessionData).length}\n` +
                      `Seer-Agent auth: ${authData.userEmail ? 'Logged in as ' + authData.userEmail : 'Not logged in'}`);
                
            } catch (error) {
                if (debugLog) {
                    debugLog.log('ERROR', 'Failed to download PlayerPrefs', error);
                }
                alert('Failed to export PlayerPrefs. See console for details.');
            }
        }
        
        // Unity callback for PlayerPrefs export (if Unity sends data back)
        window.onUnityPlayerPrefsExport = function(jsonData) {
            if (debugLog) {
                debugLog.log('INFO', 'Received PlayerPrefs data from Unity');
            }
            try {
                const unityPrefs = JSON.parse(jsonData);
                // Merge with existing export or create new download
                const exportData = {
                    metadata: {
                        exportDate: new Date().toISOString(),
                        source: 'Unity Direct Export',
                        platform: 'WebGL'
                    },
                    unityPlayerPrefs: unityPrefs,
                    webLocalStorage: {}
                };
                
                // Add web localStorage data
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key) {
                        exportData.webLocalStorage[key] = localStorage.getItem(key);
                    }
                }
                
                // Download the combined data
                const jsonContent = JSON.stringify(exportData, null, 2);
                const blob = new Blob([jsonContent], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `RisingSeer_Unity_PlayerPrefs_${new Date().getTime()}.json`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                if (debugLog) {
                    debugLog.log('INFO', 'Unity PlayerPrefs export completed');
                }
            } catch (error) {
                if (debugLog) {
                    debugLog.log('ERROR', 'Failed to process Unity PlayerPrefs data', error);
                }
            }
        }
        
        // Clear All Cache function - clears all Rising Seer data
        window.clearAllCache = function() {
            if (!confirm('This will clear ALL Rising Seer cache and data. You will be returned to the login screen. Continue?')) {
                return;
            }
            
            if (debugLog) {
                debugLog.log('INFO', 'Clearing all cache...');
            }
            
            try {
                // Clear localStorage
                const keysToRemove = [];
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key && (key.includes('rising') || key.includes('Rising') || 
                               key.includes('seer') || key.includes('Seer') ||
                               key.includes('onboarding') || key.includes('auth') ||
                               key.includes('user') || key.includes('session'))) {
                        keysToRemove.push(key);
                    }
                }
                
                keysToRemove.forEach(key => {
                    localStorage.removeItem(key);
                    if (debugLog) {
                        debugLog.log('DEBUG', `Removed localStorage: ${key}`);
                    }
                });
                
                // Clear sessionStorage
                sessionStorage.clear();
                if (debugLog) {
                    debugLog.log('DEBUG', 'Cleared sessionStorage');
                }
                
                // Clear Seer-Agent auth tokens
                localStorage.removeItem('risingSeer_authToken');
                sessionStorage.removeItem('risingSeer_authToken');
                localStorage.removeItem('risingSeer_userEmail');
                sessionStorage.removeItem('risingSeer_userEmail');
                if (debugLog) {
                    debugLog.log('INFO', 'Seer-Agent auth tokens cleared');
                }
                
                // Clear any Unity PlayerPrefs via SendMessage
                if (window.unityInstance) {
                    try {
                        // Try to send clear cache message to various possible receivers
                        window.unityInstance.SendMessage('SessionContext', 'ClearAllData', '');
                        window.unityInstance.SendMessage('OnboardingController', 'ClearCache', '');
                        window.unityInstance.SendMessage('UserContext', 'Clear', '');
                        if (debugLog) {
                            debugLog.log('DEBUG', 'Sent clear cache messages to Unity');
                        }
                    } catch (e) {
                        if (debugLog) {
                            debugLog.log('DEBUG', 'Some Unity clear cache messages may have failed', e);
                        }
                    }
                }
                
                if (debugLog) {
                    debugLog.log('INFO', 'All cache cleared - reloading page...');
                }
                
                // Reload the page after a short delay
                setTimeout(() => {
                    window.location.reload(true); // Force reload from server
                }, 1000);
                
            } catch (error) {
                if (debugLog) {
                    debugLog.log('ERROR', 'Failed to clear cache', error);
                }
                alert('Failed to clear cache. See console for details.');
            }
        }
        
        // Listen for Shift+Control+Command+Option+D
        document.addEventListener('keydown', function(e) {
            // Debug key press
            if (e.shiftKey && e.ctrlKey && e.metaKey && e.altKey) {
                if (debugLog) {
                    debugLog.log('DEBUG', 'Hyper key combo detected', { key: e.key });
                }
            }
            
            if (e.shiftKey && e.ctrlKey && e.metaKey && e.altKey && (e.key === 'D' || e.key === 'd')) {
                e.preventDefault();
                e.stopPropagation();
                window.toggleDevMode();
                return false;
            }
        }, true); // Use capture phase
        
        // Update log counter
        setInterval(() => {
            const counter = document.getElementById('log-counter');
            if (counter) counter.textContent = debugLog.logs.length;
        }, 1000);
        
        // Chrome DevTools export instructions
        window.showChromeExportInstructions = function() {
            const instructions = `
CHROME DEVTOOLS CONSOLE EXPORT:

Method 1 - Save as File:
1. Open DevTools (F12)
2. Go to Console tab
3. Right-click in the console area
4. Select "Save as..."
5. Save the console log file

Method 2 - Copy All:
1. Open DevTools (F12)
2. Go to Console tab
3. Click in console area
4. Press Ctrl+A (or Cmd+A on Mac) to select all
5. Press Ctrl+C (or Cmd+C) to copy
6. Paste into a text file

Method 3 - Console API:
1. Open DevTools Console
2. Run: copy(debugLog.logs)
3. This copies all logs to clipboard
4. Paste into a text file

Note: Our enhanced logging captures ${debugLog.logs.length} entries
Including Unity logs, errors, and all console output.`;
            
            alert(instructions);
            debugLog.log('INFO', 'Chrome export instructions shown');
        };
        
        // Bug report functionality
        document.getElementById('bug-report-button').addEventListener('click', function() {
            // Indicate modal state for global keyboard handler
            window.__rs_bugModalOpen = true;

            if (window.unityInstance && window.unityInstance.Module) {
                // Politely disable Unity's keyboard capture in 6.x builds
                window.unityInstance.Module.captureAllKeyboardInput = false;

                // Redirect Unity's hidden input shim to an inert element
                const dummy = document.createElement('div');
                window.unityInstance.Module.keyboardListeningElement = dummy;

                // Hide Unity canvas so it cannot steal accidental clicks
                window.unityInstance.Module.canvas.style.pointerEvents = 'none';
            }

            // Show modal
            document.getElementById('bug-report-modal').style.display = 'flex';

            // Focus textarea for immediate typing
            setTimeout(() => {
                const textarea = document.getElementById('bug-description');
                textarea.focus();
                textarea.select();
                debugLog.log('INFO', 'Bug report modal opened, Unity keyboard capture disabled via captureAllKeyboardInput = false');
            }, 100);
        });
        
        function closeBugReport() {
            document.getElementById('bug-report-modal').style.display = 'none';
            document.getElementById('bug-description').value = '';
            document.getElementById('report-status').innerHTML = '';

            // Modal closed – allow Unity to resume normal input capture
            window.__rs_bugModalOpen = false;

            if (window.unityInstance && window.unityInstance.Module) {
                window.unityInstance.Module.captureAllKeyboardInput = true;
                window.unityInstance.Module.keyboardListeningElement = window.unityInstance.Module.canvas;

                // Show Unity canvas again
                window.unityInstance.Module.canvas.style.pointerEvents = 'auto';
                window.unityInstance.Module.canvas.focus();

                debugLog.log('INFO', 'Unity keyboard capture restored via captureAllKeyboardInput = true');
            }
        }
        
        // Close modal on background click
        document.getElementById('bug-report-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBugReport();
            }
        });
        
        async function sendBugReport() {
            const description = document.getElementById('bug-description').value.trim();
            const statusEl = document.getElementById('report-status');
            
            if (!description) {
                statusEl.innerHTML = '<span style="color: #ff6b6b;">Please describe the issue</span>';
                return;
            }
            
            statusEl.innerHTML = '<span style="color: #FFBD59;">Sending report...</span>';
            
            try {
                // Prepare bug report data
                const bugReport = {
                    timestamp: new Date().toISOString(),
                    description: description,
                    url: window.location.href,
                    userAgent: navigator.userAgent,
                    viewport: `${window.innerWidth}x${window.innerHeight}`,
                    platform: /iPhone|iPad|iPod|Android/i.test(navigator.userAgent) ? 'mobile' : 'desktop',
                    logs: debugLog.logs.slice(-500), // Reduced to 500 logs for mobile performance
                    authState: {
                        authToken: localStorage.getItem('risingSeer_authToken') || sessionStorage.getItem('risingSeer_authToken'),
                        userEmail: localStorage.getItem('risingSeer_userEmail') || sessionStorage.getItem('risingSeer_userEmail')
                    }
                };
                
                // Send to Seer-Agent API instead of direct Firestore
                const response = await fetch('https://api.risingseer.com/bug-report', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(bugReport)
                });
                
                if (response.ok) {
                    statusEl.innerHTML = '<span style="color: #4ecdc4;">✓ Report sent successfully!</span>';
                    setTimeout(closeBugReport, 2000);
                } else {
                    throw new Error(`Server responded with ${response.status}`);
                }
                
            } catch (error) {
                if (debugLog) {
                    debugLog.log('ERROR', 'Failed to send bug report', error);
                }
                
                // Fallback: store locally
                try {
                    const localReport = {
                        timestamp: new Date().toISOString(),
                        description: description,
                        error: error.message
                    };
                    localStorage.setItem('pending_bug_report_' + Date.now(), JSON.stringify(localReport));
                    statusEl.innerHTML = '<span style="color: #ffd93d;">Report saved locally. Will retry later.</span>';
                    setTimeout(closeBugReport, 3000);
                } catch (localError) {
                    statusEl.innerHTML = '<span style="color: #ff6b6b;">Failed to send report. Please try again.</span>';
                }
            }
        }
        
        var container = document.querySelector("#unity-container");
        var canvas = document.querySelector("#unity-canvas");
        var loadingBar = document.querySelector("#unity-loading-bar");
        var progressBarFull = document.querySelector("#unity-progress-bar-full");
        var loadingText = document.querySelector("#loading-text");
        var warningBanner = document.querySelector("#unity-warning");
        
        // Track if keyboard is open on mobile - DECLARE BEFORE USE
        let keyboardOpen = false;
        let initialViewportHeight = window.innerHeight;

        function unityShowBanner(msg, type) {
            if (debugLog) {
                debugLog.log('INFO', 'Unity Banner', { message: msg, type: type });
            }
            
            // Auto-dismiss HTTP Content-Type warnings that block UI
            if (msg.includes('HTTP Response Header') || msg.includes('Content-Type')) {
                if (debugLog) {
                    debugLog.log('INFO', 'Auto-dismissing HTTP Content-Type warning to prevent UI blocking');
                }
                setTimeout(() => {
                    warningBanner.style.display = 'none';
                    warningBanner.innerHTML = '';
                }, 1000); // Show for 1 second then dismiss
            } else {
                // Show other warnings normally
                warningBanner.innerHTML = 
                    '<div style="background: ' + (type == 'error' ? '#ff6b6b' : '#ffe66d') + 
                    '; color: ' + (type == 'error' ? 'white' : 'black') + 
                    '; padding: 15px; border-radius: 8px; margin: 10px; max-width: 400px;">' + msg + '</div>';
                warningBanner.style.display = 'block';
            }
        }

        // Mobile-first responsive sizing - 9:16 portrait aspect ratio (like phone)
        function resizeCanvas() {
            // Don't resize if Unity is actively handling input (keyboard is open)
            if (document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA')) {
                if (debugLog) {
                    debugLog.log('DEBUG', 'Skipping resize - input field is active');
                }
                return;
            }
            
            // Don't resize if mobile keyboard is open
            if (keyboardOpen) {
                if (debugLog) {
                    debugLog.log('DEBUG', 'Skipping resize - mobile keyboard is open');
                }
                return;
            }
            
            var containerWidth = window.innerWidth;
            var containerHeight = window.innerHeight;
            
            if (debugLog) {
                debugLog.log('DEBUG', 'Resizing canvas', {
                    containerWidth: containerWidth,
                    containerHeight: containerHeight
                });
            }
            
            // Target mobile portrait aspect ratio
            var targetAspect = 9 / 16; // Mobile portrait (width / height)
            var containerAspect = containerWidth / containerHeight;
            
            var canvasWidth, canvasHeight;

            // Use native DPR everywhere
            const devicePR  = window.devicePixelRatio || 1;
            const referenceWidth  = 1080; // Unity build reference resolution
            const referenceHeight = 1920;

            // Fit strategy for strict 9:16 canvas – letterbox appropriately
            if (containerAspect < targetAspect) {
                /* Device is narrower than 9:16 – fit to width for display, but send fixed resolution to Unity */
                canvasWidth  = containerWidth;
                canvasHeight = canvasWidth / targetAspect;
            } else {
                /* Device is wider – fit to height for display */
                canvasHeight = containerHeight;
                canvasWidth  = canvasHeight * targetAspect;
            }
            
            // Send Unity exactly what it expects: CSS dimensions × device pixel ratio
            var pixelRatio  = devicePR;
            
            const unityInternalWidth  = Math.round(canvasWidth  * pixelRatio);
            const unityInternalHeight = Math.round(canvasHeight * pixelRatio);

            canvas.width  = unityInternalWidth;
            canvas.height = unityInternalHeight;
            
            // Center the canvas in the container
            canvas.style.position = 'absolute';
            // Account for browser UI offsets / safe areas (iOS notch etc.)
            const vv  = window.visualViewport;
            const offX = vv ? vv.offsetLeft : 0;
            const offY = vv ? vv.offsetTop  : 0;

            // Use original container-fitting dimensions for centering, not our computed canvas size
            let displayWidth, displayHeight;
            if (containerAspect < targetAspect) {
                displayWidth  = containerWidth;
                displayHeight = displayWidth / targetAspect;
            } else {
                displayHeight = containerHeight;
                displayWidth  = displayHeight * targetAspect;
            }

            canvas.style.left = (offX + (containerWidth  - displayWidth)  / 2) + 'px';
            canvas.style.top  = (offY + (containerHeight - displayHeight) / 2) + 'px';
            
            // Set canvas to display at the expected visual size
            canvas.style.width  = displayWidth + 'px';
            canvas.style.height = displayHeight + 'px';
            
            // Ensure canvas has proper touch/click handling
            canvas.style.touchAction = 'none'; // Prevent touch scrolling
            canvas.style.userSelect = 'none'; // Prevent text selection
            canvas.style.webkitUserSelect = 'none';
            canvas.style.msUserSelect = 'none';
            canvas.style.mozUserSelect = 'none';
            
            // Inform Unity of the new internal resolution when matchWebGLToCanvasSize is false
            if (window.unityInstance && typeof window.unityInstance.SetCanvasSize === 'function') {
                window.unityInstance.SetCanvasSize(canvas.width, canvas.height, true);
                if (debugLog) {
                    debugLog.log('DEBUG', 'Unity SetCanvasSize called', {
                        internalWidth: canvas.width,
                        internalHeight: canvas.height
                    });
                }
            }

            // Unity will handle its own WebGL context scaling if matchWebGLToCanvasSize true
            // We override scaling manually when it's false to keep control
            
            if (debugLog) {
                debugLog.log('DEBUG', 'Canvas resized', {
                    canvasWidth: canvasWidth,
                    canvasHeight: canvasHeight,
                    pixelRatio: pixelRatio,
                    devicePixelRatio: devicePR,
                    referenceWidth: referenceWidth,
                    unityInternalWidth: unityInternalWidth,
                    unityInternalHeight: unityInternalHeight,
                    SET_CANVAS_SCALER_TO: `${unityInternalWidth}×${unityInternalHeight}`,
                    aspectRatio: '9:16'
                });
            }
        }

        // Set canvas dimensions BEFORE Unity loads (proven solution)
        var containerWidth = window.innerWidth;
        var containerHeight = window.innerHeight;
        var targetAspect = 9 / 16;
        var containerAspect = containerWidth / containerHeight;
        
        var canvasWidth, canvasHeight;
        if (containerAspect < targetAspect) {
            canvasWidth = containerWidth;
            canvasHeight = canvasWidth / targetAspect;
        } else {
            canvasHeight = containerHeight;
            canvasWidth = canvasHeight * targetAspect;
        }
        
        // Set canvas dimensions before Unity loads
        canvas.style.width = canvasWidth + 'px';
        canvas.style.height = canvasHeight + 'px';
        canvas.style.position = 'absolute';
        canvas.style.left = ((containerWidth - canvasWidth) / 2) + 'px';
        canvas.style.top = ((containerHeight - canvasHeight) / 2) + 'px';
        
        // Initial resize (for Unity's internal sizing)
        resizeCanvas();
        
        // Mobile keyboard detection and canvas shifting
        window.addEventListener('resize', function() {
            if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
                const currentHeight = window.innerHeight;
                const heightDifference = initialViewportHeight - currentHeight;
                
                if (debugLog) {
                    debugLog.log('DEBUG', 'Mobile resize detected', {
                        initialHeight: initialViewportHeight,
                        currentHeight: currentHeight,
                        heightDifference: heightDifference
                    });
                }
                
                // If viewport height decreased significantly, keyboard is likely open
                if (heightDifference > 100) {
                    keyboardOpen = true;
                    // Shift canvas upward so active field stays above keyboard
                    var shift = Math.round(heightDifference/2);
                    container.style.transform = 'translateY(-'+ shift +'px)';
                    if (debugLog) {
                        debugLog.log('DEBUG', 'Mobile keyboard open, shifting canvas', {shift: shift});
                    }
                } else if (heightDifference < 50) {
                    keyboardOpen = false;
                    container.style.transform = '';
                    if (debugLog) {
                        debugLog.log('DEBUG', 'Mobile keyboard closed, resetting canvas position');
                    }
                }
            }
        });
        
        // Debounced resize handler to prevent input field dismissal
        let resizeTimeout;
        window.addEventListener('resize', function() {
            // Clear any pending resize
            clearTimeout(resizeTimeout);
            
            // Debounce the resize to avoid interfering with input fields
            resizeTimeout = setTimeout(() => {
                resizeCanvas();
                
                // Only trigger Unity resize if it's not during input
                if (window.unityInstance && window.unityInstance.Module && !document.activeElement) {
                    const resizeEvent = new Event('resize');
                    window.dispatchEvent(resizeEvent);
                    
                    if (debugLog) {
                        debugLog.log('INFO', 'Unity resize event triggered after debounced window resize');
                    }
                }
            }, 250); // Longer debounce to avoid input interference
        });

        var script = document.createElement("script");
        script.src = "/wp-content/themes/rising-seer/unity-app/Build/unity-app.loader.js";
        script.onload = () => {
            if (debugLog) {
                debugLog.log('INFO', 'Unity loader script loaded');
            }
            
            // Detect Safari
            const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            
            // FORCE COMPRESSED FILES - Testing if large file size causes 500 error
            const fileExtension = ".gz"; // Always use compressed
            
            var config = {
                dataUrl: "/wp-content/themes/rising-seer/unity-app/Build/unity-app.data" + fileExtension,
                frameworkUrl: "/wp-content/themes/rising-seer/unity-app/Build/unity-app.framework.js" + fileExtension,
                codeUrl: "/wp-content/themes/rising-seer/unity-app/Build/unity-app.wasm" + fileExtension,
                streamingAssetsUrl: "StreamingAssets",
                companyName: "DefaultCompany",
                productName: "Rising Seer",
                productVersion: "1.0",
                showBanner: unityShowBanner,
                // WebGL Input Settings - Use Unity's built-in scaling (proven solution)
                matchWebGLToCanvasSize: true, // Let Unity handle final scaling
                devicePixelRatio: window.devicePixelRatio || 1 // Use native DPR
            };
            
            // Safari-specific configuration
            if (isSafari) {
                if (debugLog) {
                    debugLog.log('INFO', 'Safari browser detected, applying Safari-specific configuration');
                }
                
                // Safari needs explicit WebAssembly instantiation settings
                config.webglContextAttributes = {
                    preserveDrawingBuffer: true,
                    premultipliedAlpha: false,
                    antialias: false,
                    powerPreference: "high-performance"
                };
                
                // Safari WebAssembly streaming compilation workaround
                config.streamingAssetsUrl = "/wp-content/themes/rising-seer/unity-app/StreamingAssets";
                
                // Ensure proper MIME type handling for Safari
                config.decompressionFallback = true;
                
                // Safari IndexedDB/UnityCache workaround
                // Safari has strict security policies for IndexedDB in cross-origin contexts
                try {
                    // Try to initialize IndexedDB early to avoid Unity errors
                    const testDB = indexedDB.open('test', 1);
                    testDB.onsuccess = () => {
                        testDB.result.close();
                        indexedDB.deleteDatabase('test');
                        if (debugLog) {
                            debugLog.log('INFO', 'IndexedDB available in Safari');
                        }
                    };
                    testDB.onerror = () => {
                        if (debugLog) {
                            debugLog.log('WARN', 'IndexedDB not available in Safari, Unity caching disabled');
                        }
                        // Disable Unity caching if IndexedDB is not available
                        config.cacheControl = function(url) {
                            // Disable caching for all Unity files in Safari
                            return "no-store";
                        };
                    };
                } catch (e) {
                    if (debugLog) {
                        debugLog.log('WARN', 'IndexedDB test failed in Safari', e.message);
                    }
                }
            }

            if (debugLog) {
                debugLog.log('INFO', 'Creating Unity instance with config', config);
            }

            if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
                container.className = "unity-mobile";
                // Keep device pixel ratio for proper scaling on mobile
                if (debugLog) {
                    debugLog.log('INFO', 'Mobile device detected, maintaining device pixel ratio for proper scaling');
                }
            } else {
                if (debugLog) {
                    debugLog.log('INFO', 'Desktop device detected, maintaining responsive 9:16 aspect ratio');
                }
                // Don't override canvas size - let resizeCanvas() handle it for proper 9:16 ratio
            }
            
            loadingBar.style.display = "block"; // Ensure loading bar is visible
            loadingText.style.display = "block";

            createUnityInstance(canvas, config, (progress) => {
                progressBarFull.style.width = 100 * progress + "%";
                loadingText.textContent = `Loading Rising Seer... ${Math.round(progress * 100)}%`;
                if (debugLog) {
                    // debugLog.log('DEBUG', 'Unity loading progress', { progress: (progress * 100).toFixed(1) + '%' });
                }
            }).then((unityInstance) => {
                if (debugLog) {
                    debugLog.log('INFO', 'Unity instance created successfully');
                }
                
                // CRITICAL: Make Unity instance globally available
                window.unityInstance = unityInstance;
                if (debugLog) {
                    debugLog.log('INFO', 'Unity instance set globally');
                }
                
                loadingBar.style.display = "none";
                if (debugLog) {
                    debugLog.log('INFO', 'Loading bar hidden, Unity fully loaded');
                }
                
                // WebGL Input System Activation
                // Wait for Unity to fully initialize before trying to interact
                let initAttempts = 0;
                const tryInitializeInput = () => {
                    initAttempts++;
                    
                    try {
                        // Check if Unity's UI system is ready by trying to communicate with it
                        unityInstance.SendMessage('UIManager', 'CheckReady', '');
                        if (debugLog) {
                            debugLog.log('INFO', 'Unity UI system appears ready');
                        }
                        
                        // Focus the canvas
                        canvas.focus();
                        canvas.click(); // Sometimes Unity needs an actual click
                        
                    } catch (e) {
                        if (initAttempts < 10) {
                            if (debugLog) {
                                debugLog.log('DEBUG', `Unity UI not ready yet, attempt ${initAttempts}/10`);
                            }
                            setTimeout(tryInitializeInput, 1000);
                        } else {
                            if (debugLog) {
                                debugLog.log('WARN', 'Unity UI initialization timeout', e.message);
                            }
                        }
                    }
                };
                
                // Start initialization after a delay
                setTimeout(tryInitializeInput, 2000);
                
                // Ensure proper 9:16 aspect ratio after Unity loads
                setTimeout(() => {
                    resizeCanvas();
                    if (debugLog) {
                        debugLog.log('INFO', 'Final aspect ratio adjustment applied');
                    }
                    
                    // Simple resize to ensure Unity content fits properly
                    if (unityInstance && unityInstance.Module) {
                        // Just trigger a resize event for Unity
                        const resizeEvent = new Event('resize');
                        window.dispatchEvent(resizeEvent);
                        
                        if (debugLog) {
                            debugLog.log('INFO', 'Unity resize event triggered');
                        }
                    }
                    
                    // CRITICAL: Give Unity canvas focus for input handling
                    // Lightweight focus setup – just ensure the canvas is focusable.
                    canvas.tabIndex = 1;
                    /* Removed aggressive canvas focus event listeners – Unity handles focus natively.
                       This prevents the on-screen keyboard from being dismissed when a text field gains focus. */
                }, 100);
                
                // Monitor Unity instance
                setInterval(() => {
                    if (!window.unityInstance) {
                        if (debugLog) {
                            debugLog.log('ERROR', 'Unity instance lost! This may explain the clearing issue.');
                        }
                    }
                }, 5000);
                
            }).catch((error) => {
                if (debugLog) {
                    debugLog.log('ERROR', 'Unity instance creation failed', {
                        message: error.message,
                        stack: error.stack
                    });
                }
            });
        };
        
        script.onerror = (error) => {
            if (debugLog) {
                debugLog.log('ERROR', 'Unity loader script failed to load', error);
            }
        };
        
        if (debugLog) {
            debugLog.log('INFO', 'Adding Unity loader script to DOM');
        }
        document.body.appendChild(script);
    </script>

    <script>
        /* -------------------------------------------------------------
           Keep focussed field visible when the OS keyboard is present
           ------------------------------------------------------------- */
        document.addEventListener('focusin', function(e) {
            if (debugLog) {
                debugLog.log('INFO', 'Input focusin event', {
                    target: e.target.tagName,
                    id: e.target.id,
                    placeholder: e.target.placeholder,
                    viewport: {
                        innerWidth: window.innerWidth,
                        innerHeight: window.innerHeight,
                        visualWidth: window.visualViewport?.width,
                        visualHeight: window.visualViewport?.height
                    }
                });
            }
            if (!e.target || !(e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;
            // Give the keyboard a frame to appear, then scroll
            setTimeout(function() {
                if (debugLog) {
                    debugLog.log('INFO', 'Attempting scrollIntoView for focused input');
                }
                try { e.target.scrollIntoView({block:'center', behavior:'smooth'}); } catch(_) {}
            }, 50);
        });
    </script>

    <script>
        // Pure JS viewport watcher - only shifts when input would be obscured
        (() => {
            const container = document.getElementById('unity-container');
            const vv = window.visualViewport;
            const SAFE_PAD = 12;
            
            let activeInput = null;
            let lastClickPosition = null;
            
            // Track where the user actually clicked
            document.addEventListener('pointerdown', (e) => {
                lastClickPosition = { x: e.clientX, y: e.clientY };
                if (debugLog) {
                    debugLog.log('INFO', 'Pointer down tracked', {
                        x: e.clientX,
                        y: e.clientY,
                        target: e.target.tagName
                    });
                }
            });
            
            // Track which input has focus
            document.addEventListener('focusin', (e) => {
                if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) {
                    activeInput = e.target;
                    
                    if (debugLog) {
                        const rect = e.target.getBoundingClientRect();
                        debugLog.log('INFO', 'Input focused', {
                            tagName: e.target.tagName,
                            id: e.target.id,
                            className: e.target.className,
                            placeholder: e.target.placeholder,
                            value: e.target.value?.substring(0, 20) + '...',
                            visible: e.target.offsetWidth > 0 && e.target.offsetHeight > 0,
                            position: {
                                top: rect.top,
                                bottom: rect.bottom,
                                left: rect.left,
                                right: rect.right,
                                width: rect.width,
                                height: rect.height
                            },
                            style: {
                                position: window.getComputedStyle(e.target).position,
                                display: window.getComputedStyle(e.target).display,
                                visibility: window.getComputedStyle(e.target).visibility
                            }
                        });
                    }
                    
                    // Check immediately in case keyboard is already up
                    setTimeout(checkAndAdjust, 100);
                }
            });
            
            document.addEventListener('focusout', (e) => {
                if (e.target === activeInput) {
                    activeInput = null;
                    // Reset when focus leaves
                    container.style.transform = '';
                }
            });
            
            // Also listen for viewport changes
            vv.addEventListener('resize', () => requestAnimationFrame(checkAndAdjust));
            
            function checkAndAdjust() {
                const kbHeight = window.innerHeight - vv.height;
                const kbUp = kbHeight > 0.15 * window.innerHeight;
                
                // Commented out to reduce log spam - this was logging on every animation frame
                // if (debugLog) {
                //     debugLog.log('INFO', 'Checking viewport adjustment', {
                //         windowInnerHeight: window.innerHeight,
                //         visualViewportHeight: vv.height,
                //         keyboardHeight: kbHeight,
                //         keyboardUp: kbUp,
                //         hasActiveInput: !!activeInput,
                //         containerHeight: container ? container.offsetHeight : 0
                //     });
                // }
                
                if (!kbUp || !activeInput || kbHeight <= 0) {
                    // No keyboard or no focused input - reset
                    container.style.transform = '';
                    return;
                }
                
                // For Unity WebGL, we need to use the click position, not the input bounds
                // because Unity creates a full-screen invisible input
                const inputRect = activeInput.getBoundingClientRect();
                let checkPosition = inputRect.bottom;
                
                // Debug the input rect
                if (debugLog) {
                    debugLog.log('INFO', 'Input rect details', {
                        width: inputRect.width,
                        height: inputRect.height,
                        top: inputRect.top,
                        bottom: inputRect.bottom,
                        hasClickPosition: !!lastClickPosition,
                        clickY: lastClickPosition?.y
                    });
                }
                
                // For Unity WebGL inputs, prefer click position when available
                // This handles both full-screen overlays and positioned inputs
                if (lastClickPosition) {
                    // Use the click position, but ensure we check a reasonable area around it
                    checkPosition = lastClickPosition.y + Math.min(inputRect.height / 2, 40);
                    if (debugLog) {
                        debugLog.log('INFO', 'Using click position for check', {
                            clickY: lastClickPosition.y,
                            inputHeight: inputRect.height,
                            adjustedPosition: checkPosition
                        });
                    }
                } else {
                    // Fallback to input bounds if no click position
                    if (debugLog) {
                        debugLog.log('INFO', 'Using input bounds (no click position available)');
                    }
                }
                
                // The keyboard starts at the bottom of the visual viewport
                const keyboardTop = vv.height;
                
                if (debugLog) {
                    debugLog.log('INFO', 'Position check', {
                        checkPosition: checkPosition,
                        keyboardTop: keyboardTop,
                        keyboardHeight: kbHeight,
                        windowHeight: window.innerHeight,
                        visualViewportHeight: vv.height,
                        wouldBeObscured: checkPosition > (keyboardTop - SAFE_PAD),
                        spaceAboveKeyboard: keyboardTop - checkPosition
                    });
                }
                
                // Only shift if the clicked position would actually be covered by the keyboard
                if (checkPosition > (keyboardTop - SAFE_PAD)) {
                    // Calculate minimal shift needed to keep clicked position above keyboard
                    const rawShift = checkPosition - keyboardTop + SAFE_PAD;
                    const maxShift = Math.min(
                        kbHeight * 0.8,  // Never shift more than 80% of keyboard height
                        container.offsetHeight * 0.5  // Never shift more than 50% of container height
                    );
                    const shiftAmount = Math.min(rawShift, maxShift);
                    
                    if (debugLog) {
                        debugLog.log('INFO', 'Calculating shift', {
                            checkPosition: checkPosition,
                            inputTop: inputRect.top,
                            inputBottom: inputRect.bottom,
                            keyboardTop: keyboardTop,
                            rawShiftAmount: rawShift,
                            maxAllowedShift: maxShift,
                            finalShiftAmount: shiftAmount,
                            keyboardHeight: kbHeight,
                            containerHeight: container.offsetHeight,
                            devicePixelRatio: window.devicePixelRatio,
                            willApplyShift: shiftAmount > 0 && shiftAmount < container.offsetHeight * 0.9
                        });
                    }
                    
                    // Only apply shift if it's reasonable
                    if (shiftAmount > 0 && shiftAmount < container.offsetHeight * 0.9) {
                        container.style.transform = `translateY(-${shiftAmount}px)`;
                        
                        if (debugLog) {
                            debugLog.log('SUCCESS', 'Applied transform to container', {
                                transform: container.style.transform,
                                computedTransform: window.getComputedStyle(container).transform
                            });
                        }
                    } else {
                        if (debugLog) {
                            debugLog.log('WARNING', 'Shift amount out of bounds, not applying', {
                                shiftAmount: shiftAmount,
                                containerHeight: container.offsetHeight
                            });
                        }
                        container.style.transform = '';
                    }
                } else {
                    // Input is visible, no shift needed
                    container.style.transform = '';
                    
                    if (debugLog) {
                        debugLog.log('INFO', 'No shift needed - input is visible');
                    }
                }
            }
            
            // Add smooth transition
            if (container) {
                container.style.transition = 'transform .25s ease-out';
            }
        })();
    </script>

</body>
</html>
