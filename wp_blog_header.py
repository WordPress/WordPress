"""
<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */
"""

if not wp_did_header:
	wp_did_header = True;
	
	# // Load the WordPress library.
	from . import wp_load
	# require_once __DIR__ . '/wp-load.php';

	# // Set up the WordPress query.
	wp();

	# // Load the theme template.
	require_once ABSPATH . WPINC . '/template-loader.php';
