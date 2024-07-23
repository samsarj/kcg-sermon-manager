<?php
function series_image_shortcode($atts) {
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Series ID (optional)
    ), $atts, 'series_image');

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

    // If series not found or invalid, return a message
    if (!$series || is_wp_error($series)) {
        return '<p>Series not found or invalid.</p>';
    }

    // Get the series image from ACF field
    $series_image = get_field('series_image', 'series_' . $series_id);

    if ($series_image) {
        $image_url = esc_url($series_image['url']);
        $alt_text = esc_attr($series_image['alt']);
        return '<img src="' . $image_url . '" alt="' . $alt_text . '" class="series-image">';
    } else {
        return '<p>No image available for this series.</p>';
    }
}
add_shortcode('series_image', 'series_image_shortcode');
