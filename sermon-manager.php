<?php
/*
Plugin Name: Sermon Manager
Description: A plugin to manage sermons, speakers, and series for King's Church Guildford.
Version: 1.0
Author: Sam Sarjudeen
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define the path to the ACF plugin directory
if (!defined('MY_ACF_PATH')) {
    define('MY_ACF_PATH', plugin_dir_path(__FILE__) . 'includes/acf/');
}

// Define the URL to the ACF plugin directory
if (!defined('MY_ACF_URL')) {
    define('MY_ACF_URL', plugin_dir_url(__FILE__) . 'includes/acf/');
}

// Check if ACF is already active
if (!class_exists('ACF')) {
    // Include the ACF plugin main file
    include_once(MY_ACF_PATH . 'acf.php');

    // Customize the URL setting to fix incorrect asset URLs
    add_filter('acf/settings/url', 'my_acf_settings_url');
    function my_acf_settings_url($url) {
        return MY_ACF_URL;
    }

    // Hide the ACF admin menu item
    add_filter('acf/settings/show_admin', '__return_false');
}

// Include Custom Post Types
require_once(plugin_dir_path(__FILE__) . 'includes/custom-post-types.php');

// Include Shortcodes
// require_once(plugin_dir_path(__FILE__) . 'includes/shortcodes.php');

// Include Custom Fields (ACF setup)
require_once(plugin_dir_path(__FILE__) . 'includes/custom-fields.php');

// Include Custom Columns (admin tables)
require_once(plugin_dir_path(__FILE__) . 'includes/custom-columns.php');

// Include Functions (for upload directories)
require_once(plugin_dir_path(__FILE__) . 'includes/functions.php');

// // Include sermon rss generator
require_once(plugin_dir_path(__FILE__) . 'includes/rss.php');