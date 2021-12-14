<?php
/**
 * Logo, navigation, and offset tagline Header block pattern
 */
return array(
	'title'      => __( 'Logo, navigation, and offset tagline Header', 'twentytwentytwo' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"8rem"}}}} -->
					<div class="wp-block-group alignwide" style="padding-bottom:8rem"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"max(1.25rem, 5vw)"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="wp-block-group alignwide" style="padding-top:max(1.25rem, 5vw)"><!-- wp:site-logo {"width":64} /-->

					<!-- wp:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- wp:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->
					<!-- /wp:navigation --></div>
					<!-- /wp:group -->

					<!-- wp:columns {"isStackedOnMobile":false,"align":"wide"} -->
					<div class="wp-block-columns alignwide is-not-stacked-on-mobile"><!-- wp:column {"width":"64px"} -->
					<div class="wp-block-column" style="flex-basis:64px"></div>
					<!-- /wp:column -->

					<!-- wp:column {"width":"380px"} -->
					<div class="wp-block-column" style="flex-basis:380px"><!-- wp:site-tagline {"fontSize":"small"} /--></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->',
);
