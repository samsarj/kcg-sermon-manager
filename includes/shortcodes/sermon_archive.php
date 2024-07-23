<?php

function sermon_archive_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'posts_per_page' => 10, // number of sermons per page
    ), $atts, 'sermon_archive');

    // Get attributes
    $posts_per_page = intval($atts['posts_per_page']);

    // Determine context
    if (is_tax('speaker')) {
        $type = 'speaker';
        $term = get_queried_object()->slug;
    } elseif (is_tax('series')) {
        $type = 'series';
        $term = get_queried_object()->slug;
    } else {
        $type = 'latest';
        $term = '';
    }

    // Handle pagination
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // Query arguments
    $args = array(
        'post_type' => 'sermon',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
    );

    // Modify query based on type
    if ($type == 'speaker' && !empty($term)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'speaker',
                'field' => 'slug',
                'terms' => $term,
            ),
        );
    } elseif ($type == 'series' && !empty($term)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'series',
                'field' => 'slug',
                'terms' => $term,
            ),
        );
    } elseif ($type == 'latest') {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    }

    // Handle search
    if (isset($_GET['s'])) {
        $args['s'] = sanitize_text_field($_GET['s']);
    }

    // Query the sermons
    $sermons_query = new WP_Query($args);

    // Start building the output
    $output = '<div class="sermon-archive">';

    // Conditional search form
    if (!is_tax()) { // Only show search form if not on a taxonomy archive
        $output .= '<form role="search" method="get" class="sermon-search-form" action="' . esc_url(home_url('/')) . '">';
        $output .= '<input type="hidden" name="post_type" value="sermon" />';
        $output .= '<input type="search" class="sermon-search-field" placeholder="Search sermons..." value="' . get_search_query() . '" name="s" title="Search for:" />';
        $output .= '<button type="submit" class="sermon-search-submit">Search</button>';
        $output .= '</form>';
    }

    $output .= '<div class="sermon-cards">';

    if ($sermons_query->have_posts()) {
        while ($sermons_query->have_posts()) {
            $sermons_query->the_post();

            // Get sermon details
            $sermon_id = get_the_ID();
            $sermon_title = get_the_title();
            $sermon_url = get_permalink();
            $series = get_the_terms($sermon_id, 'series');
            
            // Start the card output
            $output .= '<div class="sermon-card">';
            $output .= '<div class="sermon-card-sermon">';
            $output .= '<h2><a href="' . esc_url($sermon_url) . '">' . esc_html($sermon_title) . '</a></h2>';
            
            // Get series details
            if ($series && !is_wp_error($series)) {
                $series_name = esc_html($series[0]->name); // Assuming there's only one series per sermon
                $series_image_id = get_term_meta($series[0]->term_id, 'series_image', true); // Assuming the series image is saved as a term meta
                $series_image_url = wp_get_attachment_url($series_image_id);
                $output .= '<a href="' . get_term_link($series[0]) . '"><strong> Series: ' . $series_name . '</strong></a>';
            }

            // Call the sermon_details shortcode
            $output .= do_shortcode('[sermon_details id="' . $sermon_id . '"]');

            // Add the excerpt if it exists
            $excerpt = has_excerpt($sermon_id) ? get_the_excerpt($sermon_id) : '';
            if (!empty($excerpt)) {
                $output .= '<div class="sermon-excerpt">' . esc_html($excerpt) . '</div>';
            }
            $output .= '</div>'; // Close sermon-card-sermon
            $output .= '<div class="sermon-card-series">';
            if ($series_image_url) {
                $output .= '<img src="' . esc_url($series_image_url) . '" alt="' . esc_attr($series_name) . '" class="series-image" />';
            }
            $output .= '</div>'; // Close sermon-card-series
            $output .= '</div>'; // Close sermon-card
        }
        $output .= '</div>'; // Close sermon-cards
        
        // Pagination
        $output .= '<div class="sermon-pagination">';
        $big = 999999999; // need an unlikely integer
        $output .= paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $sermons_query->max_num_pages,
        ));
        $output .= '</div>';

        wp_reset_postdata();
    } else {
        $output .= '<p>No sermons found.</p>';
    }

    $output .= '</div>'; // Close sermon-archive

    // Return the output
    return $output;
}
add_shortcode('sermon_archive', 'sermon_archive_shortcode');
