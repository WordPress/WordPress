<?php
/**
 * Title: Archive Columns
 * Slug: ipsum/archive-columns
 * Categories: posts
 * Block Types: core/query
 * Viewport width: 1280
 * Description: Featured image beside title and meta in a responsive row — the image takes a fixed width on desktop and the full width on small screens.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"taxQuery":null,"parents":[],"excludeCurrent":null},"metadata":{"categories":["posts"],"name":"<?php echo esc_html_x( 'Archive Columns', 'Name of the Archive Columns query', 'ipsum' ); ?>"},"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-query alignwide"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|60"}},"layout":{"type":"constrained"}} -->
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Media and Content', 'Name of the group holding the media and content', 'ipsum' ); ?>"},"style":{"@mobile":{"layout":{"orientation":"vertical","justifyContent":"stretch"}},"@tablet":{"layout":{"orientation":"vertical","justifyContent":"stretch"}},"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","sizeSlug":"large","style":{"@mobile":{"layout":{"selfStretch":"fit"}},"@tablet":{"layout":{"selfStretch":"fit"}},"layout":{"selfStretch":"fixedNoShrink","flexSize":"300px"}}} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Meta', 'Name of the group holding the post title and meta', 'ipsum' ); ?>"},"style":{"layout":{"selfStretch":"fill"},"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:post-title {"isLink":true,"fontSize":"large"} /-->

<!-- wp:post-date {"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}}} /--></div>
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
