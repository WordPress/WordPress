<?php
/**
 * Three Buttons block pattern.
 *
 * @package WordPress
 */

return array(
	'title'         => __( 'Three buttons' ),
	'content'       => "<!-- wp:buttons {\"align\":\"center\"} -->\n<div class=\"wp-block-buttons aligncenter\"><!-- wp:button {\"borderRadius\":50,\"style\":{\"color\":{\"gradient\":\"linear-gradient(135deg,rgb(135,9,53) 0%,rgb(179,22,22) 100%)\",\"text\":\"#fffffa\"}}} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-text-color has-background\" style=\"border-radius:50px;background:linear-gradient(135deg,rgb(135,9,53) 0%,rgb(179,22,22) 100%);color:#fffffa\">" . __( 'About Cervantes' ) . "</a></div>\n<!-- /wp:button -->\n\n<!-- wp:button {\"borderRadius\":50,\"style\":{\"color\":{\"gradient\":\"linear-gradient(317deg,rgb(135,9,53) 0%,rgb(179,22,22) 100%)\",\"text\":\"#fffffa\"}}} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-text-color has-background\" style=\"border-radius:50px;background:linear-gradient(317deg,rgb(135,9,53) 0%,rgb(179,22,22) 100%);color:#fffffa\">" . __( 'Contact us' ) . "</a></div>\n<!-- /wp:button -->\n\n<!-- wp:button {\"borderRadius\":50,\"style\":{\"color\":{\"gradient\":\"linear-gradient(42deg,rgb(135,9,53) 0%,rgb(179,22,22) 100%)\",\"text\":\"#fffffa\"}}} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-text-color has-background\" style=\"border-radius:50px;background:linear-gradient(42deg,rgb(135,9,53) 0%,rgb(179,22,22) 100%);color:#fffffa\">" . __( 'Books' ) . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->",
	'viewportWidth' => 600,
	'categories'    => array( 'buttons' ),
	'description'   => _x( 'Three filled buttons with rounded corners, side by side.', 'Block pattern description' ),
);
