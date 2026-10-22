<?php
/**
 * Title: Single Posts
 * Slug: ipsum/hidden-single
 * Template Types: single
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

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Title', 'Name of the group holding the post title', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:post-title {"level":1,"fontSize":"2-x-large"} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Date and Categories', 'Name of the group holding the post date and categories', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:post-date {"isLink":true} /-->

<!-- wp:paragraph {"metadata":{"name":"·"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( '·', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-terms {"term":"category"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Content', 'Name of the group holding the post content', 'ipsum' ); ?>"},"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:post-content {"align":"full","layout":{"type":"constrained"}} /--></div>
<!-- /wp:group -->

<!-- wp:post-terms {"term":"post_tag","separator":"  ","prefix":"<?php esc_attr_e( 'Tags: ', 'ipsum' ); ?>","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}}} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Author Card', 'Name of the author card group', 'ipsum' ); ?>"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Avatar and Author', 'Name of the group holding the author avatar and name', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"stretch","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:avatar {"size":64} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Written by Author Name', 'Name of the group holding the byline and author name', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"0","padding":{"right":"var:preset|spacing|30","left":"var:preset|spacing|30"}},"border":{"radius":{"topLeft":"10px","topRight":"10px","bottomLeft":"10px","bottomRight":"10px"}}},"backgroundColor":"theme-5","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group has-theme-5-background-color has-background" style="border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-right:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Written by', 'Name of the byline paragraph', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( 'Written by&nbsp;', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-author-name {"isLink":true} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:pattern {"slug":"ipsum/hidden-comments"} /-->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:pattern {"slug":"ipsum/hidden-keep-reading"} /--></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer"} /-->

<!-- wp:template-part {"slug":"search-bar","className":"sticky-bottom"} /-->
