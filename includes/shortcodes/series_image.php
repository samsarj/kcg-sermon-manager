<?php
function series_image_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Series ID
    ), $atts, 'series_image');

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
