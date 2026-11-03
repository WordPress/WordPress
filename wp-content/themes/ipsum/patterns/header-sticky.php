<?php
/**
 * Title: Header Sticky
 * Slug: ipsum/header-sticky
 * Categories: header
 * Block Types: core/template-part/header
 * Viewport width: 1280
 * Description: A sticky full-width header bar with the site logo, title and navigation.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Header Wrapper', 'Name of the group wrapping the header', 'ipsum' ); ?>"},"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"bottom":"var:preset|spacing|70"}},"position":{"type":"sticky","top":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Header Sticky', 'Name of the sticky header group', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"position":{"type":"sticky","top":"0px"},"shadow":"var:preset|shadow|soft","border":{"right":[],"top":[],"bottom":{"color":"var:preset|color|accent-5","width":"1px"},"left":[]}},"backgroundColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="border-bottom-color:var(--wp--preset--color--accent-5);border-bottom-width:1px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40);box-shadow:var(--wp--preset--shadow--soft)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Header Row', 'Name of the header row group', 'ipsum' ); ?>"},"align":"wide","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Site Logo and Title', 'Name of the group holding the site logo and title', 'ipsum' ); ?>"},"style":{"layout":{"selfStretch":"fill","flexSize":null},"spacing":{"blockGap":{"left":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":48,"shouldSyncIcon":true,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}},"color":{"duotone":"var:preset|duotone|duotone-1"}}} /-->

<!-- wp:site-title {"level":0,"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"},"typography":{"textDecoration":"none"},":hover":{"color":{"text":"var:preset|color|accent-3"}}}}}} /--></div>
<!-- /wp:group -->

<!-- wp:navigation {"overlay":"navigation-overlay","icon":"menu","overlayBackgroundColor":"base","overlayTextColor":"contrast","fontSize":"small","layout":{"type":"flex","justifyContent":"right"},"ariaLabel":"<?php esc_attr_e( 'Primary', 'ipsum' ); ?>"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
