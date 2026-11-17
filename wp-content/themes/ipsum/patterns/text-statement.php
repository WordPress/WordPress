<?php
/**
 * Title: Text Statement
 * Slug: ipsum/text-statement
 * Categories: text
 * Viewport width: 1280
 * Description: A one-line statement on an inverted card. It has the opening quotation mark on top, centered, and room for a single link.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Text Statement', 'Name of the Text Statement element', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Text Statement Wrapper', 'Name of the Text Statement Wrapper element', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"},"elements":{"link":{"color":{"text":"var:preset|color|base"}}},"border":{"radius":"10px"}},"backgroundColor":"accent-3","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-base-color has-accent-3-background-color has-text-color has-background has-link-color" style="border-radius:10px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><!-- wp:quote {"className":"is-style-plain-on-color","style":{"spacing":{"blockGap":"var:preset|spacing|30"},"typography":{"textAlign":"center"}}} -->
<blockquote class="wp-block-quote has-text-align-center is-style-plain-on-color"><!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.3","fontStyle":"normal","fontWeight":"500","letterSpacing":"-0.02rem","textAlign":"center"}},"fontSize":"x-large"} -->
<p class="has-text-align-center has-x-large-font-size" style="font-style:normal;font-weight:500;letter-spacing:-0.02rem;line-height:1.3"><?php esc_html_e( 'Write the sentence first. The page can wait.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --><cite><?php echo wp_kses_post( __( 'From the <a href="#">essays</a>', 'ipsum' ) ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
