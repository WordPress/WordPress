<?php
/**
 * Large header and a button block pattern.
 *
 * @package WordPress
 */

return array(
	'title'         => __( 'Large header with a heading and a button ' ),
	'content'       => "<!-- wp:cover {\"minHeight\":575,\"minHeightUnit\":\"px\",\"customGradient\":\"linear-gradient(135deg,rgb(249,72,72) 1%,rgb(179,22,22) 100%)\",\"contentPosition\":\"center center\",\"align\":\"wide\"} -->\n<div class=\"wp-block-cover alignwide has-background-dim has-background-gradient is-position-center-center\" style=\"background:linear-gradient(135deg,rgb(249,72,72) 1%,rgb(179,22,22) 100%);min-height:575px\"><div class=\"wp-block-cover__inner-container\"><!-- wp:columns {\"align\":\"wide\"} -->\n<div class=\"wp-block-columns alignwide\"><!-- wp:column {\"width\":12} -->\n<div class=\"wp-block-column\" style=\"flex-basis:12%\"><!-- wp:spacer -->\n<div style=\"height:100px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer --></div>\n<!-- /wp:column -->\n\n<!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:spacer -->\n<div style=\"height:100px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer -->\n\n<!-- wp:paragraph {\"align\":\"left\",\"placeholder\":\"" . __( 'Write title…' ) . "\",\"style\":{\"typography\":{\"fontSize\":68,\"lineHeight\":\"1.2\"},\"color\":{\"text\":\"#fffffa\"}}} -->\n<p class=\"has-text-align-left has-text-color\" style=\"line-height:1.2;font-size:68px;color:#fffffa\"><strong>" . __( 'Thou hast seen<br>nothing yet' ) . "</strong></p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"borderRadius\":3,\"style\":{\"color\":{\"background\":\"#fffffa\",\"text\":\"#00000a\"}}} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-text-color has-background\" style=\"border-radius:3px;background-color:#fffffa;color:#00000a\">" . __( 'Read now' ) . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n\n<!-- wp:spacer -->\n<div style=\"height:100px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":12} -->\n<div class=\"wp-block-column\" style=\"flex-basis:12%\"><!-- wp:spacer -->\n<div style=\"height:100px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:cover -->",
	'viewportWidth' => 1000,
	'categories'    => array( 'header' ),
	'description'   => _x( 'A large hero section with a bright gradient background, a big heading and a filled button.', 'Block pattern description' ),
);
