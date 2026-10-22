<?php
/**
 * Title: Footer Subscribe
 * Slug: ipsum/footer-subscribe
 * Categories: footer
 * Block Types: core/template-part/footer
 * Viewport width: 1280
 * Description: A footer leading with the subscribe invitation on the toast card — a quiet pitch and a full-width button — over the credit line.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Footer Subscribe', 'Name of the subscribe footer group', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Subscribe Card', 'Name of the subscribe card group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|50"},"border":{"radius":{"topLeft":"10px","topRight":"10px","bottomLeft":"10px","bottomRight":"10px"}},"elements":{"link":{"color":{"text":"var:preset|color|theme-1"}}}},"backgroundColor":"theme-6","textColor":"theme-1","layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-group has-theme-1-color has-theme-6-background-color has-text-color has-background has-link-color" style="border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title Wrapper', 'Name of the subscribe title group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"typography":{"textAlign":"center"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size"><?php esc_html_e( 'The quiet kind of newsletter.', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"textAlign":"center"}},"fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size"><?php esc_html_e( 'New essays arrive as they’re published—no noise.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"theme-1","textColor":"theme-2","style":{"dimensions":{"width":"var:preset|dimension|100"},"elements":{"link":{"color":{"text":"var:preset|color|theme-2"}}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-theme-2-color has-theme-1-background-color has-text-color has-background has-link-color wp-element-button"><?php esc_html_e( 'Subscribe', 'ipsum' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Credit Line', 'Name of the credit line row group', 'ipsum' ); ?>"},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Credit Line', 'Name of the credit line paragraph', 'ipsum' ); ?>"},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size">
<?php
/* Translators: 1. is the start of a 'a' HTML element, 2. is the end of a 'a' HTML element */
printf( esc_html__( 'Designed with %1$sWordPress%2$s', 'ipsum' ), '<a href="' . esc_url( 'https://wordpress.org' ) . '" rel="nofollow">', '</a>' );
?>
</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Back to Top', 'Name of the back to top paragraph', 'ipsum' ); ?>"},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size"><a href="#top"><?php esc_html_e( 'Back to top ↑', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
