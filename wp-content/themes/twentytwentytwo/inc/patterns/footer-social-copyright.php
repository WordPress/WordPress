<?php
/**
 * Footer with social links and copyright
 */
return array(
	'title'      => __( 'Footer with social links and copyright', 'twentytwentytwo' ),
	'categories' => array( 'footer' ),
	'blockTypes' => array( 'core/template-part/footer' ),
	'content'    => '<!-- wp:group {"align":"full","layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}}} -->
					<div class="wp-block-group alignwide" style="padding-top:4rem;padding-bottom:4rem"><!-- wp:social-links {"iconColor":"foreground","iconColorValue":"var(--wp--preset--color--foreground)","iconBackgroundColor":"background","iconBackgroundColorValue":"var(--wp--preset--color--background)","layout":{"type":"flex","justifyContent":"center"}} -->
					<ul class="wp-block-social-links has-icon-color has-icon-background-color"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

					<!-- wp:social-link {"url":"#","service":"twitter"} /-->

					<!-- wp:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /wp:social-links -->

					<!-- wp:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"16px"}}} -->
					<p class="has-text-align-center" style="font-size:16px">' . esc_html__( '© Site Title', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group -->',
);
