<?php
/**
 * Large header with text and a button.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Large header with text and a button.', 'Block pattern title' ),
	'categories'  => array( 'header' ),
	'content'     => '<!-- wp:cover {"url":"https://s.w.org/images/core/5.8/art-01.jpg","id":null,"hasParallax":true,"dimRatio":40,"customOverlayColor":"#000000","minHeight":100,"minHeightUnit":"vh","contentPosition":"center center","align":"full"} -->
	<div class="wp-block-cover alignfull has-background-dim-40 has-background-dim has-parallax" style="background-color:#000000;background-image:url(https://s.w.org/images/core/5.8/art-01.jpg);min-height:100vh"><div class="wp-block-cover__inner-container"><!-- wp:heading {"style":{"typography":{"fontSize":"48px","lineHeight":"1.2"}},"className":"alignwide has-white-color has-text-color"} -->
	<h2 class="alignwide has-white-color has-text-color" style="font-size:48px;line-height:1.2"><strong><em>' . esc_html__( 'Overseas:' ) . '</em></strong><br><strong><em>' . esc_html__( '1500 — 1960' ) . '</em></strong></h2>
	<!-- /wp:heading -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide"><!-- wp:column {"width":"60%"} -->
	<div class="wp-block-column" style="flex-basis:60%"><!-- wp:paragraph {"style":{"color":{"text":"#ffffff"}}} -->
	<p class="has-text-color" style="color:#ffffff">' . wp_kses_post( __( 'An exhibition about the different representations of the ocean throughout time, between the sixteenth and the twentieth century. Taking place in our Open Room in <em>Floor 2</em>.' ) ) . '</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons"><!-- wp:button {"borderRadius":0,"style":{"color":{"text":"#ffffff","background":"#000000"}},"className":"is-style-outline"} -->
	<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-text-color has-background no-border-radius" style="background-color:#000000;color:#ffffff">' . esc_html__( 'Visit' ) . '</a></div>
	<!-- /wp:button --></div>
	<!-- /wp:buttons --></div>
	<!-- /wp:column -->
	
	<!-- wp:column -->
	<div class="wp-block-column"></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns --></div></div>
	<!-- /wp:cover -->',
	'description' => _x( 'Large header with background image and text and button on top', 'Block pattern description' ),
);
