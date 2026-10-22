<?php
/**
 * Title: Comments
 * Slug: ipsum/hidden-comments
 * Inserter: no
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:comments {"className":"wp-block-comments-query-loop"} -->
<div class="wp-block-comments wp-block-comments-query-loop"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Comments Titles', 'Name of the group holding the comments heading and count', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Comments', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:comments-title {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}}} /--></div>
<!-- /wp:group -->

<!-- wp:comment-template -->
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Comment Template Wrapper', 'Name of the group wrapping each comment', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"0","margin":{"bottom":"var:preset|spacing|60"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
<div class="wp-block-group" style="margin-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Comment Template Details', 'Name of the group holding the comment details', 'ipsum' ); ?>"},"style":{"layout":{"selfStretch":"fill","flexSize":null},"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"0","right":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30);padding-right:0;padding-bottom:var(--wp--preset--spacing--30);padding-left:0"><!-- wp:avatar {"size":48,"isLink":true,"linkTarget":"_blank"} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Comment Date and Author', 'Name of the group holding the comment date and author', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:comment-author-name /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Comment Date and Edit', 'Name of the group holding the comment date and edit link', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20","left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:comment-date /-->

<!-- wp:comment-edit-link {"className":"dot-before"} /-->

<!-- wp:comment-reply-link {"className":"dot-before"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:comment-content /--></div>
<!-- /wp:group -->
<!-- /wp:comment-template -->

<!-- wp:comments-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:comments-pagination-previous /-->

<!-- wp:comments-pagination-next /-->
<!-- /wp:comments-pagination -->

<!-- wp:post-comments-form {"style":{"spacing":{"margin":{"top":"var:preset|spacing|70"}}}} /--></div>
<!-- /wp:comments -->
