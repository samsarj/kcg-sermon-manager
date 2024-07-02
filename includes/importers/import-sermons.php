<?php
// Load WordPress environment
require_once('wp-load.php');

// URL to your RSS feed
$rss_feed_url = 'https://www.kcg.org.uk/podcast/';

// Function to download file and return path
function download_file($url, $path) {
    $response = wp_remote_get($url, array('timeout' => 300));
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    
    if (empty($body)) {
        return false;
    }
    
    $file = fopen($path, 'w');
    fwrite($file, $body);
    fclose($file);
    
    return $path;
}

// Fetch RSS feed
$rss = simplexml_load_file($rss_feed_url);

if ($rss) {
    // Loop through each item in RSS feed
    $count = 0; // Limit to processing five items
    foreach ($rss->channel->item as $item) {
        if ($count >= 5) break; // Process only five items

        // Extract data from RSS item
        $title = (string) $item->title;
        $author = (string) $item->author;
        $description = (string) $item->description;
        $pubDate = date('Y-m-d H:i:s', strtotime((string) $item->pubDate));
        $passage = (string) $item->children('itunes', true)->subtitle;
        $audio_url = (string) $item->enclosure->attributes()->url; // Audio file URL

        echo "Processing item: $title\n";
        echo "Speaker: $author\n";
        echo "Passage: $passage\n";
        echo "Date: $pubDate\n";
        echo "Audio URL: $audio_url\n";

        // Extract series from description
        preg_match('/Series: ([^.]+)\./', $description, $series_matches);
        $series_name = isset($series_matches[1]) ? trim($series_matches[1]) : '';
        echo "Series: $series_name\n";

        // Find series term ID in 'series' taxonomy
        $series_term = get_term_by('name', $series_name, 'series');
        if ($series_term && !is_wp_error($series_term)) {
            $series_id = $series_term->term_id;
        } else {
            echo "Series term not found: $series_name\n";
            continue;
        }
        echo "Series ID: $series_id\n";

        // Find speaker term ID in 'speaker' taxonomy
        $speaker_term = get_term_by('name', $author, 'speaker');
        if ($speaker_term && !is_wp_error($speaker_term)) {
            $speaker_id = $speaker_term->term_id;
        } else {
            echo "Speaker term not found: $author\n";
            continue;
        }
        echo "Speaker ID: $speaker_id\n";

        // Prepare ACF field name for passage and audio file
        $acf_field_passage = 'sermon_passage';
        $acf_field_audio = 'sermon_audio';

        // Download audio file and get path
        $upload_dir = wp_upload_dir();
        $audio_file_path = $upload_dir['basedir'] . '/sermons/audio/';
        $audio_file_name = date('Y-m-d', strtotime($pubDate)) . '_' . sanitize_file_name($title) . '.mp3';
        $audio_file_path .= $audio_file_name;

        // Download file and check if successful
        $downloaded = download_file($audio_url, $audio_file_path);

        if (!$downloaded) {
            echo "Failed to download audio file.\n";
            continue;
        }

        echo "Audio file downloaded: $audio_file_path\n";

        // Prepare sermon data
        $sermon_data = array(
            'post_title' => $title,
            'post_type' => 'sermon',
            'post_status' => 'publish',
            'post_author' => 1, // Change this to the appropriate author ID
            'post_date' => $pubDate,
        );

        // Insert sermon post
        $sermon_id = wp_insert_post($sermon_data);

        if (!is_wp_error($sermon_id)) {
            // Update sermon meta fields
            update_post_meta($sermon_id, 'sermon_passage', $passage);
            wp_set_object_terms($sermon_id, $series_id, 'series');
            wp_set_object_terms($sermon_id, $speaker_id, 'speaker');
            update_field($acf_field_audio, array('url' => $audio_file_path), $sermon_id);

            echo "Sermon imported successfully. Sermon ID: $sermon_id\n";
        } else {
            echo "Error importing sermon: " . $sermon_id->get_error_message() . "\n";
        }

        // Increment count
        $count++;
    }

    echo "Import process completed.\n";
} else {
    echo "Error fetching RSS feed.\n";
}
?>
