<?php
function latest_sermon_series_shortcode($atts)
{
    // Query arguments to get the latest sermon
    $latest_sermon_query = new WP_Query(array(
        'post_type' => 'sermon',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    if ($latest_sermon_query->have_posts()) {
        $latest_sermon_query->the_post();
        $post_id = get_the_ID();
        wp_reset_postdata();

        // Get the series associated with the latest sermon
        $series_terms = get_the_terms($post_id, 'series');
        
        if ($series_terms && !is_wp_error($series_terms)) {
            $series = $series_terms[0]; // Assuming there's only one series associated
            $series_id = $series->term_id;

            // Use the series shortcode function to display the series
            return do_shortcode('[series id="' . $series_id . '"]');
        } else {
            return '';
        }
    } else {
        return '<p>No sermons found.</p>';
    }
}
add_shortcode('latest_sermon_series', 'latest_sermon_series_shortcode');
