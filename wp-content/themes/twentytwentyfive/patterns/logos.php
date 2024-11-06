<?php
/**
 * Title: Logos
 * Slug: twentytwentyfive/logos
 * Categories: banner
 * Description: Showcasing the podcast's clients with a heading and a series of client logos.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","className":"is-style-section-1","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-section-1" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)">
	<!-- wp:heading {"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'The Stories Podcast is sponsored by', 'twentytwentyfive' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:spacer {"height":"var:preset|spacing|30"} -->
	<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:image {"width":"150px","aspectRatio":"4/3","scale":"contain","linkDestination":"none"} -->
		<figure class="wp-block-image is-resized"><img alt="" style="aspect-ratio:4/3;object-fit:contain;width:150px"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"width":"150px","aspectRatio":"4/3","scale":"contain","linkDestination":"none"} -->
		<figure class="wp-block-image is-resized"><img alt="" style="aspect-ratio:4/3;object-fit:contain;width:150px"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"width":"150px","aspectRatio":"4/3","scale":"contain","linkDestination":"none"} -->
		<figure class="wp-block-image is-resized"><img alt="" style="aspect-ratio:4/3;object-fit:contain;width:150px"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"width":"150px","aspectRatio":"4/3","scale":"contain","linkDestination":"none"} -->
		<figure class="wp-block-image is-resized"><img alt="" style="aspect-ratio:4/3;object-fit:contain;width:150px"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"width":"150px","aspectRatio":"4/3","scale":"contain","linkDestination":"none"} -->
		<figure class="wp-block-image is-resized"><img alt="" style="aspect-ratio:4/3;object-fit:contain;width:150px"/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
