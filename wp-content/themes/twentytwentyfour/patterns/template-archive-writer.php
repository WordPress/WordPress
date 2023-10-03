<?php
/**
 * Title: Writer Archive Template
 * Slug: twentytwentyfour/template-archive-writer
 * Template Types: archive, category, tag, author, date
 * Viewport width: 1400
 * Inserter: no
 */
?>

<!-- wp:template-part {"slug":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"blockGap":"0","margin":{"top":"0"}}},"layout":{"type":"default"}} -->
<main class="wp-block-group" style="margin-top:0">
	<!-- wp:group {"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:query-title {"type":"archive","style":{"typography":{"lineHeight":"1"},"spacing":{"padding":{"top":"var:preset|spacing|50"}}}} /-->
	</div>
	<!-- /wp:group -->
	<!-- wp:pattern {"slug":"twentytwentyfour/posts-one-column"} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
