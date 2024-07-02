<?php

// Customize columns for the 'series' taxonomy
function add_series_custom_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['series_image'] = 'Image';
    $new_columns['name'] = 'Name';
    $new_columns['description'] = 'Description';
    $new_columns['series_book'] = 'Associated Book';
    $new_columns['posts'] = 'Count';
    
    return $new_columns;
}
add_filter('manage_edit-series_columns', 'add_series_custom_columns');

// Customize columns for the 'speaker' taxonomy
function add_speaker_custom_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['speaker_image'] = 'Image';
    $new_columns['name'] = 'Name';
    $new_columns['posts'] = 'Count';
    
    return $new_columns;
}
add_filter('manage_edit-speaker_columns', 'add_speaker_custom_columns');

// Display content for custom columns in the 'series' taxonomy
function manage_series_custom_columns($out, $column_name, $term_id) {
    switch ($column_name) {
        case 'series_image':
            $image = get_field('series_image', 'series_' . $term_id);
            if ($image) {
                $out = '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '" style="max-width: 50px; height: auto;">';
            }
            break;

        case 'series_book':
            $book = get_field('series_book', 'series_' . $term_id);
            if ($book) {
                $out = esc_html($book);
            }
            break;

        case 'description':
            $term = get_term($term_id, 'series');
            if ($term && !is_wp_error($term)) {
                $out = esc_html($term->description);
            }
            break;
    }
    return $out;
}
add_filter('manage_series_custom_column', 'manage_series_custom_columns', 10, 3);

// Display content for custom columns in the 'speaker' taxonomy
function manage_speaker_custom_columns($out, $column_name, $term_id) {
    switch ($column_name) {
        case 'speaker_image':
            $image = get_field('speaker_image', 'speaker_' . $term_id);
            if ($image) {
                $out = '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '" style="max-width: 50px; height: auto;">';
            }
            break;
    }
    return $out;
}
add_filter('manage_speaker_custom_column', 'manage_speaker_custom_columns', 10, 3);

// Remove default columns from the 'series' taxonomy
function remove_default_series_columns($columns) {
    unset($columns['slug']);
    return $columns;
}
add_filter('manage_edit-series_columns', 'remove_default_series_columns');

// Remove default columns from the 'speaker' taxonomy
function remove_default_speaker_columns($columns) {
    unset($columns['slug']);
    return $columns;
}
add_filter('manage_edit-speaker_columns', 'remove_default_speaker_columns');

// Optional: Adjust column widths
function custom_admin_styles() {
    echo '<style>
        .column-series_image, .column-speaker_image {
            width: 60px;
        }
        .column-series_book, .column-description {
            width: 150px;
        }
    </style>';
}
add_action('admin_head', 'custom_admin_styles');
