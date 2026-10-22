<?php
/**
 * Title: sidebar
 * Slug: ipsum/hidden-sidebar
 * Inserter: no
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"tagName":"aside","metadata":{"name":"<?php echo esc_html_x( 'Sidebar', 'Name of the sidebar group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"default"}} -->
<aside class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Categories', 'Name of the categories group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"metadata":{"name":"<?php echo esc_html_x( 'Categories Label', 'Name of the categories heading', 'ipsum' ); ?>"},"style":{"typography":{"letterSpacing":"0rem"}},"fontSize":"small"} -->
<h2 class="wp-block-heading has-small-font-size" style="letter-spacing:0rem"><?php esc_html_e( 'Categories', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:categories {"fontSize":"small"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Recent Posts', 'Name of the recent posts group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"metadata":{"name":"<?php echo esc_html_x( 'Recent Posts Label', 'Name of the recent posts heading', 'ipsum' ); ?>"},"style":{"typography":{"letterSpacing":"0rem"}},"fontSize":"small"} -->
<h2 class="wp-block-heading has-small-font-size" style="letter-spacing:0rem"><?php esc_html_e( 'Recent posts', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:query {"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"ignore","inherit":false,"taxQuery":null,"parents":[],"excludeCurrent":null},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Recent Post', 'Name of the group holding one recent post', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"0"},"margin":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group" style="margin-bottom:var(--wp--preset--spacing--40)"><!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","letterSpacing":"0rem"}},"fontSize":"small"} /-->

<!-- wp:post-date {"format":"human-diff","isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"400","letterSpacing":"0rem"}}} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group --></aside>
<!-- /wp:group -->
