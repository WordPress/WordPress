<?php
/**
 * Video trailer block pattern
 */
return array(
	'title'      => __( 'Video trailer', 'twentytwentytwo' ),
	'categories' => array( 'featured', 'columns' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground"}}},"spacing":{"padding":{"top":"6rem","bottom":"4rem"}}},"backgroundColor":"secondary","textColor":"foreground","layout":{"inherit":true}} -->
				<div class="wp-block-group alignfull has-foreground-color has-secondary-background-color has-text-color has-background has-link-color" style="padding-top:6rem;padding-bottom:4rem"><!-- wp:columns {"align":"wide"} -->
				<div class="wp-block-columns alignwide"><!-- wp:column {"width":"33.33%"} -->
				<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:heading {"fontSize":"x-large"} -->
				<h2 class="has-x-large-font-size" id="extended-trailer">' . esc_html__( 'Extended Trailer', 'twentytwentytwo' ) . '</h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p>' . esc_html__( 'A film about hobbyist bird watchers, a catalog of different birds, paired with the noises they make. Each bird is listed by their scientific name so things seem more official.', 'twentytwentytwo' ) . '</p>
				<!-- /wp:paragraph --></div>
				<!-- /wp:column -->

				<!-- wp:column {"width":"66.66%"} -->
				<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:video -->
				<figure class="wp-block-video"><video controls src="' . esc_url( get_template_directory_uri() ) . '/assets/videos/birds.mp4"></video></figure>
				<!-- /wp:video --></div>
				<!-- /wp:column --></div>
				<!-- /wp:columns --></div>
				<!-- /wp:group -->',
);
