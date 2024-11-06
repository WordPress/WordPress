<?php
/**
 * Title: Services, team photos
 * Slug: twentytwentyfive/services-team-photos
 * Categories: banner, call-to-action, featured
 * Description: Display team photos in a services section with grid layout.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading -->
			<h2 class="wp-block-heading"><?php esc_html_e( 'Our small team is a group of driven, detail-oriented people who are passionate about their customers.', 'twentytwentyfive' ); ?></h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"layout":{"type":"grid","columnCount":2,"minimumColumnWidth":null}} -->
			<div class="wp-block-group">
				<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full"} -->
				<figure class="wp-block-image size-full">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/woman-splashing-water.webp" alt="<?php esc_attr_e( 'Woman on beach, splashing water.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/>
				</figure>
				<!-- /wp:image -->

				<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full"} -->
				<figure class="wp-block-image size-full">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/nurse.webp" alt="<?php esc_attr_e( 'Portrait of a nurse', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/>
				</figure>
				<!-- /wp:image -->

				<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full"} -->
				<figure class="wp-block-image size-full">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/typewriter.webp" alt="<?php esc_attr_e( 'Picture of a person typing on a typewriter.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/>
				</figure>
				<!-- /wp:image -->

				<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full"} -->
				<figure class="wp-block-image size-full">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/man-in-hat.webp" alt="<?php esc_attr_e( 'Man in hat, standing in front of a building.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/>
				</figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
