<?php
/**
 * Title: Poster-like section
 * Slug: twentytwentyfive/banner-poster
 * Categories: banner, media, featured
 * Description: A section that can be used as a banner or a landing page to announce an event.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/poster-image-background.webp","alt":"Picture of a historical building in ruins.","dimRatio":30,"overlayColor":"contrast","isUserOverlayColor":true,"minHeight":100,"minHeightUnit":"vh","align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|accent-1"}}},"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50","top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"textColor":"accent-1","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull has-accent-1-color has-text-color has-link-color" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50);min-height:100vh"><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-30 has-background-dim"></span><img class="wp-block-cover__image-background" alt="Picture of a historical building in ruins." src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/poster-image-background.webp" data-object-fit="cover"/>
<div class="wp-block-cover__inner-container">
	<!-- wp:group {"align":"wide","style":{"dimensions":{"minHeight":"100vh"}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"space-between","justifyContent":"stretch"}} -->
	<div class="wp-block-group alignwide" style="min-height:100vh">
		<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50"}}}} -->
		<div class="wp-block-columns alignwide">
			<!-- wp:column {"width":"66.66%"} -->
			<div class="wp-block-column" style="flex-basis:66.66%">
				<!-- wp:heading {"textAlign":"left","align":"wide","style":{"typography":{"fontSize":"12vw","lineHeight":"0.9","fontStyle":"normal","fontWeight":"300"}}} -->
				<h2 class="wp-block-heading alignwide has-text-align-left" style="font-size:12vw;font-style:normal;font-weight:300;line-height:0.9">Stories, historias, iсторії, iστορίες.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"33.33%"} -->
			<div class="wp-block-column" style="flex-basis:33.33%">
				<!-- wp:paragraph {"align":"right"} -->
				<p class="has-text-align-right">Aug 08—10 2025<br>Fuego Bar, Mexico City</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:columns {"verticalAlignment":"bottom","isStackedOnMobile":false,"align":"wide"} -->
		<div class="wp-block-columns alignwide are-vertically-aligned-bottom is-not-stacked-on-mobile">
			<!-- wp:column {"verticalAlignment":"bottom","width":"66.66%"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:66.66%">
				<!-- wp:heading {"textAlign":"left","align":"wide","style":{"typography":{"lineHeight":"0.9","fontStyle":"normal","fontWeight":"300"}},"fontSize":"xx-large"} -->
				<h2 class="wp-block-heading alignwide has-text-align-left has-xx-large-font-size" style="font-style:normal;font-weight:300;line-height:0.9">Let’s hear them.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"bottom","width":"33.33%"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:33.33%">
				<!-- wp:paragraph {"align":"right"} -->
				<p class="has-text-align-right">#stories</p>
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
