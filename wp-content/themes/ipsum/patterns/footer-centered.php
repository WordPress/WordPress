<?php
/**
 * Title: Footer Centered
 * Slug: ipsum/footer-centered
 * Categories: footer
 * Block Types: core/template-part/footer
 * Viewport width: 1280
 * Description: A centered footer on the toast card — the site name as a cropped wordmark over the tagline, with the credit line beneath.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Footer Centered', 'Name of the centered footer group', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Footer Centered Wrapper', 'Name of the centered footer card group', 'ipsum' ); ?>"},"className":"is-style-crop-contents","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"elements":{"link":{"color":{"text":"var:preset|color|theme-1"}}},"border":{"radius":{"topLeft":"10px","topRight":"10px","bottomLeft":"10px","bottomRight":"10px"}}},"backgroundColor":"theme-6","textColor":"theme-1","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-crop-contents has-theme-1-color has-theme-6-background-color has-text-color has-background has-link-color" style="border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Tagline', 'Name of the group holding the site title and tagline', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group alignfull"><!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Title - Fit Text', 'Name of the fit-text site name paragraph', 'ipsum' ); ?>"},"style":{"typography":{"lineHeight":"1"},"spacing":{"margin":{"top":"-10rem"}}},"fitText":true} -->
<p class="has-fit-text" style="margin-top:-10rem;line-height:1"><?php esc_html_e( 'Ipsum', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:site-tagline {"style":{"typography":{"textAlign":"center"}}} /--></div>
<!-- /wp:group -->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Credit Line', 'Name of the credit line paragraph', 'ipsum' ); ?>"},"style":{"typography":{"textAlign":"center"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-text-align-center has-manrope-font-family has-small-font-size">
<?php
/* Translators: 1. is the start of a 'a' HTML element, 2. is the end of a 'a' HTML element */
printf( esc_html__( 'Designed with %1$sWordPress%2$s', 'ipsum' ), '<a href="' . esc_url( 'https://wordpress.org' ) . '" rel="nofollow">', '</a>' );
?>
</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
