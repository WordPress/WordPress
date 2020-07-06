<?php
/**
 * Two images side by side block pattern.
 *
 * @package WordPress
 */

return array(
	'title'      => __( 'Two images side by side' ),
	'categories' => array( 'gallery' ),
	'content'    => "<!-- wp:gallery {\"ids\":[null,null]} -->\n<figure class=\"wp-block-gallery columns-2 is-cropped\"><ul class=\"blocks-gallery-grid\"><li class=\"blocks-gallery-item\"><figure><img src=\"https://s.w.org/images/core/5.3/Glacial_lakes,_Bhutan.jpg\" alt=\"\" data-id=\"\"/></figure></li><li class=\"blocks-gallery-item\"><figure><img src=\"https://s.w.org/images/core/5.3/Sediment_off_the_Yucatan_Peninsula.jpg\" alt=\"\" data-id=\"\"/></figure></li></ul></figure>\n<!-- /wp:gallery -->",
);
