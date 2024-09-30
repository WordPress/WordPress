<?php
/**
 * Title: Event RSVP
 * Slug: twentytwentyfive/event-rsvp
 * Keywords: call-to-action, rsvp, event
 * Categories: call-to-action
 * Block Types: core/post-content
 * Viewport width: 1400
 * Description: RSVP for an upcoming event with a cover image and event details.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull">
	<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"grid","minimumColumnWidth":"70rem"}} -->
	<div class="wp-block-group alignfull">
		<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|80","left":"var:preset|spacing|40","right":"var:preset|spacing|40","top":"var:preset|spacing|40"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
		<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--40)">
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
			<div class="wp-block-group">
				<!-- wp:heading {"level":1} -->
				<h1 class="wp-block-heading">Stories, historias, iсторії, iστορίες</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"fontSize":"x-large"} -->
				<p class="has-x-large-font-size">Dec 12, 2024 — 10AM to 6PM</p>
				<!-- /wp:paragraph -->

				<!-- wp:spacer {"height":"0px","style":{"layout":{"selfStretch":"fixed","flexSize":"100px"}}} -->
				<div style="height:0px" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph {"align":"left","style":{"typography":{"writingMode":"vertical-rl","textTransform":"uppercase","lineHeight":"0.6"}}} -->
			<p class="has-text-align-left" style="line-height:0.6;text-transform:uppercase;writing-mode:vertical-rl">Free WorKshop</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"grid","minimumColumnWidth":"40rem","columnCount":null}} -->
	<div class="wp-block-group alignfull">
		<!-- wp:group {"className":"is-style-section-2","style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
		<div class="wp-block-group is-style-section-2" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--40)">
			<!-- wp:paragraph -->
			<p>This immersive event celebrates the universal human experience through the lenses of history and ancestry, featuring a diverse array of photographers whose works capture the essence of different cultures and historical moments.</p>
			<!-- /wp:paragraph -->

			<!-- wp:spacer {"height":"0px","style":{"layout":{"selfStretch":"fixed","flexSize":"100px"}}} -->
			<div style="height:0px" aria-hidden="true" class="wp-block-spacer"></div>
			<!-- /wp:spacer -->

			<!-- wp:heading {"fontSize":"xx-large"} -->
			<h2 class="wp-block-heading has-xx-large-font-size"><a href="#">RSVP</a></h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->

		<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/image-from-rawpixel-id-8812207.webp","dimRatio":0,"overlayColor":"base","isUserOverlayColor":true,"isDark":false,"layout":{"type":"constrained"}} -->
		<div class="wp-block-cover is-light">
			<span aria-hidden="true" class="wp-block-cover__background has-base-background-color has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background" alt="Close up photo of white flowers on a grey background" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/image-from-rawpixel-id-8812207.webp" data-object-fit="cover"/>
			<div class="wp-block-cover__inner-container">
				<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
				<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->
			</div>
		</div>
		<!-- /wp:cover -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
