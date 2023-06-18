<?php
/**
 * Title: Featured Category Focus
 * Slug: woocommerce-blocks/featured-category-focus
 * Categories: WooCommerce
 */
?>
<!-- wp:group {"align":"full","style":{"color":{"background":"#84bfe1"},"spacing":{"padding":{"top":"var:preset|spacing|70","right":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|70"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#84bfe1;padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--70)">
	<!-- wp:image {"id":1,"width":502,"height":335,"sizeSlug":"full","linkDestination":"none"} -->
	<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( plugins_url( 'images/pattern-placeholders/floor-home-decoration-fireplace-property-living-room.png', dirname( __FILE__ ) ) ); ?>" alt="" class="wp-image-1" width="502" height="335"/></figure>
	<!-- /wp:image -->

	<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
	<p class="has-text-align-center has-large-font-size">Announcing our newest collection</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button {"style":{"color":{"text":"#ffffff","background":"#000000"},"border":{"width":"0px","style":"none"}}} -->
		<div class="wp-block-button">
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="wp-block-button__link has-text-color has-background wp-element-button" style="border-style:none;border-width:0px;color:#ffffff;background-color:#000000">Shop now</a>
		</div>
		<!-- /wp:button --></div>
	<!-- /wp:buttons --></div>
<!-- /wp:group -->
