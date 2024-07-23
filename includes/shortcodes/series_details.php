<?php

function series_details_shortcode($atts) {
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Series ID (optional)
    ), $atts, 'series_details');

    // If an ID is provided, use it
    if (!empty($atts['id'])) {
        $series_id = $atts['id'];
    } else {
        // Get the term ID from the current context if no ID is provided
        if (is_tax('series')) {
            $term = get_queried_object();
            $series_id = $term->term_id;
        } else {
            return '<p>No series ID provided and not on a series archive page.</p>';
        }
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
