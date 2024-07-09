<?php
// Register Sermon Fields
if (function_exists('acf_add_local_field_group')) {

    // Sermon Fields
    acf_add_local_field_group(array(
        'key' => 'group_sermon_fields',
        'title' => 'Sermon Fields',
        'fields' => array(
            array(
                'key' => 'field_audio_file',
                'label' => 'Audio File',
                'name' => 'sermon_audio',
                'type' => 'file',
                'instructions' => 'Upload the audio file for this sermon.',
                'required' => true,
                'return_format' => 'array,'
            ),
            array(
                'key' => 'field_passage',
                'label' => 'Passage',
                'name' => 'sermon_passage',
                'type' => 'text',
            ),
        ),
        'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'sermon',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'active' => true,
	'description' => '',
    ));

    acf_add_local_field_group(array(
        'key' => 'group_series_details',
        'title' => 'Series Details',
        'fields' => array(
            array(
                'key' => 'field_series_image',
                'label' => 'Image',
                'name' => 'series_image',
                'type' => 'image',
                'instructions' => 'Upload an image for this series.',
                'return_format' => 'array',
            ),
            array(
                'key' => 'field_series_book',
                'label' => 'Associated Book',
                'name' => 'series_book',
                'type' => 'select',
                'choices' => array(
                    '' => 'Topical', // Default option
                    'Genesis' => 'Genesis',
                    'Exodus' => 'Exodus',
                    'Leviticus' => 'Leviticus',
                    'Numbers' => 'Numbers',
                    'Deuteronomy' => 'Deuteronomy',
                    'Joshua' => 'Joshua',
                    'Judges' => 'Judges',
                    'Ruth' => 'Ruth',
                    '1 Samuel' => '1 Samuel',
                    '2 Samuel' => '2 Samuel',
                    '1 Kings' => '1 Kings',
                    '2 Kings' => '2 Kings',
                    '1 Chronicles' => '1 Chronicles',
                    '2 Chronicles' => '2 Chronicles',
                    'Ezra' => 'Ezra',
                    'Nehemiah' => 'Nehemiah',
                    'Esther' => 'Esther',
                    'Job' => 'Job',
                    'Psalms' => 'Psalms',
                    'Proverbs' => 'Proverbs',
                    'Ecclesiastes' => 'Ecclesiastes',
                    'Song of Songs' => 'Song of Songs',
                    'Isaiah' => 'Isaiah',
                    'Jeremiah' => 'Jeremiah',
                    'Lamentations' => 'Lamentations',
                    'Ezekiel' => 'Ezekiel',
                    'Daniel' => 'Daniel',
                    'Hosea' => 'Hosea',
                    'Joel' => 'Joel',
                    'Amos' => 'Amos',
                    'Obadiah' => 'Obadiah',
                    'Jonah' => 'Jonah',
                    'Micah' => 'Micah',
                    'Nahum' => 'Nahum',
                    'Habakkuk' => 'Habakkuk',
                    'Zephaniah' => 'Zephaniah',
                    'Haggai' => 'Haggai',
                    'Zechariah' => 'Zechariah',
                    'Malachi' => 'Malachi',
                    'Matthew' => 'Matthew',
                    'Mark' => 'Mark',
                    'Luke' => 'Luke',
                    'John' => 'John',
                    'Acts' => 'Acts',
                    'Romans' => 'Romans',
                    '1 Corinthians' => '1 Corinthians',
                    '2 Corinthians' => '2 Corinthians',
                    'Galatians' => 'Galatians',
                    'Ephesians' => 'Ephesians',
                    'Philippians' => 'Philippians',
                    'Colossians' => 'Colossians',
                    '1 Thessalonians' => '1 Thessalonians',
                    '2 Thessalonians' => '2 Thessalonians',
                    '1 Timothy' => '1 Timothy',
                    '2 Timothy' => '2 Timothy',
                    'Titus' => 'Titus',
                    'Philemon' => 'Philemon',
                    'Hebrews' => 'Hebrews',
                    'James' => 'James',
                    '1 Peter' => '1 Peter',
                    '2 Peter' => '2 Peter',
                    '1 John' => '1 John',
                    '2 John' => '2 John',
                    '3 John' => '3 John',
                    'Jude' => 'Jude',
                    'Revelation' => 'Revelation',
                ),
            ),
            
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'series',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));

    acf_add_local_field_group(array(
        'key' => 'group_speaker_details',
        'title' => 'Speaker Details',
        'fields' => array(
            array(
                'key' => 'field_speaker_image',
                'label' => 'Image',
                'name' => 'speaker_image',
                'type' => 'image',
                'instructions' => 'Upload a picture of the speaker.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'speaker',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));
    
    acf_add_local_field_group(array(
        'key' => 'group_rss_feed_settings',
        'title' => 'RSS Feed Settings',
        'fields' => array(
            array(
                'key' => 'field_rss_feed_name',
                'label' => 'RSS Feed Name',
                'name' => 'rss_feed_name',
                'type' => 'text',
            ),
            array(
                'key' => 'field_rss_feed_description',
                'label' => 'RSS Feed Description',
                'name' => 'rss_feed_description',
                'type' => 'textarea',
            ),
            array(
                'key' => 'field_rss_feed_author',
                'label' => 'RSS Feed Author',
                'name' => 'rss_feed_author',
                'type' => 'text',
            ),
            array(
                'key' => 'field_rss_feed_owner_name',
                'label' => 'Owner Name',
                'name' => 'rss_feed_owner_name',
                'type' => 'text',
            ),
            array(
                'key' => 'field_rss_feed_owner_email',
                'label' => 'Owner Email Address',
                'name' => 'rss_feed_owner_email',
                'type' => 'email',
            ),
            array(
                'key' => 'field_rss_feed_image',
                'label' => 'RSS Feed Image',
                'name' => 'rss_feed_image',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'thumbnail',
                'library' => 'all',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'rss-feed-settings',
                ),
            ),
        ),
    ));
}
