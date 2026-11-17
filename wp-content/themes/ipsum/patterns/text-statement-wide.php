<?php
/**
 * Title: Text Statement Wide
 * Slug: ipsum/text-statement-wide
 * Categories: text
 * Viewport width: 1280
 * Description: The statement at band width, without the card. It has the quotation mark and one oversized line, left aligned.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Text Statement Wide', 'Name of the Text Statement Wide element', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:0"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Text Statement Wide Wrapper', 'Name of the Text Statement Wide Wrapper element', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:quote {"className":"is-style-plain","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
<blockquote class="wp-block-quote is-style-plain"><!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.2","fontStyle":"normal","fontWeight":"400"}},"fontSize":"2-x-large"} -->
<p class="has-2-x-large-font-size" style="font-style:normal;font-weight:400;line-height:1.2"><?php esc_html_e( 'Write the sentence first. The page can wait.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --><cite><?php echo wp_kses_post( __( 'From the <a href="#">essays</a>', 'ipsum' ) ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
