<?php

function latest_sermon_shortcode($atts)
{
    // Fetch the latest sermon
    $latest_sermon = new WP_Query(array(
        'post_type' => 'sermon',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    if ($latest_sermon->have_posts()) {
        $latest_sermon->the_post();
        $post_id = get_the_ID();
        wp_reset_postdata();

        // Build the output
        $output = '<div class="latest-sermon">';
        
        $output .= do_shortcode('[sermon_title id="' . $post_id . '" heading="h3"]');
        // Call the sermon shortcode
        $output .= do_shortcode('[sermon id="' . $post_id . '"]');

        $output .= '</div>';
        
        return $output;
    } else {
        return '<p>Latest sermon not found.</p>';
    }
}
add_shortcode('latest_sermon', 'latest_sermon_shortcode');
