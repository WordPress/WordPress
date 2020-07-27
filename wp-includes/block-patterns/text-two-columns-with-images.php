<?php
/**
 * Two columns of text, each with an image on top block pattern.
 *
 * @package WordPress
 */

return array(
	'title'       => __( 'Two columns of text with images' ),
	'categories'  => array( 'columns' ),
	'content'     => "<!-- wp:group -->\n<div class=\"wp-block-group\"><div class=\"wp-block-group__inner-container\"><!-- wp:columns -->\n<div class=\"wp-block-columns\"><!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:image {\"sizeSlug\":\"large\"} -->\n<figure class=\"wp-block-image size-large\"><img src=\"https://s.w.org/images/core/5.5/don-quixote-02.jpg\" alt=\"\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:paragraph {\"textColor\":\"black\",\"style\":{\"typography\":{\"fontSize\":18}}} -->\n<p class=\"has-black-color has-text-color\" style=\"font-size:18px\">" . __( 'You must know, then, that the above-named gentleman whenever he was at leisure (which was mostly all the year round) gave himself up to reading books of chivalry with such ardour and avidity that he almost entirely neglected the pursuit of his field-sports, and even the management of his property; and to such a pitch did his eagerness and infatuation go that he sold many an acre of tillageland to buy books of chivalry to read, and brought home as many of them as he could get.' ) . "</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:column -->\n\n<!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:image {\"sizeSlug\":\"large\"} -->\n<figure class=\"wp-block-image size-large\"><img src=\"https://s.w.org/images/core/5.5/don-quixote-04.jpg\" alt=\"\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:paragraph {\"textColor\":\"black\",\"style\":{\"typography\":{\"fontSize\":18}}} -->\n<p class=\"has-black-color has-text-color\" style=\"font-size:18px\">" . __( 'But of all there were none he liked so well as those of the famous Feliciano de Silva\'s composition, for their lucidity of style and complicated conceits were as pearls in his sight, particularly when in his reading he came upon courtships and cartels, where he often found passages like "the reason of the unreason with which my reason is afflicted so weakens my reason that with reason I murmur at your beauty;" or again, "the high heavens render you deserving of the desert your greatness deserves."' ) . "</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:group -->",
	'description' => _x( 'Two columns of text, each with an image on top.', 'Block pattern description' ),
);
