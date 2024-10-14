<?php
/**
 * Title: Header with large title
 * Slug: twentytwentyfive/header-large-title
 * Categories: header
 * Block Types: core/template-part/header
 * Description: Header with large site title and right-aligned navigation.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"style":{"border":{"bottom":{"color":"var:preset|color|accent-6","width":"1px"}},"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--accent-6);border-bottom-width:1px;padding-top:0;padding-bottom:0">
	<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">
		<!-- wp:site-title {"level":0,"style":{"typography":{"fontSize":"100px","lineHeight":"1.2"}}} /-->
		<!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group" style="padding-right:0;padding-left:0">
			<!-- wp:navigation {"overlayBackgroundColor":"base","overlayTextColor":"contrast","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","justifyContent":"right","orientation":"vertical"}} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
