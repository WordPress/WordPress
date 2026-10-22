<?php
/**
 * Title: Page Blog Home
 * Slug: ipsum/page-blog-home
 * Categories: ipsum_page
 * Block Types: core/post-content
 * Post Types: page
 * Viewport width: 1280
 * Description: A blog home as a page — the opening statement, the latest posts in full, and the subscribe invitation.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Text Statement', 'Name of the Text Statement element', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Text Statement Wrapper', 'Name of the Text Statement Wrapper element', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-text-quote-mark","style":{"typography":{"fontStyle":"normal","fontWeight":"400","textAlign":"left","lineHeight":"1.3"}},"fontSize":"x-large"} -->
<p class="has-text-align-left is-style-text-quote-mark has-x-large-font-size" style="font-style:normal;font-weight:400;line-height:1.3"><?php echo wp_kses_post( __( 'I teach and write about books, slow mornings, and the occasional recipe that actually works—start with the <a href="#">essays</a>.', 'ipsum' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Full Posts', 'Name of the Full Posts element', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[],"excludeCurrent":null},"align":"full","layout":{"type":"default"}} -->
<div class="wp-block-query alignfull"><!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Template Wrapper', 'Name of the group wrapping each post in a list', 'ipsum' ); ?>"},"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|70"},"padding":{"bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-bottom:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Featured Image Wrapper', 'Name of the group wrapping the featured image', 'ipsum' ); ?>"},"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:post-featured-image {"isLink":true,"sizeSlug":"large","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|30"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Title', 'Name of the group holding the post title', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:post-title {"isLink":true,"fontSize":"2-x-large"} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Date and Categories', 'Name of the group holding the post date and categories', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:post-date {"isLink":true} /-->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( '·', 'Name of the · element', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( '·', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-terms {"term":"category"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Content', 'Name of the group holding the post content', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-content {"align":"full","layout":{"type":"constrained"}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Comments Wrapper', 'Name of the group wrapping the comments', 'ipsum' ); ?>"},"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Comments and CTA', 'Name of the group holding the comments and the call to action', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:post-comments-link /-->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( '·', 'Name of the · element', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( '·', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"ipsum/comments-cta"}},"name":"<?php echo esc_html_x( 'Comments CTA', 'Name of the comments call to action paragraph', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( 'Join the conversation', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( '›', 'Name of the › element', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( '›', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
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

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Pagination Wrapper', 'Name of the group wrapping the pagination', 'ipsum' ); ?>"},"align":"wide","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide"><!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Subscribe', 'Name of the Subscribe element', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Subscribe Wrapper', 'Name of the Subscribe Wrapper element', 'ipsum' ); ?>"},"style":{"border":{"radius":"10px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"elements":{"link":{"color":{"text":"var:preset|color|theme-1"}}}},"backgroundColor":"theme-6","textColor":"theme-1","layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-group has-theme-1-color has-theme-6-background-color has-text-color has-background has-link-color" style="border-radius:10px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Paragraph', 'Name of the Title and Paragraph element', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"typography":{"textAlign":"left"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-left has-x-large-font-size"><?php esc_html_e( 'The next post can find you.', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"textAlign":"left"}}} -->
<p class="has-text-align-left"><?php esc_html_e( 'Leave your address, and new essays will arrive as they’re published—no noise.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
<div class="wp-block-buttons"><!-- wp:button {"textColor":"theme-1","className":"is-style-outline","style":{"elements":{"link":{"color":{"text":"var:preset|color|theme-1"}}}},"borderColor":"theme-1"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-theme-1-color has-text-color has-link-color has-border-color has-theme-1-border-color wp-element-button"><?php esc_html_e( 'Subscribe', 'ipsum' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
