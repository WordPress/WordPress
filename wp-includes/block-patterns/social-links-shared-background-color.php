<?php
/**
 * Social links with a shared background color.
 *
 * @package WordPress
 * @since 5.8.0
 * @deprecated 6.7.0 This pattern is deprecated. Please use the Social Links block instead.
 */

return array(
	'title'         => _x( 'Social links with a shared background color', 'Block pattern title' ),
	'categories'    => array( 'buttons' ),
	'blockTypes'    => array( 'core/social-links' ),
	'viewportWidth' => 500,
	'content'       => '<!-- wp:social-links {"customIconColor":"#ffffff","iconColorValue":"#ffffff","customIconBackgroundColor":"#3962e3","iconBackgroundColorValue":"#3962e3","className":"has-icon-color"} -->
						<ul class="wp-block-social-links has-icon-color has-icon-background-color"><!-- wp:social-link {"url":"https://wordpress.org","service":"wordpress"} /-->
						<!-- wp:social-link {"url":"#","service":"chain"} /-->
						<!-- wp:social-link {"url":"#","service":"mail"} /--></ul>
						<!-- /wp:social-links -->',
);
