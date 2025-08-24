<?php
/**
 * Template Name: Rising Seer Splash Page
 * 
 * VERBOSE LOGGING: This template creates a full-screen splash page
 * for investor presentations and coming soon displays
 * BRANDING UPDATE: Updated from Cosmopyxis to Rising Seer branding
 * 
 * @package RisingSeer
 * @version 1.0.0
 */

// VERBOSE: Prevent direct access to template file
if (!defined('ABSPATH')) {
    exit;
}

// VERBOSE: Log splash page template loading
error_log('Rising Seer: Splash page template loaded');

// VERBOSE: Remove default header and footer for full-screen experience
remove_action('astra_header', 'astra_header_markup');
remove_action('astra_footer', 'astra_footer_markup');

get_header(); ?>

<div class="rising-seer-splash-wrapper">
    <div class="rising-seer-splash">
        <div class="rising-seer-splash-content">
            
            <div class="logo-container">
            <?php
            // Static wordmark (text + decorative stars)
            $wordmark_path = get_stylesheet_directory() . '/assets/rising-seer-text-and-stars.svg';
            if (file_exists($wordmark_path)) {
                echo '<div class="wordmark-wrapper">';
                echo file_get_contents($wordmark_path);
                echo '</div>';
            } else {
                echo '<!-- wordmark svg missing -->';
            }

            // Interactive icon (eye tracking)
            $icon_path = get_stylesheet_directory() . '/assets/rising-seer-logo-eye-tracking.svg';
            if (file_exists($icon_path)) {
                $svg_icon = file_get_contents($icon_path);
                // CHANGE: convert gold drip path class from st1 to st2 so it uses existing gold fill
                $svg_icon = str_replace('class="st1"', 'class="st2"', $svg_icon);
                // Ensure any internal style definition renamed as well
                $svg_icon = str_replace('.st1', '.st2', $svg_icon);
                // add id and classes to root svg
                $svg_icon = preg_replace('/<svg\s/', '<svg id="rising-icon" class="rising-seer-logo mystical-pulse" ', $svg_icon, 1);
                // prefix internal style class selectors with #rising-icon to scope them
                $svg_icon = preg_replace_callback('/<style[^>]*>(.*?)<\/style>/s', function($matches){
                    $scoped = preg_replace('/\.st(\d+)/', '#rising-icon .st$1', $matches[1]);
                    // Force gold drip (st1) to our brand gold
                    $scoped = preg_replace('/(#rising-icon \.st1\s*\{[^}]*)fill:\s*[^;]+;?/i', '$1fill:#FFBD59;', $scoped);
                    return '<style>'.$scoped.'</style>';
                }, $svg_icon, 1);
                echo '<div class="icon-wrapper">' . $svg_icon . '</div>';
                // append overriding style to ensure gold drip color
                echo '<style>#rising-icon .st1 { fill: #FFBD59 !important; }</style>';
                // JS to mark icon ready once layout complete
                echo '<script>document.addEventListener("DOMContentLoaded",function(){const icon=document.getElementById("rising-icon");if(icon){const chk=setInterval(function(){if(icon.getBoundingClientRect().width){icon.classList.add("eye-ready");clearInterval(chk);}},50);}});</script>';
            } else {
                echo '<!-- icon svg missing -->';
            }
            ?>
            </div>
            
            <?php 
            // VERBOSE: Display page content if any
            if (have_posts()) : 
                while (have_posts()) : the_post();
                    if (get_the_content()): 
            ?>
                        <div class="rising-seer-splash-description">
                            <?php the_content(); ?>
                        </div>
            <?php 
                    endif;
                endwhile;
            endif;
            ?>
            
            <div class="rising-seer-splash-actions">
                <!-- VERBOSE: Primary action button for engagement -->
                <a href="mailto:hello@risingseer.com?subject=Rising%20Seer%20Inquiry" class="ast-button mystical-glow rising-seer-primary-btn">
                    Learn More
                </a>
                
                <!-- VERBOSE: Secondary action - disabled for now -->
                <span class="ast-button rising-seer-secondary-btn rising-seer-disabled-btn">
                    Get Early Access
                </span>
            </div>
            
            <!-- VERBOSE: Mystical animation elements for visual appeal -->
            <div class="mystical-particles" id="mystical-particles">
                <!-- VERBOSE: Gold stars will be generated dynamically by JavaScript -->
            </div>
            
        </div>
    </div>
</div>

<!-- VERBOSE: Eye tracking script loaded via WordPress enqueuing in functions.php -->

<style>
/* VERBOSE: Inline styles for immediate splash page functionality */
.rising-seer-splash-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 9999;
    background: radial-gradient(circle at center, var(--rising-seer-primary-purple) 0%, var(--rising-seer-deep-gradient) 70%);
}

.rising-seer-splash-actions {
    margin-top: 40px;
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.rising-seer-splash-actions .ast-button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-align: center !important;
    text-decoration: none !important;
    min-width: 150px !important;
    padding: 12px 24px !important;
}

.rising-seer-secondary-btn {
    background: transparent !important;
    border: 2px solid var(--rising-seer-accent-gold) !important;
    color: var(--rising-seer-accent-gold) !important;
}

.rising-seer-secondary-btn:hover {
    background: var(--rising-seer-accent-gold) !important;
    color: var(--rising-seer-deep-gradient) !important;
}

.rising-seer-splash-description {
    max-width: 500px;
    margin: 20px auto;
    font-size: 1.1rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.9);
}

/* VERBOSE: Enhanced mystical particle animation for visual appeal */
.mystical-particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none;
    overflow: hidden;
    z-index: 1;
}

.star-particle {
    position: absolute;
    width: 16px;
    height: 16px;
    background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/gold-star.svg');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0;
    transition: opacity 2s ease-in-out;
    filter: drop-shadow(0 0 4px var(--rising-seer-glow-gold));
}

/* VERBOSE: Fallback for browsers that don't support SVG */
.star-particle.fallback {
    background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/gold-star.png');
    background-color: var(--rising-seer-accent-gold);
    border-radius: 50%;
    box-shadow: 0 0 8px var(--rising-seer-glow-gold);
}

/* VERBOSE: Responsive design for mobile devices */
@media (max-width: 768px) {
    .rising-seer-splash-content {
        max-width: 90vw; /* VERBOSE: Wider on mobile for readability */
        padding: 20px;
    }
    
    .rising-seer-logo {
        max-width: 120px;
    }
    
    .rising-seer-splash-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .ast-button {
        width: 200px;
        text-align: center;
    }
}

/* VERBOSE: Tablet responsive design */
@media (min-width: 769px) and (max-width: 1024px) {
    .rising-seer-splash-content {
        max-width: 60vw; /* VERBOSE: Slightly wider on tablets */
    }
}

/* VERBOSE: Large desktop optimization */
@media (min-width: 1400px) {
    .rising-seer-splash-content {
        max-width: 45vw; /* VERBOSE: Slightly narrower on huge screens but still bold */
    }
    
    .rising-seer-logo {
        max-width: 250px;
    }
}

.rising-seer-splash-content {
    max-width: 50vw; /* VERBOSE: 50% of viewport width - bigger and bolder! */
    min-width: 300px; /* VERBOSE: Minimum width for mobile readability */
    width: 100%;
    padding: 40px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* VERBOSE: Disabled button styling - unclickable but visible */
.rising-seer-disabled-btn {
    opacity: 0.5 !important;
    cursor: default !important;
    pointer-events: none !important;
    background: transparent !important;
    border: 2px solid rgba(255, 189, 89, 0.3) !important;
    color: rgba(255, 189, 89, 0.5) !important;
}

.rising-seer-disabled-btn:hover {
    background: transparent !important;
    color: rgba(255, 189, 89, 0.5) !important;
    transform: none !important;
    box-shadow: none !important;
}
</style>

<?php 
// VERBOSE: Log successful splash page rendering
error_log('Rising Seer: Splash page template rendered successfully');

// VERBOSE: Skip footer for full-screen experience
// get_footer(); 
?>

<script>
// VERBOSE: Enhanced Dynamic Gold Star Field System
document.addEventListener('DOMContentLoaded', function() {
    console.log('Rising Seer: Enhanced gold star field initializing...');
    
    const particleContainer = document.getElementById('mystical-particles');
    let maxStars = 20; // Maximum number of stars normally
    let activeStars = [];
    
    // VERBOSE: Check if SVG is supported
    function supportsSVG() {
        return document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1");
    }
    
    // VERBOSE: Create a single gold star with random properties
    function createGoldStar() {
        const star = document.createElement('div');
        star.className = 'star-particle';
        
        // VERBOSE: Use SVG if supported, otherwise fallback to PNG/CSS
        if (!supportsSVG()) {
            star.classList.add('fallback');
        }
        
        // VERBOSE: Random size between 12px and 20px for better visibility
        const size = Math.random() * 8 + 12;
        star.style.width = size + 'px';
        star.style.height = size + 'px';
        
        // VERBOSE: Random position across entire screen
        star.style.left = Math.random() * 100 + '%';
        star.style.top = Math.random() * 100 + '%';
        
        // VERBOSE: Random opacity for variety
        const maxOpacity = Math.random() * 0.7 + 0.4; // Between 0.4 and 1.1
        
        // VERBOSE: Add subtle rotation for more dynamic effect
        const rotation = Math.random() * 360;
        star.style.transform = `rotate(${rotation}deg)`;
        
        particleContainer.appendChild(star);
        
        // VERBOSE: Fade in after a brief delay
        setTimeout(() => {
            star.style.opacity = maxOpacity;
        }, 100);
        
        return {
            element: star,
            maxOpacity: maxOpacity,
            lifetime: Math.random() * 5000 + 4000, // VERBOSE: Live for 4-9 seconds
            rotation: rotation
        };
    }
    
    // VERBOSE: Remove a star with fade out effect
    function removeGoldStar(starObj) {
        starObj.element.style.opacity = '0';
        setTimeout(() => {
            if (starObj.element.parentNode) {
                starObj.element.parentNode.removeChild(starObj.element);
            }
        }, 2000); // VERBOSE: Wait for fade out transition
    }
    
    // VERBOSE: Main star management loop
    function manageGoldStars() {
        // VERBOSE: Remove expired stars
        activeStars = activeStars.filter(starObj => {
            if (Date.now() - starObj.birthTime > starObj.lifetime) {
                removeGoldStar(starObj);
                return false;
            }
            return true;
        });
        
        // VERBOSE: Add new stars if below maximum
        while (activeStars.length < maxStars) {
            const newStar = createGoldStar();
            newStar.birthTime = Date.now();
            activeStars.push(newStar);
        }
    }
    
    // Easter Egg: explode stars on logo click
    const logoEl = document.querySelector('.rising-seer-logo');
    if (logoEl) {
        logoEl.addEventListener('click', () => {
            const previousMax = maxStars;
            maxStars = 150; // supernova!

            // instantly spawn a burst of stars
            for (let i = 0; i < 120; i++) {
                const s = createGoldStar();
                s.birthTime = Date.now();
                activeStars.push(s);
            }

            // revert after about 2.5 s
            setTimeout(() => { maxStars = previousMax; }, 2500);
        });
    }
    
    // VERBOSE: Start the gold star field
    manageGoldStars();
    
    // VERBOSE: Continuously manage stars every 600ms
    setInterval(manageGoldStars, 600);
    
    console.log('Rising Seer: Enhanced gold star field active with ' + maxStars + ' maximum stars');
    
    // VERBOSE: Add click handlers for action buttons
    const buttons = document.querySelectorAll('.rising-seer-splash-actions .ast-button');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Rising Seer: Action button clicked - ' + this.textContent);
            // VERBOSE: Add custom tracking or actions here
        });
    });
    
    // VERBOSE: Enhanced mystical cursor effect
    document.addEventListener('mousemove', function(e) {
        const cursor = document.querySelector('.mystical-cursor');
        if (!cursor) {
            const newCursor = document.createElement('div');
            newCursor.className = 'mystical-cursor';
            newCursor.style.cssText = `
                position: fixed;
                width: 24px;
                height: 24px;
                background: radial-gradient(circle, var(--rising-seer-accent-gold) 0%, transparent 70%);
                border-radius: 50%;
                pointer-events: none;
                z-index: 10000;
                opacity: 0.7;
                transition: transform 0.1s ease;
                filter: blur(1px);
            `;
            document.body.appendChild(newCursor);
        }
        
        const mysticalCursor = document.querySelector('.mystical-cursor');
        if (mysticalCursor) {
            mysticalCursor.style.left = e.clientX - 12 + 'px';
            mysticalCursor.style.top = e.clientY - 12 + 'px';
        }
    });
});
</script>
<?php wp_footer(); ?>
<?php exit; ?> 