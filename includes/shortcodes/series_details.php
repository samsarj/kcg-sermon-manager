<?php
function series_details_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Series ID
    ), $atts, 'series_details');

    // Get series ID from attributes
    $series_id = $atts['id'];

    // If no ID provided, return a message
    if (empty($series_id)) {
        return '<p>No series ID provided.</p>';
    }

    // Get the series term object
    $series = get_term($series_id, 'series');

    // If series not found, return a message
    if (is_wp_error($series) || !$series) {
        return '<p>Series not found.</p>';
    }

    // Get fields
    $series_book = get_field('series_book', 'series_' . $series->term_id);
    $series_description = term_description($series->term_id, 'series');

    // Start building the output
    $output = '<div class="series-details">';

    // Add series book
    if ($series_book) {
        if ($series_book === 'topical') {
            $output .= '<p>Part of a topical series.</p>';
        } else {
            $output .= '<p>A series in ' . esc_html($series_book) . '.</p>';
        }
    }

    // Add series description if it exists
    if ($series_description) {
        $output .= '<div class="series-description">' . wp_kses_post($series_description) . '</div>';
    }

    // Close the output div
    $output .= '</div>';

    // Return the output
    return $output;
}
add_shortcode('series_details', 'series_details_shortcode');
