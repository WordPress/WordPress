<?php
/**
 * Title: Header Default
 * Slug: ipsum/header-default
 * Categories: header
 * Block Types: core/template-part/header
 * Viewport width: 1280
 * Description: The theme’s default header — site title and tagline on the left, navigation on the right.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Header Wrapper', 'Name of the group wrapping the header', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|60","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Header Row', 'Name of the header row group', 'ipsum' ); ?>"},"align":"full","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group alignfull"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Tagline', 'Name of the group holding the site title and tagline', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"},"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:site-title {"level":0} /-->

<!-- wp:site-tagline {"style":{"layout":{"selfStretch":"fill","flexSize":null}}} /--></div>
<!-- /wp:group -->

<!-- wp:navigation {"overlay":"navigation-overlay","icon":"menu","fontSize":"small","ariaLabel":"<?php esc_attr_e( 'Primary', 'ipsum' ); ?>"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
