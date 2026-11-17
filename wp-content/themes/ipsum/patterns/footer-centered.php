<?php
/**
 * Title: Footer Centered
 * Slug: ipsum/footer-centered
 * Categories: footer
 * Block Types: core/template-part/footer
 * Viewport width: 1280
 * Description: A centered footer on an inverted card. The site name runs large over the tagline, with the credit line beneath.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Footer Centered', 'Name of the centered footer group', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Footer Centered Wrapper', 'Name of the centered footer card group', 'ipsum' ); ?>"},"className":"is-style-crop-contents","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|20"},"elements":{"link":{"color":{"text":"var:preset|color|base"}}},"border":{"radius":"10px"}},"backgroundColor":"accent-3","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-crop-contents has-base-color has-accent-3-background-color has-text-color has-background has-link-color" style="border-radius:10px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Tagline', 'Name of the group holding the site title and tagline', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|20"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group alignfull"><!-- wp:site-title {"level":0,"isLink":false,"metadata":{"name":"<?php echo esc_html_x( 'Title - Fit Text', 'Name of the fit-text site name paragraph', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"400","lineHeight":"0","textAlign":"center"},"spacing":{"padding":{"bottom":"var:preset|spacing|80","top":"var:preset|spacing|60"}},"@mobile":{"spacing":{"padding":{"right":"var:preset|spacing|30","left":"var:preset|spacing|30","top":"var:preset|spacing|20","bottom":"var:preset|spacing|80"}}}},"fitText":true} /-->

<!-- wp:site-tagline {"style":{"typography":{"textAlign":"center","fontWeight":"700"}}} /--></div>
<!-- /wp:group -->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Credit Line', 'Name of the credit line paragraph', 'ipsum' ); ?>"},"style":{"typography":{"textAlign":"center"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-text-align-center has-manrope-font-family has-small-font-size">
<?php
printf(
	/* translators: Designed with WordPress. %s: WordPress link. */
	esc_html__( 'Designed with %s', 'ipsum' ),
	'<a href="' . esc_url( __( 'https://wordpress.org', 'ipsum' ) ) . '" rel="nofollow">WordPress</a>'
);
?>
</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
