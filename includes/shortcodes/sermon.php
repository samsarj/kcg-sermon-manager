<?php

function sermon_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Post ID
    ), $atts, 'sermon');

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

    // Start building the output
    $output = '<div class="sermon-main">';

    // Call the sermon_details shortcode
    $output .= do_shortcode('[sermon_details id="' . $post_id . '"]');

    // Get ACF fields
    $sermon_audio = get_field('sermon_audio', $post_id);

    // Add the excerpt if it exists
    $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : '';
    if (!empty($excerpt)) {
        $output .= '<div class="sermon-excerpt">' . esc_html($excerpt) . '</div>';
    }

    // Add the audio player if audio file exists
    if ($sermon_audio) {
        $audio_url = esc_url($sermon_audio['url']);
        $output .= '<audio controls>';
        $output .= '<source src="' . $audio_url . '" type="audio/mpeg">';
        $output .= 'Your browser does not support the audio element.';
        $output .= '</audio>';
    } else {
        $output .= '<p>No audio file available for this sermon.</p>';
    }

    // Close the output div
    $output .= '</div>';

    // Return the output
    return $output;
}
add_shortcode('sermon', 'sermon_shortcode');
?>
