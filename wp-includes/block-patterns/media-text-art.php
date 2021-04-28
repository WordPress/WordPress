<?php
/**
 * Media & text with image on the right.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Media & text with image on the right', 'Block pattern title' ),
	'categories'  => array( 'header' ),
	'content'     => '<!-- wp:media-text {"align":"full","mediaPosition":"right","mediaId":null,"mediaLink":"#","mediaType":"image","mediaWidth":56,"verticalAlignment":"center","className":"is-style-default"} -->
	<div class="wp-block-media-text alignfull has-media-on-the-right is-stacked-on-mobile is-vertically-aligned-center is-style-default" style="grid-template-columns:auto 56%"><figure class="wp-block-media-text__media"><img src="https://s.w.org/images/core/5.8/art-02.jpg" alt="' . esc_attr__( 'A green and brown rural landscape leading into a bright blue ocean and slightly cloudy sky, done in oil paints.' ) . '"/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"style":{"color":{"text":"#000000"}}} -->
	<h2 class="has-text-color" style="color:#000000"><strong>' . esc_html__( 'Shore with Blue Sea' ) . '</strong></h2>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.1","fontSize":"17px"},"color":{"text":"#636363"}}} -->
	<p class="has-text-color" style="color:#636363;font-size:17px;line-height:1.1">' . esc_html__( 'Eleanor Harris&nbsp;(American, 1901-1942)' ) . '</p>
	<!-- /wp:paragraph --></div></div>
	<!-- /wp:media-text -->',
	'description' => _x( 'Media and text block with image to the right and text to the left', 'Block pattern description' ),
);
