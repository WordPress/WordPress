<?php
/**
 * Title: Hero Epigraph
 * Slug: ipsum/hero-epigraph
 * Categories: banner, header
 * Block Types: core/template-part/header
 * Viewport width: 1280
 * Description: A full-height opening statement — site title and menu on top, one oversized line about the blog, and an invitation to read.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Hero Epigraph', 'Name of the hero epigraph group', 'ipsum' ); ?>"},"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:cover {"overlayColor":"theme-1","isUserOverlayColor":true,"minHeight":400,"contentPosition":"top center","isDark":false,"textColor":"theme-2","style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|60"}},"layout":{"type":"default"}} -->
<div class="wp-block-cover is-light has-theme-2-color has-text-color has-custom-content-position is-position-top-center" style="padding-right:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40);min-height:400px"><span aria-hidden="true" class="wp-block-cover__background has-theme-1-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Header Wrapper', 'Name of the group wrapping the header', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|40"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Header Row', 'Name of the header row group', 'ipsum' ); ?>"},"align":"full","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group alignfull"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Tagline', 'Name of the group holding the site title and tagline', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"},"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:site-title {"level":0} /-->

<!-- wp:site-tagline {"style":{"layout":{"selfStretch":"fill","flexSize":null}}} /--></div>
<!-- /wp:group -->

<!-- wp:navigation {"overlay":"navigation-overlay","icon":"menu","fontSize":"small","ariaLabel":"<?php esc_attr_e( 'Primary', 'ipsum' ); ?>"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:heading {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|70"}}},"fitText":true} -->
<h2 class="wp-block-heading alignwide has-fit-text" style="margin-top:var(--wp--preset--spacing--80);margin-bottom:var(--wp--preset--spacing--70)">
<?php
/* Translators: %1$s is a line break HTML element */
printf( esc_html__( 'I cook, I run, and I write about both%1$s—usually in that order.', 'ipsum' ), '<br>' );
?>
</h2>
<!-- /wp:heading -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Read the blog line', 'Name of the group holding the read the blog line', 'ipsum' ); ?>"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:600"><?php echo esc_html__( 'Read the blog.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:group -->
