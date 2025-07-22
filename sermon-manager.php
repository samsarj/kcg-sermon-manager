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

// // Include sermon rss generator
require_once (plugin_dir_path(__FILE__) . 'includes/rss.php');

function enqueue_sermon_styles() {
    // Enqueue the sermon-manager stylesheet
    wp_enqueue_style('sermon-manager-styles', plugin_dir_url(__FILE__) . 'css/sermon-manager.css');
    
    // Enqueue the layout-specific stylesheet
    wp_enqueue_style('sermon-single-styles', plugin_dir_url(__FILE__) . 'css/sermon-single.css');
}

add_action('wp_enqueue_scripts', 'enqueue_sermon_styles');

// Function to get series image for archives
function get_sermon_series_image($post_id) {
    $series_terms = get_the_terms($post_id, 'series');
    
    if (!$series_terms || is_wp_error($series_terms)) {
        return '';
    }
    
    $series = $series_terms[0]; // Assuming one series per sermon
    $series_image_id = get_term_meta($series->term_id, 'series_image', true);
    
    if (!$series_image_id) {
        return '';
    }
    
    $series_image_url = wp_get_attachment_url($series_image_id);
    
    $output = '<div class="sermon-series-image">';
    $output .= '<img src="' . esc_url($series_image_url) . '" alt="' . esc_attr($series->name) . '" width="150" height="150" />';
    $output .= '</div>';
    
    return $output;
}

// Remove the additional filter since we're handling it in the main content function

function custom_sermon_content_filter($content) {
    global $post;
    
    // Only process sermon posts
    if (!is_object($post) || $post->post_type !== 'sermon') {
        return $content;
    }

    // For single sermon pages
    if (is_single() && get_post_type() === 'sermon') {
        return get_sermon_single_content($post->ID);
    }
    
    // For archive pages, add minimal sermon info
    if (is_archive() || is_home()) {
        return $content . get_sermon_archive_content($post->ID);
    }
    
    return $content;
}
add_filter('the_content', 'custom_sermon_content_filter');

// Function to get single sermon content
function get_sermon_single_content($post_id) {
    $output = '<div class="sermon-main">';
    
    // Get sermon details (passage, date, speaker)
    $output .= get_sermon_details_html($post_id);

    // Get sermon excerpt
    $output .= get_sermon_excerpt_html($post_id);
    
    // Get audio player
    $output .= get_sermon_audio_html($post_id);
    
    // Get the full series info with image, title, and description
    $series_terms = get_the_terms($post_id, 'series');
    if ($series_terms && !is_wp_error($series_terms)) {
        // Assuming only one series per sermon
        $output .= get_full_series_info_html($series_terms[0]->term_id);
    }

    $output .= '</div>';
    return $output;
}

// Function to get archive sermon content
function get_sermon_archive_content($post_id) {
    $output = '<div class="sermon-archive-details">';
    // Just sermon details for archives (passage, date, speaker)
    $output .= get_sermon_details_html($post_id);
    $output .= '</div>';
    return $output;
}

// Reusable function for sermon details
function get_sermon_details_html($post_id) {
    $output = '<div class="sermon-details">';
    
    // Get ACF fields
    $sermon_passage = get_field('sermon_passage', $post_id);

    // Get WP meta fields
    $sermon_date = get_the_date('D jS F Y', $post_id);
    $speaker = get_the_terms($post_id, 'speaker');
    
    // Output sermon passage if it exists
    if ($sermon_passage) {
        $passage_url = urlencode($sermon_passage);
        $bible_gateway_url = 'https://www.biblegateway.com/passage/?search=' . $passage_url . '&version=NIVUK';
        $output .= '<a href="' . esc_url($bible_gateway_url) . '" target="_blank" rel="noopener noreferrer">' . esc_html($sermon_passage) . '</a>';
    } else {
        $output .= 'No passage available.';
    }
    
    // Output sermon date if it exists
    if ($sermon_date) {
        $output .= '<div class="sermon-date">' . esc_html($sermon_date) . '</div>';
    }
    
    // Output speaker(s) if it exists
    if ($speaker && !is_wp_error($speaker)) {
        $speaker_name = esc_html($speaker[0]->name); // Assuming there's only one speaker per sermon
        $speaker_link = get_term_link($speaker[0]);
        $output .= '<a href="' . esc_url($speaker_link) . '">' . $speaker_name . '</a>';
    } else {
        $output .= 'No speaker information available.';
    }
    
    $output .= '</div>';
    return $output;
}

// Function to get sermon excerpt
function get_sermon_excerpt_html($post_id) {
    $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : '';
    
    if (!empty($excerpt)) {
        return '<div class="sermon-excerpt">' . esc_html($excerpt) . '</div>';
    }
    
    return '';
}

// Function to get series info HTML (for archives - minimal)
function get_series_info_html($series_id) {
    $series = get_term($series_id, 'series');
    
    if (!$series || is_wp_error($series)) {
        return '';
    }
    
    $output = '<div class="series-info-minimal">';
    $output .= '<div class="series-title">Series: <a href="' . esc_url(get_term_link($series)) . '">' . esc_html($series->name) . '</a></div>';
    $output .= '</div>';
    
    return $output;
}

// Function to get full series info HTML (for single sermons)
function get_full_series_info_html($series_id) {
    $series = get_term($series_id, 'series');
    
    if (!$series || is_wp_error($series)) {
        return '';
    }

    $output = '<a href="' . esc_url(get_term_link($series)) . '" class="kcg-card series-info-full">';

    // Series image
    $series_image_id = get_term_meta($series_id, 'series_image', true);
    if ($series_image_id) {
        $series_image_url = wp_get_attachment_url($series_image_id);
        // $output .= '<div class="series-image-wrapper">';
        $output .= '<img src="' . esc_url($series_image_url) . '" alt="' . esc_attr($series->name) . '" class="series-image" />';
        // $output .= '</div>';
    }
    
    // Series details
    $output .= '<div class="series-details">';

    // Series title
    $output .= '<h2 class="series-title">' . esc_html($series->name) . '</h2>';

    // Add series book
    $series_book = get_field('series_book', 'series_' . $series_id);
    if ($series_book) {
        if ($series_book === 'topical') {
            $output .= '<p>Part of a topical series.</p>';
        } else {
            $output .= '<p>A series in ' . esc_html($series_book) . '.</p>';
        }
    }

    // Series description
    $series_description = term_description($series_id, 'series');
    if ($series_description) {
        $output .= '<div class="series-description">' . wp_kses_post($series_description) . '</div>';
    }
    $output .= '</div>'; // Close series-details div
    
    $output .= '</a>';
    
    return $output;
}

// Reusable function for sermon audio
function get_sermon_audio_html($post_id) {
    $sermon_audio = get_field('sermon_audio', $post_id);
    $output = '';
    
    if ($sermon_audio) {
        $audio_url = esc_url($sermon_audio['url']);
        $output .= '<div class="sermon-audio">';
        $output .= '<audio controls>';
        $output .= '<source src="' . $audio_url . '" type="audio/mpeg">';
        $output .= 'Your browser does not support the audio element.';
        $output .= '</audio>';
        $output .= '</div>';
    } else {
        $output .= '<div class="sermon-audio"><p>No audio file available for this sermon.</p></div>';
    }
    
    return $output;
}
