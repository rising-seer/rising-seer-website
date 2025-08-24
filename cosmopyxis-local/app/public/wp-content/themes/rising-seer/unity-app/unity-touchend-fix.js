// iOS Safari TouchEnd Loss Fix for Unity WebGL - WORKING BASELINE
(function(){
  function attachFix(){
    const canvas = document.getElementById('unity-canvas');
    if(!canvas) return false;
    const isTouchDevice = ('ontouchstart' in window) || navigator.maxTouchPoints>0;
    if(!isTouchDevice){console.log('[Unity Template] TouchEnd Fix skipped – non-touch'); return false;}
    console.log('[Unity Template] TouchEnd Loss Fix ACTIVE - baseline version');
    
    // Track primary touch originating on the canvas
    let activeTouchId=null;
    let lastX=0, lastY=0;
    let lastUpTime=0;

    canvas.addEventListener('touchstart',e=>{
      if(activeTouchId!==null) return; // already tracking
      const t=e.changedTouches[0];
      activeTouchId=t.identifier;
      lastX=t.clientX; lastY=t.clientY;
    },{passive:true});

    // Update last position while finger moves
    document.addEventListener('touchmove',e=>{
      if(activeTouchId===null) return;
      for(const t of e.changedTouches){
        if(t.identifier===activeTouchId){lastX=t.clientX; lastY=t.clientY; break;}
      }
    },{passive:true});

    // On touchend anywhere in the doc, if it matches our active touch, synthesize events
    document.addEventListener('touchend',e=>{
      if(activeTouchId===null) return;
      for(const t of e.changedTouches){
        if(t.identifier!==activeTouchId) continue;
        const now=Date.now();
        if(now-lastUpTime<100) return; // debounce duplicates
        lastUpTime=now;
        activeTouchId=null; // Clear to prevent re-trigger

        const opts={bubbles:true,cancelable:true,clientX:lastX,clientY:lastY};
        console.log('[Unity Template] Synthesizing unlock sequence at',lastX,lastY);
        
        // Simple approach: always add the missing mouseup Unity needs
        canvas.dispatchEvent(new MouseEvent('mouseup',opts));
        
        // Add safety click after delay
        setTimeout(()=>{
          canvas.dispatchEvent(new MouseEvent('click',opts));
        }, 50);
        break;
      }
    },{passive:true});
    return true;
  }
  if(!attachFix()){
    // canvas not yet in DOM; wait for it
    document.addEventListener('DOMContentLoaded', attachFix, { once:true });
    window.addEventListener('load', attachFix, { once:true });
  }
})();

// === DEBUG EVENT LOGGER (2025-08-04) ===
['touchstart','touchend','pointerdown','pointerup','mousedown','mouseup','click'].forEach(function(type){
  // capture phase
  window.addEventListener(type,function(e){
    var id = e.pointerId!==undefined?e.pointerId:(e.changedTouches&&e.changedTouches[0]?e.changedTouches[0].identifier:'-');
    console.log('[DBG-EVENT] '+type+' CAPTURE id='+id+' target='+e.target.tagName);
  },true);
  // bubble phase
  window.addEventListener(type,function(e){
    var id = e.pointerId!==undefined?e.pointerId:(e.changedTouches&&e.changedTouches[0]?e.changedTouches[0].identifier:'-');
    console.log('[DBG-EVENT] '+type+' BUBBLE  id='+id+' target='+e.target.tagName);
  },false);
});

// === POST-FRAME UNLOCK (2025-08-04) ===
console.log('[UNLOCK-SHIM] Script file loaded - testing basic execution');
(function(){
  console.log('[UNLOCK-SHIM] Post-frame unlock shim initializing...');
  
  let canvas = null;
  let lastX = 0, lastY = 0;
  let setupComplete = false;
  
  function findAndSetupCanvas() {
    const allCanvases = document.querySelectorAll('canvas');
    console.log('[UNLOCK-SHIM] Found canvases:', allCanvases.length, Array.from(allCanvases).map(c => c.id || c.className || 'no-id'));
    
    canvas = document.getElementById('unity-canvas') || document.querySelector('canvas');
    
    if (!canvas) {
      console.log('[UNLOCK-SHIM] No canvas found yet, will retry...');
      return false;
    }
    
    if (setupComplete) {
      console.log('[UNLOCK-SHIM] Setup already complete, skipping');
      return true;
    }
    
    console.log('[UNLOCK-SHIM] Canvas found, setting up listeners:', canvas.id || canvas.className || 'no-id');
    setupComplete = true;
    
    // Setup pointer tracking
    window.addEventListener('pointerdown', e => {
      lastX = e.clientX; lastY = e.clientY;
      console.log('[UNLOCK-SHIM] Pointer captured:', lastX, lastY);
    }, true);
    
    // Listen for Unity log messages that indicate window switches
    let lastWindowSwitchTime = 0;
    const originalLog = console.log;
    console.log = function(...args) {
      const message = args.join(' ');
      
      // Detect window switch patterns from Unity logs
      if (message.match(/WindowManager\.OpenWindowByIndex\(\d+\) called successfully/)) {
        
        const now = Date.now();
        if (now - lastWindowSwitchTime > 500) { // Debounce multiple logs
          lastWindowSwitchTime = now;
          console.log('[UNLOCK-SHIM] Window switch detected, scheduling unlock sequence in 500ms');
          
          setTimeout(() => {
            // Use exact coordinates you specified
            const x = 239;
            const y = 204;
            
            // Generate a fake touch ID (negative number like real touches)
            const fakeId = -Math.floor(Math.random() * 1000000);
            
            originalLog('[UNLOCK-SHIM] Synthesizing complete touch sequence at', x, y, 'id:', fakeId);
          
          // 1. POINTERDOWN with ID
          const pointerDownOpts = {bubbles: true, cancelable: true, clientX: x, clientY: y, pointerId: fakeId};
          canvas.dispatchEvent(new PointerEvent('pointerdown', pointerDownOpts));
          
          // 2. TOUCHSTART - create proper Touch objects (async to prevent Unity recursion)
          try {
            const touch = new Touch({identifier: fakeId, target: canvas, clientX: x, clientY: y});
            const touchStartOpts = {bubbles: true, cancelable: true, touches: [touch], changedTouches: [touch]};
            const touchStart = new TouchEvent('touchstart', touchStartOpts);
            setTimeout(() => canvas.dispatchEvent(touchStart), 0);
          } catch(e) {
            originalLog('[UNLOCK-SHIM] TouchEvent creation failed, skipping touch events');
          }
          
          // 3. POINTERUP with same ID
          const pointerUpOpts = {bubbles: true, cancelable: true, clientX: x, clientY: y, pointerId: fakeId};
          canvas.dispatchEvent(new PointerEvent('pointerup', pointerUpOpts));
          
          // 4. TOUCHEND - create proper Touch objects (async to prevent Unity recursion)
          try {
            const touch = new Touch({identifier: fakeId, target: canvas, clientX: x, clientY: y});
            const touchEndOpts = {bubbles: true, cancelable: true, touches: [], changedTouches: [touch]};
            const touchEnd = new TouchEvent('touchend', touchEndOpts);
            setTimeout(() => canvas.dispatchEvent(touchEnd), 0);
          } catch(e) {
            originalLog('[UNLOCK-SHIM] TouchEvent creation failed, skipping touch events');
          }
          
          // 5. MOUSEUP (no ID)
          const mouseUpOpts = {bubbles: true, cancelable: true, clientX: x, clientY: y};
          canvas.dispatchEvent(new MouseEvent('mouseup', mouseUpOpts));
          
          // 6. CLICK (no ID) - delayed like Unity Template
          setTimeout(() => {
            const clickOpts = {bubbles: true, cancelable: true, clientX: x, clientY: y};
            canvas.dispatchEvent(new MouseEvent('click', clickOpts));
            originalLog('[UNLOCK-SHIM] Complete touch sequence finished');
          }, 50);
          }, 500); // Wait 500ms for Unity to lock before unlocking
        }
      }
      
      // Call original console.log
      return originalLog.apply(console, arguments);
    };
    
    // Listen for Unity messages
    window.addEventListener('message', (e) => {
      if (e.data && typeof e.data === 'string' && e.data.includes('canvas')) {
        console.log('[UNLOCK-SHIM] Unity message received:', e.data);
      }
    });
    
    console.log('[UNLOCK-SHIM] Post-frame unlock shim ready');
    return true;
  }
  
  // Try initial setup
  if (!findAndSetupCanvas()) {
    // Canvas not ready, poll for it
    console.log('[UNLOCK-SHIM] Canvas not ready, starting polling...');
    const pollInterval = setInterval(() => {
      if (findAndSetupCanvas()) {
        clearInterval(pollInterval);
        console.log('[UNLOCK-SHIM] Polling stopped - canvas found and setup complete');
      }
    }, 500);
    
    // Stop polling after 30 seconds to prevent infinite polling
    setTimeout(() => {
      clearInterval(pollInterval);
      console.warn('[UNLOCK-SHIM] Stopped polling after 30 seconds');
    }, 30000);
  }
})();
