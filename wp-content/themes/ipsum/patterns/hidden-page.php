<?php
/**
 * Title: Pages
 * Slug: ipsum/hidden-page
 * Template Types: page
 * Inserter: no
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:template-part {"slug":"header"} /-->

<!-- wp:group {"tagName":"main","metadata":{"name":"<?php echo esc_html_x( 'Body', 'Name of the main content area group', 'ipsum' ); ?>"},"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Featured Image Wrapper', 'Name of the group wrapping the featured image', 'ipsum' ); ?>"},"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:post-featured-image {"sizeSlug":"large","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|30"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Title', 'Name of the group holding the post title', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:post-title {"level":1,"fontSize":"2-x-large"} /--></div>
<!-- /wp:group -->

<!-- wp:post-content {"align":"full","layout":{"type":"constrained"}} /--></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer"} /-->

<!-- wp:template-part {"slug":"search-bar","className":"sticky-bottom"} /-->
