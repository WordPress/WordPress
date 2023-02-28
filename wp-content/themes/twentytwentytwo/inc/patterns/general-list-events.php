<?php
/**
 * List of events block pattern
 */
return array(
	'title'      => __( 'List of events', 'twentytwentytwo' ),
	'categories' => array( 'featured', 'text' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--wp--custom--spacing--large, 8rem)","bottom":"var(--wp--custom--spacing--large, 8rem)"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"primary","textColor":"background"} -->
					<div class="wp-block-group alignfull has-background-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--custom--spacing--large, 8rem);padding-bottom:var(--wp--custom--spacing--large, 8rem)"><!-- wp:group {"align":"full","layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull"><!-- wp:heading {"align":"wide","style":{"typography":{"fontSize":"clamp(3.25rem, 8vw, 6.25rem)","lineHeight":"1.15"},"spacing":{"margin":{"bottom":"2rem"}}}} -->
					<h2 class="alignwide" style="font-size:clamp(3.25rem, 8vw, 6.25rem);line-height:1.15;margin-bottom:2rem"><em>' . esc_html__( 'Speaker Series', 'twentytwentytwo' ) . '</em></h2>
					<!-- /wp:heading -->

					<!-- wp:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:separator {"color":"background","align":"wide","className":"is-style-wide"} -->
					<hr class="wp-block-separator alignwide has-text-color has-background has-background-background-color has-background-color is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
					<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"210px"} -->
					<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:210px"><!-- wp:paragraph -->
					<p>' . esc_html__( 'May 14th, 2022, 6 PM', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size" id="jesus-rodriguez">' . esc_html__( 'Jesús Rodriguez', 'twentytwentytwo' ) . '</h2>
					<!-- /wp:heading --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph -->
					<p>' . wp_kses_post( __( 'The Vintagé Theater<br>245 Arden Rd.<br>Gardenville, NH', 'twentytwentytwo' ) ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:separator {"color":"background","align":"wide","className":"is-style-wide"} -->
					<hr class="wp-block-separator alignwide has-text-color has-background has-background-background-color has-background-color is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
					<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"210px"} -->
					<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:210px"><!-- wp:paragraph -->
					<p>' . esc_html__( 'May 16th, 2022, 6 PM', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size" id="jesus-rodriguez">' . esc_html__( 'Doug Stilton', 'twentytwentytwo' ) . '</h2>
					<!-- /wp:heading --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph -->
					<p>' . wp_kses_post( __( 'The Swell Theater<br>120 River Rd.<br>Rainfall, NH', 'twentytwentytwo' ) ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:separator {"color":"background","align":"wide","className":"is-style-wide"} -->
					<hr class="wp-block-separator alignwide has-text-color has-background has-background-background-color has-background-color is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
					<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"210px"} -->
					<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:210px"><!-- wp:paragraph -->
					<p>' . esc_html__( 'May 18th, 2022, 7 PM', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size" id="jesus-rodriguez">' . esc_html__( 'Amy Jensen', 'twentytwentytwo' ) . '</h2>
					<!-- /wp:heading --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph -->
					<p>' . wp_kses_post( __( 'The Vintagé Theater<br>245 Arden Rd.<br>Gardenville, NH', 'twentytwentytwo' ) ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:separator {"color":"background","align":"wide","className":"is-style-wide"} -->
					<hr class="wp-block-separator alignwide has-text-color has-background has-background-background-color has-background-color is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
					<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"210px"} -->
					<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:210px"><!-- wp:paragraph -->
					<p>' . esc_html__( 'May 20th, 2022, 6 PM', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size" id="jesus-rodriguez">' . esc_html__( 'Emery Driscoll', 'twentytwentytwo' ) . '</h2>
					<!-- /wp:heading --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph -->
					<p>' . wp_kses_post( __( 'The Swell Theater<br>120 River Rd.<br>Rainfall, NH', 'twentytwentytwo' ) ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:separator {"color":"background","align":"wide","className":"is-style-wide"} -->
					<hr class="wp-block-separator alignwide has-text-color has-background has-background-background-color has-background-color is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:group {"align":"wide"} -->
					<div class="wp-block-group alignwide"><!-- wp:social-links {"iconColor":"background","iconColorValue":"var(--wp--preset--color--background)","className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"right"}} -->
					<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"wordpress"} /-->

					<!-- wp:social-link {"url":"#","service":"instagram"} /-->

					<!-- wp:social-link {"url":"#","service":"twitter"} /--></ul>
					<!-- /wp:social-links --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group -->',
);
