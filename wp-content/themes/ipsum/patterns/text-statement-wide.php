<?php
/**
 * Title: Text Statement Wide
 * Slug: ipsum/text-statement-wide
 * Categories: text
 * Viewport width: 1280
 * Description: The statement at band width — the quotation mark and one oversized line, left aligned.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Text Statement Wide', 'Name of the Text Statement Wide element', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:0"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Text Statement Wide Wrapper', 'Name of the Text Statement Wide Wrapper element', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:paragraph {"className":"is-style-text-quote-mark","style":{"typography":{"lineHeight":"1.2","fontStyle":"normal","fontWeight":"400","textAlign":"left"}},"fontSize":"2-x-large"} -->
<p class="has-text-align-left is-style-text-quote-mark has-2-x-large-font-size" style="font-style:normal;font-weight:400;line-height:1.2"><?php echo wp_kses_post( __( 'I teach and write about books, slow mornings, and the occasional recipe that actually works—start with the <a href="#">essays</a>.', 'ipsum' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
