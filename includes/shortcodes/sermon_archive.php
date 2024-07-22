<?php

function sermon_archive_shortcode($atts)
{
    // Attributes with default values
    $atts = shortcode_atts(array(
        'type' => 'latest', // default to 'latest', can be 'speaker' or 'series'
        'term' => '', // speaker or series term slug
        'posts_per_page' => 10, // number of sermons per page
    ), $atts, 'sermon_archive');

    // Get attributes
    $type = $atts['type'];
    $term = $atts['term'];
    $posts_per_page = intval($atts['posts_per_page']);

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

    // Search form
    $output .= '<form role="search" method="get" class="sermon-search-form" action="' . esc_url(home_url('/')) . '">';
    $output .= '<input type="hidden" name="post_type" value="sermon" />';
    $output .= '<input type="search" class="sermon-search-field" placeholder="Search sermons..." value="' . get_search_query() . '" name="s" title="Search for:" />';
    $output .= '<button type="submit" class="sermon-search-submit">Search</button>';
    $output .= '</form>';

    if ($sermons_query->have_posts()) {
        $output .= '<table class="sermon-table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th>Title</th>';
        $output .= '<th>Date</th>';
        $output .= '<th>Speaker</th>';
        $output .= '<th>Series</th>';
        $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        while ($sermons_query->have_posts()) {
            $sermons_query->the_post();

            // Get sermon details
            $sermon_title = get_the_title();
            $sermon_url = get_permalink();
            $sermon_date = get_the_date('D jS F Y');
            $speaker = get_the_terms(get_the_ID(), 'speaker');
            $series = get_the_terms(get_the_ID(), 'series');

            $output .= '<tr>';
            $output .= '<td><a href="' . esc_url($sermon_url) . '">' . esc_html($sermon_title) . '</a></td>';
            $output .= '<td>' . esc_html($sermon_date) . '</td>';

            // Speaker column
            if ($speaker && !is_wp_error($speaker)) {
                $output .= '<td>' . esc_html($speaker[0]->name) . '</td>';
            } else {
                $output .= '<td>N/A</td>';
            }

            // Series column
            if ($series && !is_wp_error($series)) {
                $output .= '<td>' . esc_html($series[0]->name) . '</td>';
            } else {
                $output .= '<td>N/A</td>';
            }

            $output .= '</tr>';
        }

        $output .= '</tbody>';
        $output .= '</table>';

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

    $output .= '</div>';

    // Return the output
    return $output;
}
add_shortcode('sermon_archive', 'sermon_archive_shortcode');
