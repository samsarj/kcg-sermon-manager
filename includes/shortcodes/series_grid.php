<?php

function series_grid_shortcode() {
    // Get all series terms
    $series_terms = get_terms(array(
        'taxonomy' => 'series',
        'hide_empty' => false,
    ));

    if (empty($series_terms) || is_wp_error($series_terms)) {
        return '<p>No series found.</p>';
    }

    // Retrieve the ACF field group for series
    $field_group = acf_get_field_group('group_series_details');
    $fields = acf_get_fields($field_group['key']);

    $book_choices = array();
    foreach ($fields as $field) {
        if ($field['name'] === 'series_book') {
            $book_choices = $field['choices'];
            break;
        }
    }

    // Array to store series with their latest sermon date
    $series_with_dates = array();

    foreach ($series_terms as $series) {
        $series_id = $series->term_id;

        // Get the latest sermon in this series
        $latest_sermon = new WP_Query(array(
            'post_type' => 'sermon',
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'series',
                    'field' => 'term_id',
                    'terms' => $series_id,
                ),
            ),
        ));

        if ($latest_sermon->have_posts()) {
            $latest_sermon->the_post();
            $latest_sermon_date = get_the_date('Y-m-d H:i:s');
            wp_reset_postdata();
        } else {
            $latest_sermon_date = '0000-00-00 00:00:00'; // Default to the oldest possible date if no sermons are found
        }

        $series_with_dates[] = array(
            'series' => $series,
            'latest_sermon_date' => $latest_sermon_date,
        );
    }

    // Sort series by latest sermon date, newest first
    usort($series_with_dates, function($a, $b) {
        return strcmp($b['latest_sermon_date'], $a['latest_sermon_date']);
    });

    // Build the filter dropdowns
    $output = '<form method="GET" class="series-filter-form">';
    $output .= '<label for="book-filter">Filter by Book:</label>';
    $output .= '<select name="book" id="book-filter">';
    $output .= '<option value="">All Books</option>';

    foreach ($book_choices as $value => $label) {
        $selected = (isset($_GET['book']) && $_GET['book'] == $value) ? 'selected' : '';
        $output .= '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
    }
    
    $output .= '</select>';

    $output .= '<label for="date-filter">Sort by Date:</label>';
    $output .= '<select name="date" id="date-filter">';
    $output .= '<option value="desc"' . (isset($_GET['date']) && $_GET['date'] == 'desc' ? 'selected' : '') . '>Newest First</option>';
    $output .= '<option value="asc"' . (isset($_GET['date']) && $_GET['date'] == 'asc' ? 'selected' : '') . '>Oldest First</option>';
    $output .= '</select>';

    $output .= '<button type="submit">Filter</button>';
    $output .= '</form>';

    // Start building the output
    $output .= '<div class="series-grid">';

    // Apply the filters
    $filtered_series = array_filter($series_with_dates, function($item) {
        $book_filter = isset($_GET['book']) ? $_GET['book'] : '';
        if ($book_filter) {
            $series_book = get_field('series_book', 'series_' . $item['series']->term_id);
            return $series_book == $book_filter;
        }
        return true;
    });

    // Sort the filtered series by date if specified
    if (isset($_GET['date']) && $_GET['date'] == 'asc') {
        usort($filtered_series, function($a, $b) {
            return strcmp($a['latest_sermon_date'], $b['latest_sermon_date']);
        });
    } else {
        usort($filtered_series, function($a, $b) {
            return strcmp($b['latest_sermon_date'], $a['latest_sermon_date']);
        });
    }

    foreach ($filtered_series as $item) {
        $series = $item['series'];
        $series_id = $series->term_id;
        $series_name = esc_html($series->name);
        $series_link = get_term_link($series_id, 'series');
        $series_image = get_field('series_image', 'series_' . $series_id);
        $title_in_image = get_field('series_title_in_image', 'series_' . $series_id);

        $output .= '<div class="series-grid-item">';
        $output .= '<a href="' . esc_url($series_link) . '">';

        if ($series_image) {
            $image_url = esc_url($series_image['url']);
            $alt_text = esc_attr($series_image['alt']);
            $output .= '<img src="' . $image_url . '" alt="' . $alt_text . '" class="series-grid-image">';
            if (!$title_in_image) {
                $output .= '<div class="series-grid-title"><h5>' . $series_name . '</h5></div>';
            }
        } else {
            $output .= '<div class="series-grid-title"><h5>' . $series_name . '</h5></div>';
        }

        $output .= '</a>';
        $output .= '</div>';
    }

    $output .= '</div>';

    // Return the output
    return $output;
}
add_shortcode('series_grid', 'series_grid_shortcode');
