<?php
/**
 * Title: Page Soon
 * Slug: ipsum/page-soon
 * Categories: ipsum_page
 * Block Types: core/post-content
 * Post Types: page
 * Viewport width: 1280
 * Description: A full-height coming-soon cover — the tagline as headline, a short note from the author, and a button to subscribe.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Page Soon', 'Name of the Page Soon element', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-monobloc.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>","overlayColor":"theme-4","isUserOverlayColor":true,"contentPosition":"top left","sizeSlug":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|70"}},"border":{"radius":{"topLeft":"10px","topRight":"10px","bottomLeft":"10px","bottomRight":"10px"}},"dimensions":{"aspectRatio":"2/3"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover has-custom-content-position is-position-top-left" style="border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70)"><img class="wp-block-cover__image-background size-full" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-monobloc.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-theme-4-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Cover Inner Wrapper', 'Name of the Cover Inner Wrapper element', 'ipsum' ); ?>"},"style":{"border":{"radius":{"topLeft":"5px","topRight":"5px","bottomLeft":"5px","bottomRight":"5px"}},"elements":{"link":{"color":{"text":"var:preset|color|theme-2"}}},"spacing":{"padding":{"right":"var:preset|spacing|30","left":"var:preset|spacing|30","top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"backgroundColor":"theme-1","textColor":"theme-2","layout":{"type":"default"}} -->
<div class="wp-block-group has-theme-2-color has-theme-1-background-color has-text-color has-background has-link-color" style="border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-left-radius:5px;border-bottom-right-radius:5px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:heading {"style":{"typography":{"textAlign":"left"}},"fontSize":"2-x-large"} -->
<h2 class="wp-block-heading has-text-align-left has-2-x-large-font-size"><?php esc_html_e( 'Until real words arrive.', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Paragraph and Buttons', 'Name of the Paragraph and Buttons element', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"textAlign":"left"}},"fontSize":"small"} -->
<p class="has-text-align-left has-small-font-size"><?php esc_html_e( 'This page is a placeholder in the most honest sense: a blog is being written behind it. There will be essays that take their time, pictures standing in for the hours they were taken, and the occasional sound worth pressing play on. Leave your address and the first post will find you the moment it\'s ready.', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"border":{"radius":{"topLeft":"5px","topRight":"5px","bottomLeft":"5px","bottomRight":"5px"}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" style="border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-left-radius:5px;border-bottom-right-radius:5px"><?php esc_html_e( 'Get the first post', 'ipsum' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:group -->
