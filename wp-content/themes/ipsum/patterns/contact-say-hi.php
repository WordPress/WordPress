<?php
/**
 * Title: Contact Say Hi
 * Slug: ipsum/contact-say-hi
 * Categories: contact
 * Viewport width: 1280
 * Description: A centered contact invitation — a display heading with the reach-out link over a row of social icons.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Contact Say Hi', 'Name of the say hi contact group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50","margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Contact Say Hi Wrapper', 'Name of the say hi contact wrapper group', 'ipsum' ); ?>"},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"className":"is-style-text-display","style":{"typography":{"textAlign":"center"}}} -->
<h2 class="wp-block-heading has-text-align-center is-style-text-display"><?php echo wp_kses_post( __( 'Got questions? <br><a href="#" rel="nofollow">Feel free to reach out.</a>', 'ipsum' ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"var:preset|spacing|40","metadata":{"name":"<?php echo esc_html_x( 'S', 'Name of the spacer', 'ipsum' ); ?>"}} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:social-links {"size":"has-normal-icon-size","className":"is-style-logos-only","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-normal-icon-size is-style-logos-only"><!-- wp:social-link {"url":"#","service":"mastodon"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"feed"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
