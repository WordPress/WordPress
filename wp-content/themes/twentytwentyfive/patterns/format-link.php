<?php
/**
 * Title: Link format
 * Slug: twentytwentyfive/format-link
 * Categories: twentytwentyfive_post-format, featured
 * Description: A link post format with a description and an emphasized link for key content.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"Link format"},"className":"is-style-section-3","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-section-3" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
	<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} -->
	<p style="font-style:normal;font-weight:700">The Stories Book, a fine collection of moments in time featuring photographs from Louis Fleckenstein, Paul Strand and Asahachi Kōno, is available for pre-order</p>
	<!-- /wp:paragraph -->

	<!-- wp:group {"style":{"elements":{"link":{"color":{"text":"var:preset|color|accent-4"}}}},"textColor":"accent-4","fontSize":"medium","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group has-accent-4-color has-text-color has-link-color has-medium-font-size">
		<!-- wp:paragraph -->
		<p><a href="#">https://example.com</a></p>
		<!-- /wp:paragraph -->
		</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
