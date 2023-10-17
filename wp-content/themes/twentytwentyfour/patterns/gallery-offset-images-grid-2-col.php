<?php
/**
 * Title: Offset gallery, 2 columns
 * Slug: twentytwentyfour/gallery-offset-images-grid-2-col
 * Categories: gallery, portfolio
 * Keywords: project, images, media, masonry, columns
 * Viewport width: 1400
 */
?>
<!-- wp:group {"metadata":{"name":"Portfolio Images"},"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50","top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"0","left":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"}}}} -->
	<div class="wp-block-columns alignwide" style="margin-top:0;margin-bottom:0">
		<!-- wp:column {"style":{"spacing":{"blockGap":"0"}}} -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"4/3","scale":"cover","className":"is-style-rounded"} -->
			<figure class="wp-block-image is-style-rounded">
				<img alt="" style="aspect-ratio:4/3;object-fit:cover" />
			</figure>
			<!-- /wp:image -->

			<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
			<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
			<!-- /wp:spacer -->

			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","className":"is-style-rounded"} -->
			<figure class="wp-block-image is-style-rounded">
				<img alt="" style="aspect-ratio:3/4;object-fit:cover" />
			</figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"style":{"spacing":{"blockGap":"0"}}} -->
		<div class="wp-block-column">
			<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
			<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
			<!-- /wp:spacer -->

			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","className":"is-style-rounded"} -->
			<figure class="wp-block-image is-style-rounded"><img alt="" style="aspect-ratio:3/4;object-fit:cover" />
			</figure>
			<!-- /wp:image -->

			<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
			<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
			<!-- /wp:spacer -->

			<!-- wp:image {"aspectRatio":"1","scale":"cover","className":"is-style-rounded"} -->
			<figure class="wp-block-image is-style-rounded"><img alt="" style="aspect-ratio:1;object-fit:cover" />
			</figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
