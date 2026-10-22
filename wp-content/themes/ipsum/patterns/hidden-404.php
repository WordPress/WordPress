<?php
/**
 * Title: 404
 * Slug: ipsum/hidden-404
 * Inserter: no
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:template-part {"slug":"header"} /-->

<!-- wp:group {"tagName":"main","metadata":{"name":"<?php echo esc_html_x( 'Body', 'Name of the main content area group', 'ipsum' ); ?>"},"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Post Title', 'Name of the group holding the post title', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":1,"fitText":true} -->
<h1 class="wp-block-heading has-fit-text"><?php esc_html_e( 'Page not found.', 'ipsum' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'The page you are looking for doesn\'t exist, or it has been moved. Please try searching using the form below.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'ipsum' ); ?>","showLabel":false,"placeholder":"<?php esc_attr_e( 'Type here...', 'ipsum' ); ?>","buttonText":"<?php esc_attr_e( 'Search', 'ipsum' ); ?>","buttonPosition":"button-inside","buttonUseIcon":true,"ariaLabel":"<?php echo esc_attr_x( 'Site', 'Accessible name of the search form', 'ipsum' ); ?>"} /--></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer"} /-->
