<?php
/**
 * Three columns with images and text.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Three columns with images and text', 'Block pattern title' ),
	'categories'  => array( 'columns' ),
	'content'     => '<!-- wp:group {"align":"full","style":{"color":{"background":"#f8f4e4"}}} -->
	<div class="wp-block-group alignfull has-background" style="background-color:#f8f4e4"><div class="wp-block-group__inner-container"><!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide"><!-- wp:column -->
	<div class="wp-block-column"><!-- wp:spacer -->
	<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:heading {"level":6,"style":{"color":{"text":"#000000"}}} -->
	<h6 class="has-text-color" style="color:#000000">' . esc_html__( 'ECOLOGY' ) . '</h6>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.1","fontSize":"5vw"},"color":{"text":"#000000"}}} -->
	<p class="has-text-color" style="color:#000000;font-size:5vw;line-height:1.1"><strong>' . esc_html__( 'Natural resources.' ) . '</strong></p>
	<!-- /wp:paragraph --><!-- wp:spacer {"height":5} -->
	<div style="height:5px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->
	
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide"><!-- wp:column {"width":"33.38%"} -->
	<div class="wp-block-column" style="flex-basis:33.38%"><!-- wp:paragraph {"style":{"color":{"text":"#000000"}},"fontSize":"extra-small"} -->
	<p class="has-text-color has-extra-small-font-size" style="color:#000000">' . wp_kses_post( __( '<em>Nature</em>, in the common sense, refers to essences unchanged by man; space, the air, the river, the leaf.&nbsp;<em>Art</em>&nbsp;is applied to the mixture of his will with the same things, as in a house, a canal, a statue, a picture. But his operations taken together are so insignificant, a little chipping, baking, patching, and washing, that in an impression so grand as that of the world on the human mind, they do not vary the result.' ) ) . '</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"width":"33%"} -->
	<div class="wp-block-column" style="flex-basis:33%"><!-- wp:spacer -->
	<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
	
	<!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none"} -->
	<figure class="wp-block-image size-large"><img src="https://s.w.org/images/core/5.8/outside-01.jpg" alt="' . esc_attr__( 'The sun setting through a dense forest of trees.' ) . '" /></figure>
	<!-- /wp:image --></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"width":"33.62%"} -->
	<div class="wp-block-column" style="flex-basis:33.62%"><!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none"} -->
	<figure class="wp-block-image size-large"><img src="https://s.w.org/images/core/5.8/outside-02.jpg" alt="' . esc_attr__( 'Wind turbines standing on a grassy plain, against a blue sky.' ) . '" /></figure>
	<!-- /wp:image --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->
	
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide"><!-- wp:column {"width":"67%"} -->
	<div class="wp-block-column" style="flex-basis:67%"><!-- wp:image {"align":"right","id":null,"sizeSlug":"large","linkDestination":"none"} -->
	<div class="wp-block-image"><figure class="alignright size-large"><img src="https://s.w.org/images/core/5.8/outside-03.jpg" alt="' . esc_attr__( 'The sun shining over a ridge leading down into the shore. In the distance, a car drives down a road.' ) . '" /></figure></div>
	<!-- /wp:image --></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"verticalAlignment":"center","width":"33%"} -->
	<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:33%"><!-- wp:paragraph {"style":{"color":{"text":"#000000"}},"fontSize":"extra-small"} -->
	<p class="has-text-color has-extra-small-font-size" style="color:#000000">' . esc_html__( "Undoubtedly we have no questions to ask which are unanswerable. We must trust the perfection of the creation so far, as to believe that whatever curiosity the order of things has awakened in our minds, the order of things can satisfy. Every man's condition is a solution in hieroglyphic to those inquiries he would put." ) . '</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns --></div></div>
	<!-- /wp:group -->',
	'description' => _x( 'Three columns with images and text', 'Block pattern description' ),
);
