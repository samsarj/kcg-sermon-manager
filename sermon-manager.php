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

// // Define the path to the ACF plugin directory
// if (!defined('MY_ACF_PATH')) {
//     define('MY_ACF_PATH', plugin_dir_path(__FILE__) . 'includes/acf/');
// }

// // Define the URL to the ACF plugin directory
// if (!defined('MY_ACF_URL')) {
//     define('MY_ACF_URL', plugin_dir_url(__FILE__) . 'includes/acf/');
// }

// // Check if ACF is already active
// if (!class_exists('ACF')) {
//     // Include the ACF plugin main file
//     include_once (MY_ACF_PATH . 'acf.php');

//     // Customize the URL setting to fix incorrect asset URLs
//     add_filter('acf/settings/url', 'my_acf_settings_url');
//     function my_acf_settings_url($url)
//     {
//         return MY_ACF_URL;
//     }

//     // Hide the ACF admin menu item
//     add_filter('acf/settings/show_admin', '__return_false');
// }

// Include Custom Post Types
require_once (plugin_dir_path(__FILE__) . 'includes/custom-post-types.php');

// Include all shortcode files from the includes/shortcodes directory
$shortcode_files = glob(plugin_dir_path(__FILE__) . 'includes/shortcodes/*.php');

foreach ($shortcode_files as $file) {
    require_once $file;
}

// Include Custom Fields (ACF setup)
require_once (plugin_dir_path(__FILE__) . 'includes/custom-fields.php');

// Include Custom Columns (admin tables)
// require_once (plugin_dir_path(__FILE__) . 'includes/custom-columns.php');

// Include Functions (for upload directories)
require_once (plugin_dir_path(__FILE__) . 'includes/functions.php');    

// // Include sermon rss generator
require_once (plugin_dir_path(__FILE__) . 'includes/rss.php');

function enqueue_sermon_styles() {
    // Enqueue the sermon-manager stylesheet
    wp_enqueue_style('sermon-manager-styles', plugin_dir_url(__FILE__) . 'css/sermon-manager.css');
}

add_action('wp_enqueue_scripts', 'enqueue_sermon_styles');

function custom_sermon_content_filter($content) {
    global $post;
    
    // Check if the current post is of type 'sermon'
    if ($post->post_type === 'sermon') {
        // Build the custom sermon content using the shortcode
        $sermon_content = '<div class="single-sermon-container">';
        $sermon_content .= do_shortcode('[sermon_single id="' . $post->ID . '"]');
        $sermon_content .= '</div>';
    
        // Return the custom sermon content
        return $sermon_content;
    }
    
    // Return original content for non-sermon post types
    return $content;
}
add_filter('the_content', 'custom_sermon_content_filter');