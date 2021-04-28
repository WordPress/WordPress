<?php
/**
 * Media and text with image on the right.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Media and text with image on the right', 'Block pattern title' ),
	'categories'  => array( 'header' ),
	'content'     => '<!-- wp:media-text {"align":"full","mediaId":null,"mediaType":"image","verticalAlignment":"center"} -->
	<div class="wp-block-media-text alignfull is-stacked-on-mobile is-vertically-aligned-center"><figure class="wp-block-media-text__media"><img src="https://s.w.org/images/core/5.8/architecture-04.jpg" alt="' . esc_attr__( 'Close-up, abstract view of architecture.' ) . '"/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"textAlign":"center","level":3,"style":{"color":{"text":"#000000"}}} -->
	<h3 class="has-text-align-center has-text-color" style="color:#000000"><strong>' . esc_html__( 'Open Spaces' ) . '</strong></h3>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph {"align":"center","fontSize":"extra-small"} -->
	<p class="has-text-align-center has-extra-small-font-size"><a href="#">' . esc_html__( 'See case study ↗' ) . '</a></p>
	<!-- /wp:paragraph --></div></div>
	<!-- /wp:media-text -->',
	'description' => _x( 'Media and text block with image to the left and text to the right', 'Block pattern description' ),
);
