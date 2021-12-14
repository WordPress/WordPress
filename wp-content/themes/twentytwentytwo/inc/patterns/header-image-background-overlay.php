<?php
/**
 * Header with image background and overlay block pattern
 */
return array(
	'title'      => __( 'Header with image background and overlay', 'twentytwentytwo' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- wp:cover {"url":"' . esc_url( get_template_directory_uri() ) . '/assets/images/ducks.jpg","dimRatio":90,"overlayColor":"primary","focalPoint":{"x":"0.26","y":"0.34"},"minHeight":100,"minHeightUnit":"px","contentPosition":"center center","align":"full","style":{"spacing":{"padding":{"top":"max(1.25rem, 5vw)","right":"max(1.25rem, 5vw)","bottom":"max(1.25rem, 5vw)","left":"max(1.25rem, 5vw)"}},"color":{"duotone":["#000000","#ffffff"]}}} -->
					<div class="wp-block-cover alignfull" style="padding-top:max(1.25rem, 5vw);padding-right:max(1.25rem, 5vw);padding-bottom:max(1.25rem, 5vw);padding-left:max(1.25rem, 5vw);min-height:100px"><span aria-hidden="true" class="has-primary-background-color has-background-dim-90 wp-block-cover__gradient-background has-background-dim"></span><img class="wp-block-cover__image-background" alt="' . esc_attr__( 'Painting of ducks in the water.', 'twentytwentytwo' ) . '" src="' . esc_url( get_template_directory_uri() ) . '/assets/images/ducks.jpg" style="object-position:26% 34%" data-object-fit="cover" data-object-position="26% 34%"/><div class="wp-block-cover__inner-container"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"0rem","top":"0px","right":"0px","left":"0px"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"textColor":"background","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="wp-block-group alignwide has-background-color has-text-color has-link-color" style="padding-top:0px;padding-right:0px;padding-bottom:0rem;padding-left:0px"><!-- wp:site-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} /-->

					<!-- wp:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- wp:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->
					<!-- /wp:navigation --></div>
					<!-- /wp:group --></div></div>
					<!-- /wp:cover -->',
);
