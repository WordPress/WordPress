<?php
/**
 * Title: Hero Colophon
 * Slug: ipsum/hero-colophon
 * Categories: banner, about, header
 * Block Types: core/template-part/header
 * Viewport width: 1280
 * Description: A blogger card introducing the person behind the blog — site icon, greeting, short bio and the menu.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Hero Colophon', 'Name of the hero colophon group', 'ipsum' ); ?>"},"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Colophon Card', 'Name of the colophon card group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"},"border":{"radius":{"topLeft":"10px","topRight":"10px","bottomLeft":"10px","bottomRight":"10px"}}},"backgroundColor":"theme-5","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group has-theme-5-background-color has-background" style="border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:site-logo {"width":80,"style":{"color":{"duotone":"var:preset|duotone|duotone-1"}}} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Colophon Text', 'Name of the colophon text group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"},"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"2-x-large"} -->
<h2 class="wp-block-heading has-2-x-large-font-size"><?php echo esc_html__( 'Howdy, I’m Lorem', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} -->
<p style="margin-bottom:var(--wp--preset--spacing--40)"><?php echo esc_html__( 'Teacher in Porto. This is where I keep my notes on books, slow mornings, and the occasional recipe that actually works.', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:navigation {"overlay":"navigation-overlay","icon":"menu","style":{"spacing":{"blockGap":"var:preset|spacing|30"},"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","ariaLabel":"<?php esc_attr_e( 'Primary', 'ipsum' ); ?>"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
