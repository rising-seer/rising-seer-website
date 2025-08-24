<?php
/**
 * WP Migrate Compatibility Fix for WordPress 6.8+
 * 
 * WordPress 6.2+ replaced the Requests class with WpOrg\Requests\Requests
 * This creates an alias to maintain backward compatibility for WP Migrate
 */

// Only create alias if the old class doesn't exist but the new one does
if (!class_exists('Requests') && class_exists('WpOrg\Requests\Requests')) {
    class_alias('WpOrg\Requests\Requests', 'Requests');
}