<?php
/**
 * Template part for displaying the post thumbnail
 * @package Twenty8teen
 */

if ( ! is_attachment() ) {
	$default = twenty8teen_default_identimages();
	$classes = get_theme_mod( 'featured_image_classes', $default['featured_image_classes'] );
	$html = get_the_post_thumbnail( null, 'post-thumbnail',
		array( 'class' => twenty8teen_widget_get_classes( $classes ) )
		);

	if ( $html ) {
		$before = is_singular() ? '' : '<a ' . twenty8teen_attributes( 'a',
			array( 'href' => get_permalink() ), false ) . '>';
		$after = $before ? '</a>' : '';
		echo $before;
		echo $html;
		echo $after;
	}
}
