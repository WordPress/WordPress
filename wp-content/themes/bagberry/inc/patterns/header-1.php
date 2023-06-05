<?php  

$img_dir = get_template_directory_uri() . '/assets/img';

return array(
	'name' => 'header-1',
	'title' => 'Header',
	'blockTypes' => array( 'core/template-part/header' ),
	'content' => '<!-- wp:group {"className":"hide-on-mobile","layout":{"type":"constrained"}} -->
    <div class="wp-block-group hide-on-mobile"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-center" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:column {"verticalAlignment":"center","width":"33%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:33%"><!-- wp:site-logo {"width":100,"shouldSyncIcon":false} /--></div>
    <!-- /wp:column -->
    
    <!-- wp:column {"verticalAlignment":"center","width":"34%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:34%"><!-- wp:navigation {"ref":16,"overlayMenu":"never","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"},"typography":{"letterSpacing":"-0.015em"}},"fontSize":"medium"} /--></div>
    <!-- /wp:column -->
    
    <!-- wp:column {"verticalAlignment":"center","width":"33%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:33%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"right"}} -->
    <div class="wp-block-group"><!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search products…","width":250,"widthUnit":"px","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"query":{"post_type":"product"},"style":{"border":{"radius":"21px"}}} /-->
    
    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
    <div class="wp-block-group"><!-- wp:image {"id":219,"width":25,"height":24,"sizeSlug":"full","linkDestination":"custom"} -->
    <figure class="wp-block-image size-full is-resized"><a href="/my-account/"><img src="https://storage.googleapis.com/agni_patterns_bucket/bagberry/myaccount.png" alt="" class="wp-image-219" width="25" height="24"/></a></figure>
    <!-- /wp:image -->
    
    <!-- wp:woocommerce/mini-cart {"addToCartBehaviour":"open_drawer","hasHiddenPrice":true} /--></div>
    <!-- /wp:group --></div>
    <!-- /wp:group --></div>
    <!-- /wp:column --></div>
    <!-- /wp:columns --></div>
    <!-- /wp:group -->
    
    <!-- wp:group {"className":"hide-on-desktop","layout":{"type":"constrained"}} -->
    <div class="wp-block-group hide-on-desktop"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-center" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:column {"verticalAlignment":"center","width":"100%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:100%"><!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
    <div class="wp-block-group"><!-- wp:group {"layout":{"type":"constrained","justifyContent":"left"}} -->
    <div class="wp-block-group"><!-- wp:site-logo {"width":100,"shouldSyncIcon":false} /--></div>
    <!-- /wp:group -->
    
    <!-- wp:group {"layout":{"type":"constrained","contentSize":"360px"}} -->
    <div class="wp-block-group"><!-- wp:navigation {"ref":16,"layout":{"type":"flex","justifyContent":"left"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"fontSize":"medium"} /--></div>
    <!-- /wp:group --></div>
    <!-- /wp:group --></div>
    <!-- /wp:column -->
    
    <!-- wp:column {"verticalAlignment":"center","width":"100%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:100%"><!-- wp:group {"style":{"spacing":{}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"right"}} -->
    <div class="wp-block-group"><!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search products…","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"query":{"post_type":"product"},"style":{"border":{"radius":"21px"}}} /-->
    
    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
    <div class="wp-block-group"><!-- wp:image {"id":219,"width":25,"height":24,"sizeSlug":"full","linkDestination":"custom"} -->
    <figure class="wp-block-image size-full is-resized"><a href="/my-account/"><img src="https://storage.googleapis.com/agni_patterns_bucket/bagberry/myaccount.png" alt="" class="wp-image-219" width="25" height="24"/></a></figure>
    <!-- /wp:image -->
    
    <!-- wp:woocommerce/mini-cart {"addToCartBehaviour":"open_drawer","hasHiddenPrice":true} /--></div>
    <!-- /wp:group --></div>
    <!-- /wp:group --></div>
    <!-- /wp:column --></div>
    <!-- /wp:columns --></div>
    <!-- /wp:group -->
    
    <!-- wp:group {"layout":{"inherit":true,"type":"constrained"}} -->
    <div class="wp-block-group"><!-- wp:separator {"align":"full","backgroundColor":"accent-border","className":"is-style-wide"} -->
    <hr class="wp-block-separator alignfull has-text-color has-accent-border-color has-alpha-channel-opacity has-accent-border-background-color has-background is-style-wide"/>
    <!-- /wp:separator --></div>
    <!-- /wp:group -->',
	'categories'    => array( 'featured', 'theme-patterns' ),
	'viewportWidth' => 1680
);
