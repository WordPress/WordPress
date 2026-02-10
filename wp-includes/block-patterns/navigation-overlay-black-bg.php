<?php
/**
 * Navigation: Overlay with black background.
 *
 * @package WordPress
 */

return array(
	'title'      => _x( 'Overlay with black background', 'Block pattern title' ),
	'blockTypes' => array( 'core/template-part/navigation-overlay' ),
	'categories' => array( 'navigation' ),
	'content'    => '<!-- wp:group {"metadata":{"name":"' . esc_attr( __( 'Navigation Overlay' ) ) . '"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"dimensions":{"minHeight":"100vh"},"elements":{"link":{"color":{"text":"var:preset|color|white"}}},"color":{"background":"#000000"}},"textColor":"white","layout":{"type":"default"}} -->
<div class="wp-block-group has-white-color has-text-color has-background has-link-color" style="background-color:#000000;min-height:100vh;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:navigation {"style":{"typography":{"lineHeight":"1"}},"fontSize":"xx-large","layout":{"type":"flex","orientation":"vertical"}} /-->

<!-- wp:navigation-overlay-close {"displayMode":"text","style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"white"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
);
