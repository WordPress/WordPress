<?php
/**
 * Title: Membership Call to Action
 * Slug: ipsum/cta-membership
 * Categories: call-to-action
 * Keywords: subscribe, newsletter, join, signup
 * Viewport width: 1280
 * Description: A membership invitation on a tinted card. It has the site screenshot cover above the pitch and two buttons.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Membership Call to Action', 'Name of the membership call to action group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Membership Call to Action Wrapper', 'Name of the membership card group', 'ipsum' ); ?>"},"className":"is-style-crop-contents","style":{"border":{"radius":"10px"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"accent-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-crop-contents has-accent-5-background-color has-background" style="border-radius:10px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:cover {"url":"<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/ipsum-polygons.webp' ) ); ?>","alt":"<?php echo esc_attr_x( 'Rows of glossy black hollow cones catching the light.', 'Alt text for the polygons image', 'ipsum' ); ?>","dimRatio":0,"overlayColor":"contrast","isUserOverlayColor":true,"focalPoint":{"x":0.5,"y":0},"contentPosition":"bottom center","sizeSlug":"full","metadata":{"name":"<?php echo esc_html_x( 'Site Screenshot', 'Name of the site screenshot cover', 'ipsum' ); ?>"},"style":{"dimensions":{"aspectRatio":"16/9"},"border":{"radius":"10px"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover has-custom-content-position is-position-bottom-center" style="border-radius:10px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><img class="wp-block-cover__image-background size-full" alt="<?php echo esc_attr_x( 'Rows of glossy black hollow cones catching the light.', 'Alt text for the polygons image', 'ipsum' ); ?>" src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/ipsum-polygons.webp' ) ); ?>" style="object-position:50% 0%" data-object-fit="cover" data-object-position="50% 0%"/><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container"></div></div>
<!-- /wp:cover -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Copy', 'Name of the membership copy group', 'ipsum' ); ?>"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Paragraph', 'Name of the Title and Paragraph element', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"2-x-large"} -->
<h2 class="wp-block-heading has-2-x-large-font-size"><?php esc_html_e( 'Become a friend of the blog', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Friends get every essay, the unposted monthly letter, first seats when the blog exits, a print from the archive now and then—and the good feeling of running a quiet space. Cancel anytime. No hard feelings.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"dimensions":{"width":"var:preset|dimension|100"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Join Us', 'ipsum' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline","style":{"dimensions":{"width":"var:preset|dimension|100"}}} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'View plans', 'ipsum' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
