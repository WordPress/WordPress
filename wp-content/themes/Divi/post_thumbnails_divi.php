<?php
add_theme_support( 'post-thumbnails' );

global $et_theme_image_sizes;

$et_theme_image_sizes = array(
	'400x250'  => 'et-pb-post-main-image',
	'1080x675' => 'et-pb-post-main-image-fullwidth',
	'400x284'   => 'et-pb-portfolio-image',
	'510x382'   => 'et-pb-portfolio-module-image',
	'1080x9999' => 'et-pb-portfolio-image-single',
	'400x516'   => 'et-pb-gallery-module-image-portrait',
);

$et_theme_image_sizes = apply_filters( 'et_theme_image_sizes', $et_theme_image_sizes );
$crop = apply_filters( 'et_post_thumbnails_crop', true );

if ( is_array( $et_theme_image_sizes ) ){
	foreach ( $et_theme_image_sizes as $image_size_dimensions => $image_size_name ){
		$dimensions = explode( 'x', $image_size_dimensions );

		if ( in_array( $image_size_name, array( 'et-pb-portfolio-image-single' ) ) )
			$crop = false;

		add_image_size( $image_size_name, $dimensions[0], $dimensions[1], $crop );

		$crop = apply_filters( 'et_post_thumbnails_crop', true );
	}
}