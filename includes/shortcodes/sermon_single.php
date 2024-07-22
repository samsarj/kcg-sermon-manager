<?php
function sermon_single_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Post ID
    ), $atts, 'sermon_single');

    // Get post ID from attributes
    $post_id = $atts['id'];

    // If no ID provided, return a message
    if (empty($post_id)) {
        return '<p>No sermon ID provided.</p>';
    }

    // Get the post object
    $post = get_post($post_id);

    // If post not found or not a sermon, return a message
    if (!$post || $post->post_type !== 'sermon') {
        return '<p>Sermon not found or invalid post type.</p>';
    }

    // Build the output
    $output = '<div class="sermon-single">';

    // Call the sermon_title shortcode with h1
    $output .= do_shortcode('[sermon_title id="' . $post_id . '" heading="h1"]');

    // Call the sermon shortcode
    $output .= do_shortcode('[sermon id="' . $post_id . '"]');

    // Get series details
    $series = get_the_terms($post_id, 'series');
    if ($series && !is_wp_error($series)) {
        $series_id = $series[0]->term_id; // Assuming there's only one series per sermon
        // Call the series shortcode
        $output .= do_shortcode('[sermon_series id="' . $series_id . '"]');
    }

    $output .= '</div>';

    // Return the output
    return $output;
}
add_shortcode('sermon_single', 'sermon_single_shortcode');
