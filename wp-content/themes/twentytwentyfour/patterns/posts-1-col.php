<?php
/**
 * Title: List of posts, 1 column
 * Slug: twentytwentyfour/posts-1-col
 * Categories: query
 * Block Types: core/query
 * Description: A list of posts, 1 column.
 */
?>

<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":"0","postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"layout":{"type":"constrained"}} -->
<div class="wp-block-query">
	<!-- wp:query-no-results -->
	<!-- wp:pattern {"slug":"twentytwentyfour/hidden-no-results"} /-->
	<!-- /wp:query-no-results -->

	<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
	<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:0;padding-bottom:var(--wp--preset--spacing--50);padding-left:0">
		<!-- wp:post-template {"align":"full","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"default","columnCount":3}} -->
		<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} /-->
		<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap"}} -->
		<div class="wp-block-group">
			<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"bottom":"0"}}},"fontSize":"x-large"} /-->
			<!-- wp:template-part {"slug":"post-meta"} /-->
			<!-- wp:post-excerpt {"fontSize":"small"} /-->
			<!-- wp:spacer {"height":"var:preset|spacing|30"} -->
			<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer">
			</div>
			<!-- /wp:spacer -->
		</div>
		<!-- /wp:group -->
		<!-- /wp:post-template -->
		<!-- wp:spacer {"height":"var:preset|spacing|50","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
		<div style="margin-top:0;margin-bottom:0;height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
		<!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-next /-->
		<!-- /wp:query-pagination -->

	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:query -->
