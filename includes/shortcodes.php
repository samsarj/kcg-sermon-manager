<?php
function display_sermons($atts) {
    $atts = shortcode_atts(
        array(
            'per_page' => 10,
        ),
        $atts,
        'sermons'
    );

    $args = array(
        'post_type' => 'sermon',
        'posts_per_page' => $atts['per_page']
    );

    $sermons = new WP_Query($args);
    $output = '<ul>';

    if ($sermons->have_posts()) {
        while ($sermons->have_posts()) {
            $sermons->the_post();
            $output .= '<li>' . get_the_title() . '</li>';
        }
    } else {
        $output .= '<li>No sermons found.</li>';
    }

    $output .= '</ul>';
    wp_reset_postdata();

    return $output;
}
add_shortcode('sermons', 'display_sermons');

function display_sermon_images() {
    $args = array(
        'taxonomy' => 'series', // Specify the taxonomy name
        'hide_empty' => false,   // Show even if no posts are assigned
    );

    $series = get_terms($args);
    $output = '<div class="sermon-grid">';

    if ($series) {
        foreach ($series as $term) {
            $image = get_field('series_image', 'series_' . $term->term_id); // Get ACF image field value
            $output .= '<div class="sermon-item">';
            if ($image) {
                $output .= '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '">';
            } else {
                $output .= '<img src="' . esc_url(get_template_directory_uri() . '/path/to/default-image.jpg') . '" alt="Default Image">';
            }
            $output .= '<h3>' . esc_html($term->name) . '</h3>';
            $output .= '</div>';
        }
    } else {
        $output .= '<p>No series found.</p>';
    }

    $output .= '</div>';

    return $output;
}
add_shortcode('sermon_images', 'display_sermon_images');

function list_sermons_series_speakers($atts) {
    $args = array(
        'post_type' => array('series', 'speaker'),
        'posts_per_page' => -1
    );

    $posts = new WP_Query($args);
    $output = '<ul>';

    if ($posts->have_posts()) {
        while ($posts->have_posts()) {
            $posts->the_post();
            $output .= '<li>' . get_the_title() . '</li>';
        }
    } else {
        $output .= '<li>No items found.</li>';
    }

    $output .= '</ul>';
    wp_reset_postdata();

    return $output;
}
add_shortcode('list_sermons', 'list_sermons_series_speakers');

function display_latest_sermon() {
    $args = array(
        'post_type' => 'sermon',
        'posts_per_page' => 1
    );

    $latest_sermon = new WP_Query($args);
    $output = '';

    if ($latest_sermon->have_posts()) {
        while ($latest_sermon->have_posts()) {
            $latest_sermon->the_post();
            $output .= '<div class="latest-sermon">';
            $output .= '<h2>' . get_the_title() . '</h2>';
            $output .= '<p>' . get_the_content() . '</p>';
            $output .= '</div>';
        }
    } else {
        $output .= '<p>No latest sermon found.</p>';
    }

    wp_reset_postdata();

    return $output;
}
add_shortcode('latest_sermon', 'display_latest_sermon');

function display_latest_series($atts) {
    $atts = shortcode_atts(
        array(
            'show_title' => true,
            'show_description' => true
        ),
        $atts,
        'latest_series'
    );

    $args = array(
        'post_type' => 'series',
        'posts_per_page' => 1
    );

    $latest_series = new WP_Query($args);
    $output = '';

    if ($latest_series->have_posts()) {
        while ($latest_series->have_posts()) {
            $latest_series->the_post();
            $output .= '<div class="latest-series">';
            if ($atts['show_title']) {
                $output .= '<h2>' . get_the_title() . '</h2>';
            }
            if ($atts['show_description']) {
                $output .= '<p>' . get_the_content() . '</p>';
            }
            $output .= '</div>';
        }
    } else {
        $output .= '<p>No latest series found.</p>';
    }

    wp_reset_postdata();

    return $output;
}
add_shortcode('latest_series', 'display_latest_series');
