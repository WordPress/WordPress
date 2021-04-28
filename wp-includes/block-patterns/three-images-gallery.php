<?php
/**
 * Three columns with offset images.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Three columns with offset images', 'Block pattern title' ),
	'categories'  => array( 'gallery' ),
	'content'     => '<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide"><!-- wp:column {"width":"25%"} -->
	<div class="wp-block-column" style="flex-basis:25%"><!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none","className":"is-style-default"} -->
	<figure class="wp-block-image size-large is-style-default"><img src="https://s.w.org/images/core/5.8/architecture-01.jpg" alt="' . esc_attr__( 'Close-up, abstract view of geometric architecture.' ) . '" /></figure>
	<!-- /wp:image --></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"width":"25%"} -->
	<div class="wp-block-column" style="flex-basis:25%"><!-- wp:spacer {"height":500} -->
	<div style="height:500px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
	
	<!-- wp:spacer {"height":150} -->
	<div style="height:150px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
	
	<!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none"} -->
	<figure class="wp-block-image size-large"><img src="https://s.w.org/images/core/5.8/architecture-02.jpg" alt="' . esc_attr__( 'Close-up, angled view of a window on a white building.' ) . '" /></figure>
	<!-- /wp:image --></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"width":"45%"} -->
	<div class="wp-block-column" style="flex-basis:45%"><!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none","className":"is-style-default"} -->
	<figure class="wp-block-image size-large is-style-default"><img src="https://s.w.org/images/core/5.8/architecture-03.jpg" alt="' . esc_attr__( 'Close-up of the corner of a white, geometric building with both sharp points and round corners.' ) . '" /></figure>
	<!-- /wp:image -->
	
	<!-- wp:spacer {"height":285} -->
	<div style="height:285px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->',
	'description' => _x( 'Three columns with offset images', 'Block pattern description' ),
);
