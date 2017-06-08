<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's thumbnails image sizes
 *
 * @filter us_config_image-sizes
 */

return array(

	// Size 1: Used for Blog layouts and set by Theme Options
	'tnail-3x2' => array(
		'width' => intval( us_get_option( 'blog_img_width', 600 ) ),
		'height' => intval( us_get_option( 'blog_img_height', 400 ) ),
		'crop' => TRUE,
	),

	// Size 2: 600xAny - portfolio item, blog masonry mode, gallery masonry
	'tnail-masonry' => array(
		'width' => 600,
		'height' => 0,
		'crop' => FALSE,
	),

	// Size 3: 600x600 - gallery large
	'tnail-1x1' => array(
		'width' => 600,
		'height' => 600,
		'crop' => TRUE,
	),

	// Size 4: 350x350 - small image blog, gallery medium, person
	'tnail-1x1-small' => array(
		'width' => 350,
		'height' => 350,
		'crop' => TRUE,
	),

);
