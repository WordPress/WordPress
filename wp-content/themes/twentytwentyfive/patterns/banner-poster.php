<?php
/**
 * Title: Poster-like section
 * Slug: twentytwentyfive/banner-poster
 * Categories: banner, media
 * Description: A section that can be used as a banner or a landing page to announce an event.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/poster-image-background.webp","alt":"Picture of a historical building in ruins.","dimRatio":30,"overlayColor":"contrast","isUserOverlayColor":true,"minHeight":100,"minHeightUnit":"vh","align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|accent-1"}}},"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50","top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"textColor":"accent-1","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull has-accent-1-color has-text-color has-link-color" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50);min-height:100vh"><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-30 has-background-dim"></span><img class="wp-block-cover__image-background" alt="<?php esc_attr_e( 'Picture of a historical building in ruins.', 'twentytwentyfive' ); ?>" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/poster-image-background.webp" data-object-fit="cover"/>
<div class="wp-block-cover__inner-container">
	<!-- wp:group {"align":"wide","style":{"dimensions":{"minHeight":"100vh"}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"space-between","justifyContent":"stretch"}} -->
	<div class="wp-block-group alignwide" style="min-height:100vh">
		<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50"}}}} -->
		<div class="wp-block-columns alignwide">
			<!-- wp:column {"width":"80%"} -->
			<div class="wp-block-column" style="flex-basis:80%">
				<!-- wp:heading {"textAlign":"left","align":"wide","style":{"typography":{"fontSize":"12vw","lineHeight":"0.9","fontStyle":"normal","fontWeight":"300"}}} -->
				<h2 class="wp-block-heading alignwide has-text-align-left" style="font-size:12vw;font-style:normal;font-weight:300;line-height:0.9">
					<?php
					echo wp_kses_post(
						/* translators: This string contains the word "Stories" in four different languages with the first item in the locale's language. */
						_x( '“Stories, <span lang="es">historias</span>, <span lang="uk">iсторії</span>, <span lang="el">iστορίες</span>”', 'Placeholder heading in four languages.', 'twentytwentyfive' )
					);
					?>
				</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"20%"} -->
			<div class="wp-block-column" style="flex-basis:20%">
				<!-- wp:paragraph {"align":"right"} -->
				<p class="has-text-align-right"><?php echo esc_html_x( 'Aug 08—10 2025', 'Example event date in pattern.', 'twentytwentyfive' ); ?><br><?php esc_html_e( 'Fuego Bar, Mexico City', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:columns {"verticalAlignment":"bottom","isStackedOnMobile":false,"align":"wide"} -->
		<div class="wp-block-columns alignwide are-vertically-aligned-bottom is-not-stacked-on-mobile">
			<!-- wp:column {"verticalAlignment":"bottom","width":"80%"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:80%">
				<!-- wp:heading {"textAlign":"left","align":"wide","style":{"typography":{"lineHeight":"0.9","fontStyle":"normal","fontWeight":"300"}},"fontSize":"xx-large"} -->
				<h2 class="wp-block-heading alignwide has-text-align-left has-xx-large-font-size" style="font-style:normal;font-weight:300;line-height:0.9"><?php esc_html_e( 'Let’s hear them.', 'twentytwentyfive' ); ?></h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"bottom","width":"20%"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:20%">
				<!-- wp:paragraph {"align":"right"} -->
				<p class="has-text-align-right"><?php esc_html_e( '#stories', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
