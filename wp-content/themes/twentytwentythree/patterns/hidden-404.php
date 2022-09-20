<?php
/**
 * Title: Hidden 404
 * Slug: twentytwentythree/hidden-404
 * Inserter: no
 */
?>
<!-- wp:heading {"level":1,"className":"has-text-align-center"} -->
<h1 class="has-text-align-center">
	<?php echo esc_html_x( '404', 'Error code for a webpage that is not found.', 'twentytwentythree' ); ?>
</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">
	<?php echo esc_html_x( 'This page could not be found.', 'Message to convey that a webpage could not be found', 'twentytwentythree' ); ?>
</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"var(--wp--preset--spacing--40)"} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:search {"label":"<?php echo esc_html_x( 'Search', 'label', 'twentytwentythree' ); ?>","placeholder":"<?php echo esc_html_x( 'Search...', 'placeholder for search field', 'twentytwentythree' ); ?>","showLabel":false,"width":100,"widthUnit":"%","buttonText":"<?php esc_html_e( 'Search', 'twentytwentythree' ); ?>","buttonUseIcon":true,"align":"center"} /-->
