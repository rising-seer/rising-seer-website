<?php
/**
 * Rising Seer Child Theme Functions
 * Minimal clean version to ensure theme activation works
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function rising_seer_styles() {
    wp_enqueue_style('astra-parent', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('rising-seer-style', get_stylesheet_uri(), array('astra-parent'));
}
add_action('wp_enqueue_scripts', 'rising_seer_styles');

/**
 * NUCLEAR OPTION: Force inject eye tracking script
 */
function rising_seer_enqueue_scripts() {
    // Ensure jQuery is registered and enqueue our script after it
    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'rising-seer-eye-tracking',
        get_stylesheet_directory_uri() . '/assets/eye-tracking.js',
        array('jquery'),
        '1.0.0',
        true // load in footer for better performance
    );
}
add_action('wp_enqueue_scripts', 'rising_seer_enqueue_scripts');

/**
 * Theme setup
 */
function rising_seer_setup() {
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'rising_seer_setup'); 