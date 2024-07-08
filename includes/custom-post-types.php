<?php
// Register Custom Post Type for Sermons
function sm_register_sermon_post_type() {
    $labels = array(
        'name' => 'Sermons',
        'singular_name' => 'Sermon',
        'menu_name' => 'Sermons',
        'name_admin_bar' => 'Sermon',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Sermon',
        'edit_item' => 'Edit Sermon',
        'new_item' => 'New Sermon',
        'view_item' => 'View Sermon',
        'search_items' => 'Search Sermons',
        'not_found' => 'No sermons found',
        'not_found_in_trash' => 'No sermons found in trash',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-format-quote',
        'query_var' => true,
        'rewrite' => array('slug' => 'sermon'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 20,
        'supports' => array('title', 'thumbnail'),
    );

    register_post_type('sermon', $args);

    // Flush rewrite rules on plugin activation
    flush_rewrite_rules();
}
add_action('init', 'sm_register_sermon_post_type');

// Register Custom Taxonomy for Sermon Series
function sm_register_sermon_series_taxonomy() {
    $labels = array(
        'name' => 'Series',
        'singular_name' => 'Series',
        'search_items' => 'Search Series',
        'all_items' => 'All Series',
        'parent_item' => 'Parent Series',
        'parent_item_colon' => 'Parent Series:',
        'edit_item' => 'Edit Series',
        'update_item' => 'Update Series',
        'add_new_item' => 'Add New Series',
        'new_item_name' => 'New Series Name',
        'menu_name' => 'Series',
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'series'),
        'supports' => array('thumbnail'),
    );

    register_taxonomy('series', 'sermon', $args);

    // Flush rewrite rules on plugin activation
    flush_rewrite_rules();
}
add_action('init', 'sm_register_sermon_series_taxonomy');


// Register Custom Taxonomy for Speakers
function sm_register_speaker_taxonomy() {
    $labels = array(
        'name' => 'Speakers',
        'singular_name' => 'Speaker',
        'search_items' => 'Search Speakers',
        'popular_items' => 'Popular Speakers',
        'all_items' => 'All Speakers',
        'edit_item' => 'Edit Speaker',
        'update_item' => 'Update Speaker',
        'add_new_item' => 'Add New Speaker',
        'new_item_name' => 'New Speaker Name',
        'separate_items_with_commas' => 'Separate speakers with commas',
        'add_or_remove_items' => 'Add or remove speakers',
        'choose_from_most_used' => 'Choose from the most used speakers',
        'menu_name' => 'Speakers',
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'speakers'),
    );

    register_taxonomy('speaker', 'sermon', $args);

    // Flush rewrite rules on plugin activation
    flush_rewrite_rules();
}
add_action('init', 'sm_register_speaker_taxonomy');






// Add custom fields to Sermon post type
function add_sermon_custom_fields() {
    add_meta_box(
        'sermon_audio_box', // Meta box ID
        'Sermon Audio', // Meta box title
        'render_sermon_audio_box', // Callback function to render the meta box
        'sermon', // Post type
        'normal', // Context
        'high' // Priority
    );
    
    add_meta_box(
        'sermon_passage_box', // Meta box ID
        'Sermon Passage', // Meta box title
        'render_sermon_passage_box', // Callback function to render the meta box
        'sermon', // Post type
        'normal', // Context
        'high' // Priority
    );
}
add_action('add_meta_boxes', 'add_sermon_custom_fields');

// Render the audio file upload field in the sermon editor
function render_sermon_audio_box($post) {
    wp_nonce_field(basename(__FILE__), 'sermon_audio_nonce');
    $audio_file_id = get_post_meta($post->ID, 'sermon_audio', true);
    $audio_file_url = wp_get_attachment_url($audio_file_id);
    ?>
    <div class="custom-media-uploader">
        <input type="hidden" name="sermon_audio" id="sermon_audio" value="<?php echo esc_attr($audio_file_id); ?>">
        <button type="button" class="button button-secondary" id="sermon_audio_button">Upload Audio File</button>
        <span class="custom-media-file-url"><?php echo $audio_file_url ? '<a href="' . esc_url($audio_file_url) . '">' . esc_html(basename($audio_file_url)) . '</a>' : ''; ?></span>
    </div>
    <?php
}

// Callback function to render sermon passage text field
function render_sermon_passage_box($post) {
    $passage = get_post_meta($post->ID, 'sermon_passage', true);
    ?>
    <input type="text" id="sermon_passage" name="sermon_passage" value="<?php echo esc_attr($passage); ?>" style="width: 100%;">
    <p class="description">Enter the passage for this sermon.</p>
    <?php
}

// Save custom fields data
function save_sermon_custom_fields($post_id) {
    if (isset($_POST['sermon_audio'])) {
        update_post_meta($post_id, 'sermon_audio', sanitize_text_field($_POST['sermon_audio']));
    }
    if (isset($_POST['sermon_passage'])) {
        update_post_meta($post_id, 'sermon_passage', sanitize_text_field($_POST['sermon_passage']));
    }
}
add_action('save_post', 'save_sermon_custom_fields');
