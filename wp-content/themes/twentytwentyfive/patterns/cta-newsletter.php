<?php
/**
 * Title: Newsletter sign-up
 * Slug: twentytwentyfive/cta-newsletter
 * Keywords: call-to-action, newsletter
 * Categories: call-to-action
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"tagName":"aside","align":"full","className":"is-style-section-3","style":{"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50","top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}},"dimensions":{"minHeight":""}},"layout":{"type":"constrained","contentSize":"800px"}} -->
<aside class="wp-block-group alignfull is-style-section-3" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:group {"style":{"dimensions":{"minHeight":"360px"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
	<div class="wp-block-group" style="min-height:360px;margin-top:0;margin-bottom:0">
		<!-- wp:heading {"textAlign":"center","fontSize":"xx-large"} -->
		<h2 class="wp-block-heading has-text-align-center has-xx-large-font-size"><?php esc_html_e( 'Sign up to get daily stories', 'twentytwentyfive' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center","className":"is-style-text-subtitle"} -->
		<p class="has-text-align-center is-style-text-subtitle"><?php esc_html_e( 'Get access to a curated collection of moments in time featuring photographs from historical relevance.', 'twentytwentyfive' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:spacer {"height":"var:preset|spacing|30"} -->
		<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
		<div class="wp-block-buttons"><!-- wp:button {"textAlign":"center"} -->
			<div class="wp-block-button"><a class="wp-block-button__link has-text-align-center wp-element-button"><?php esc_html_e( 'Subscribe', 'twentytwentyfive' ); ?></a></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</aside>
<!-- /wp:group -->
