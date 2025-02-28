<?php
/**
 * Title: Vertical site header
 * Slug: twentytwentyfive/vertical-header
 * Categories: header
 * Block Types: core/template-part/vertical-header
 * Description: Vertical site header with site title and navigation.
 * Viewport width: 300
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"wide","style":{"position":{"type":"sticky","top":"0px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
	<!-- wp:group {"align":"wide","style":{"dimensions":{"minHeight":"100vh"}},"layout":{"type":"constrained","justifyContent":"center"}} -->
	<div class="wp-block-group alignwide" style="min-height:100vh;">
		<!-- wp:group {"align":"full","layout":{"type":"flex","orientation":"vertical","justifyContent":"center","verticalAlignment":"center"}} -->
		<div class="wp-block-group alignfull">
			<!-- wp:navigation {"overlayBackgroundColor":"base","overlayTextColor":"contrast","overlayMenu":"always","style":{"spacing":{"margin":{"top":"0"},"blockGap":"var:preset|spacing|20"},"layout":{"selfStretch":"fit","flexSize":null}},"layout":{"type":"flex","justifyContent":"right","orientation":"horizontal","flexWrap":"wrap"}} /-->
			<!-- wp:site-title {"level":0,"style":{"typography":{"writingMode":"vertical-rl"}},"fontSize":"large"} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
