<?php
/**
 * Small header with dark background block pattern
 */
return array(
	'title'      => __( 'Small header with dark background', 'twentytwentytwo' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"padding":{"top":"0px","bottom":"0px"}}},"backgroundColor":"foreground","textColor":"background","layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull has-background-color has-foreground-background-color has-text-color has-background has-link-color" style="padding-top:0px;padding-bottom:0px;"><!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"0px"}}},"layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull" style="padding-top:0px;padding-bottom:0px;"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var(--wp--custom--spacing--small, 1.25rem)","bottom":"var(--wp--custom--spacing--large, 8rem)"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="wp-block-group alignwide" style="padding-top:var(--wp--custom--spacing--small, 1.25rem);padding-bottom:var(--wp--custom--spacing--large, 8rem)"><!-- wp:group {"layout":{"type":"flex"}} -->
					<div class="wp-block-group"><!-- wp:site-logo {"width":64} /-->

					<!-- wp:site-title {"style":{"typography":{"fontStyle":"italic","fontWeight":"400"}}} /--></div>
					<!-- /wp:group -->

					<!-- wp:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- wp:page-list /-->
					<!-- /wp:navigation --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group -->

					<!-- wp:image {"align":"wide","width":2000,"height":474,"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image alignwide size-full is-resized"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-transparent-d.png" alt="' . esc_attr__( 'Illustration of a bird flying.', 'twentytwentytwo' ) . '" width="2000" height="474"/></figure>
					<!-- /wp:image --></div>
					<!-- /wp:group -->
					<!-- wp:spacer {"height":66} -->
					<div style="height:66px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->',
);
