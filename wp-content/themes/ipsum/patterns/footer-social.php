<?php
/**
 * Title: Footer Social
 * Slug: ipsum/footer-social
 * Categories: footer
 * Block Types: core/template-part/footer
 * Viewport width: 1280
 * Description: A centered footer — the social icons over the site title in uppercase, with the credit line beneath.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Footer Social', 'Name of the social footer group', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|60"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Social Links', 'Name of the title and social links group', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group alignfull"><!-- wp:social-links {"size":"has-small-icon-size","className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-small-icon-size is-style-logos-only"><!-- wp:social-link {"url":"#","service":"mastodon"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"feed"} /--></ul>
<!-- /wp:social-links -->

<!-- wp:site-title {"level":2,"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"400","textAlign":"center"}},"fontSize":"large"} /--></div>
<!-- /wp:group -->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Credit Line', 'Name of the credit line paragraph', 'ipsum' ); ?>"},"style":{"typography":{"textAlign":"center"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-text-align-center has-manrope-font-family has-small-font-size">
<?php
/* Translators: 1. is the start of a 'a' HTML element, 2. is the end of a 'a' HTML element */
printf( esc_html__( 'Designed with %1$sWordPress%2$s', 'ipsum' ), '<a href="' . esc_url( 'https://wordpress.org' ) . '" rel="nofollow">', '</a>' );
?>
</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
