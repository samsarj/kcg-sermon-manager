<?php
/*
Plugin Name: Sermon Manager
Description: A plugin to manage sermons, speakers, and series for King's Church Guildford.
Version: 1.0
Author: Sam Sarjudeen
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include Custom Post Types
require_once(plugin_dir_path(__FILE__) . 'includes/custom-post-types.php');

// Include Shortcodes
require_once(plugin_dir_path(__FILE__) . 'includes/shortcodes.php');

// Include Custom Fields (ACF setup)
require_once(plugin_dir_path(__FILE__) . 'includes/custom-fields.php');

// Include Custom Columns (admin tables)
require_once(plugin_dir_path(__FILE__) . 'includes/custom-columns.php');

// Include Functions (for upload directories)
require_once(plugin_dir_path(__FILE__) . 'includes/functions.php');