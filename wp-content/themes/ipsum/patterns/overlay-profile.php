<?php
/**
 * Title: Overlay Profile
 * Slug: ipsum/overlay-profile
 * Categories: navigation
 * Block Types: core/template-part/navigation-overlay
 * Viewport width: 1280
 * Description: A navigation overlay led by the author — avatar, name and role over the menu, extra links, and a full-width subscribe button.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Overlay Profile', 'Name of the profile overlay group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|50"},"dimensions":{"minHeight":"100vh"}},"backgroundColor":"theme-1","layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group has-theme-1-background-color has-background" style="min-height:100vh;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Action Bar', 'Name of the action bar group', 'ipsum' ); ?>"},"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group alignwide"><!-- wp:navigation-overlay-close /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Identity', 'Name of the identity group', 'ipsum' ); ?>"},"align":"wide","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide"><!-- wp:image {"width":"160px","aspectRatio":"1","scale":"cover","focalPoint":{"x":0.5,"y":1},"sizeSlug":"full","linkDestination":"none","align":"center"} -->
<figure class="wp-block-image aligncenter size-full is-resized"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-portrait-tall.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php esc_attr_e( 'Portrait of the author.', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover;object-position:50% 100%;width:160px;height:auto"/></figure>
<!-- /wp:image -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Identity', 'Name of the identity group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20","left":"var:preset|spacing|20"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"typography":{"textAlign":"center"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size"><?php esc_html_e( 'Lorem', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Role', 'Name of the role paragraph', 'ipsum' ); ?>"},"style":{"typography":{"textAlign":"center"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-text-align-center has-manrope-font-family has-small-font-size"><?php esc_html_e( 'Teacher in Porto', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Menu', 'Name of the menu group', 'ipsum' ); ?>"},"align":"full","style":{"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:navigation {"showSubmenuIcon":false,"submenuVisibility":"always","overlayMenu":"never","fontSize":"large","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"},"ariaLabel":"<?php echo esc_attr_x( 'Expanded', 'Accessible name of the navigation in the overlay', 'ipsum' ); ?>"} /-->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Extra Links', 'Name of the extra links group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Link', 'Name of the link paragraph', 'ipsum' ); ?>"},"style":{"typography":{"textAlign":"center"}}} -->
<p class="has-text-align-center"><a href="#"><?php esc_html_e( 'Subscribe', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Link', 'Name of the link paragraph', 'ipsum' ); ?>"},"style":{"typography":{"textAlign":"center"}}} -->
<p class="has-text-align-center"><a href="#"><?php esc_html_e( 'Say hello', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Link', 'Name of the link paragraph', 'ipsum' ); ?>"},"style":{"typography":{"textAlign":"center"}}} -->
<p class="has-text-align-center"><a href="#"><?php esc_html_e( 'See the dates', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Link', 'Name of the link paragraph', 'ipsum' ); ?>"},"style":{"typography":{"textAlign":"center"}}} -->
<p class="has-text-align-center"><a href="#"><?php esc_html_e( 'RSS', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Overlay Footer', 'Name of the overlay footer group', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group alignwide"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"dimensions":{"width":"var:preset|dimension|100"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Get the next post!', 'ipsum' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
