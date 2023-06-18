<?php
/**
 * Title: Product Collections: Featured Collection
 * Slug: woocommerce-blocks/product-collections-featured-collection
 * Categories: WooCommerce
 * Block Types: core/query/woocommerce/product-query
 */
?>

<!-- wp:columns {"align":"wide","style":{"color":{"background":"#333333"},"spacing":{"padding":{"top":"1.3rem","right":"1.3rem","bottom":"1.3rem","left":"1.3rem"}}},"textColor":"white"} -->
<div class="wp-block-columns alignwide has-white-color has-text-color has-background" style="background-color:#333333;padding-top:1.3rem;padding-right:1.3rem;padding-bottom:1.3rem;padding-left:1.3rem">
	<!-- wp:column {"verticalAlignment":"center","width":"33.33%","style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-column is-vertically-aligned-center" style="padding-right:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40);flex-basis:33.33%">
		<!-- wp:heading {"textAlign":"center","textColor":"background","fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-text-align-center has-background-color has-text-color has-x-large-font-size">
			<?php echo __( 'This week\'s popular products', 'woocommerce' ) ?>
		</h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column {"verticalAlignment":"center","width":"66.66%"} -->
	<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.66%">
		<!-- wp:query {"queryId":5,"query":{"perPage":"3","pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"__woocommerceAttributes":[],"__woocommerceStockStatus":["instock","onbackorder"]},"displayLayout":{"type":"flex","columns":3},"namespace":"woocommerce/product-query"} -->
		<div class="wp-block-query">
			<!-- wp:post-template {"__woocommerceNamespace":"woocommerce/product-query/product-template"} -->
				<!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"0"},"color":{"background":"#484848"},"border":{"radius":{"topLeft":"0px","topRight":"0px","bottomLeft":"4px","bottomRight":"4px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
				<div class="wp-block-group has-background" style="border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-left-radius:4px;border-bottom-right-radius:4px;background-color:#484848;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">
					<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"border":{"radius":{"topLeft":"4px","topRight":"4px","bottomLeft":"0px","bottomRight":"0px"},"top":{"color":"#ffffff","style":"solid","width":"3px"},"right":{"color":"#ffffff","style":"solid","width":"3px"},"bottom":{"color":"#ffffff","style":"solid","width":"3px"},"left":{"color":"#ffffff","style":"solid","width":"3px"}}}} /-->

					<!-- wp:group {"style":{"spacing":{"blockGap":"0","padding":{"top":"20px","right":"20px","left":"20px","bottom":"10px"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left","verticalAlignment":"center"}} -->
					<div class="wp-block-group has-link-color" style="padding-top:20px;padding-right:20px;padding-bottom:10px;padding-left:20px">
						<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textColor":"white","fontFamily":"system-font","style":{"typography":{"fontSize":"16px","fontStyle":"normal","fontWeight":"700"}}} /-->
						<!-- wp:post-title {"level":3,"isLink":true,"style":{"spacing":{"margin":{"bottom":"0.75rem","top":"0"}},"typography":{"fontStyle":"normal","fontWeight":"300","fontSize":"16px","textDecoration":"none"}},"textColor":"white","__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->
			<!-- /wp:post-template -->
		</div>
		<!-- /wp:query -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
