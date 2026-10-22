<?php
/**
 * Title: All Archives Sidebar
 * Slug: ipsum/hidden-archive-sidebar
 * Template Types: archive, category, tag
 * Inserter: no
 * Description: Archive listing beside a classic sidebar with categories and recent posts, on the wide width.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:template-part {"slug":"header"} /-->

<!-- wp:group {"tagName":"main","metadata":{"name":"<?php echo esc_html_x( 'Body', 'Name of the main content area group', 'ipsum' ); ?>"},"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"66.66%","layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Section Title', 'Name of the group holding the section title', 'ipsum' ); ?>"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:query-title {"type":"archive"} /--></div>
<!-- /wp:group -->

<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"taxQuery":null,"parents":[],"excludeCurrent":null},"metadata":{"categories":["posts"],"name":"<?php echo esc_html_x( 'Archive Simple', 'Name of the Archive Simple query', 'ipsum' ); ?>"},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"hover-together","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"default"}} -->
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Archive Row', 'Name of the archive row group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:post-title {"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"large"} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Date and Categories', 'Name of the group holding the post date and categories', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"bottom":"var:preset|spacing|50"}},"border":{"bottom":{"color":"var:preset|color|theme-4","style":"dotted","width":"1px"},"top":[],"right":[],"left":[]}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--theme-4);border-bottom-style:dotted;border-bottom-width:1px;padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:post-date {"format":"M j","isLink":true} /-->

<!-- wp:paragraph {"metadata":{"name":"·"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( '·', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-terms {"term":"category"} /-->

<!-- wp:paragraph {"metadata":{"name":"·"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( '·', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-time-to-read /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'No Results Wrapper', 'Name of the group shown when a query has no results', 'ipsum' ); ?>"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'No posts have been published in this section yet.', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Pagination Wrapper', 'Name of the group wrapping the pagination', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--60)"><!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:query --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:template-part {"slug":"sidebar","area":"uncategorized"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer-wide","area":"footer"} /-->

<!-- wp:template-part {"slug":"search-bar","className":"sticky-bottom"} /-->
