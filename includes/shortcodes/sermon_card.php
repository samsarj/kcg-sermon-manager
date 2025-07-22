<?php

function sermon_card_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Post ID
    ), $atts, 'sermon_card');

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

    // Get sermon details
    $sermon_id = get_the_ID();
    $sermon_title = get_the_title();
    $sermon_url = get_permalink();
    $series = get_the_terms($sermon_id, 'series');

    // Start the card output
    $output = '<div class="sermon-card">';
    $output .= '<div class="sermon-card-sermon">';

    // Call the sermon shortcode
    $output .= do_shortcode('[sermon id="' . $sermon_id . '"]');
    $output .= '</div>';

    // Close the output div
    $output .= '</div>';

    // Return the output
    return $output;
}
add_shortcode('sermon_card', 'sermon_card_shortcode');
