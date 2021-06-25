<?php
/**
 * Query: Grid.
 *
 * @package WordPress
 */

return array(
	'title'      => _x( 'Grid', 'Block pattern title' ),
	'blockTypes' => array( 'core/query' ),
	'categories' => array( 'query' ),
	'content'    => '<!-- wp:query {"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"flex","columns":3}} -->
					<div class="wp-block-query">
					<!-- wp:post-template -->
					<!-- wp:group {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}}},"layout":{"inherit":false}} -->
					<div class="wp-block-group" style="padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:post-title {"isLink":true} /-->
					<!-- wp:post-excerpt {"wordCount":20} /-->
					<!-- wp:post-date /--></div>
					<!-- /wp:group -->
					<!-- /wp:post-template -->
					</div>
					<!-- /wp:query -->',
);
