<?php
/**
 * Large header block pattern.
 *
 * @package WordPress
 */

return array(
	'title'         => __( 'Large header' ),
	'content'       => "<!-- wp:group {\"align\":\"wide\"} -->\n<div class=\"wp-block-group alignwide\"><div class=\"wp-block-group__inner-container\"><!-- wp:cover {\"gradient\":\"pale-ocean\",\"align\":\"wide\"} -->\n<div class=\"wp-block-cover alignwide has-background-dim has-background-gradient has-pale-ocean-gradient-background\"><div class=\"wp-block-cover__inner-container\"><!-- wp:paragraph {\"align\":\"center\",\"placeholder\":\"Write title…\",\"textColor\":\"black\",\"style\":{\"typography\":{\"fontSize\":72}}} -->\n<p class=\"has-text-align-center has-black-color has-text-color\" style=\"font-size:72px\"><strong>" . __( 'Large header' ) . "</strong></p>\n<!-- /wp:paragraph --></div></div>\n<!-- /wp:cover --></div></div>\n<!-- /wp:group -->",
	'viewportWidth' => 1000,
	'categories'    => array( 'header' ),
);
