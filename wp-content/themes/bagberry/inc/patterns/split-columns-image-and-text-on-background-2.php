<?php

return array(
	'name' => 'split-columns-image-and-text-on-background-2',
	'title' => 'Split Columns Image and Text on Background 2',
	'content' => '<!-- wp:columns -->
    <div class="wp-block-columns"><!-- wp:column {"width":"50%"} -->
    <div class="wp-block-column" style="flex-basis:50%"><!-- wp:image {"id":37,"sizeSlug":"large","linkDestination":"none"} -->
    <figure class="wp-block-image size-large"><img src="https://storage.googleapis.com/agni_patterns_bucket/bagberry/product-single-1-909x1024.jpeg" alt="" class="wp-image-37"/></figure>
    <!-- /wp:image --></div>
    <!-- /wp:column -->
    
    <!-- wp:column {"width":"50%","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","right":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|60"}}},"backgroundColor":"accent"} -->
    <div class="wp-block-column has-accent-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--60);flex-basis:50%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","contentSize":"500px","justifyContent":"left"}} -->
    <div class="wp-block-group"><!-- wp:heading {"level":3,"textColor":"lite"} -->
    <h3 class="has-lite-color has-text-color">About the designer</h3>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph {"textColor":"lite","fontSize":"large"} -->
    <p class="has-lite-color has-text-color has-large-font-size">Phasellus finibus, libero vel pretium tincidunt, neque enim vestibulum diam, in porttitor dui eros sed magna. Curabitur sed luctus turpis.</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:buttons -->
    <div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"lite","textColor":"accent","style":{"border":{"radius":"30px"}}} -->
    <div class="wp-block-button"><a class="wp-block-button__link has-accent-color has-lite-background-color has-text-color has-background wp-element-button" style="border-radius:30px">More from designer</a></div>
    <!-- /wp:button --></div>
    <!-- /wp:buttons --></div>
    <!-- /wp:group --></div>
    <!-- /wp:column --></div>
    <!-- /wp:columns -->',
'categories'    => array( 'featured', 'theme-patterns' ),
'viewportWidth' => 1680
);
