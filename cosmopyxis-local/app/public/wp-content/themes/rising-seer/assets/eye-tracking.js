/**
 * Rising Seer Eye Tracking System
 * 
 * VERBOSE LOGGING: This script creates the mystical eye-tracking effect
 * where the iris and pupil follow the user's cursor movement
 * 
 * @package RisingSeer
 * @version 1.0.0
 */

// Debug switch – set to true for verbose console logs
const RS_EYE_DEBUG = false;

if (!RS_EYE_DEBUG) {
    // Silence log/info/debug/error to keep console clean
    console.log = function(){};
    console.info = function(){};
    console.debug = function(){};
    console.error = function(){};
}

if (RS_EYE_DEBUG) {
    console.log('🔮 RISING SEER: EYE TRACKING SCRIPT FILE LOADED! 🔮');
}

(function($) {
    'use strict';
    
    // VERBOSE: Wait for DOM to be ready
    $(document).ready(function() {
        console.log('🔮 RISING SEER: EYE TRACKING DOM READY! 🔮');
        console.log('Rising Seer: Eye tracking system initializing...');
        
        // VERBOSE: Configuration for eye tracking (CORRECTED: iris=black=small, pupil=gold=large)
        const eyeConfig = {
            maxIrisMovement: 50,        // Iris (black, larger) should move less
            maxPupilMovement: 125,       // Pupil (gold, smaller) should move more for dramatic effect
            smoothing: 0.25,            // Movement smoothing (0-1, lower = smoother, increased for responsiveness)
            updateInterval: 16          // ~60fps update rate
        };
        
        let eyeElements = {
            logo: null,
            iris: null,
            pupil: null,
            eyeCenter: { x: 0, y: 0 },
            eyeRect: null
        };
        
        let currentPosition = { iris: { x: 0, y: 0 }, pupil: { x: 0, y: 0 } };
        let targetPosition = { iris: { x: 0, y: 0 }, pupil: { x: 0, y: 0 } };
        let animationFrame = null;
        
        // VERBOSE: Initialize the eye tracking system
        function initializeEyeTracking() {
            // VERBOSE: Find the logo element (must have eye-ready flag to ensure layout)
            eyeElements.logo = $('.rising-seer-logo.eye-ready');
            
            if (eyeElements.logo.length === 0) {
                console.log('Rising Seer: Logo not ready yet, retrying in 100ms');
                setTimeout(initializeEyeTracking, 100);
                return false;
            }
            
            console.log('Rising Seer: Logo element type:', eyeElements.logo.prop('tagName'));
            console.log('Rising Seer: Logo is IMG:', eyeElements.logo.is('img'));
            console.log('Rising Seer: Logo is SVG:', eyeElements.logo.is('svg'));
            
            // VERBOSE: Check if it's an SVG or IMG element
            if (eyeElements.logo.is('img')) {
                console.log('Rising Seer: Logo is IMG, converting to inline SVG...');
                // VERBOSE: If it's an IMG, we need to replace it with inline SVG
                loadInlineSVG();
            } else if (eyeElements.logo.is('svg')) {
                console.log('Rising Seer: Logo is already inline SVG, initializing directly...');
                // VERBOSE: If it's already SVG, initialize directly
                initializeSVGElements();
            } else {
                console.log('Rising Seer: Logo is not SVG or IMG, eye tracking disabled');
                console.log('Rising Seer: Logo element:', eyeElements.logo);
                return false;
            }
            
            return true;
        }
        
        // VERBOSE: Load SVG inline for manipulation
        function loadInlineSVG() {
            const logoSrc = eyeElements.logo.attr('src');
            const logoAlt = eyeElements.logo.attr('alt') || 'Rising Seer Logo';
            const logoClasses = eyeElements.logo.attr('class');
            
            console.log('Rising Seer: Loading SVG from:', logoSrc);
            console.log('Rising Seer: Logo classes:', logoClasses);
            
            // VERBOSE: Load SVG content via AJAX
            $.get(logoSrc)
                .done(function(data) {
                    console.log('Rising Seer: SVG data loaded successfully');
                    console.log('Rising Seer: SVG data type:', typeof data);
                    console.log('Rising Seer: SVG data preview:', data);
                    
                    // VERBOSE: Replace IMG with inline SVG
                    const $svg = $(data).find('svg');
                    console.log('Rising Seer: Found SVG elements:', $svg.length);
                    
                    if ($svg.length === 0) {
                        // VERBOSE: Try getting SVG directly if it's the root element
                        const $svgDirect = $(data);
                        if ($svgDirect.is('svg')) {
                            console.log('Rising Seer: SVG is root element, using directly');
                            $svgDirect.attr('class', logoClasses);
                            $svgDirect.attr('alt', logoAlt);
                            
                            eyeElements.logo.replaceWith($svgDirect);
                            eyeElements.logo = $svgDirect;
                            
                            initializeSVGElements();
                        } else {
                            console.error('Rising Seer: No SVG element found in loaded data');
                        }
                    } else {
                        $svg.attr('class', logoClasses);
                        $svg.attr('alt', logoAlt);
                        
                        eyeElements.logo.replaceWith($svg);
                        eyeElements.logo = $svg;
                        
                        initializeSVGElements();
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Rising Seer: Failed to load SVG for eye tracking');
                    console.error('Rising Seer: Error details:', status, error);
                    console.error('Rising Seer: XHR response:', xhr.responseText);
                });
        }
        
        // VERBOSE: Initialize SVG elements for tracking
        function initializeSVGElements() {
            console.log('Rising Seer: Initializing SVG elements...');
            console.log('Rising Seer: Logo element:', eyeElements.logo);
            console.log('Rising Seer: Logo HTML:', eyeElements.logo.html());
            
            // VERBOSE: Find iris and pupil elements within the SVG
            eyeElements.iris = eyeElements.logo.find('#iris');
            eyeElements.pupil = eyeElements.logo.find('#pupil');
            
            console.log('Rising Seer: Found iris elements:', eyeElements.iris.length);
            console.log('Rising Seer: Found pupil elements:', eyeElements.pupil.length);
            console.log('Rising Seer: Iris element:', eyeElements.iris);
            console.log('Rising Seer: Pupil element:', eyeElements.pupil);
            
            // VERBOSE: Also try finding all elements with IDs for debugging
            const allIds = eyeElements.logo.find('[id]');
            console.log('Rising Seer: All elements with IDs:', allIds.length);
            allIds.each(function() {
                console.log('Rising Seer: Found element with ID:', $(this).attr('id'));
            });
            
            if (eyeElements.iris.length === 0 || eyeElements.pupil.length === 0) {
                console.log('Rising Seer: Iris or pupil elements not found in SVG');
                console.log('Rising Seer: Available groups in SVG:');
                eyeElements.logo.find('g').each(function() {
                    console.log('Rising Seer: Group ID:', $(this).attr('id'));
                });
                return;
            }
            
            // VERBOSE: Set transform origins to center
            eyeElements.iris.css('transform-origin', 'center');
            eyeElements.pupil.css('transform-origin', 'center');
            
            // VERBOSE: Calculate eye center position
            updateEyePosition();
            
            // VERBOSE: Start tracking
            startEyeTracking();
            
            console.log('Rising Seer: Eye tracking initialized successfully');
        }
        
        // VERBOSE: Update eye center position (call on resize)
        function updateEyePosition() {
            if (!eyeElements.logo || eyeElements.logo.length === 0) return;
            
            eyeElements.eyeRect = eyeElements.logo[0].getBoundingClientRect();
            eyeElements.eyeCenter = {
                x: eyeElements.eyeRect.left + eyeElements.eyeRect.width / 2,
                y: eyeElements.eyeRect.top + eyeElements.eyeRect.height / 2
            };
        }
        
        // VERBOSE: Calculate target positions based on cursor (PERCENTAGE FROM SCREEN CENTER!)
        function calculateTargetPositions(mouseX, mouseY) {
            // VERBOSE: Get screen dimensions
            const screenWidth = window.innerWidth;
            const screenHeight = window.innerHeight;
            const screenCenterX = screenWidth / 2;
            const screenCenterY = screenHeight / 2;
            
            // VERBOSE: Calculate percentage from screen center (-1 to +1)
            const percentFromCenterX = (mouseX - screenCenterX) / (screenWidth / 2);
            const percentFromCenterY = (mouseY - screenCenterY) / (screenHeight / 2);
            
            // VERBOSE: Clamp to -1 to +1 range (in case cursor goes outside viewport)
            const clampedX = Math.max(-1, Math.min(1, percentFromCenterX));
            const clampedY = Math.max(-1, Math.min(1, percentFromCenterY));
            
            console.log(`Rising Seer: Mouse (${mouseX}, ${mouseY}), Center (${screenCenterX.toFixed(1)}, ${screenCenterY.toFixed(1)}), Raw % (${percentFromCenterX.toFixed(3)}, ${percentFromCenterY.toFixed(3)}), Clamped % (${clampedX.toFixed(3)}, ${clampedY.toFixed(3)})`);
            
            // VERBOSE: Calculate SEPARATE movement for iris and pupil (pupil 2.5x more sensitive!)
            const irisX = clampedX * eyeConfig.maxIrisMovement;
            const irisY = clampedY * eyeConfig.maxIrisMovement;
            
            const pupilX = clampedX * eyeConfig.maxPupilMovement;
            const pupilY = clampedY * eyeConfig.maxPupilMovement;
            
            console.log(`Rising Seer: Iris (${irisX.toFixed(1)}, ${irisY.toFixed(1)}), Pupil (${pupilX.toFixed(1)}, ${pupilY.toFixed(1)}) - Pupil 5x more sensitive`);
            
            targetPosition.iris = {
                x: irisX,
                y: irisY
            };
            
            targetPosition.pupil = {
                x: pupilX,
                y: pupilY
            };
        }
        
        // VERBOSE: Smooth animation update
        function updateEyeAnimation() {
            // VERBOSE: Smooth interpolation towards target
            currentPosition.iris.x += (targetPosition.iris.x - currentPosition.iris.x) * eyeConfig.smoothing;
            currentPosition.iris.y += (targetPosition.iris.y - currentPosition.iris.y) * eyeConfig.smoothing;
            
            currentPosition.pupil.x += (targetPosition.pupil.x - currentPosition.pupil.x) * eyeConfig.smoothing;
            currentPosition.pupil.y += (targetPosition.pupil.y - currentPosition.pupil.y) * eyeConfig.smoothing;
            
            // VERBOSE: Apply transforms - SEPARATE MOVEMENT (pupil 2.5x more sensitive!)
            eyeElements.iris.css('transform', `translate(${currentPosition.iris.x}px, ${currentPosition.iris.y}px)`);
            eyeElements.pupil.css('transform', `translate(${currentPosition.pupil.x}px, ${currentPosition.pupil.y}px)`);
            
            // VERBOSE: Continue animation
            animationFrame = requestAnimationFrame(updateEyeAnimation);
        }
        
        // VERBOSE: Start eye tracking
        function startEyeTracking() {
            // VERBOSE: Mouse move handler
            $(document).on('mousemove.eyeTracking', function(e) {
                calculateTargetPositions(e.clientX, e.clientY);
            });
            
            // VERBOSE: Window resize handler
            $(window).on('resize.eyeTracking', function() {
                updateEyePosition();
            });
            
            // VERBOSE: Start animation loop
            updateEyeAnimation();
            
            console.log('Rising Seer: Eye tracking started');
        }
        
        // VERBOSE: Stop eye tracking (cleanup)
        function stopEyeTracking() {
            $(document).off('mousemove.eyeTracking');
            $(window).off('resize.eyeTracking');
            
            if (animationFrame) {
                cancelAnimationFrame(animationFrame);
                animationFrame = null;
            }
            
            console.log('Rising Seer: Eye tracking stopped');
        }
        
        // VERBOSE: Enhanced glow effect on hover
        function initializeGlowEffects() {
            if (!eyeElements.logo) return;
            
            eyeElements.logo.on('mouseenter', function() {
                $(this).css('filter', 'drop-shadow(0 0 30px #FFBD59) drop-shadow(0 0 60px rgba(255, 189, 89, 0.5))');
            });
            
            eyeElements.logo.on('mouseleave', function() {
                $(this).css('filter', 'drop-shadow(0 0 20px #FFBD59)');
            });
        }
        
        // VERBOSE: Initialize everything
        function initialize() {
            console.log('Rising Seer: Initializing eye tracking system...');
            
            if (initializeEyeTracking()) {
                // VERBOSE: Add enhanced glow effects
                setTimeout(initializeGlowEffects, 100);
                
                console.log('Rising Seer: Eye tracking system fully initialized');
            }
        }
        
        // VERBOSE: Start the system
        initialize();
        
        // VERBOSE: Immediate debug check
        setTimeout(function() {
            console.log('🔍 IMMEDIATE DEBUG CHECK:');
            console.log('- Logo elements found:', jQuery('.rising-seer-logo').length);
            console.log('- SVG elements found:', jQuery('svg').length);
            console.log('- Elements with rising-seer-logo class:', jQuery('.rising-seer-logo'));
            
            if (jQuery('.rising-seer-logo').length > 0) {
                const logo = jQuery('.rising-seer-logo').first();
                console.log('- Logo tag name:', logo.prop('tagName'));
                console.log('- Logo HTML preview:', logo.html() ? logo.html().substring(0, 200) + '...' : 'No HTML content');
            }
        }, 1000);
        
        // VERBOSE: Expose system for debugging
        window.risingSeerEyeTracking = {
            config: eyeConfig,
            elements: eyeElements,
            currentPosition: currentPosition,
            targetPosition: targetPosition,
            stop: stopEyeTracking,
            start: startEyeTracking,
            updatePosition: updateEyePosition,
            // VERBOSE: Debug functions for precise testing
            testCenter: function() {
                const centerX = window.innerWidth / 2;
                const centerY = window.innerHeight / 2;
                console.log(`🎯 Testing EXACT center: (${centerX}, ${centerY})`);
                calculateTargetPositions(centerX, centerY);
            },
            testTopMiddle: function() {
                const centerX = window.innerWidth / 2;
                const topY = 0;
                console.log(`⬆️ Testing TOP middle: (${centerX}, ${topY})`);
                calculateTargetPositions(centerX, topY);
            },
            testBottomMiddle: function() {
                const centerX = window.innerWidth / 2;
                const bottomY = window.innerHeight;
                console.log(`⬇️ Testing BOTTOM middle: (${centerX}, ${bottomY})`);
                calculateTargetPositions(centerX, bottomY);
            },
            testCorners: function() {
                console.log('🔍 Testing all corners and center...');
                this.testTopMiddle();
                this.testBottomMiddle();
                this.testCenter();
            }
        };
    });
    
})(jQuery); 