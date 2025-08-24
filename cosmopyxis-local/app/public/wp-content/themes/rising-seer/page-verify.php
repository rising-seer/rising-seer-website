<?php
/**
 * Template Name: Email Verification
 * 
 * Handles email verification redirects from Firebase
 * Provides seamless same-device flow and clear cross-device instructions
 * 
 * @package RisingSeer
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Log verification page loading
error_log('Rising Seer: Email verification page loaded');

// Remove default header and footer for full-screen experience
remove_action('astra_header', 'astra_header_markup');
remove_action('astra_footer', 'astra_footer_markup');

get_header(); ?>

<div class="rising-seer-verify-wrapper">
    <div class="rising-seer-verify">
        <div class="rising-seer-verify-content">
            
            <!-- Verification Status Container -->
            <div class="verify-status-container">
                <!-- Loading State (shown initially) -->
                <div id="verify-loading" class="verify-state">
                    <div class="loading-spinner"></div>
                    <h1 id="loading-title">Processing...</h1>
                    <p id="loading-message">Please wait while we process your request.</p>
                </div>
                
                <!-- Success State (hidden initially) -->
                <div id="verify-success" class="verify-state" style="display: none;">
                    <div class="success-icon">✨</div>
                    <h1>Email Verified!</h1>
                    <p>Your email has been successfully verified.</p>
                    
                    <!-- Same Device Message -->
                    <div id="same-device" class="device-message" style="display: none;">
                        <p>Redirecting you back to Rising Seer...</p>
                        <div class="redirect-progress"></div>
                    </div>
                    
                    <!-- Different Device Message -->
                    <div id="different-device" class="device-message" style="display: none;">
                        <button onclick="openRisingSeerApp()" class="ast-button mystical-glow rising-seer-primary-btn">
                            Open Rising Seer
                        </button>
                        <div class="cross-device-note">
                            <strong>Verified on a different device?</strong><br>
                            Return to your original device to continue automatically.
                        </div>
                    </div>
                </div>
                
                <!-- Error State (hidden initially) -->
                <div id="verify-error" class="verify-state" style="display: none;">
                    <div class="error-icon">⚠️</div>
                    <h1>Verification Issue</h1>
                    <p id="error-message">There was an issue with verification. Please try again.</p>
                    <div class="error-actions">
                        <a href="https://risingseer.com/app" class="ast-button mystical-glow rising-seer-primary-btn">
                            Return to Rising Seer
                        </a>
                    </div>
                </div>
                
                <!-- Password Reset Form (hidden initially) -->
                <div id="password-reset" class="verify-state" style="display: none;">
                    <div class="reset-icon">🔐</div>
                    <h1>Reset Your Password</h1>
                    <p>Enter your new password below.</p>
                    
                    <form id="password-reset-form" class="reset-form">
                        <div class="form-group">
                            <input type="password" 
                                   id="new-password" 
                                   placeholder="New Password" 
                                   required 
                                   minlength="6"
                                   class="password-input">
                            <div class="password-requirements">
                                Minimum 6 characters
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <input type="password" 
                                   id="confirm-password" 
                                   placeholder="Confirm Password" 
                                   required 
                                   minlength="6"
                                   class="password-input">
                        </div>
                        
                        <div id="password-error" class="form-error" style="display: none;"></div>
                        
                        <button type="submit" class="ast-button mystical-glow rising-seer-primary-btn">
                            Reset Password
                        </button>
                    </form>
                </div>
                
                <!-- Password Reset Success (hidden initially) -->
                <div id="password-success" class="verify-state" style="display: none;">
                    <div class="success-icon">✨</div>
                    <h1>Password Updated!</h1>
                    <p>Your password has been successfully reset.</p>
                    <p>Redirecting you to Rising Seer...</p>
                    <div class="redirect-progress"></div>
                </div>
            </div>
            
            <!-- Mystical animation elements -->
            <div class="mystical-particles" id="mystical-particles"></div>
            
        </div>
    </div>
</div>

<style>
/* Verification Page Specific Styles */
.rising-seer-verify-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 9999;
    background: radial-gradient(circle at center, var(--rising-seer-primary-purple) 0%, var(--rising-seer-deep-gradient) 70%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.rising-seer-verify {
    width: 100%;
    max-width: 500px;
    padding: 20px;
}

.rising-seer-verify-content {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 60px 40px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    text-align: center;
    position: relative;
    z-index: 10;
}

.verify-state h1 {
    color: var(--rising-seer-accent-gold);
    font-size: 2.5rem;
    margin-bottom: 20px;
    font-weight: 600;
}

.verify-state p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 30px;
}

/* Loading Spinner */
.loading-spinner {
    width: 60px;
    height: 60px;
    margin: 0 auto 30px;
    border: 3px solid rgba(255, 189, 89, 0.2);
    border-top-color: var(--rising-seer-accent-gold);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Success Icon */
.success-icon, .error-icon {
    font-size: 80px;
    margin-bottom: 20px;
    display: block;
}

/* Redirect Progress Bar */
.redirect-progress {
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    margin-top: 20px;
    overflow: hidden;
}

.redirect-progress::after {
    content: '';
    display: block;
    width: 0;
    height: 100%;
    background: var(--rising-seer-accent-gold);
    animation: progress 2s ease-in-out forwards;
}

@keyframes progress {
    to { width: 100%; }
}

/* Device Messages */
.device-message {
    margin-top: 30px;
}

.cross-device-note {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
}

/* Action Button */
.rising-seer-primary-btn {
    background: var(--rising-seer-accent-gold) !important;
    color: var(--rising-seer-deep-gradient) !important;
    padding: 14px 32px !important;
    font-size: 1.1rem !important;
    border-radius: 8px !important;
    text-decoration: none !important;
    display: inline-block !important;
    transition: all 0.3s ease !important;
    margin-bottom: 20px;
    border: none !important;
    cursor: pointer;
}

.rising-seer-primary-btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 10px 30px rgba(255, 189, 89, 0.3) !important;
}

/* Password Reset Form Styles */
.reset-form {
    margin-top: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.password-input {
    width: 100%;
    padding: 14px 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 189, 89, 0.3);
    border-radius: 8px;
    color: white;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.password-input:focus {
    outline: none;
    border-color: var(--rising-seer-accent-gold);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 0 20px rgba(255, 189, 89, 0.2);
}

.password-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.password-requirements {
    margin-top: 8px;
    font-size: 0.85rem;
    color: rgba(255, 189, 89, 0.7);
}

.form-error {
    color: #ff6b6b;
    font-size: 0.9rem;
    margin-bottom: 20px;
    padding: 10px;
    background: rgba(255, 107, 107, 0.1);
    border-radius: 6px;
}

.reset-icon {
    font-size: 60px;
    margin-bottom: 20px;
    display: block;
}

/* Responsive */
@media (max-width: 768px) {
    .rising-seer-verify-content {
        padding: 40px 20px;
    }
    
    .verify-state h1 {
        font-size: 2rem;
    }
}

/* Reuse star particle styles from splash page */
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
</style>

<script>
// Inject Firebase Web API Key from env (falls back to inline string if not set)
const FIREBASE_API_KEY = '<?php echo getenv("FIREBASE_WEB_API_KEY") ?: ""; ?>';

// Email Verification & Password Reset Handler
document.addEventListener('DOMContentLoaded', function() {
    console.log('Rising Seer: Action handler initializing...');
    
    // Parse URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const mode = urlParams.get('mode');
    const oobCode = urlParams.get('oobCode');
    const continueUrl = urlParams.get('continueUrl') || 'https://risingseer.com/app';
    
    // State elements
    const loadingState = document.getElementById('verify-loading');
    const successState = document.getElementById('verify-success');
    const errorState = document.getElementById('verify-error');
    const sameDeviceMsg = document.getElementById('same-device');
    const differentDeviceMsg = document.getElementById('different-device');
    const passwordResetState = document.getElementById('password-reset');
    const passwordSuccessState = document.getElementById('password-success');
    const loadingTitle = document.getElementById('loading-title');
    const loadingMessage = document.getElementById('loading-message');
    
    // Handle different modes
    async function handleAction() {
        if (mode === 'resetPassword' && oobCode) {
            // Password Reset Flow
            loadingTitle.textContent = 'Verifying Reset Link...';
            loadingMessage.textContent = 'Please wait while we verify your password reset link.';
            
            // For password reset, we can't verify the code without using it
            // So we'll show the form immediately and handle errors when they submit
            setTimeout(() => {
                loadingState.style.display = 'none';
                passwordResetState.style.display = 'block';
                
                // Setup form handler
                setupPasswordResetForm(oobCode);
            }, 1000);
            
        } else if (mode === 'verifyEmail' && oobCode) {
            // Email Verification Flow
            loadingTitle.textContent = 'Verifying Email...';
            loadingMessage.textContent = 'Please wait while we confirm your email address.';
            
        } else {
            // Invalid parameters
            showError('Invalid action link. Please check your email and try again.');
        }
    }
    
    async function handleVerification() {
        try {
            // Apply the oobCode with Firebase REST API
            if (!FIREBASE_API_KEY) {
                console.error('Missing FIREBASE_API_KEY');
                showError('Server configuration error. Please contact support.');
                return;
            }

            const applyResp = await fetch(`https://identitytoolkit.googleapis.com/v1/accounts:update?key=${FIREBASE_API_KEY}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ oobCode: oobCode })
            });

            if (!applyResp.ok) {
                const errJson = await applyResp.json().catch(() => ({}));
                console.error('Firebase applyOobCode failed', errJson);
                showError('Verification code invalid or expired. Please request a new verification email.');
                return;
            }

            console.log('Firebase oobCode applied – email is now verified in Auth');

            // Check multiple indicators for same device/session
            const sessionId = localStorage.getItem('risingSeerSessionId');
            const hasFirebaseToken = localStorage.getItem('FirebaseIdToken');
            const hasUserEmail = localStorage.getItem('FirebaseUserEmail');
            
            // WebGL apps might not set risingSeerSessionId, so check Firebase tokens too
            const isSameDevice = sessionId || (hasFirebaseToken && hasUserEmail);
            
            if (isSameDevice) {
                // Same device - set verification flag and redirect
                localStorage.setItem('emailVerified', 'true');
                localStorage.setItem('emailVerificationTime', Date.now().toString());
                
                // Try to focus existing tab first
                if (window.opener && !window.opener.closed) {
                    window.opener.focus();
                    window.close();
                    return;
                }
                
                // Otherwise redirect
                window.location.replace(continueUrl);
                return;
            }
            
            // Different device - show the UI
            setTimeout(() => {
                loadingState.style.display = 'none';
                successState.style.display = 'block';
                differentDeviceMsg.style.display = 'block';
            }, 1200);
            
        } catch (error) {
            console.error('Verification error:', error);
            showError('An unexpected error occurred. Please try again.');
        }
    }
    
    function setupPasswordResetForm(oobCode) {
        const form = document.getElementById('password-reset-form');
        const newPasswordInput = document.getElementById('new-password');
        const confirmPasswordInput = document.getElementById('confirm-password');
        const passwordError = document.getElementById('password-error');
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Validate passwords match
            if (newPassword !== confirmPassword) {
                passwordError.textContent = 'Passwords do not match';
                passwordError.style.display = 'block';
                return;
            }
            
            // Validate password length
            if (newPassword.length < 6) {
                passwordError.textContent = 'Password must be at least 6 characters';
                passwordError.style.display = 'block';
                return;
            }
            
            passwordError.style.display = 'none';
            
            try {
                // Disable form while processing
                form.querySelector('button').disabled = true;
                form.querySelector('button').textContent = 'Resetting...';
                
                // Call Firebase REST API to reset password
                const resetResp = await fetch(`https://identitytoolkit.googleapis.com/v1/accounts:resetPassword?key=${FIREBASE_API_KEY}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        oobCode: oobCode,
                        newPassword: newPassword
                    })
                });
                
                if (!resetResp.ok) {
                    const errJson = await resetResp.json().catch(() => ({}));
                    console.error('Password reset failed', errJson);
                    passwordError.textContent = 'Failed to reset password. Please try again.';
                    passwordError.style.display = 'block';
                    form.querySelector('button').disabled = false;
                    form.querySelector('button').textContent = 'Reset Password';
                    return;
                }
                
                console.log('Password successfully reset');
                
                // Show success and redirect
                passwordResetState.style.display = 'none';
                passwordSuccessState.style.display = 'block';
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.replace(continueUrl);
                }, 2000);
                
            } catch (error) {
                console.error('Password reset error:', error);
                passwordError.textContent = 'An error occurred. Please try again.';
                passwordError.style.display = 'block';
                form.querySelector('button').disabled = false;
                form.querySelector('button').textContent = 'Reset Password';
            }
        });
    }
    
    function showError(message) {
        loadingState.style.display = 'none';
        errorState.style.display = 'block';
        if (message) {
            document.getElementById('error-message').textContent = message;
        }
    }
    
    // Initialize action handler (verification or password reset)
    handleAction();
    
    // Initialize star field (reuse from splash page)
    initializeStarField();
});

// Function to open Rising Seer app
function openRisingSeerApp() {
    const appUrl = 'https://risingseer.com/app';
    
    // Set verification flags even for cross-device scenario
    localStorage.setItem('emailVerified', 'true');
    localStorage.setItem('emailVerificationTime', Date.now().toString());
    
    // Try to find and focus existing window
    const existingWindow = window.open('', 'RisingSeerApp');
    
    if (existingWindow && existingWindow.location && existingWindow.location.href.includes('risingseer.com')) {
        // Existing window found - focus it
        existingWindow.focus();
        
        // Optionally close this verification window after a delay
        setTimeout(() => {
            window.close();
        }, 500);
    } else {
        // No existing window or can't access it - open new one
        window.open(appUrl, 'RisingSeerApp');
    }
}

// Simplified star field from splash page
function initializeStarField() {
    const particleContainer = document.getElementById('mystical-particles');
    const maxStars = 15; // Fewer stars for cleaner look
    let activeStars = [];
    
    function createGoldStar() {
        const star = document.createElement('div');
        star.className = 'star-particle';
        
        const size = Math.random() * 8 + 12;
        star.style.width = size + 'px';
        star.style.height = size + 'px';
        star.style.left = Math.random() * 100 + '%';
        star.style.top = Math.random() * 100 + '%';
        
        const maxOpacity = Math.random() * 0.5 + 0.3;
        particleContainer.appendChild(star);
        
        setTimeout(() => {
            star.style.opacity = maxOpacity;
        }, 100);
        
        return {
            element: star,
            lifetime: Math.random() * 5000 + 4000,
            birthTime: Date.now()
        };
    }
    
    function manageStars() {
        // Remove expired stars
        activeStars = activeStars.filter(starObj => {
            if (Date.now() - starObj.birthTime > starObj.lifetime) {
                starObj.element.style.opacity = '0';
                setTimeout(() => {
                    if (starObj.element.parentNode) {
                        starObj.element.remove();
                    }
                }, 2000);
                return false;
            }
            return true;
        });
        
        // Add new stars
        while (activeStars.length < maxStars) {
            activeStars.push(createGoldStar());
        }
    }
    
    manageStars();
    setInterval(manageStars, 800);
}
</script>

<?php wp_footer(); ?>
<?php exit; ?>