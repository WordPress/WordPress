<?php
/**
 * Title: Overlay Contents
 * Slug: ipsum/overlay-contents
 * Categories: navigation
 * Block Types: core/template-part/navigation-overlay
 * Viewport width: 1280
 * Description: A navigation overlay working as the site’s table of contents — a large menu list with submenus in view and a search field underneath.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Overlay Contents', 'Name of the navigation overlay contents group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"dimensions":{"minHeight":"100vh"},"elements":{"link":{"color":{"text":"var:preset|color|theme-6"}}}},"backgroundColor":"theme-1","textColor":"theme-6","layout":{"type":"default"}} -->
<div class="wp-block-group has-theme-6-color has-theme-1-background-color has-text-color has-background has-link-color" style="min-height:100vh;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Action Bar', 'Name of the action bar group', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20)"><!-- wp:navigation-overlay-close {"style":{"elements":{"link":{"color":{"text":"var:preset|color|theme-2"}}}},"textColor":"theme-2"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Navigation', 'Name of the group holding the site title and navigation', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"},"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:heading {"metadata":{"name":"<?php echo esc_html_x( 'Nav Title', 'Name of the navigation title heading', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"elements":{"link":{"color":{"text":"var:preset|color|theme-2"}}}},"textColor":"theme-2","fontSize":"small","fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-theme-2-color has-text-color has-link-color has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e( 'Menu', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:navigation {"textColor":"theme-2","showSubmenuIcon":false,"submenuVisibility":"always","overlayMenu":"never","style":{"typography":{"letterSpacing":"-0.02rem"}},"fontSize":"2-x-large","layout":{"type":"flex","orientation":"vertical"},"ariaLabel":"<?php echo esc_attr_x( 'Expanded', 'Accessible name of the navigation in the overlay', 'ipsum' ); ?>"} /--></div>
<!-- /wp:group -->

<!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'ipsum' ); ?>","buttonText":"<?php esc_attr_e( 'Search', 'ipsum' ); ?>","buttonPosition":"button-inside","buttonUseIcon":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"ariaLabel":"<?php echo esc_attr_x( 'Menu', 'Accessible name of the search form in the navigation overlay', 'ipsum' ); ?>"} /--></div>
<!-- /wp:group -->
