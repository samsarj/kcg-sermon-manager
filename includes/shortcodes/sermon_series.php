<?php
function sermon_series_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Series ID
    ), $atts, 'sermon_series');

    // Get the series ID from attributes
    $series_id = $atts['id'];

    // If no ID provided, return a message
    if (empty($series_id)) {
        return '<p>No series ID provided.</p>';
    }

    // Get the series term object
    $series = get_term($series_id, 'series');

    // If series not found or invalid, return a message
    if (!$series || is_wp_error($series)) {
        return '<p>Series not found or invalid.</p>';
    }

    // Build the output
    $output = '';
    $output .= '<div class="sermon-series">';
    // Call the series image shortcode
    $output .= do_shortcode('[series_image id="' . $series_id . '"]');

    $output .= '<div class="sermon-series-content">';    
    // Call the series title shortcode
    $output .= do_shortcode('[series_title id="' . $series_id . '"]');

    // Call the series details shortcode
    $output .= do_shortcode('[series_details id="' . $series_id . '"]');

    $output .= '</div>';
    $output .= '</div>';
    // Return the combined output
    return $output;
}
add_shortcode('sermon_series', 'sermon_series_shortcode');
