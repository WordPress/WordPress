<?php
/**
 * Title: Instagram grid
 * Slug: twentytwentyfive/media-instagram-grid
 * Categories: media, gallery, featured
 * Viewport width: 1440
 * Description: A grid section with photos and a link to an Instagram profile.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"grid","minimumColumnWidth":"18rem"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:cover {"overlayColor":"contrast","isUserOverlayColor":true,"isDark":false,"style":{"dimensions":{"aspectRatio":"1"},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"textColor":"base"} -->
		<div class="wp-block-cover is-light has-base-color has-text-color has-link-color">
			<span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-100 has-background-dim"></span>
			<div class="wp-block-cover__inner-container">
				<!-- wp:group {"style":{"dimensions":{"minHeight":"100%"},"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
				<div class="wp-block-group" style="min-height:100%">
					<!-- wp:heading {"fontSize":"large"} -->
					<h2 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Instagram', 'twentytwentyfive' ); ?></h2>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
					<p class="has-text-align-center has-medium-font-size"><a href="#"><?php echo esc_html_x( '@example', 'Example username for social media account.', 'twentytwentyfive' ); ?></a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
		</div>
		<!-- /wp:cover -->

		<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/flower-meadow-square.webp" alt="<?php esc_attr_e( 'Photo of a field full of flowers, a blue sky and a tree.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/vash-gon-square.webp" alt="<?php esc_attr_e( 'Profile portrait of a native person.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/coral-square.webp" alt="<?php esc_attr_e( 'View of the deep ocean.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/agenda-img-4.webp" alt="<?php esc_attr_e( 'Portrait of an African Woman dressed in traditional costume, wearing decorative jewelry.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/parthenon-square.webp" alt="<?php esc_attr_e( 'The acropolis in Athens.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/dallas-creek-square.webp" alt="<?php esc_attr_e( 'Close up of two flowers on a dark background.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/marshland-birds-square.webp" alt="<?php esc_attr_e( 'Birds on a lake.', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
