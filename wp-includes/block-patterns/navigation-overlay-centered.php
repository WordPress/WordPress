<?php
/**
 * Navigation: Overlay with centered navigation.
 *
 * @package WordPress
 */

return array(
	'title'      => _x( 'Overlay with centered navigation', 'Block pattern title' ),
	'blockTypes' => array( 'core/template-part/navigation-overlay' ),
	'categories' => array( 'navigation' ),
	'content'    => '<!-- wp:group {"metadata":{"name":"' . esc_attr( __( 'Navigation Overlay' ) ) . '"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"dimensions":{"minHeight":"100vh"},"elements":{"link":{"color":{"text":"var:preset|color|black"}}},"color":{"background":"#eeeeee"}},"textColor":"black","layout":{"type":"default"}} -->
<div class="wp-block-group has-black-color has-text-color has-background has-link-color" style="background-color:#eeeeee;min-height:100vh;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group alignwide"><!-- wp:navigation-overlay-close /--></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"dimensions":{"minHeight":"90vh"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide" style="min-height:90vh"><!-- wp:navigation {"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
);
