<?php
/**
 * Query: Small image and title.
 *
 * @package WordPress
 */

return array(
	'title'      => __( 'Small image and title', 'gutenberg' ),
	'blockTypes' => array( 'core/query' ),
	'categories' => array( 'query' ),
	'content'    => '<!-- wp:query {"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
					<div class="wp-block-query">
					<!-- wp:query-loop -->
					<!-- wp:columns {"verticalAlignment":"center"} -->
					<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"25%"} -->
					<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:25%"><!-- wp:post-featured-image {"isLink":true} /--></div>
					<!-- /wp:column -->
					<!-- wp:column {"verticalAlignment":"center","width":"75%"} -->
					<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:75%"><!-- wp:post-title {"isLink":true} /--></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->
					<!-- /wp:query-loop -->
					</div>
					<!-- /wp:query -->',
);
