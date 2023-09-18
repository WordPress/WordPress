<?php
/**
 * About page with large image and buttons
 */
return array(
	'title'      => __( 'About page with large image and buttons', 'twentytwentytwo' ),
	'categories' => array( 'pages', 'buttons' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--wp--custom--spacing--small, 1.25rem)","bottom":"var(--wp--custom--spacing--small, 1.25rem)"}}},"layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull" style="padding-top:var(--wp--custom--spacing--small, 1.25rem);padding-bottom:var(--wp--custom--spacing--small, 1.25rem)"><!-- wp:image {"align":"wide","width":2100,"height":1260,"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image alignwide size-full is-resized"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-gray-b.jpg" alt="" width="2100" height="1260"/></figure>
					<!-- /wp:image -->

					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link">' . esc_html__( 'Purchase my work', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link">' . esc_html__( 'Support my studio', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link">' . esc_html__( 'Take a class', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link">' . esc_html__( 'Read about me', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link">' . esc_html__( 'Learn about my process', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link">' . esc_html__( 'Join my mailing list', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:social-links {"iconColor":"primary","iconColorValue":"var(--wp--preset--color--primary)","className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"center"}} -->
					<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"wordpress"} /-->

					<!-- wp:social-link {"url":"#","service":"facebook"} /-->

					<!-- wp:social-link {"url":"#","service":"twitter"} /-->

					<!-- wp:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /wp:social-links --></div>
					<!-- /wp:group -->',
);
