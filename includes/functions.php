<?php
// Define upload directory constants
define('SERIES_IMAGES_UPLOAD_DIR', 'sermons/series-images');
define('SERMON_AUDIO_UPLOAD_DIR', 'sermons/audio');

// Include the Filesystem API
require_once ABSPATH . '/wp-admin/includes/file.php';

// Modify the upload directory for ACF image fields
add_filter('acf/upload_prefilter/key=field_series_image', 'acf_modify_series_image_upload_dir', 10, 1);
function acf_modify_series_image_upload_dir($errors) {
    // Get the upload directory
    $upload_dir = wp_upload_dir();
    $custom_dir = $upload_dir['basedir'] . '/' . SERIES_IMAGES_UPLOAD_DIR;

    // Ensure the directory exists
    if (!file_exists($custom_dir)) {
        wp_mkdir_p($custom_dir);
    }

    // Define the new upload path and URL
    add_filter('upload_dir', function($dirs) use ($custom_dir) {
        $dirs['path'] = $custom_dir;
        $dirs['url'] = $dirs['baseurl'] . '/' . SERIES_IMAGES_UPLOAD_DIR;
        $dirs['subdir'] = '/' . SERIES_IMAGES_UPLOAD_DIR;
        return $dirs;
    });

    return $errors;
}

// Modify the upload directory for ACF file fields
add_filter('acf/upload_prefilter/key=field_audio_file', 'acf_modify_sermon_audio_upload_dir', 10, 1);
function acf_modify_sermon_audio_upload_dir($errors) {
    // Get the upload directory
    $upload_dir = wp_upload_dir();
    $custom_dir = $upload_dir['basedir'] . '/' . SERMON_AUDIO_UPLOAD_DIR;

    // Ensure the directory exists
    if (!file_exists($custom_dir)) {
        wp_mkdir_p($custom_dir);
    }

    // Define the new upload path and URL
    add_filter('upload_dir', function($dirs) use ($custom_dir) {
        $dirs['path'] = $custom_dir;
        $dirs['url'] = $dirs['baseurl'] . '/' . SERMON_AUDIO_UPLOAD_DIR;
        $dirs['subdir'] = '/' . SERMON_AUDIO_UPLOAD_DIR;
        return $dirs;
    });

    return $errors;
}

// Restrict media library to series images folder for the series image ACF field
add_filter('ajax_query_attachments_args', 'restrict_series_image_media_library');
function restrict_series_image_media_library($query) {
    // Check if the current ACF field is the series image field
    if (isset($_POST['query']['_acfuploader']) && $_POST['query']['_acfuploader'] === 'field_series_image') {
        // Modify the query to restrict to series images folder
        $query['meta_query'] = array(
            array(
                'key'     => '_wp_attached_file',
                'value'   => SERIES_IMAGES_UPLOAD_DIR . '/',
                'compare' => 'LIKE'
            )
        );
    }
    return $query;
}

// Restrict media library to sermon audio folder for the audio file ACF field
add_filter('ajax_query_attachments_args', 'restrict_sermon_audio_media_library');
function restrict_sermon_audio_media_library($query) {
    // Check if the current ACF field is the audio file field
    if (isset($_POST['query']['_acfuploader']) && $_POST['query']['_acfuploader'] === 'field_audio_file') {
        // Modify the query to restrict to sermon audio folder
        $query['meta_query'] = array(
            array(
                'key'     => '_wp_attached_file',
                'value'   => SERMON_AUDIO_UPLOAD_DIR . '/',
                'compare' => 'LIKE'
            )
        );
    }
    return $query;
}

// Debug function to log errors
function debug_log($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}

// Hook into ACF file upload filter to log details for series image
add_filter('acf/upload_prefilter/key=field_series_image', 'debug_series_image_upload', 10, 1);
function debug_series_image_upload($errors) {
    debug_log('Uploading series image:');
    debug_log($errors);
    return $errors;
}

// Hook into ACF file upload filter to log details for sermon audio
add_filter('acf/upload_prefilter/key=field_audio_file', 'debug_audio_file_upload', 10, 1);
function debug_audio_file_upload($errors) {
    debug_log('Uploading sermon audio file:');
    debug_log($errors);
    return $errors;
}
