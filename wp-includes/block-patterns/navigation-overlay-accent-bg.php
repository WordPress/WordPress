<?php
/**
 * Navigation: Overlay with orange background.
 *
 * @package WordPress
 */

return array(
	'title'      => _x( 'Overlay with orange background', 'Block pattern title' ),
	'blockTypes' => array( 'core/template-part/navigation-overlay' ),
	'categories' => array( 'navigation' ),
	'content'    => '<!-- wp:group {"metadata":{"name":"' . esc_attr( __( 'Navigation Overlay' ) ) . '"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50","top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}},"color":{"background":"#f57600"},"dimensions":{"minHeight":"100vh"},"elements":{"link":{"color":{"text":"var:preset|color|black"}}}},"textColor":"black","layout":{"type":"grid","columnCount":2,"minimumColumnWidth":"600px","rowCount":2,"isManualPlacement":true}} -->
<div class="wp-block-group has-black-color has-text-color has-background has-link-color" style="background-color:#f57600;min-height:100vh;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"layout":{"columnStart":1,"rowStart":1}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:navigation-overlay-close {"style":{"layout":{"columnStart":1,"rowStart":1}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"typography":{"lineHeight":"0.8"},"layout":{"columnStart":1,"rowStart":2}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"bottom"}} -->
<div class="wp-block-group" style="line-height:0.8"><!-- wp:site-title {"fontSize":"large"} /-->

<!-- wp:site-tagline {"style":{"typography":{"lineHeight":"1.2"},"elements":{"link":{"color":{"text":"#000000a6"}}},"color":{"text":"#000000a6"}},"fontSize":"large"} /--></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"10rem","style":{"layout":{"columnStart":2,"rowStart":2}}} -->
<div style="height:10rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:navigation {"overlayMenu":"never","style":{"typography":{"lineHeight":"1"},"layout":{"columnStart":2,"rowStart":1}},"fontSize":"large","layout":{"type":"flex","orientation":"vertical"}} /--></div>
<!-- /wp:group -->',
);
