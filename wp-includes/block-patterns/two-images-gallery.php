<?php
/**
 * Two images side by side.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Two images side by side', 'Block pattern title' ),
	'categories'  => array( 'gallery' ),
	'content'     => '<!-- wp:gallery {"ids":[null,null],"linkTo":"none","align":"wide"} -->
	<figure class="wp-block-gallery alignwide columns-2 is-cropped"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><img src="https://s.w.org/images/core/5.8/nature-above-01.jpg" alt="' . esc_attr__( 'An aerial view of waves crashing against a shore.' ) . '" data-full-url="https://s.w.org/images/core/5.8/nature-above-01.jpg" data-link="#" /></figure></li><li class="blocks-gallery-item"><figure><img src="https://s.w.org/images/core/5.8/nature-above-02.jpg" alt="' . esc_attr__( 'An aerial view of a field. A road runs through the upper right corner.' ) . '" data-full-url="https://s.w.org/images/core/5.8/nature-above-02.jpg" data-link="#" /></figure></li></ul></figure>
	<!-- /wp:gallery -->',
	'description' => _x( 'Two images side by side', 'Block pattern description' ),
);
