<?php
function sermon_title_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Post ID
        'heading' => 'h2', // Default heading level
    ), $atts, 'sermon_title');

    // Get post ID from attributes
    $post_id = $atts['id'];
    $heading_level = esc_html($atts['heading']);

    // Ensure heading level is valid (between h1 and h6)
    if (!in_array($heading_level, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
        $heading_level = 'h2'; // Fallback to default if invalid
    }

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

    // Get the sermon title
    $sermon_title = get_the_title($post_id);
    $sermon_url = get_permalink($post_id);

    // Build the output
    $output = '<' . $heading_level . ' class="sermon-title">';
    // $output .= '<a href="' . $sermon_url . '">';
    $output .= esc_html($sermon_title);
    // $output .= '</a>';
    $output .= '</' . $heading_level . '>';

    // Return the sermon title
    return $output;
}
add_shortcode('sermon_title', 'sermon_title_shortcode');
