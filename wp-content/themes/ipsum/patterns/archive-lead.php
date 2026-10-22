<?php
/**
 * Title: Archive Lead
 * Slug: ipsum/archive-lead
 * Categories: posts
 * Block Types: core/query
 * Viewport width: 1280
 * Description: A section lead — a small Manrope label over the five latest posts in the compact, imageless listing.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Archive Lead', 'Name of the archive lead group', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Section Title', 'Name of the group holding the section title', 'ipsum' ); ?>"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"align":"wide","style":{"typography":{"lineHeight":"1","letterSpacing":"0.01rem","fontStyle":"normal","fontWeight":"600"},"spacing":{"margin":{"top":"0"}}},"fontSize":"small","fontFamily":"manrope"} -->
<h2 class="wp-block-heading alignwide has-manrope-font-family has-small-font-size" style="margin-top:0;font-style:normal;font-weight:600;letter-spacing:0.01rem;line-height:1"><?php esc_html_e( 'Watch, Read, Listen', 'ipsum' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:query {"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"ignore","inherit":false,"taxQuery":null,"parents":[],"excludeCurrent":null},"metadata":{"name":"<?php echo esc_html_x( 'Archive List', 'Name of the archive list query', 'ipsum' ); ?>"},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"hover-together","style":{"spacing":{"blockGap":"var:preset|spacing|60"}},"layout":{"type":"default"}} -->
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Template Wrapper', 'Name of the group wrapping each post in a list', 'ipsum' ); ?>"},"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-bottom:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Title', 'Name of the group holding the post title', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:post-title {"level":3,"isLink":true,"fontSize":"2-x-large"} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Date and Categories', 'Name of the group holding the post date and categories', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:post-date {"isLink":true} /-->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( '·', 'Name of the · element', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( '·', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-terms {"term":"category"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Template Copy', 'Name of the group holding the post excerpt and read more link', 'ipsum' ); ?>"},"layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:post-excerpt {"showMoreOnNewLine":false} /-->

<!-- wp:read-more {"content":"<?php esc_attr_e( 'Read more ›', 'ipsum' ); ?>"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'No Results Wrapper', 'Name of the group shown when a query has no results', 'ipsum' ); ?>"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'No posts have been published in this section yet.', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:group --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
