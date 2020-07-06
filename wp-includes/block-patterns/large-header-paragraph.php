<?php
/**
 * Large header and a paragraph block pattern.
 *
 * @package WordPress
 */

return array(
	'title'         => __( 'Large header and a paragraph' ),
	'content'       => "<!-- wp:group {\"align\":\"wide\"} -->\n<div class=\"wp-block-group alignwide\"><div class=\"wp-block-group__inner-container\"><!-- wp:cover {\"customGradient\":\"radial-gradient(rgb(122,220,180) 0%,rgb(0,208,130) 100%)\",\"contentPosition\":\"center center\",\"align\":\"wide\"} -->\n<div class=\"wp-block-cover alignwide has-background-dim has-background-gradient is-position-center-center\" style=\"background:radial-gradient(rgb(122,220,180) 0%,rgb(0,208,130) 100%)\"><div class=\"wp-block-cover__inner-container\"><!-- wp:paragraph {\"align\":\"center\",\"placeholder\":\"Write title…\",\"textColor\":\"black\",\"style\":{\"typography\":{\"fontSize\":90,\"lineHeight\":\"1.2\"}}} -->\n<p class=\"has-text-align-center has-black-color has-text-color\" style=\"line-height:1.2;font-size:90px\"><strong>" . __( '"Sir Knight"' ) . "</strong></p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"align\":\"center\",\"textColor\":\"black\"} -->\n<p class=\"has-text-align-center has-black-color has-text-color\">" . __( 'If your worship be disposed to alight, you will fail of nothing here but of a bed as for all other accommodations, you may be supplied to your mind.' ) . "</p>\n<!-- /wp:paragraph --></div></div>\n<!-- /wp:cover --></div></div>\n<!-- /wp:group -->",
	'viewportWidth' => 1000,
	'categories'    => array( 'header' ),
);
