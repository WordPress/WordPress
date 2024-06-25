<?php
/**
 * Simple blog posts block pattern
 */
return array(
	'title'      => __( 'Simple blog posts', 'twentytwentytwo' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"perPage":10},"layout":{"inherit":true}} -->
					<div class="wp-block-query"><!-- wp:post-template -->
					<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"1rem","bottom":"1rem"}},"typography":{"fontStyle":"normal","fontWeight":"300"},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"primary","fontSize":"var(--wp--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"} /-->

					<!-- wp:post-featured-image {"isLink":true} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:group {"layout":{"type":"flex"}} -->
					<div class="wp-block-group"><!-- wp:post-date {"format":"F j, Y","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"small"} /-->

					<!-- wp:post-terms {"term":"category","fontSize":"small"} /-->

					<!-- wp:post-terms {"term":"post_tag","fontSize":"small"} /--></div>
					<!-- /wp:group -->

					<!-- wp:spacer {"height":128} -->
					<div style="height:128px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->
					<!-- /wp:post-template -->

					<!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<!-- wp:query-pagination-previous {"fontSize":"small"} /-->

					<!-- wp:query-pagination-numbers /-->

					<!-- wp:query-pagination-next {"fontSize":"small"} /-->
					<!-- /wp:query-pagination --></div>
					<!-- /wp:query -->',
);
