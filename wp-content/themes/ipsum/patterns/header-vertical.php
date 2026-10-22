<?php
/**
 * Title: Header Vertical
 * Slug: ipsum/header-vertical
 * Categories: header
 * Block Types: core/template-part/header
 * Viewport width: 1280
 * Description: A sticky, viewport-tall header rail — the menu button on top and the site title running vertically beneath it.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Header Vertical', 'Name of the vertical header group', 'ipsum' ); ?>"},"align":"wide","style":{"position":{"type":"sticky","top":"0px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Header Vertical Wrapper', 'Name of the vertical header wrapper group', 'ipsum' ); ?>"},"align":"wide","style":{"dimensions":{"minHeight":"100vh"}},"layout":{"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group alignwide" style="min-height:100vh"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Vertical Stack', 'Name of the vertical stack group', 'ipsum' ); ?>"},"align":"full","layout":{"type":"flex","orientation":"vertical","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group alignfull"><!-- wp:navigation {"overlayMenu":"always","overlayBackgroundColor":"theme-6","overlayTextColor":"theme-1","ariaLabel":"<?php esc_attr_e( 'Primary', 'ipsum' ); ?>","style":{"spacing":{"margin":{"top":"0"},"blockGap":"var:preset|spacing|20"},"layout":{"selfStretch":"fit","flexSize":null}},"layout":{"type":"flex","justifyContent":"right","orientation":"horizontal","flexWrap":"wrap"}} /-->

<!-- wp:site-title {"level":0,"style":{"typography":{"writingMode":"vertical-rl","textTransform":"uppercase"}},"fontSize":"small"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
