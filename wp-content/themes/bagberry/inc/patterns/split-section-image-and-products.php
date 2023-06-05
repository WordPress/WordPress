<?php

return array(
	'name' => 'split-section-image-and-products',
	'title' => 'Split Section Image and Products',
	'content' => '<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"0"}}},"backgroundColor":"accent-background","layout":{"type":"constrained"}} -->
    <div class="wp-block-group alignfull has-accent-background-background-color has-background" style="margin-top:0"><!-- wp:spacer {"height":"4vh"} -->
    <div style="height:4vh" aria-hidden="true" class="wp-block-spacer"></div>
    <!-- /wp:spacer -->
    
    <!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
    <div class="wp-block-group" style="margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3} -->
    <h3>New bags for all your occasions</h3>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph -->
    <p><a href="#">Explore all bags</a></p>
    <!-- /wp:paragraph --></div>
    <!-- /wp:group -->
    
    <!-- wp:columns -->
    <div class="wp-block-columns"><!-- wp:column -->
    <div class="wp-block-column"><!-- wp:woocommerce/product-new {"columns":2,"rows":2} /--></div>
    <!-- /wp:column -->
    
    <!-- wp:column -->
    <div class="wp-block-column"><!-- wp:image {"id":45,"width":760,"height":950,"sizeSlug":"large","linkDestination":"none"} -->
    <figure class="wp-block-image size-large is-resized"><img src="https://storage.googleapis.com/agni_patterns_bucket/bagberry/lookbook-image-1-819x1024.jpeg" alt="" class="wp-image-45" width="760" height="950"/></figure>
    <!-- /wp:image --></div>
    <!-- /wp:column --></div>
    <!-- /wp:columns -->
    
    <!-- wp:spacer {"height":"4vh"} -->
    <div style="height:4vh" aria-hidden="true" class="wp-block-spacer"></div>
    <!-- /wp:spacer --></div>
    <!-- /wp:group -->',
'categories'    => array( 'featured', 'theme-patterns' ),
'viewportWidth' => 1680
);
