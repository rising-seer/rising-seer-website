/**
 * Rising Seer Eye Tracking - Simple Test Version
 * 
 * VERBOSE LOGGING: Simplified version to test if scripts are loading
 * 
 * @package RisingSeer
 * @version 1.0.0
 */

// VERBOSE: Immediate test - this should show up in console right away
console.log('🚀 SIMPLE EYE TRACKING TEST LOADED! 🚀');
console.log('Current time:', new Date().toLocaleTimeString());

// VERBOSE: Test jQuery availability
if (typeof jQuery !== 'undefined') {
    console.log('✅ jQuery is available');
    
    jQuery(document).ready(function($) {
        console.log('✅ jQuery document ready fired');
        
        // VERBOSE: Test logo detection
        setTimeout(function() {
            const logoCount = $('.rising-seer-logo').length;
            const svgCount = $('svg').length;
            
            console.log('🔍 SIMPLE TEST RESULTS:');
            console.log('- Logo elements (.rising-seer-logo):', logoCount);
            console.log('- SVG elements:', svgCount);
            console.log('- Page title:', document.title);
            console.log('- Current URL:', window.location.href);
            
            if (logoCount > 0) {
                const $logo = $('.rising-seer-logo').first();
                console.log('- Logo tag:', $logo.prop('tagName'));
                console.log('- Logo classes:', $logo.attr('class'));
                
                // VERBOSE: Test if we can find eye parts
                const irisCount = $logo.find('#iris').length;
                const pupilCount = $logo.find('#pupil').length;
                
                console.log('- Iris elements found:', irisCount);
                console.log('- Pupil elements found:', pupilCount);
                
                if (irisCount > 0 && pupilCount > 0) {
                    console.log('🎉 SUCCESS: Eye parts found! Eye tracking should work!');
                    
                    // VERBOSE: Simple test movement
                    const $iris = $logo.find('#iris');
                    const $pupil = $logo.find('#pupil');
                    
                    console.log('🧪 Testing eye movement...');
                    $iris.css('transform', 'translate(5px, 5px)');
                    $pupil.css('transform', 'translate(10px, 10px)');
                    
                    setTimeout(function() {
                        $iris.css('transform', 'translate(0px, 0px)');
                        $pupil.css('transform', 'translate(0px, 0px)');
                        console.log('✅ Eye movement test completed!');
                    }, 2000);
                    
                } else {
                    console.log('❌ Eye parts not found - eye tracking will not work');
                }
            } else {
                console.log('❌ No logo found - eye tracking will not work');
            }
        }, 1000);
    });
    
} else {
    console.log('❌ jQuery is not available');
}

// VERBOSE: Global test function
window.testEyeTracking = function() {
    console.log('🧪 Manual eye tracking test triggered');
    
    if (typeof jQuery !== 'undefined') {
        const $iris = jQuery('#iris');
        const $pupil = jQuery('#pupil');
        
        if ($iris.length > 0 && $pupil.length > 0) {
            console.log('Moving eyes to test position...');
            $iris.css('transform', 'translate(15px, 10px)');
            $pupil.css('transform', 'translate(20px, 15px)');
            
            setTimeout(function() {
                console.log('Returning eyes to center...');
                $iris.css('transform', 'translate(0px, 0px)');
                $pupil.css('transform', 'translate(0px, 0px)');
            }, 3000);
        } else {
            console.log('❌ Eye elements not found for manual test');
        }
    } else {
        console.log('❌ jQuery not available for manual test');
    }
}; 