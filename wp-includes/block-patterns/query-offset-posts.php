<?php
/**
 * Query: Offset.
 *
 * @package WordPress
 */

return array(
	'title'      => _x( 'Offset', 'Block pattern title' ),
	'blockTypes' => array( 'core/query' ),
	'categories' => array( 'query' ),
	'content'    => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}}},"layout":{"inherit":false}} -->
					<div class="wp-block-group" style="padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:columns -->
					<div class="wp-block-columns"><!-- wp:column {"width":"50%"} -->
					<div class="wp-block-column" style="flex-basis:50%"><!-- wp:query {"query":{"perPage":2,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"list"}} -->
					<div class="wp-block-query"><!-- wp:post-template -->
					<!-- wp:post-featured-image /-->
					<!-- wp:post-title /-->
					<!-- wp:post-date /-->
					<!-- wp:spacer {"height":200} -->
					<div style="height:200px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column -->
					<!-- wp:column {"width":"50%"} -->
					<div class="wp-block-column" style="flex-basis:50%"><!-- wp:query {"query":{"perPage":2,"pages":0,"offset":2,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"list"}} -->
					<div class="wp-block-query"><!-- wp:post-template -->
					<!-- wp:spacer {"height":200} -->
					<div style="height:200px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->
					<!-- wp:post-featured-image /-->
					<!-- wp:post-title /-->
					<!-- wp:post-date /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->',
);
