<?php
function series_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'id' => '', // Series ID
    ), $atts, 'series');

    // Get series ID from attributes
    $series_id = $atts['id'];

    // If no ID provided, return a message
    if (empty($series_id)) {
        return '<p>No series ID provided.</p>';
    }

    // Get the series term object
    $series = get_term($series_id, 'series');

    // If series not found, return a message
    if (is_wp_error($series) || !$series) {
        return '<p>Series not found.</p>';
    }

    $output = '';
    $output .= do_shortcode('[series_title id="' . $series_id . '" heading="h3"]');
            // Call the series shortcode
            $output .= do_shortcode('[series_detail id="' . $series_id . '"]');


    // Get sermons in this series
    $args = array(
        'post_type' => 'sermon',
        'tax_query' => array(
            array(
                'taxonomy' => 'series',
                'field'    => 'term_id',
                'terms'    => $series->term_id,
            ),
        ),
        'posts_per_page' => -1, // Show all sermons in the series
    );
    $sermons_query = new WP_Query($args);

    if ($sermons_query->have_posts()) {
        $output .= '<h3>Sermons in this series:</h3>';
        $output .= '<ul class="sermons-list">';
        while ($sermons_query->have_posts()) {
            $sermons_query->the_post();
            $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        $output .= '</ul>';
        wp_reset_postdata();
    } else {
        $output .= '<p>No sermons found in this series.</p>';
    }

    // Close the output div
    $output .= '</div>';

    // Return the output
    return $output;
}
add_shortcode('series', 'series_shortcode');
