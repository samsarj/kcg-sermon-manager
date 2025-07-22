<?php
function series_featured_image_shortcode($atts)
{
    global $post;

    // Attributes with default values
    $atts = shortcode_atts(array(
        'size' => 'medium', // Image size (thumbnail, medium, large, full)
        'class' => 'series-image', // CSS class for the image
    ), $atts, 'series_featured_image');

    // If we're not in a post context, try to get the current post ID
    if (!$post) {
        $post_id = get_the_ID();
        if (!$post_id) {
            return '';
        }
    } else {
        $post_id = $post->ID;
    }

    // Check if this is a sermon post
    if (get_post_type($post_id) !== 'sermon') {
        return '';
    }

    // Get the series terms for this sermon
    $series_terms = get_the_terms($post_id, 'series');

    if (!$series_terms || is_wp_error($series_terms)) {
        return '';
    }

    // Use the first series (assuming one series per sermon)
    $series = $series_terms[0];
    $series_id = $series->term_id;

    // Get the series image from ACF field
    $series_image = get_field('series_image', 'series_' . $series_id);

    if (!$series_image) {
        return '';
    }

    // Get the image data
    $image_url = esc_url($series_image['url']);
    $alt_text = esc_attr($series_image['alt']);

    if (!$alt_text) {
        $alt_text = $series->name . ' series image';
    }

    // Build the image HTML
    $output = '<img src="' . $image_url . '" alt="' . $alt_text . '" class="' . esc_attr($atts['class']) . '" />';

    return $output;
}
add_shortcode('series_featured_image', 'series_featured_image_shortcode');
