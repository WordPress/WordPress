<?php
/**
 * Title: WooCommerce Featured Products 5-item grid
 * Slug: woocommerce-blocks/featured-products-5-item-grid
 * Categories: WooCommerce
 * Block Types: core/query/woocommerce/product-query
 */
?>
<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"800"},"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-bottom:var(--wp--preset--spacing--40);font-style:normal;font-weight:800">
	Shop new arrivals
</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":2,"query":{"perPage":"5","pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"__woocommerceAttributes":[],"__woocommerceStockStatus":["instock","onbackorder"]},"displayLayout":{"type":"flex","columns":5},"namespace":"woocommerce/product-query","align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-query alignwide">
	<!-- wp:post-template {"__woocommerceNamespace":"woocommerce/product-query/product-template"} -->

		<!-- wp:cover {"useFeaturedImage":true,"dimRatio":0,"minHeight":190,"minHeightUnit":"px","contentPosition":"top right","isDark":false,"align":"right","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} -->
		<div class="wp-block-cover alignright is-light has-custom-content-position is-position-top-right" style="margin-bottom:var(--wp--preset--spacing--40);min-height:190px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span>
			<div class="wp-block-cover__inner-container">
				<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
				<div class="wp-block-group">
					<!-- wp:woocommerce/product-sale-badge {"isDescendentOfQueryLoop":true,"style":{"border":{"width":"0px","style":"none","radius":"0px"},"typography":{"textTransform":"capitalize","fontSize":"12px","fontStyle":"normal","fontWeight":"400"}}} /-->
				</div>
				<!-- /wp:group -->
			</div>
		</div>
		<!-- /wp:cover -->

		<!-- wp:post-title {"level":3,"isLink":true,"style":{"spacing":{"margin":{"top":"0","right":"0","bottom":"0rem"}},"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->
	
		<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"left","fontSize":"small","style":{"spacing":{"margin":{"bottom":"0","top":"var:preset|spacing|20"}}}} /-->

	<!-- /wp:post-template -->

	<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
	<p></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:query -->

<!-- wp:buttons {"align":"wide","layout":{"type":"flex","verticalAlignment":"center","justifyContent":"center"}} -->
<div class="wp-block-buttons alignwide">
	<!-- wp:button {"textAlign":"center"} -->
	<div class="wp-block-button">
		<a class="wp-block-button__link has-text-align-center wp-element-button" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">
			Shop All
		</a>
	</div>
	<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
