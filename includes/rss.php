<?php

function render_sermon_rss_settings() {
    ?>
    <div class="wrap">
        <h1>RSS Feed Settings</h1>
        <p>Manage RSS feed details for your Sermons.</p>

        <?php
        if (function_exists('acf_form')) {
            acf_form(array(
                'post_id' => 'options', // Options page ID
                'field_groups' => array('group_rss_feed_settings'), // ACF field group key
                'submit_value' => 'Save Changes',
                'updated_message' => 'Settings saved successfully!',
            ));
        } else {
            echo '<p>ACF plugin is required to display settings form.</p>';
        }
        ?>

    </div>
    <?php
}

// Hook into the admin menu setup
add_action('admin_menu', 'register_sermon_rss_submenu_page');

// Function to register the submenu page under Sermons
function register_sermon_rss_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=sermon', // Parent menu slug (Sermons post type)
        'RSS Feed Settings',         // Page title
        'RSS Feed',                  // Menu title
        'manage_options',            // Capability required to access
        'sermon-rss-settings',       // Menu slug
        'render_sermon_rss_settings' // Callback function to render the page
    );
}


function generate_sermon_feed() {
    add_feed('sermons', 'sermon_feed_callback');
}
add_action('init', 'generate_sermon_feed');

function sermon_feed_callback() {
    $posts = get_posts(array('post_type' => 'sermon', 'posts_per_page' => -1));

    // Get RSS feed details from ACF options page
    $rss_feed_name = get_field('rss_feed_name', 'option');
    $rss_feed_description = get_field('rss_feed_description', 'option');
    $rss_feed_author = get_field('rss_feed_author', 'option');
    $rss_feed_owner_name = get_field('rss_feed_owner_name', 'option');
    $rss_feed_owner_email = get_field('rss_feed_owner_email', 'option');
    $rss_feed_image = get_field('rss_feed_image', 'option');

    header('Content-Type: application/rss+xml; charset=' . get_option('blog_charset'), true);

    echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
    echo '<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">';
    echo '<channel>';
    echo '<title>' . esc_html($rss_feed_name) . '</title>';
    echo '<link>' . esc_url(get_bloginfo('url')) . '</link>';
    echo '<description>' . esc_html($rss_feed_description) . '</description>';
    echo '<language>en-us</language>';
    echo '<itunes:author>' . esc_html($rss_feed_author) . '</itunes:author>';
    if ($rss_feed_image) {
        echo '<itunes:image href="' . esc_url($rss_feed_image) . '" />';
    }
    echo '<itunes:owner>';
    echo '<itunes:name>' . esc_html($rss_feed_owner_name) . '</itunes:name>';
    echo '<itunes:email>' . esc_html($rss_feed_owner_email) . '</itunes:email>';
    echo '</itunes:owner>';

    foreach ($posts as $post) {
        setup_postdata($post);

        $sermon_audio = get_field('sermon_audio', $post->ID);
        $sermon_passage = get_field('sermon_passage', $post->ID);
        $series = wp_get_post_terms($post->ID, 'series');
        $series_name = !empty($series) ? $series[0]->name : '';
        $series_image_data = !empty($series) ? get_field('series_image', 'series_' . $series[0]->term_id) : '';
        $series_image_url = is_array($series_image_data) ? $series_image_data['url'] : '';

        $speakers = wp_get_post_terms($post->ID, 'speaker');
        $speaker_name = !empty($speakers) ? $speakers[0]->name : '';

        // Ensure the audio URL is a string
        $audio_url = '';
        $audio_filesize = 0;

        if (is_numeric($sermon_audio)) {
            $audio_url = wp_get_attachment_url($sermon_audio);
            $audio_file = get_attached_file($sermon_audio);
            if ($audio_file && file_exists($audio_file)) {
                $audio_filesize = filesize($audio_file);
            }
        }

        // Debugging output
        // error_log("Post ID: " . $post->ID);
        // error_log("Audio URL: " . print_r($audio_url, true));
        // error_log("Series Image URL: " . print_r($series_image_url, true));
        // error_log("Series Name: " . print_r($series_name, true));
        // error_log("Sermon Passage: " . print_r($sermon_passage, true));
        // error_log("Speaker Name: " . print_r($speaker_name, true));

        echo '<item>';
        echo '<title>' . esc_html(get_the_title($post->ID)) . '</title>';
        echo '<link>' . esc_url(get_permalink($post->ID)) . '</link>';
        
        if (!empty($sermon_passage)) {
            // echo '<description>' . esc_html(get_the_excerpt($post->ID)) . '</description>';
            // echo '<itunes:summary>' . esc_html($sermon_passage) . '</itunes:summary>';
            echo '<description>' . esc_html($sermon_passage) . '</description>';
        }
        if (!empty($audio_url)) {
            echo '<enclosure url="' . esc_url($audio_url) . '" length="' . esc_attr($audio_filesize) . '" type="audio/mpeg" />';
        }
        echo '<guid>' . esc_url(get_permalink($post->ID)) . '</guid>';
        echo '<pubDate>' . esc_html(get_the_date('r', $post->ID)) . '</pubDate>';
        if (!empty($series_image_url)) {
            echo '<itunes:image href="' . esc_url($series_image_url) . '" />';
        }
        if (!empty($series_name)) {
            echo '<itunes:season>' . esc_html($series_name) . '</itunes:season>';
        }
        if (!empty($speaker_name)) {
            echo '<itunes:author>' . esc_html($speaker_name) . '</itunes:author>';
        }
        echo '</item>';
    }

    echo '</channel>';
    echo '</rss>';

    wp_reset_postdata();
}
