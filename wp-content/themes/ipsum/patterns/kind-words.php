<?php
/**
 * Title: Kind Words
 * Slug: ipsum/kind-words
 * Categories: testimonials
 * Viewport width: 1280
 * Description: Two reader quotes on toast cards, each with an avatar with rounded corners and a one-line attribution.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Kind Words', 'Name of the kind words group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Quote Outer Block', 'Name of the quote card group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":{"topLeft":"10px","topRight":"10px","bottomLeft":"10px","bottomRight":"10px"}}},"backgroundColor":"theme-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-theme-5-background-color has-background" style="border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:image {"width":"80px","aspectRatio":"1","scale":"cover","focalPoint":{"x":0.5,"y":0.5},"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-avatar-cora.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'Black-and-white portrait of a woman with short curly hair in a dark blazer.', 'Alt text for a reader portrait', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover;object-position:50% 50%;width:80px;height:auto"/></figure>
<!-- /wp:image -->

<!-- wp:quote {"className":"is-style-plain","style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
<blockquote class="wp-block-quote is-style-plain" style="font-style:normal;font-weight:400"><!-- wp:paragraph -->
<p><?php esc_html_e( '“I subscribed for the essays and stayed for the pictures. It’s the only email I open slowly.”', 'ipsum' ); ?></p>
<!-- /wp:paragraph --><cite><?php esc_html_e( 'June Alvarado, Porto', 'ipsum' ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Quote Outer Block', 'Name of the quote card group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":{"topLeft":"10px","topRight":"10px","bottomLeft":"10px","bottomRight":"10px"}}},"backgroundColor":"theme-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-theme-5-background-color has-background" style="border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:image {"width":"80px","aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-avatar-iris.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'Black-and-white portrait of a smiling woman with curly hair.', 'Alt text for a reader portrait', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover;width:80px;height:auto"/></figure>
<!-- /wp:image -->

<!-- wp:quote {"className":"is-style-plain","style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
<blockquote class="wp-block-quote is-style-plain" style="font-style:normal;font-weight:400"><!-- wp:paragraph -->
<p><?php esc_html_e( '“Reads like a letter from a friend who takes their time. Even the quiet weeks feel on purpose.”', 'ipsum' ); ?></p>
<!-- /wp:paragraph --><cite><?php esc_html_e( 'Maria Mercer, Leipzig', 'ipsum' ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
