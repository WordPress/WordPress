<?php
/**
 * Navigation: Overlay.
 *
 * @package WordPress
 */

return array(
	'title'      => _x( 'Navigation Overlay', 'Block pattern title' ),
	'blockTypes' => array( 'core/template-part/navigation-overlay' ),
	'categories' => array( 'navigation' ),
	'content'    => '<!-- wp:group {"metadata":{"name":"' . esc_attr( __( 'Navigation Overlay' ) ) . '"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"dimensions":{"minHeight":"100vh"}},"backgroundColor":"white","layout":{"type":"default"}} -->
<div class="wp-block-group has-white-background-color has-background" style="min-height:100vh;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group alignwide"><!-- wp:navigation-overlay-close /--></div>
<!-- /wp:group -->

<!-- wp:navigation {"layout":{"type":"flex","orientation":"vertical"},"showSubmenuIcon":false,"submenuVisibility":"always"} /--></div>
<!-- /wp:group -->',
);
