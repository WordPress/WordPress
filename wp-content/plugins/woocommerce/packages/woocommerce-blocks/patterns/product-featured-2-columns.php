<?php
/**
 * Title: Featured Products 2 Columns
 * Slug: woocommerce-blocks/featured-products-2-cols
 * Categories: WooCommerce
 */
?>

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide">
	<!-- wp:column {"width":"66.66%"} -->
	<div class="wp-block-column" style="flex-basis:66.66%">
		<!-- wp:query {"queryId":7,"query":{"perPage":"4","pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":false,"__woocommerceAttributes":[],"__woocommerceStockStatus":["instock","onbackorder"]},"displayLayout":{"type":"flex","columns":2},"namespace":"woocommerce/product-query"} -->
		<div class="wp-block-query">
			<!-- wp:post-template {"__woocommerceNamespace":"woocommerce/product-query/product-template"} -->
			<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","isDescendentOfQueryLoop":true,"style":{"spacing":{"margin":{"bottom":"24px","top":"0"}}}} /-->

			<!-- wp:columns {"verticalAlignment":"bottom"} -->
			<div class="wp-block-columns are-vertically-aligned-bottom">
				<!-- wp:column {"verticalAlignment":"bottom"} -->
				<div class="wp-block-column is-vertically-aligned-bottom">
					<!-- wp:post-title {"textAlign":"left","level":3,"isLink":true,"style":{"spacing":{"margin":{"bottom":"0rem","top":"0"}}},"fontSize":"medium","__woocommerceNamespace":"woocommerce/product-query/product-title"} /--></div>
				<!-- /wp:column -->

				<!-- wp:column {"verticalAlignment":"bottom"} -->
				<div class="wp-block-column is-vertically-aligned-bottom">
					<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"right","fontSize":"small","style":{"spacing":{"margin":{"bottom":"0rem","top":"0"}}}} /--></div>
				<!-- /wp:column --></div>
			<!-- /wp:columns -->

			<!-- /wp:post-template -->

			<!-- wp:query-no-results -->
			<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
			<p></p>
			<!-- /wp:paragraph -->
			<!-- /wp:query-no-results -->
		</div>
		<!-- /wp:query -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column {"width":"33.33%"} -->
	<div class="wp-block-column" style="flex-basis:33.33%">
		<!-- wp:heading {"level":4} -->
		<h4 class="wp-block-heading"><strong>Fan Favorites</strong></h4>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>Get ready to start the season right. All the fan favorites in one place at the best price.</p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"width":50} -->
			<div class="wp-block-button has-custom-width wp-block-button__width-50">
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="wp-block-button__link wp-element-button">Shop All</a>
			</div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
