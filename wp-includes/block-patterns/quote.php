<?php
/**
 * Quote block pattern.
 *
 * @package WordPress
 */

return array(
	'title'         => __( 'Quote' ),
	'content'       => "<!-- wp:group -->\n<div class=\"wp-block-group\"><div class=\"wp-block-group__inner-container\"><!-- wp:image {\"align\":\"center\",\"width\":164,\"height\":164,\"sizeSlug\":\"large\",\"className\":\"is-style-rounded\"} -->\n<div class=\"wp-block-image is-style-rounded\"><figure class=\"aligncenter size-large is-resized\"><img src=\"https://s.w.org/images/core/5.5/don-quixote-03.jpg\" alt=\"" . __( 'Pencil drawing of Don Quixote' ) . "\" width=\"164\" height=\"164\"/></figure></div>\n<!-- /wp:image -->\n\n<!-- wp:quote {\"align\":\"center\",\"className\":\"is-style-large\"} -->\n<blockquote class=\"wp-block-quote has-text-align-center is-style-large\"><p>" . __( '"Do you see over yonder, friend Sancho, thirty or forty hulking giants? I intend to do battle with them and slay them."' ) . '</p><cite>' . __( '— Don Quixote' ) . "</cite></blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:separator {\"className\":\"is-style-dots\"} -->\n<hr class=\"wp-block-separator is-style-dots\"/>\n<!-- /wp:separator --></div></div>\n<!-- /wp:group -->",
	'viewportWidth' => 800,
	'categories'    => array( 'text' ),
	'description'   => _x( 'A quote and citation with an image above, and a separator at the bottom.', 'Block pattern description' ),
);
