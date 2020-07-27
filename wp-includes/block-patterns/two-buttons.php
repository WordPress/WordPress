<?php
/**
 * Two Buttons block pattern.
 *
 * @package WordPress
 */

return array(
	'title'         => __( 'Two buttons' ),
	'content'       => "<!-- wp:buttons {\"align\":\"center\"} -->\n<div class=\"wp-block-buttons aligncenter\"><!-- wp:button {\"borderRadius\":2,\"style\":{\"color\":{\"background\":\"#ba0c49\",\"text\":\"#fffffa\"}}} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-text-color has-background\" style=\"border-radius:2px;background-color:#ba0c49;color:#fffffa\">" . __( 'Download now' ) . "</a></div>\n<!-- /wp:button -->\n\n<!-- wp:button {\"borderRadius\":2,\"style\":{\"color\":{\"text\":\"#ba0c49\"}},\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link has-text-color\" style=\"border-radius:2px;color:#ba0c49\">" . __( 'About Cervantes' ) . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->",
	'viewportWidth' => 500,
	'categories'    => array( 'buttons' ),
	'description'   => _x( 'Two buttons, one filled and one outlined, side by side.', 'Block pattern description' ),
);
