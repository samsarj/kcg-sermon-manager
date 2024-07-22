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

    // Get ACF fields
    $sermon_audio = get_field('sermon_audio', $post_id);
    $sermon_passage = get_field('sermon_passage', $post_id);

    // Get the sermon passage if it exists
    if ($sermon_passage) {
        $passage_url = urlencode($sermon_passage);
        $bible_gateway_url = 'https://www.biblegateway.com/passage/?search=' . $passage_url . '&version=NIVUK';
        $passage_output = '<a href="' . esc_url($bible_gateway_url) . '" target="_blank" rel="noopener noreferrer">' . esc_html($sermon_passage) . '</a>';
    } else {
        $passage_output = '<p>No passage available.</p>';
    }

    // Get speaker details
    $speaker = get_the_terms($post_id, 'speaker');
    if ($speaker && !is_wp_error($speaker)) {
        $speaker_name = esc_html($speaker[0]->name); // Assuming there's only one speaker per sermon
        $speaker_link = get_term_link($speaker[0]);
        $speaker_output = '<a href="' . esc_url($speaker_link) . '">' . $speaker_name . '</a>';
    } else {
        $speaker_output = '<p>No speaker information available.</p>';
    }

    // Get sermon date
    $sermon_date = get_the_date('D jS F Y', $post_id);
    $date_output = esc_html($sermon_date);

    // Add details to the output
    $output .= '<div class="sermon-details">';
    $output .= '<div class="sermon-detail">' . $passage_output . '</div>';
    $output .= '<div class="sermon-detail">' . $speaker_output . '</div>';
    $output .= '<div class="sermon-detail">' . $date_output . '</div>';
    $output .= '</div>';

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
