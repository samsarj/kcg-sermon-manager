<?php

function register_rss_feed_settings() {
    register_setting('rss_feed_settings_group', 'rss_feed_name');
    register_setting('rss_feed_settings_group', 'rss_feed_description');
    register_setting('rss_feed_settings_group', 'rss_feed_author');
    register_setting('rss_feed_settings_group', 'rss_feed_owner_name');
    register_setting('rss_feed_settings_group', 'rss_feed_owner_email');
    register_setting('rss_feed_settings_group', 'rss_feed_image');
}
add_action('admin_init', 'register_rss_feed_settings');


function render_rss_feed_settings_page() {
    ?>
    <div class="wrap">
        <h1>RSS Feed Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('rss_feed_settings_group'); ?>
            <?php do_settings_sections('rss_feed_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">RSS Feed Name</th>
                    <td><input type="text" name="rss_feed_name" value="<?php echo esc_attr(get_option('rss_feed_name')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">RSS Feed Description</th>
                    <td><textarea name="rss_feed_description"><?php echo esc_attr(get_option('rss_feed_description')); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">RSS Feed Author</th>
                    <td><input type="text" name="rss_feed_author" value="<?php echo esc_attr(get_option('rss_feed_author')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Owner Name</th>
                    <td><input type="text" name="rss_feed_owner_name" value="<?php echo esc_attr(get_option('rss_feed_owner_name')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Owner Email Address</th>
                    <td><input type="email" name="rss_feed_owner_email" value="<?php echo esc_attr(get_option('rss_feed_owner_email')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">RSS Feed Image</th>
                    <td>
                        <input type="text" id="rss_feed_image" name="rss_feed_image" value="<?php echo esc_attr(get_option('rss_feed_image')); ?>" />
                        <button type="button" class="button" id="upload_image_button">Upload Image</button>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function register_rss_feed_settings_submenu() {
    add_submenu_page(
        'edit.php?post_type=sermon',
        'RSS Feed Settings',
        'RSS Feed',
        'manage_options',
        'rss-feed-settings',
        'render_rss_feed_settings_page'
    );
}
add_action('admin_menu', 'register_rss_feed_settings_submenu');

function rss_feed_settings_scripts() {
    wp_enqueue_media();
    ?>
    <script>
    jQuery(document).ready(function($){
        $('#upload_image_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({ 
                title: 'Upload Image',
                multiple: false
            }).open()
            .on('select', function(e){
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#rss_feed_image').val(image_url);
            });
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'rss_feed_settings_scripts');


// Generate RSS Feed
function generate_sermon_feed() {
    add_feed('sermons', 'sermon_feed_callback');
}
add_action('init', 'generate_sermon_feed');

function sermon_feed_callback() {
    $rss_feed_name = get_option('rss_feed_name');
    $rss_feed_description = get_option('rss_feed_description');
    $rss_feed_author = get_option('rss_feed_author');
    $rss_feed_owner_name = get_option('rss_feed_owner_name');
    $rss_feed_owner_email = get_option('rss_feed_owner_email');
    $rss_feed_image = get_option('rss_feed_image');

    $posts = get_posts(array('post_type' => 'sermon', 'posts_per_page' => -1));

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

        $audio_url = '';
        $audio_filesize = 0;

        if (is_numeric($sermon_audio)) {
            $audio_url = wp_get_attachment_url($sermon_audio);
            $audio_file = get_attached_file($sermon_audio);
            if ($audio_file && file_exists($audio_file)) {
                $audio_filesize = filesize($audio_file);
            }
        }

        echo '<item>';
        echo '<title>' . esc_html(get_the_title($post->ID)) . '</title>';
        echo '<link>' . esc_url(get_permalink($post->ID)) . '</link>';
        
        if (!empty($sermon_passage)) {
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

// Enqueue necessary scripts and styles for ACF
function my_acf_admin_enqueue_scripts() {
    if (function_exists('acf_enqueue_uploader')) {
        acf_enqueue_uploader();
    }
}
add_action('admin_enqueue_scripts', 'my_acf_admin_enqueue_scripts');
