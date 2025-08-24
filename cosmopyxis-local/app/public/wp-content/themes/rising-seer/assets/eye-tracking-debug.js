/**
 * Rising Seer Eye Tracking Debug Helper
 * 
 * VERBOSE LOGGING: This script provides debugging tools for the eye tracking system
 * Use in browser console to test and debug eye tracking functionality
 * 
 * @package RisingSeer
 * @version 1.0.0
 */

// VERBOSE: Debug helper functions for eye tracking
window.risingSeerDebug = {
    
    // VERBOSE: Test eye tracking with simulated mouse positions
    testEyeTracking: function() {
        console.log('Rising Seer Debug: Testing eye tracking...');
        
        if (!window.risingSeerEyeTracking) {
            console.error('Rising Seer Debug: Eye tracking system not found!');
            return;
        }
        
        const positions = [
            { x: 100, y: 100 },
            { x: window.innerWidth - 100, y: 100 },
            { x: window.innerWidth - 100, y: window.innerHeight - 100 },
            { x: 100, y: window.innerHeight - 100 },
            { x: window.innerWidth / 2, y: window.innerHeight / 2 }
        ];
        
        let index = 0;
        const interval = setInterval(() => {
            if (index >= positions.length) {
                clearInterval(interval);
                console.log('Rising Seer Debug: Eye tracking test completed');
                return;
            }
            
            const pos = positions[index];
            console.log(`Rising Seer Debug: Testing position ${index + 1}: (${pos.x}, ${pos.y})`);
            
            // VERBOSE: Simulate mouse move event
            const event = new MouseEvent('mousemove', {
                clientX: pos.x,
                clientY: pos.y
            });
            document.dispatchEvent(event);
            
            index++;
        }, 1000);
    },
    
    // VERBOSE: Show current eye tracking state
    showState: function() {
        if (!window.risingSeerEyeTracking) {
            console.error('Rising Seer Debug: Eye tracking system not found!');
            return;
        }
        
        const state = window.risingSeerEyeTracking;
                 console.log('Rising Seer Debug: Current Eye Tracking State (ANCHORED SYSTEM):', {
             config: state.config,
             eyeCenter: state.elements.eyeCenter,
             currentPosition: state.currentPosition,
             targetPosition: state.targetPosition,
             logoFound: !!state.elements.logo,
             irisFound: !!state.elements.iris,
             pupilFound: !!state.elements.pupil,
             anchoredSystem: 'Pupil anchored to iris + extra movement'
         });
    },
    
    // VERBOSE: Toggle eye tracking on/off
    toggle: function() {
        if (!window.risingSeerEyeTracking) {
            console.error('Rising Seer Debug: Eye tracking system not found!');
            return;
        }
        
        if (this.isActive) {
            window.risingSeerEyeTracking.stop();
            this.isActive = false;
            console.log('Rising Seer Debug: Eye tracking stopped');
        } else {
            window.risingSeerEyeTracking.start();
            this.isActive = true;
            console.log('Rising Seer Debug: Eye tracking started');
        }
    },
    
    // VERBOSE: Reset eye position to center
    resetEyes: function() {
        if (!window.risingSeerEyeTracking) {
            console.error('Rising Seer Debug: Eye tracking system not found!');
            return;
        }
        
        const elements = window.risingSeerEyeTracking.elements;
        if (elements.iris && elements.pupil) {
            elements.iris.css('transform', 'translate(0px, 0px)');
            elements.pupil.css('transform', 'translate(0px, 0px)');
            console.log('Rising Seer Debug: Eyes reset to center position');
        }
    },
    
    // VERBOSE: Highlight eye elements for debugging
    highlightEyes: function() {
        if (!window.risingSeerEyeTracking) {
            console.error('Rising Seer Debug: Eye tracking system not found!');
            return;
        }
        
        const elements = window.risingSeerEyeTracking.elements;
        
        if (elements.iris) {
            elements.iris.css('stroke', '#FF0000');
            elements.iris.css('stroke-width', '2px');
            console.log('Rising Seer Debug: Iris highlighted in red');
        }
        
        if (elements.pupil) {
            elements.pupil.css('stroke', '#00FF00');
            elements.pupil.css('stroke-width', '2px');
            console.log('Rising Seer Debug: Pupil highlighted in green');
        }
    },
    
    // VERBOSE: Remove eye highlighting
    removeHighlight: function() {
        if (!window.risingSeerEyeTracking) {
            console.error('Rising Seer Debug: Eye tracking system not found!');
            return;
        }
        
        const elements = window.risingSeerEyeTracking.elements;
        
        if (elements.iris) {
            elements.iris.css('stroke', '');
            elements.iris.css('stroke-width', '');
        }
        
        if (elements.pupil) {
            elements.pupil.css('stroke', '');
            elements.pupil.css('stroke-width', '');
        }
        
        console.log('Rising Seer Debug: Eye highlighting removed');
    },
    
    isActive: true
};

// VERBOSE: Auto-run basic diagnostics when script loads
$(document).ready(function() {
    setTimeout(function() {
        console.log('Rising Seer Debug: Debug helper loaded');
        console.log('Rising Seer Debug: Available commands:');
        console.log('  - risingSeerDebug.testEyeTracking() - Test eye movement');
        console.log('  - risingSeerDebug.showState() - Show current state');
        console.log('  - risingSeerDebug.toggle() - Toggle eye tracking');
        console.log('  - risingSeerDebug.resetEyes() - Reset to center');
        console.log('  - risingSeerDebug.highlightEyes() - Highlight eye elements');
        console.log('  - risingSeerDebug.removeHighlight() - Remove highlighting');
        
        // VERBOSE: Auto-check if eye tracking is working
        if (window.risingSeerEyeTracking) {
            console.log('Rising Seer Debug: ✅ Eye tracking system detected');
        } else {
            console.warn('Rising Seer Debug: ⚠️ Eye tracking system not found - may still be loading');
        }
    }, 2000);
}); 