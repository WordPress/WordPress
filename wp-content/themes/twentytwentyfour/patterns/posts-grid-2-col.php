<?php
/**
 * Title: Grid of posts featuring the first post, 2 columns
 * Slug: twentytwentyfour/posts-grid-2-col
 * Categories: query
 * Block Types: core/query
 * Description: A grid of posts featuring the first post, 2 columns.
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"align":"wide","style":{"typography":{"lineHeight":"1"},"spacing":{"margin":{"top":"0"}}},"fontSize":"x-large"} -->
	<h2 class="wp-block-heading alignwide has-x-large-font-size" style="margin-top:0;line-height:1"><?php esc_html_e( 'Watch, Read, Listen', 'twentytwentyfour' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:spacer {"height":"var:preset|spacing|10","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
	<div style="margin-top:0;margin-bottom:0;height:var(--wp--preset--spacing--10)" aria-hidden="true" class="wp-block-spacer">
	</div>
	<!-- /wp:spacer -->

	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|30"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"60%"} -->
		<div class="wp-block-column" style="flex-basis:60%">
			<!-- wp:query {"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false}} -->
			<div class="wp-block-query">
				<!-- wp:post-template {"style":{"spacing":{"blockGap":"0"}}} -->
				<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/4"} /-->

				<!-- wp:spacer {"height":"var:preset|spacing|10"} -->
				<div style="height:var(--wp--preset--spacing--10)" aria-hidden="true" class="wp-block-spacer">
				</div>
				<!-- /wp:spacer -->

				<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap"}} -->
				<div class="wp-block-group">
					<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt {"excerptLength":35} /-->

					<!-- wp:template-part {"slug":"post-meta"} /-->

				</div>
				<!-- /wp:group -->
				<!-- /wp:post-template -->
			</div>
			<!-- /wp:query -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"40%"} -->
		<div class="wp-block-column" style="flex-basis:40%">
			<!-- wp:query {"query":{"perPage":2,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
			<div class="wp-block-query">
				<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
				<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3"} /-->

				<!-- wp:spacer {"height":"5px","style":{"layout":{}}} -->
				<div style="height:5px" aria-hidden="true" class="wp-block-spacer">
				</div>
				<!-- /wp:spacer -->

				<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap"}} -->
				<div class="wp-block-group">
					<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"large"} /-->

					<!-- wp:post-excerpt {"excerptLength":14,"fontSize":"small"} /-->
					<!-- wp:template-part {"slug":"post-meta"} /-->

				</div>
				<!-- /wp:group -->
				<!-- /wp:post-template -->
			</div>
			<!-- /wp:query -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
