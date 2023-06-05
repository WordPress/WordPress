<?php
/**
 * Title: WooCommerce Minimal Header | Dark
 * Slug: woocommerce-blocks/minimal-header-dark
 * Categories: WooCommerce
 * Block Types: core/template-part/header
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"2%","bottom":"16px","left":"2%","top":"16px"},"margin":{"top":"0px","bottom":"0px"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"black","textColor":"white","className":"sticky-header","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignfull sticky-header has-background-color has-white-color has-black-background-color has-text-color has-background has-link-color" style="margin-top:0px;margin-bottom:0px;padding-top:16px;padding-right:2%;padding-bottom:16px;padding-left:2%">
	<!-- wp:group {"style":{"spacing":{"blockGap":"40px"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-group">
		<!-- wp:site-logo {"shouldSyncIcon":false} /-->
		<!-- wp:site-title /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"8px"},"typography":{"fontSize":"13px"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
	<div class="wp-block-group" style="font-size:13px">
		<!-- wp:group {"style":{"spacing":{"blockGap":"16px"}},"layout":{"type":"flex","flexWrap":"nowrap"},"fontSize":"small"} -->
		<div class="wp-block-group has-small-font-size">
			<!-- wp:navigation {"textColor":"background","overlayMenu":"always","layout":{"type":"flex","justifyContent":"center"}} /-->
			<!-- wp:separator {"className":"is-style-wide"} -->
			<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
			<!-- /wp:separator -->
		</div>
		<!-- /wp:group -->

		<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconClass":"wc-block-customer-account__account-icon"} /-->

		<!-- wp:woocommerce/mini-cart {"textColor":"background","style":{"typography":{"fontSize":"13px"}}} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
