<?php
/**
 * Text-based grid of posts block pattern
 */
return array(
	'title'      => __( 'Text-based grid of posts', 'twentytwentytwo' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- wp:query {"query":{"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":12},"displayLayout":{"type":"flex","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template -->

					<!-- wp:separator {"align":"wide","className":"is-style-wide"} -->
					<hr class="wp-block-separator alignwide is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<!-- wp:query-pagination-previous {"fontSize":"small"} /-->

					<!-- wp:query-pagination-numbers /-->

					<!-- wp:query-pagination-next {"fontSize":"small"} /-->
					<!-- /wp:query-pagination --></div>
					<!-- /wp:query -->',
);
