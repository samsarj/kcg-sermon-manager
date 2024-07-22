<?php
function series_title_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Series ID
        'heading' => 'h4', // Default heading level
    ), $atts, 'series_title');

    // Get series ID from attributes
    $series_id = $atts['id'];
    $heading_level = esc_html($atts['heading']);

    // Ensure heading level is valid (between h1 and h6)
    if (!in_array($heading_level, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
        $heading_level = 'h4'; // Fallback to default if invalid
    }

    // If no ID provided, return a message
    if (empty($series_id)) {
        return '<p>No series ID provided.</p>';
    }

    // Get the series term
    $series = get_term($series_id, 'series');

    // If series not found or invalid, return a message
    if (!$series || is_wp_error($series)) {
        return '<p>Series not found or invalid ID.</p>';
    }

    // Get the series title and URL
    $series_title = esc_html($series->name);
    $series_url = get_term_link($series);

    // Build the output
    $output = '<' . $heading_level . ' class="series-title">';
    $output .= '<a href="' . esc_url($series_url) . '">' . $series_title . '</a>';
    $output .= '</' . $heading_level . '>';

    // Return the series title
    return $output;
}
add_shortcode('series_title', 'series_title_shortcode');
