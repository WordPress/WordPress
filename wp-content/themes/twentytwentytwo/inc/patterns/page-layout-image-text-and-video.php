<?php
/**
 * Page layout with image, text and video.
 */
return array(
	'title'      => __( 'Page layout with image, text and video', 'twentytwentytwo' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--wp--custom--spacing--large, 8rem)","bottom":"var(--wp--custom--spacing--large, 8rem)"}}},"backgroundColor":"primary","textColor":"background"} -->
					<div class="wp-block-group alignfull has-background-color has-primary-background-color has-text-color has-background" style="padding-top:var(--wp--custom--spacing--large, 8rem);padding-bottom:var(--wp--custom--spacing--large, 8rem)"><!-- wp:group {"layout":{"inherit":true}} -->
					<div class="wp-block-group"><!-- wp:heading {"level":1,"align":"wide","style":{"typography":{"fontSize":"clamp(3rem, 6vw, 4.5rem)"}}} -->
					<h1 class="alignwide" style="font-size:clamp(3rem, 6vw, 4.5rem)">' . wp_kses_post( __( '<em>Warble</em>, a film about <br>hobbyist bird watchers.', 'twentytwentytwo' ) ) . '</h1>
					<!-- /wp:heading -->

					<!-- wp:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column {"width":"33.33%"} -->
					<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size">' . esc_html__( 'Screening', 'twentytwentytwo' ) . '</h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>' . wp_kses_post( __( 'May 14th, 2022 @ 7:00PM<br>The Vintagé Theater,<br>245 Arden Rd.<br>Gardenville, NH', 'twentytwentytwo' ) ) . '</p>
					<!-- /wp:paragraph -->

					<!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"secondary","textColor":"primary"} -->
					<div class="wp-block-button"><a class="wp-block-button__link has-primary-color has-secondary-background-color has-text-color has-background">' . esc_html__( 'Buy Tickets', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:column -->

					<!-- wp:column {"width":"66.66%"} -->
					<div class="wp-block-column" style="flex-basis:66.66%"></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->

					<!-- wp:image {"align":"full","width":2400,"height":1178,"style":{"color":{}}} -->
					<figure class="wp-block-image alignfull is-resized"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-transparent-a.png" alt="' . esc_attr__( 'An illustration of a bird in flight', 'twentytwentytwo' ) . '" width="2400" height="1178"/></figure>
					<!-- /wp:image -->

					<!-- wp:group {"align":"full","layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull"><!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column {"width":"33.33%"} -->
					<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:heading {"fontSize":"x-large"} -->
					<h2 class="has-x-large-font-size">' . esc_html__( 'Extended Trailer', 'twentytwentytwo' ) . '</h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>' . esc_html__( 'Oh hello. My name’s Angelo, and you’ve found your way to my blog. I write about a range of topics, but lately I’ve been sharing my hopes for next year.', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column {"width":"66.66%"} -->
					<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:video {"id":181} -->
					<figure class="wp-block-video"><video controls src="' . esc_url( get_template_directory_uri() ) . '/assets/videos/birds.mp4"></video></figure>
					<!-- /wp:video --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group -->',
);
