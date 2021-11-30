<?php
/**
 * Header with centered logo and black background
 */
return array(
	'title'      => __( 'Header with centered logo and black background', 'twentytwentytwo' ),
	'categories' => array( 'twentytwentytwo-headers' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"bottom":"max(1.25rem, 5vw)","top":"max(1.25rem, 5vw)"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"foreground","textColor":"background","layout":{"type":"flex","justifyContent":"center"}} -->
					<div class="wp-block-group alignfull has-background-color has-foreground-background-color has-text-color has-background has-link-color" style="padding-top:max(1.25rem, 5vw);padding-bottom:max(1.25rem, 5vw)"><!-- wp:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- wp:navigation-link {"isTopLevelLink":true} /-->

					<!-- wp:navigation-link {"isTopLevelLink":true} /-->

					<!-- wp:site-logo {"width":90} /-->

					<!-- wp:navigation-link {"isTopLevelLink":true} /-->

					<!-- wp:navigation-link {"isTopLevelLink":true} /-->
					<!-- /wp:navigation --></div>
					<!-- /wp:group -->',
);
