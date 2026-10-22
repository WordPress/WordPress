<?php
/**
 * Title: Keep Reading
 * Slug: ipsum/hidden-keep-reading
 * Inserter: no
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Keep Reading', 'Name of the keep reading group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"metadata":{"name":"<?php echo esc_html_x( 'Keep Reading Label', 'Name of the keep reading heading', 'ipsum' ); ?>"},"style":{"typography":{"letterSpacing":"0rem"}},"fontSize":"small"} -->
<h2 class="wp-block-heading has-small-font-size" style="letter-spacing:0rem"><?php esc_html_e( 'Keep Reading', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"ignore","inherit":false,"taxQuery":null,"parents":[],"excludeCurrent":true},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"default"}} -->
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Recent Post', 'Name of the group holding one recent post', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group"><!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"500","letterSpacing":"0rem"}},"fontSize":"medium"} /-->

<!-- wp:post-date {"format":"human-diff","isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"400","letterSpacing":"0rem"}}} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
