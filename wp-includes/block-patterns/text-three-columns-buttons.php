<?php
/**
 * Three columns of text, each a button at the bottom block pattern.
 *
 * @package WordPress
 */

return array(
	'title'         => __( 'Three columns of text with buttons' ),
	'categories'    => array( 'columns' ),
	'content'       => "<!-- wp:group {\"align\":\"wide\"} -->\n<div class=\"wp-block-group alignwide\"><div class=\"wp-block-group__inner-container\"><!-- wp:columns {\"align\":\"wide\"} -->\n<div class=\"wp-block-columns alignwide\"><!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:paragraph {\"textColor\":\"black\"} -->\n<p class=\"has-black-color has-text-color\">" . __( 'Which treats of the character and pursuits of the famous Don Quixote of La Mancha.' ) . "</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"borderRadius\":50,\"style\":{\"color\":{\"text\":\"#ba0c49\"}},\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link has-text-color\" style=\"border-radius:50px;color:#ba0c49\">Chapter One</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column -->\n\n<!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:paragraph {\"textColor\":\"black\"} -->\n<p class=\"has-black-color has-text-color\">" . __( 'Which treats of the first sally the ingenious Don Quixote made from home.' ) . "</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"borderRadius\":50,\"style\":{\"color\":{\"text\":\"#ba0c49\"}},\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link has-text-color\" style=\"border-radius:50px;color:#ba0c49\">Chapter Two</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column -->\n\n<!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:paragraph {\"textColor\":\"black\"} -->\n<p class=\"has-black-color has-text-color\">" . __( 'Wherein is related the droll way in which Don Quixote had himself dubbed a knight.' ) . "</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"borderRadius\":50,\"style\":{\"color\":{\"text\":\"#ba0c49\"}},\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link has-text-color\" style=\"border-radius:50px;color:#ba0c49\">Chapter Three</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:group -->",
	'viewportWidth' => 1000,
	'description'   => _x( 'Three small columns of text, each with an outlined button with rounded corners at the bottom.', 'Block pattern description' ),
);
