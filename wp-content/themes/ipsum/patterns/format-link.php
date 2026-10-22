<?php
/**
 * Title: Format Link
 * Slug: ipsum/format-link
 * Categories: posts
 * Viewport width: 1280
 * Description: A link-format post body — a bold line about the destination, with the link set off by a dotted rule.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Format Link', 'Name of the link format group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Format Link', 'Name of the link format card group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|40"},"border":{"radius":{"topLeft":"10px","topRight":"10px","bottomLeft":"10px","bottomRight":"10px"}}},"backgroundColor":"theme-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-theme-5-background-color has-background" style="border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="font-style:normal;font-weight:700"><?php esc_html_e( 'Elsewhere: a long read on how lorem ipsum took over design—five centuries of scrambled Cicero standing in for the words to come.', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Link Line', 'Name of the link line group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|20","left":"var:preset|spacing|20"}},"border":{"left":{"width":"1px","style":"dotted"}}},"fontSize":"medium","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group has-medium-font-size" style="border-left-style:dotted;border-left-width:1px;padding-right:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)"><!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Link', 'Name of the link paragraph', 'ipsum' ); ?>"},"style":{"typography":{"lineHeight":"1"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="line-height:1"><a href="#"><?php esc_html_e( 'https://example.com', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
