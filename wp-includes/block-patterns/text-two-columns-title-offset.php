<?php
/**
 * Two columns of text with offset heading.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Two columns of text with offset heading', 'Block pattern title' ),
	'categories'  => array( 'columns', 'text' ),
	'content'     => '<!-- wp:group {"align":"full","style":{"color":{"background":"#f2f0e9"}}} -->
	<div class="wp-block-group alignfull has-background" style="background-color:#f2f0e9"><!-- wp:spacer {"height":70} -->
	<div style="height:70px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
	
	<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"width":"50%"} -->
	<div class="wp-block-column" style="flex-basis:50%"><!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.1","fontSize":"30px"},"color":{"text":"#000000"}}} -->
	<p class="has-text-color" style="color:#000000;font-size:30px;line-height:1.1"><strong>' . esc_html__( 'Oceanic Inspiration' ) . '</strong></p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"width":"50%"} -->
	<div class="wp-block-column" style="flex-basis:50%"><!-- wp:separator {"customColor":"#000000","className":"is-style-wide"} -->
	<hr class="wp-block-separator has-text-color has-background is-style-wide" style="background-color:#000000;color:#000000"/>
	<!-- /wp:separator --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->
	
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide"><!-- wp:column -->
	<div class="wp-block-column"></div>
	<!-- /wp:column -->
	
	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:paragraph {"style":{"color":{"text":"#000000"}},"fontSize":"extra-small"} -->
	<p class="has-text-color has-extra-small-font-size" style="color:#000000">' . esc_html__( 'Winding veils round their heads, the women walked on deck. They were now moving steadily down the river, passing the dark shapes of ships at anchor, and London was a swarm of lights with a pale yellow canopy drooping above it. There were the lights of the great theatres, the lights of the long streets, lights that indicated huge squares of domestic comfort, lights that hung high in air.' ) . '</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column -->
	
	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:paragraph {"style":{"color":{"text":"#000000"}},"fontSize":"extra-small"} -->
	<p class="has-text-color has-extra-small-font-size" style="color:#000000">' . esc_html__( 'No darkness would ever settle upon those lamps, as no darkness had settled upon them for hundreds of years. It seemed dreadful that the town should blaze for ever in the same spot; dreadful at least to people going away to adventure upon the sea, and beholding it as a circumscribed mound, eternally burnt, eternally scarred. From the deck of the ship the great city appeared a crouched and cowardly figure, a sedentary miser.' ) . '</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->

	<!-- wp:spacer {"height":40} -->
	<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer --></div>
	<!-- /wp:group -->',
	'description' => _x( 'Two columns of text with offset heading', 'Block pattern description' ),
);
