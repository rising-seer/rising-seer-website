/**
 * Rising Seer Gold Star Animation System
 * 
 * VERBOSE LOGGING: This script creates an enhanced mystical star field
 * using the Rising Seer gold star sprites for visual appeal
 * 
 * @package RisingSeer
 * @version 1.0.0
 */

(function($) {
    'use strict';
    
    // VERBOSE: Wait for DOM to be ready
    $(document).ready(function() {
        console.log('Rising Seer: Gold star animation system initializing...');
        
        // VERBOSE: Configuration for star system
        const starConfig = {
            maxStars: 25,
            minSize: 10,
            maxSize: 18,
            minOpacity: 0.3,
            maxOpacity: 0.9,
            minLifetime: 3000,
            maxLifetime: 8000,
            spawnInterval: 500,
            fadeInDuration: 1500,
            fadeOutDuration: 2000
        };
        
        let activeStars = [];
        let starContainer = null;
        
        // VERBOSE: Initialize the star container
        function initializeStarContainer() {
            starContainer = document.getElementById('mystical-particles');
            if (!starContainer) {
                // VERBOSE: Create container if it doesn't exist
                starContainer = document.createElement('div');
                starContainer.id = 'mystical-particles';
                starContainer.className = 'mystical-particles';
                starContainer.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    pointer-events: none;
                    overflow: hidden;
                    z-index: 1;
                `;
                document.body.appendChild(starContainer);
            }
            console.log('Rising Seer: Star container initialized');
        }
        
        // VERBOSE: Check browser capabilities
        function getBrowserCapabilities() {
            return {
                supportsSVG: document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1"),
                supportsTransform: 'transform' in document.documentElement.style,
                supportsFilter: 'filter' in document.documentElement.style
            };
        }
        
        // VERBOSE: Create a single gold star element
        function createGoldStar() {
            const capabilities = getBrowserCapabilities();
            const star = document.createElement('div');
            star.className = 'star-particle rising-seer-star';
            
            // VERBOSE: Calculate random properties
            const size = Math.random() * (starConfig.maxSize - starConfig.minSize) + starConfig.minSize;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const opacity = Math.random() * (starConfig.maxOpacity - starConfig.minOpacity) + starConfig.minOpacity;
            const rotation = Math.random() * 360;
            const lifetime = Math.random() * (starConfig.maxLifetime - starConfig.minLifetime) + starConfig.minLifetime;
            
            // VERBOSE: Set star styling
            star.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}%;
                top: ${y}%;
                opacity: 0;
                transition: opacity ${starConfig.fadeInDuration}ms ease-in-out;
                z-index: 2;
            `;
            
            // VERBOSE: Set background image based on browser support
            if (capabilities.supportsSVG && risingSeerData && risingSeerData.starImageUrl) {
                star.style.backgroundImage = `url('${risingSeerData.starImageUrl}')`;
                star.style.backgroundSize = 'contain';
                star.style.backgroundRepeat = 'no-repeat';
                star.style.backgroundPosition = 'center';
            } else {
                // VERBOSE: Fallback for older browsers
                star.classList.add('fallback');
                if (risingSeerData && risingSeerData.starImageFallback) {
                    star.style.backgroundImage = `url('${risingSeerData.starImageFallback}')`;
                    star.style.backgroundSize = 'contain';
                    star.style.backgroundRepeat = 'no-repeat';
                    star.style.backgroundPosition = 'center';
                } else {
                    star.style.backgroundColor = '#FFBD59';
                    star.style.borderRadius = '50%';
                }
            }
            
            // VERBOSE: Add transform effects if supported
            if (capabilities.supportsTransform) {
                star.style.transform = `rotate(${rotation}deg)`;
            }
            
            // VERBOSE: Add filter effects if supported
            if (capabilities.supportsFilter) {
                star.style.filter = 'drop-shadow(0 0 4px #FFBD59)';
            } else {
                star.style.boxShadow = '0 0 8px #FFBD59';
            }
            
            // VERBOSE: Add to container
            starContainer.appendChild(star);
            
            // VERBOSE: Fade in after brief delay
            setTimeout(() => {
                star.style.opacity = opacity;
            }, 100);
            
            // VERBOSE: Return star object with metadata
            return {
                element: star,
                birthTime: Date.now(),
                lifetime: lifetime,
                maxOpacity: opacity,
                size: size,
                rotation: rotation
            };
        }
        
        // VERBOSE: Remove a star with fade out effect
        function removeGoldStar(starObj) {
            if (starObj.element && starObj.element.parentNode) {
                starObj.element.style.transition = `opacity ${starConfig.fadeOutDuration}ms ease-out`;
                starObj.element.style.opacity = '0';
                
                setTimeout(() => {
                    if (starObj.element && starObj.element.parentNode) {
                        starObj.element.parentNode.removeChild(starObj.element);
                    }
                }, starConfig.fadeOutDuration);
            }
        }
        
        // VERBOSE: Main star management system
        function manageStarField() {
            // VERBOSE: Remove expired stars
            activeStars = activeStars.filter(starObj => {
                const age = Date.now() - starObj.birthTime;
                if (age > starObj.lifetime) {
                    removeGoldStar(starObj);
                    return false;
                }
                return true;
            });
            
            // VERBOSE: Add new stars if below maximum
            while (activeStars.length < starConfig.maxStars) {
                const newStar = createGoldStar();
                activeStars.push(newStar);
            }
            
            // VERBOSE: Log current star count for debugging
            if (activeStars.length > 0) {
                console.log(`Rising Seer: Managing ${activeStars.length} active stars`);
            }
        }
        
        // VERBOSE: Add subtle animation to existing stars
        function animateStars() {
            activeStars.forEach(starObj => {
                if (starObj.element && starObj.element.parentNode) {
                    // VERBOSE: Add subtle twinkle effect
                    const currentOpacity = parseFloat(starObj.element.style.opacity);
                    const variation = (Math.random() - 0.5) * 0.2;
                    const newOpacity = Math.max(0.1, Math.min(starObj.maxOpacity, currentOpacity + variation));
                    starObj.element.style.opacity = newOpacity;
                }
            });
        }
        
        // VERBOSE: Initialize the star system
        function initializeStarSystem() {
            initializeStarContainer();
            
            // VERBOSE: Start initial star creation
            manageStarField();
            
            // VERBOSE: Set up regular intervals
            setInterval(manageStarField, starConfig.spawnInterval);
            setInterval(animateStars, 2000); // VERBOSE: Twinkle every 2 seconds
            
            console.log(`Rising Seer: Star system active with max ${starConfig.maxStars} stars`);
        }
        
        // VERBOSE: Enhanced cursor interaction
        function initializeCursorEffects() {
            let mysticalCursor = null;
            
            $(document).on('mousemove', function(e) {
                if (!mysticalCursor) {
                    mysticalCursor = $('<div class="mystical-cursor"></div>');
                    mysticalCursor.css({
                        position: 'fixed',
                        width: '20px',
                        height: '20px',
                        background: 'radial-gradient(circle, #FFBD59 0%, transparent 70%)',
                        borderRadius: '50%',
                        pointerEvents: 'none',
                        zIndex: 10000,
                        opacity: 0.6,
                        transition: 'transform 0.1s ease',
                        filter: 'blur(1px)'
                    });
                    $('body').append(mysticalCursor);
                }
                
                mysticalCursor.css({
                    left: e.clientX - 10 + 'px',
                    top: e.clientY - 10 + 'px'
                });
            });
        }
        
        // VERBOSE: Add parallax effect to logo
        function initializeParallaxEffects() {
            const $logo = $('.rising-seer-logo');
            if ($logo.length) {
                $(document).on('mousemove', function(e) {
                    const x = (e.clientX / window.innerWidth) * 8 - 4;
                    const y = (e.clientY / window.innerHeight) * 8 - 4;
                    $logo.css('transform', `translate(${x}px, ${y}px)`);
                });
            }
        }
        
        // VERBOSE: Performance monitoring
        function monitorPerformance() {
            setInterval(() => {
                const starCount = activeStars.length;
                const containerChildren = starContainer ? starContainer.children.length : 0;
                
                if (starCount !== containerChildren) {
                    console.warn(`Rising Seer: Star count mismatch - Active: ${starCount}, DOM: ${containerChildren}`);
                }
                
                // VERBOSE: Log performance metrics
                console.log(`Rising Seer: Performance - Active stars: ${starCount}, DOM elements: ${containerChildren}`);
            }, 10000); // VERBOSE: Check every 10 seconds
        }
        
        // VERBOSE: Initialize all systems
        function initialize() {
            console.log('Rising Seer: Initializing all animation systems...');
            
            initializeStarSystem();
            initializeCursorEffects();
            initializeParallaxEffects();
            monitorPerformance();
            
            console.log('Rising Seer: All animation systems initialized successfully');
        }
        
        // VERBOSE: Start the system
        initialize();
        
        // VERBOSE: Expose system for debugging
        window.risingSeerStars = {
            activeStars: activeStars,
            config: starConfig,
            manageStarField: manageStarField,
            createGoldStar: createGoldStar
        };
    });
    
})(jQuery); 