<?php
function series_shortcode($atts) {
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Series ID (optional)
        'heading' => 'h4', // Default heading level
    ), $atts, 'series');

    // Get the series ID from attributes
    $series_id = $atts['id'];
    $heading_level = esc_html($atts['heading']);

    // Ensure heading level is valid (between h1 and h6)
    if (!in_array($heading_level, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
        $heading_level = 'h4'; // Fallback to default if invalid
    }

    // If no ID is provided, try to get it from the current context
    if (empty($series_id)) {
        if (is_tax('series')) {
            $term = get_queried_object();
            $series_id = $term->term_id;
        } else {
            return '<p>No series ID provided and not on a series archive page.</p>';
        }
    }

    // Get the series term object
    $series = get_term($series_id, 'series');

    // If series not found or invalid, return a message
    if (!$series || is_wp_error($series)) {
        return '<p>Series not found or invalid.</p>';
    }

    // Get the series title and URL
    $series_title = esc_html($series->name);
    $series_url = get_term_link($series);

    // Build the output
    $output = '';
    $output .= '<div class="sermon-series">';
    // Call the series image shortcode
    $output .= do_shortcode('[series_image id="' . $series_id . '"]');

    $output .= '<div class="sermon-series-content">';    
    // Call the series title shortcode
    $output .= '<' . $heading_level . ' class="series-title">';
    $output .= '<a href="' . esc_url($series_url) . '">' . $series_title . '</a>';
    $output .= '</' . $heading_level . '>';

    // Call the series details shortcode
    $output .= do_shortcode('[series_details id="' . $series_id . '"]');

    $output .= '</div>';
    $output .= '</div>';
    // Return the combined output
    return $output;
}
add_shortcode('series', 'series_shortcode');
