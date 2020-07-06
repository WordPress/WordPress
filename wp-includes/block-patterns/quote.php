<?php
/**
 * Quote block pattern.
 *
 * @package WordPress
 */

return array(
	'title'         => __( 'Quote' ),
	'content'       => "<!-- wp:quote {\"className\":\"is-style-large\"} -->\n<blockquote class=\"wp-block-quote is-style-large\"><p>" . __( '"Sir Knight, if your worship be disposed to alight, you will fail of nothing here but of a bed as for all other accommodations, you may be supplied to your mind."' ) . '</p><cite>' . __( '— Don Quixote' ) . "</cite></blockquote>\n<!-- /wp:quote -->",
	'viewportWidth' => 800,
	'categories'    => array( 'text' ),
);
