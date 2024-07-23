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

// Include Custom Post Types
require_once (plugin_dir_path(__FILE__) . 'includes/custom-post-types.php');

// Include all shortcode files from the includes/shortcodes directory
$shortcode_files = glob(plugin_dir_path(__FILE__) . 'includes/shortcodes/*.php');

foreach ($shortcode_files as $file) {
    require_once $file;
}

// Include Custom Fields (ACF setup)
require_once (plugin_dir_path(__FILE__) . 'includes/custom-fields.php');

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
        $sermon_content = do_shortcode('[sermon_single id="' . $post->ID . '"]');
    
        // Return the custom sermon content
        return $sermon_content;
    }
    
    // Return original content for non-sermon post types
    return $content;
}
add_filter('the_content', 'custom_sermon_content_filter');
