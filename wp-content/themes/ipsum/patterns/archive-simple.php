<?php
/**
 * Title: Archive Simple
 * Slug: ipsum/archive-simple
 * Categories: posts
 * Block Types: core/query
 * Viewport width: 1280
 * Description: A lean chronological list — titles, dates, categories, and reading time. Inherits the template’s query; turn that off when using it inside a page.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"taxQuery":null,"parents":[],"excludeCurrent":null},"metadata":{"categories":["posts"],"name":"<?php echo esc_html_x( 'Archive Simple', 'Name of the Archive Simple query', 'ipsum' ); ?>"},"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-query alignwide"><!-- wp:post-template {"className":"hover-together","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"default"}} -->
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
<!-- /wp:query -->
