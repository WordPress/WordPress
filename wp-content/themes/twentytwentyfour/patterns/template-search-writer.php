<?php
/**
 * Title: Writer Search Results Template
 * Slug: twentytwentyfour/search-writer
 * Template Types: search
 * Viewport width: 1400
 * Inserter: no
 */
?>

<!-- wp:group {"style":{"position":{"type":"sticky","top":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group">
	<!-- wp:template-part {"slug":"header","theme":"twentytwentyfour"} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"main","style":{"spacing":{"blockGap":"0","margin":{"top":"0"}}},"layout":{"type":"default"}} -->
<main class="wp-block-group" style="margin-top:0">
	<!-- wp:group {"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:query-title {"type":"search","style":{"typography":{"lineHeight":"1"},"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|30"}}}} /-->
		<!-- wp:pattern {"slug":"twentytwentyfour/hidden-search"} /-->
	</div>
	<!-- /wp:group -->
	<!-- wp:pattern {"slug":"twentytwentyfour/posts-one-column"} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer","theme":"twentytwentyfour"} /-->
