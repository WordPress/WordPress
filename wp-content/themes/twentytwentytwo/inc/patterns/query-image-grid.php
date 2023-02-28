<?php
/**
 * Grid of image posts block pattern
 */
return array(
	'title'      => __( 'Grid of image posts', 'twentytwentytwo' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- wp:query {"query":{"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","inherit":false,"perPage":12},"displayLayout":{"type":"flex","columns":3},"layout":{"inherit":true}} -->
					<div class="wp-block-query"><!-- wp:post-template -->
					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"200px"} /-->

					<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":"0.5rem"}}} -->
					<div class="wp-block-columns is-not-stacked-on-mobile"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:post-title {"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"spacing":{"margin":{"top":"0.2em"}}},"fontSize":"small","fontFamily":"system-font"} /--></div>
					<!-- /wp:column -->

					<!-- wp:column {"width":"4em"} -->
					<div class="wp-block-column" style="flex-basis:4em"><!-- wp:post-date {"textAlign":"right","format":"m.d.y","style":{"typography":{"fontStyle":"italic","fontWeight":"400"}},"fontSize":"small"} /--></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->
					<!-- /wp:post-template -->

					<!-- wp:separator {"className":"is-style-wide"} -->
					<hr class="wp-block-separator alignwide is-style-wide"/>
					<!-- /wp:separator -->

					<!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
					<!-- wp:query-pagination-previous {"fontSize":"small"} /-->

					<!-- wp:query-pagination-numbers /-->

					<!-- wp:query-pagination-next {"fontSize":"small"} /-->
					<!-- /wp:query-pagination --></div>
					<!-- /wp:query -->',
);
