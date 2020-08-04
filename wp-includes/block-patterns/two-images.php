<?php
/**
 * Two images side by side block pattern.
 *
 * @package WordPress
 */

return array(
	'title'       => __( 'Two images side by side' ),
	'categories'  => array( 'gallery' ),
	'description' => _x( 'An image gallery with two example images.', 'Block pattern description' ),
	'content'     => "<!-- wp:gallery {\"ids\":[null,null],\"align\":\"wide\"} -->\n<figure class=\"wp-block-gallery alignwide columns-2 is-cropped\"><ul class=\"blocks-gallery-grid\"><li class=\"blocks-gallery-item\"><figure><img src=\"https://s.w.org/images/core/5.5/don-quixote-05.jpg\" alt=\"" . __( 'An old pencil drawing of Don Quixote and Sancho Panza sitting on their horses, by Wilhelm Marstrand.' ) . '"/></figure></li><li class="blocks-gallery-item"><figure><img src="https://s.w.org/images/core/5.5/don-quixote-01.jpg" alt="' . __( 'An old pencil drawing of Don Quixote and Sancho Panza sitting on their horses, by Wilhelm Marstrand.' ) . "\"/></figure></li></ul></figure>\n<!-- /wp:gallery -->",
);
