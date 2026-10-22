<?php
/**
 * Title: Archive Photographic
 * Slug: ipsum/archive-photographic
 * Categories: posts
 * Block Types: core/query
 * Viewport width: 1280
 * Description: The images lead: a grid of square photographs with small captions of title and date.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"taxQuery":null,"parents":[],"excludeCurrent":null},"metadata":{"categories":["posts"],"name":"<?php echo esc_html_x( 'Archive Photographic', 'Name of the Archive Photographic query', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"default"}} -->
<div class="wp-block-query alignfull" style="padding-left:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40)"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Photo Card', 'Name of the photo card group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"1"} /-->

<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'No Results Wrapper', 'Name of the group shown when a query has no results', 'ipsum' ); ?>"},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'No posts have been published in this section yet.', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Pagination Wrapper', 'Name of the group wrapping the pagination', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--60)"><!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:query -->
