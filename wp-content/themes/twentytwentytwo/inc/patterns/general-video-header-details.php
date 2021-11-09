<?php
/**
 * Video with header and details block pattern
 */
return array(
	'title'      => __( 'Video with header and details', 'twentytwentytwo' ),
	'categories' => array( 'twentytwentytwo-general' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"8rem","bottom":"8rem","left":"0px","right":"0px"}},"elements":{"link":{"color":{"text":"var:preset|color|secondary"}}}},"backgroundColor":"foreground","textColor":"secondary"} -->
					<div class="wp-block-group alignfull has-secondary-color has-foreground-background-color has-text-color has-background has-link-color" style="padding-top:8rem;padding-right:0px;padding-bottom:8rem;padding-left:0px"><!-- wp:group {"align":"full","style":{"spacing":{"padding":{"left":"max(1.25rem, 5vw)","right":"max(1.25rem, 5vw)"}}},"layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull" style="padding-right:max(1.25rem, 5vw);padding-left:max(1.25rem, 5vw)"><!-- wp:heading {"level":1,"align":"wide","style":{"typography":{"fontSize":"clamp(3rem, 6vw, 4.5rem)"}}} -->
					<h1 class="alignwide" id="warble-a-film-about-hobbyist-bird-watchers-1" style="font-size:clamp(3rem, 6vw, 4.5rem)">' . wp_kses_post( __( '<em>Warble</em>, a film about <br>hobbyist bird watchers.', 'twentytwentytwo' ) ) . '</h1>
					<!-- /wp:heading -->

					<!-- wp:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:video {"align":"wide"} -->
					<figure class="wp-block-video alignwide"><video controls src="' . esc_url( get_template_directory_uri() ) . '/assets/videos/birds.mp4"></video></figure>
					<!-- /wp:video -->

					<!-- wp:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column {"width":"50%"} -->
					<div class="wp-block-column" style="flex-basis:50%"><!-- wp:paragraph -->
					<p><strong>' . esc_html__( 'Featuring', 'twentytwentytwo' ) . '</strong></p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:paragraph -->
					<p>' . wp_kses_post( __( 'Jesús Rodriguez<br>Doug Stilton<br>Emery Driscoll<br>Megan Perry<br>Rowan Price', 'twentytwentytwo' ) ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:paragraph -->
					<p>' . wp_kses_post( __( 'Angelo Tso<br>Edward Stilton<br>Amy Jensen<br>Boston Bell<br>Shay Ford', 'twentytwentytwo' ) ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group -->',
);
