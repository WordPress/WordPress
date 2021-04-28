<?php
/**
 * Quote.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Quote', 'Block pattern title' ),
	'categories'  => array( 'text' ),
	'blockTypes'  => array( 'core/quote' ),
	'content'     => '<!-- wp:group -->
	<div class="wp-block-group"><div class="wp-block-group__inner-container"><!-- wp:separator {"className":"is-style-default"} -->
	<hr class="wp-block-separator is-style-default"/>
	<!-- /wp:separator -->
	
	<!-- wp:image {"align":"center","id":null,"width":150,"height":150,"sizeSlug":"large","linkDestination":"none","className":"is-style-rounded"} -->
	<div class="wp-block-image is-style-rounded"><figure class="aligncenter size-large is-resized"><img src="https://s.w.org/images/core/5.8/portrait.jpg" alt="' . esc_attr__( 'A side profile of a woman in a russet-colored turtleneck and white bag. She looks up with her eyes closed.' ) . '" width="150" height="150"/></figure></div>
	<!-- /wp:image -->
	
	<!-- wp:quote {"align":"center","className":"is-style-large"} -->
	<blockquote class="wp-block-quote has-text-align-center is-style-large"><p>' . esc_html__( "\"Contributing makes me feel like I'm being useful to the planet.\"" ) . '</p><cite>' . wp_kses_post( __( '— Anna Wong, <em>Volunteer</em>' ) ) . '</cite></blockquote>
	<!-- /wp:quote -->
	
	<!-- wp:separator {"className":"is-style-default"} -->
	<hr class="wp-block-separator is-style-default"/>
	<!-- /wp:separator --></div></div>
	<!-- /wp:group -->',
	'description' => _x( 'Testimonial quote with portrait', 'Block pattern description' ),
);
