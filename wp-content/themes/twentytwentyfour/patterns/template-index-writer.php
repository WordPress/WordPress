<?php
/**
 * Title: Writer Index Template
 * Slug: twentytwentyfour/template-index-writer
 * Template Types: index, home
 * Viewport width: 1400
 * Inserter: no
 */
?>

<!-- wp:template-part {"slug":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"blockGap":"0","margin":{"top":"0"}}},"layout":{"type":"default"}} -->
<main class="wp-block-group" style="margin-top:0">
	<!-- wp:group {"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"level":1,"style":{"typography":{"lineHeight":"1"},"spacing":{"padding":{"top":"var:preset|spacing|50"}}}} -->
		<h1 class="wp-block-heading" style="padding-top:var(--wp--preset--spacing--50);line-height:1"><?php esc_html_e( 'Watch, Read, Listen', 'twentytwentyfour' ); ?></h1>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->
	<!-- wp:pattern {"slug":"twentytwentyfour/posts-one-column"} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
