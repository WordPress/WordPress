<?php
/**
 * Pricing table block pattern
 */
return array(
	'title'      => __( 'Pricing table', 'twentytwentytwo' ),
	'categories' => array( 'featured', 'columns', 'buttons' ),
	'content'    => '<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:separator {"className":"is-style-wide"} -->
					<hr class="wp-block-separator is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:heading {"style":{"typography":{"fontSize":"var(--wp--custom--typography--font-size--gigantic, clamp(2.75rem, 6vw, 3.25rem))","lineHeight":"0.5"}}} -->
					<h2 id="1" style="font-size:var(--wp--custom--typography--font-size--gigantic, clamp(2.75rem, 6vw, 3.25rem));line-height:0.5">' . esc_html( _x( '1', 'First item in a numbered list.', 'twentytwentytwo' ) ) . '</h2>
					<!-- /wp:heading -->

					<!-- wp:heading {"level":3,"fontSize":"x-large"} -->
					<h3 class="has-x-large-font-size" id="pigeon"><em>' . esc_html__( 'Pigeon', 'twentytwentytwo' ) . '</em></h3>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>' . esc_html__( 'Help support our growing community by joining at the Pigeon level. Your support will help pay our writers, and you’ll get access to our exclusive newsletter.', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph -->

					<!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"foreground","width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link has-foreground-background-color has-background">' . esc_html__( '$25', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons -->

					<!-- wp:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:separator {"className":"is-style-wide"} -->
					<hr class="wp-block-separator is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:heading {"style":{"typography":{"fontSize":"clamp(2.75rem, 6vw, 3.25rem)","lineHeight":"0.5"}}} -->
					<h2 id="2" style="font-size:clamp(2.75rem, 6vw, 3.25rem);line-height:0.5">' . esc_html( _x( '2', 'Second item in a numbered list.', 'twentytwentytwo' ) ) . '</h2>
					<!-- /wp:heading -->

					<!-- wp:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size" id="sparrow"><meta charset="utf-8"><em>' . esc_html__( 'Sparrow', 'twentytwentytwo' ) . '</em></h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>' . esc_html__( 'Join at the Sparrow level and become a member of our flock! You’ll receive our newsletter, plus a bird pin that you can wear with pride when you’re out in nature.', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph -->

					<!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"foreground","width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link has-foreground-background-color has-background">' . esc_html__( '$75', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons -->

					<!-- wp:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:separator {"className":"is-style-wide"} -->
					<hr class="wp-block-separator is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:heading {"style":{"typography":{"fontSize":"clamp(2.75rem, 6vw, 3.25rem)","lineHeight":"0.5"}}} -->
					<h2 id="3" style="font-size:clamp(2.75rem, 6vw, 3.25rem);line-height:0.5">' . esc_html( _x( '3', 'Third item in a numbered list.', 'twentytwentytwo' ) ) . '</h2>
					<!-- /wp:heading -->

					<!-- wp:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size" id="falcon"><meta charset="utf-8"><em>' . esc_html__( 'Falcon', 'twentytwentytwo' ) . '</em></h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>' . esc_html__( 'Play a leading role for our community by joining at the Falcon level. This level earns you a seat on our board, where you can help plan future birdwatching expeditions.', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph -->

					<!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"foreground","width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link has-foreground-background-color has-background">' . esc_html__( '$150', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons -->

					<!-- wp:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->',
);
