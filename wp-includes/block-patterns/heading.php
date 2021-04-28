<?php
/**
 * Heading.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Heading', 'Block pattern title' ),
	'categories'  => array( 'text' ),
	'blockTypes'  => array( 'core/heading' ),
	'content'     => '<!-- wp:heading {"align":"wide","style":{"typography":{"fontSize":"48px","lineHeight":"1.1"}}} -->
	<h2 class="alignwide" style="font-size:48px;line-height:1.1">' . esc_html__( "We're a studio in Berlin with an international practice in architecture, urban planning and interior design. We believe in sharing knowledge and promoting dialogue to increase the creative potential of collaboration." ) . '</h2>
	<!-- /wp:heading -->',
	'description' => _x( 'Heading text', 'Block pattern description' ),
);
