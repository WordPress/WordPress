<?php
/**
 * Three columns of text.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Three columns of text', 'Block pattern title' ),
	'categories'  => array( 'columns', 'text' ),
	'content'     => '<!-- wp:columns {"align":"full","style":{"color":{"text":"#000000","background":"#ffffff"}}} -->
	<div class="wp-block-columns alignfull has-text-color has-background" style="background-color:#ffffff;color:#000000"><!-- wp:column -->
	<div class="wp-block-column"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"24px","lineHeight":"1.3"}}} -->
	<h3 style="font-size:24px;line-height:1.3"><strong><a href="http://wordpress.org">' . esc_html__( 'Virtual Tour ↗' ) . '</a></strong></h3>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph -->
	<p>' . esc_html__( 'Get a virtual tour of the museum. Ideal for schools and events.' ) . '</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column -->
	
	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"24px","lineHeight":"1.3"}}} -->
	<h3 style="font-size:24px;line-height:1.3"><strong><a href="https://wordpress.org">' . esc_html__( 'Current Shows ↗' ) . '</a></strong></h3>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph -->
	<p>' . esc_html__( 'Stay updated and see our current exhibitions here.' ) . '</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column -->
	
	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"24px","lineHeight":"1.3"}}} -->
	<h3 style="font-size:24px;line-height:1.3"><strong><a href="https://wordpress.org">' . esc_html__( 'Useful Info ↗' ) . '</a></strong></h3>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph -->
	<p>' . esc_html__( 'Get to know our opening times, ticket prices and discounts.' ) . '</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->',
	'description' => _x( 'Three columns of text', 'Block pattern description' ),
);
