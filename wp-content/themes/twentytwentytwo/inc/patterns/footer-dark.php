<?php
/**
 * Dark footer wtih title and citation
 */
return array(
	'title'      => __( 'Dark footer with title and citation', 'twentytwentytwo' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- wp:group {"align":"full","layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull"><!-- wp:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"padding":{"top":"max(1.25rem, 5vw)","bottom":"max(1.25rem, 5vw)"}}},"backgroundColor":"foreground","textColor":"background","layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull has-background-color has-foreground-background-color has-text-color has-background has-link-color" style="padding-top:max(1.25rem, 5vw);padding-bottom:max(1.25rem, 5vw)"><!-- wp:group {"align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="wp-block-group alignwide"><!-- wp:site-title {"level":0} /-->

					<!-- wp:paragraph {"align":"right"} -->
					<p class="has-text-align-right">' .
					sprintf(
						/* Translators: WordPress link. */
						esc_html__( 'Proudly powered by %s', 'twentytwentytwo' ),
						'<a href="' . esc_url( __( 'https://wordpress.org', 'twentytwentytwo' ) ) . '" rel="nofollow">WordPress</a>'
					) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group -->',
);
