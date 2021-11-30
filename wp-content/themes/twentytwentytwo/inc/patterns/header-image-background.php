<?php
/**
 * Header with image background block pattern
 */
return array(
	'title'      => __( 'Header with image background', 'twentytwentytwo' ),
	'categories' => array( 'twentytwentytwo-headers' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- wp:cover {"url":"' . esc_url( get_stylesheet_directory_uri() ) . '/assets/images/flight-path-on-gray-c.jpg","dimRatio":0,"focalPoint":{"x":"0.58","y":"0.58"},"minHeight":400,"contentPosition":"center center","align":"full","style":{"spacing":{"padding":{"top":"max(1.25rem, 5vw)","right":"max(1.25rem, 5vw)","bottom":"8rem","left":"max(1.25rem, 5vw)"}},"color":{}}} -->
					<div class="wp-block-cover alignfull" style="padding-top:max(1.25rem, 5vw);padding-right:max(1.25rem, 5vw);padding-bottom:8rem;padding-left:max(1.25rem, 5vw);min-height:400px"><span aria-hidden="true" class="has-background-dim-0 wp-block-cover__gradient-background has-background-dim"></span><img class="wp-block-cover__image-background" alt="' . esc_attr__( 'Illustration of a flying bird', 'twentytwentytwo' ) . '" src="' . esc_url( get_stylesheet_directory_uri() ) . '/assets/images/flight-path-on-gray-c.jpg" style="object-position:58% 58%" data-object-fit="cover" data-object-position="58% 58%"/><div class="wp-block-cover__inner-container"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"0rem","top":"0px","right":"0px","left":"0px"}},"elements":{"link":{"color":{"text":"var:preset|color|foreground"}}}},"textColor":"foreground","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="wp-block-group alignwide has-foreground-color has-text-color has-link-color" style="padding-top:0px;padding-right:0px;padding-bottom:0rem;padding-left:0px"><!-- wp:site-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} /-->

					<!-- wp:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- wp:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->
					<!-- /wp:navigation --></div>
					<!-- /wp:group -->

					<!-- wp:spacer {"height":500} -->
					<div style="height:500px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer --></div></div>
					<!-- /wp:cover -->',
);
