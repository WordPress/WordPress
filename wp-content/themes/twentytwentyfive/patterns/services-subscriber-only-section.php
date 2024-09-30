<?php
/**
 * Title: Services, subscriber only section
 * Slug: twentytwentyfive/services-subscriber-only-section
 * Categories: call-to-action, featured, services
 * Description: A subscriber-only section highlighting exclusive services and offerings.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"var:preset|spacing|50","padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|70","left":"var:preset|spacing|70"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:heading {"fontSize":"xx-large"} -->
			<h2 class="wp-block-heading has-xx-large-font-size">Subscribe to get unlimited access</h2>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"is-style-checkmark-list","style":{"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"padding":{"left":"var:preset|spacing|30"}}}} -->
			<ul style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)" class="wp-block-list is-style-checkmark-list">
				<!-- wp:list-item {"fontSize":"medium"} -->
				<li class="has-medium-font-size">Get access to our paid articles and weekly newsletter.</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item {"fontSize":"medium"} -->
				<li class="has-medium-font-size">Join our IRL event.</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item {"fontSize":"medium"} -->
				<li class="has-medium-font-size">Get a free tote bag.</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item {"fontSize":"medium"} -->
				<li class="has-medium-font-size">An elegant addition of home decor collection.</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item {"fontSize":"medium"} -->
				<li class="has-medium-font-size">Join our forums.</li>
				<!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left","flexWrap":"nowrap"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-fill"} -->
				<div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button">Subscribe</a></div>
				<!-- /wp:button -->

				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">View plans</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size">Cancel or pause anytime.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
			<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/services-subscriber-photo.webp" alt="Smartphones capturing a scenic wildflower meadow with trees"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
