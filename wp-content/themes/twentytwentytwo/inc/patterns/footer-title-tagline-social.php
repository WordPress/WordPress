<?php
/**
 * Footer with title, tagline, and social links
 */
return array(
	'title'      => __( 'Footer with title, tagline, and social links', 'twentytwentytwo' ),
	'categories' => array( 'twentytwentytwo-footers' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"max(1.25rem, 5vw)","right":"max(1.25rem, 5vw)","bottom":"max(1.25rem, 5vw)","left":"max(1.25rem, 5vw)"}}},"layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull" style="padding-top:max(1.25rem, 5vw);padding-right:max(1.25rem, 5vw);padding-bottom:max(1.25rem, 5vw);padding-left:max(1.25rem, 5vw)"><!-- wp:image {"align":"full","sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image alignfull size-full"><img src="' . esc_url( get_stylesheet_directory_uri() ) . '/assets/images/flight-path-on-gray-b.jpg" alt="' . esc_html__( 'Illustration of a flying bird', 'twentytwentytwo' ) . '"/></figure>
					<!-- /wp:image -->

					<!-- wp:spacer {"height":16} -->
					<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:columns {"align":"full"} -->
					<div class="wp-block-columns alignfull"><!-- wp:column -->
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

					<!-- wp:columns {"align":"full"} -->
					<div class="wp-block-columns alignfull"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link">' . esc_html__( 'Read about me', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:buttons -->
					<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link">' . esc_html__( 'See my process', 'twentytwentytwo' ) . '</a></div>
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

					<!-- wp:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:social-links {"iconColor":"primary","iconColorValue":"var(--wp--custom--color--primary)","className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"center"}} -->
					<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"https://wordpress.org/","service":"wordpress"} /-->

					<!-- wp:social-link {"url":"https://www.facebook.com/","service":"facebook"} /-->

					<!-- wp:social-link {"url":"https://twitter.com/","service":"twitter"} /-->

					<!-- wp:social-link {"url":"https://www.instagram.com/","service":"instagram"} /--></ul>
					<!-- /wp:social-links --></div>
					<!-- /wp:group -->',
);
