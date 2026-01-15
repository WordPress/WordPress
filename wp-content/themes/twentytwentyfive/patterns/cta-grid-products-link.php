<?php
/**
 * Title: Call to action with grid layout with products and link
 * Slug: twentytwentyfive/cta-grid-products-link
 * Categories: call-to-action, featured
 * Viewport width: 1400
 * Description: A call to action featuring product images.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading {"style":{"typography":{"fontSize":"9.6rem","letterSpacing":"-0.02em"}}} -->
		<h2 class="wp-block-heading" style="font-size:9.6rem;letter-spacing:-0.02em"><?php esc_html_e( 'Our online store.', 'twentytwentyfive' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:group {"layout":{"type":"grid","columnCount":null,"minimumColumnWidth":"10rem"}} -->
		<div class="wp-block-group">
			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/grid-flower-2.webp' ); ?>" alt="<?php esc_attr_e( 'Black and white flower', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
			<!-- /wp:image -->

			<!-- wp:cover {"dimRatio":0,"isDark":false,"style":{"dimensions":{"aspectRatio":"1"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}},"textColor":"contrast","fontSize":"medium"} -->
			<div class="wp-block-cover is-light has-contrast-color has-text-color has-link-color has-medium-font-size"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container">
				<!-- wp:paragraph {"align":"center"} -->
				<p class="has-text-align-center"><?php esc_html_e( 'Delivered every week', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->
			</div></div>
			<!-- /wp:cover -->

			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/malibu-plantlife.webp' ); ?>" alt="<?php esc_attr_e( 'Closeup of plantlife in the Malibu Canyon area', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
			<!-- /wp:image -->

			<!-- wp:cover {"overlayColor":"contrast","isUserOverlayColor":true,"style":{"dimensions":{"aspectRatio":"1"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-cover"><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
				<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
				<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->
			</div></div>
			<!-- /wp:cover -->

			<!-- wp:cover {"dimRatio":0,"isDark":false,"style":{"dimensions":{"aspectRatio":"1"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}},"textColor":"contrast"} -->
			<div class="wp-block-cover is-light has-contrast-color has-text-color has-link-color" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container">
				<!-- wp:group {"style":{"spacing":{"blockGap":"0","padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"fontSize":"medium","layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
				<div class="wp-block-group has-medium-font-size" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
					<!-- wp:paragraph {"align":"center"} -->
					<p class="has-text-align-center">
						<?php
						printf(
							/* translators: %s: Starting price, split into three rows using HTML <br> tags. The price value has a font size set.*/
							esc_html__( 'Starting at%s/month', 'twentytwentyfive' ),
							'<br /><span style="font-size:2.63rem;">' . esc_html__( '30€', 'twentytwentyfive' ) . '</span><br />'
						);
						?>
					</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div></div>
			<!-- /wp:cover -->

			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/akaka-falls-state-park-flora.webp' ); ?>" alt="<?php esc_attr_e( 'Flora of Akaka Falls State Park', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
			<!-- /wp:image -->

			<!-- wp:cover {"dimRatio":0,"isDark":false,"style":{"dimensions":{"aspectRatio":"1"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}},"textColor":"contrast","fontSize":"medium"} -->
			<div class="wp-block-cover is-light has-contrast-color has-text-color has-link-color has-medium-font-size"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container">
				<!-- wp:paragraph {"align":"center"} -->
				<p class="has-text-align-center"><?php esc_html_e( 'Tailored to your needs', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->
			</div></div>
			<!-- /wp:cover -->

			<!-- wp:cover {"dimRatio":0,"isDark":false,"style":{"dimensions":{"aspectRatio":"1"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}},"textColor":"contrast","fontSize":"medium"} -->
			<div class="wp-block-cover is-light has-contrast-color has-text-color has-link-color has-medium-font-size"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container">
				<!-- wp:paragraph {"align":"center"} -->
				<p class="has-text-align-center"><?php esc_html_e( 'Free shipping', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->
			</div></div>
			<!-- /wp:cover -->

			<!-- wp:cover {"overlayColor":"accent-2","isUserOverlayColor":true,"isDark":false,"style":{"dimensions":{"aspectRatio":"1"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-cover is-light"><span aria-hidden="true" class="wp-block-cover__background has-accent-2-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
				<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
				<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->
			</div></div>
			<!-- /wp:cover -->

			<!-- wp:cover {"dimRatio":0,"isDark":false,"style":{"dimensions":{"aspectRatio":"1"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}},"textColor":"contrast","fontSize":"medium"} -->
			<div class="wp-block-cover is-light has-contrast-color has-text-color has-link-color has-medium-font-size"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container">
				<!-- wp:paragraph {"align":"center"} -->
				<p class="has-text-align-center"><?php esc_html_e( 'Cancel anytime', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->
			</div></div>
			<!-- /wp:cover -->

			<!-- wp:cover {"overlayColor":"accent-3","isUserOverlayColor":true,"style":{"dimensions":{"aspectRatio":"1"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-cover"><span aria-hidden="true" class="wp-block-cover__background has-accent-3-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
				<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
				<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->
			</div></div>
			<!-- /wp:cover -->

			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/botany-flowers.webp' ); ?>" alt="<?php esc_attr_e( 'Botany flowers', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
			<!-- /wp:image -->

			<!-- wp:cover {"overlayColor":"accent-1","isUserOverlayColor":true,"isDark":false,"style":{"dimensions":{"aspectRatio":"1"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-cover is-light"><span aria-hidden="true" class="wp-block-cover__background has-accent-1-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
				<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
				<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->
			</div></div>
			<!-- /wp:cover -->

			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/star-thristle-flower.webp' ); ?>" alt="<?php esc_attr_e( 'Black and white flower', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:group -->

		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"width":100} -->
			<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Shop now', 'twentytwentyfive' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
