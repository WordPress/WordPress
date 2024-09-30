<?php
/**
 * Title: Coming soon
 * Slug: twentytwentyfive/page-coming-soon
 * Categories: twentytwentyfive_page, featured
 * Keywords: starter
 * Block Types: core/post-content
 * Post Types: page, wp_template
 * Viewport width: 1400
 * Description: A full-width cover banner that can be applied to a page or it can work as a single landing page.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/coming-soon-bg-image.webp","alt":"Photo of a field full of flowers, a blue sky and a tree.","dimRatio":10,"minHeight":100,"minHeightUnit":"vh","align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|accent-1"}},"heading":{"color":{"text":"var:preset|color|accent-1"}}},"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50","top":"200px","bottom":"200px"},"margin":{"top":"0","bottom":"0"},"blockGap":"var:preset|spacing|40"}},"textColor":"accent-1","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull has-accent-1-color has-text-color has-link-color" style="margin-top:0;margin-bottom:0;padding-top:200px;padding-right:var(--wp--preset--spacing--50);padding-bottom:200px;padding-left:var(--wp--preset--spacing--50);min-height:100vh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-10 has-background-dim"></span><img class="wp-block-cover__image-background" alt="Photo of a field full of flowers, a blue sky and a tree." src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/coming-soon-bg-image.webp" data-object-fit="cover"/>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"textAlign":"center","level":1,"className":"is-style-text-annotation"} -->
			<h1 class="wp-block-heading has-text-align-center is-style-text-annotation">Event</h1>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->

		<!-- wp:heading {"textAlign":"center","fontSize":"xx-large"} -->
		<h2 class="wp-block-heading has-text-align-center has-xx-large-font-size">Something great is coming soon</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">Subscribe to get notified when our website is ready.</p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
		<div class="wp-block-buttons">
			<!-- wp:button {"backgroundColor":"accent-1","textColor":"contrast","className":"is-style-fill","style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}},"borderColor":"accent-1"} -->
			<div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-contrast-color has-accent-1-background-color has-text-color has-background has-link-color has-border-color has-accent-1-border-color wp-element-button">Subscribe</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
</div>
<!-- /wp:cover -->
