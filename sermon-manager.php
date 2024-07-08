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
require_once(plugin_dir_path(__FILE__) . 'includes/shortcodes.php');

// Include Custom Fields (ACF setup)
require_once(plugin_dir_path(__FILE__) . 'includes/custom-fields.php');

// Include Custom Columns (admin tables)
require_once(plugin_dir_path(__FILE__) . 'includes/custom-columns.php');

// Include Functions (for upload directories)
require_once(plugin_dir_path(__FILE__) . 'includes/functions.php');

function generate_sermon_feed() {
    add_feed('sermons', 'sermon_feed_callback');
}
add_action('init', 'generate_sermon_feed');

function sermon_feed_callback() {
    $posts = get_posts(array('post_type' => 'sermon', 'posts_per_page' => -1));

    header('Content-Type: application/rss+xml; charset=' . get_option('blog_charset'), true);

    echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
    echo '<rss version="2.0">';
    echo '<channel>';
    echo '<title>' . get_bloginfo('name') . ' - Sermons</title>';
    echo '<link>' . get_bloginfo('url') . '</link>';
    echo '<description>' . get_bloginfo('description') . '</description>';

    foreach ($posts as $post) {
        setup_postdata($post);
        $audio_url = wp_get_attachment_url(get_post_meta($post->ID, '_sermon_audio', true));

        echo '<item>';
        echo '<title>' . get_the_title($post->ID) . '</title>';
        echo '<link>' . get_permalink($post->ID) . '</link>';
        echo '<description>' . get_the_excerpt($post->ID) . '</description>';
        echo '<enclosure url="' . esc_url($audio_url) . '" length="' . filesize(get_attached_file(get_post_meta($post->ID, '_sermon_audio', true))) . '" type="audio/mpeg" />';
        echo '<guid>' . get_permalink($post->ID) . '</guid>';
        echo '<pubDate>' . get_the_date('r', $post->ID) . '</pubDate>';
        echo '</item>';
    }

    echo '</channel>';
    echo '</rss>';
}
