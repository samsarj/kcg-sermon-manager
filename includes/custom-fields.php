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
                    // Add more books as needed
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
}
