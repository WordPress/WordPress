<?php
/**
 * Featured posts block pattern
 */
return array(
	'title'      => __( 'Featured posts', 'twentytwentytwo' ),
	'categories' => array( 'featured', 'query' ),
	'content'    => '<!-- wp:group {"align":"wide","layout":{"inherit":false}} -->
					<div class="wp-block-group alignwide"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase"}}} -->
					<p style="text-transform:uppercase">' . esc_html__( 'Latest posts', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph -->

					<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":3}} -->
					<div class="wp-block-query"><!-- wp:post-template -->
					<!-- wp:post-featured-image {"isLink":true,"width":"","height":"310px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:group -->',
);
