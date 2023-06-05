<?php

add_action( 'init', 'bagberry_register_block_patterns', 9 );

function bagberry_register_block_patterns() {


	$block_pattern_categories = array(
		'theme-patterns' => array( 'label' => __( 'Theme Patterns', 'bagberry' ) )
	);

	/**
	 * Filters the theme block pattern categories.
	 *
	 * @since Bagberry 1.0
	 *
	 */
	$block_pattern_categories = apply_filters( 'bagberry_block_pattern_categories', $block_pattern_categories );

	foreach ( $block_pattern_categories as $name => $properties ) {
		if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}

	$block_patterns = array(
		'collage-images-with-text',
		'collage-images-with-text-3',
		'split-columns-image-with-text-buttons',
		'offset-gallery-with-text',
		'offset-image-with-text',
		'collage-images-with-text-2',
		'three-columns-product-categories',
		'testimonial-with-images',
		'simple-two-columns-posts',
		'five-columns-products',
		'footer-2',
		'footer-3',
		'header-3',
		'header-2',
		'header-4',
		'three-columns-image-and-text',
		'three-columns-image-and-text-on-background',
		'simple-cover-on-background',
		'split-section-image-and-products',
		'split-columns-image-and-text-on-background-2',
		'top-cover',
		'footer-1',
		'four-columns-products',
		'shop-cover',
		'two-columns-image-and-text',
		'split-columns-image-and-text-on-background',
		'header-1',
		'split-columns-image-and-text',
		'two-columns-image-and-text-2'
	);

	/**
	 * Filters the theme block patterns
	 * 
	 * @since Bagberry 1.0
	 * 
	 */
	$block_patterns = apply_filters( 'bagberry_block_patterns', $block_patterns );

	foreach ( $block_patterns as $block_pattern ) {
		$pattern_file = get_theme_file_path( '/inc/patterns/' . $block_pattern . '.php' );

		register_block_pattern(
			'bagberry/' . $block_pattern,
			require $pattern_file
		);
	}

}
