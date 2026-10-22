<?php
/**
 * Title: Navigation Overlay
 * Slug: ipsum/navigation-overlay
 * Categories: navigation
 * Block Types: core/template-part/navigation-overlay
 * Viewport width: 1280
 * Description: The theme’s default navigation overlay — centered logo, site title and tagline, and a large vertical menu on a dark background.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Navigation Overlay', 'Name of the navigation overlay group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"dimensions":{"minHeight":"100vh"},"elements":{"link":{"color":{"text":"var:preset|color|theme-1"}}}},"backgroundColor":"theme-6","textColor":"theme-1","layout":{"type":"default"}} -->
<div class="wp-block-group has-theme-1-color has-theme-6-background-color has-text-color has-background has-link-color" style="min-height:100vh;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Action Bar', 'Name of the action bar group', 'ipsum' ); ?>"},"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group alignwide"><!-- wp:navigation-overlay-close /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Navigation Content', 'Name of the navigation content group', 'ipsum' ); ?>"},"align":"wide","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Site Logo Wrapper', 'Name of the group wrapping the site logo', 'ipsum' ); ?>"},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Site Logo', 'Name of the site logo group', 'ipsum' ); ?>"},"style":{"dimensions":{"minHeight":"80px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group" style="min-height:80px"><!-- wp:site-logo {"width":80,"isLink":false,"align":"center"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Tagline', 'Name of the group holding the site title and tagline', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"0","bottom":"0"},"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"},"blockGap":{"top":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30);padding-top:0;padding-bottom:0"><!-- wp:site-title {"level":0,"style":{"typography":{"textAlign":"center"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"large"} /-->

<!-- wp:site-tagline {"style":{"typography":{"textAlign":"center"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"medium"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Navigation', 'Name of the navigation group', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:navigation {"showSubmenuIcon":false,"submenuVisibility":"always","overlayMenu":"never","style":{"typography":{"textTransform":"uppercase"}},"fontSize":"2-x-large","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"},"ariaLabel":"<?php echo esc_attr_x( 'Expanded', 'Accessible name of the navigation in the overlay', 'ipsum' ); ?>"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
