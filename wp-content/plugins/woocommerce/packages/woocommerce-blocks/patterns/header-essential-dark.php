<?php
/**
 * Title: WooCommerce Essential Header | Dark
 * Slug: woocommerce-blocks/header-essential-dark
 * Categories: WooCommerce
 * Block Types: core/template-part/header
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"2%","bottom":"32px","left":"2%","top":"32px"},"margin":{"top":"0px","bottom":"0px"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"black","textColor":"white","className":"sticky-header has-background-color","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignfull sticky-header has-background-color has-white-color has-black-background-color has-text-color has-background has-link-color" style="margin-top:0px;margin-bottom:0px;padding-top:32px;padding-right:2%;padding-bottom:32px;padding-left:2%">
	<!-- wp:group {"style":{"spacing":{"blockGap":"40px"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-group">
		<!-- wp:site-logo {"shouldSyncIcon":false} /-->
		<!-- wp:search {"label":"Search","showLabel":false,"buttonText":"Search","buttonUseIcon":true} /-->
		<!-- wp:navigation {"textColor":"background","layout":{"type":"flex","justifyContent":"center"}} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"8px"},"typography":{"fontSize":"18px"},"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
	<div class="wp-block-group" style="font-size:18px">
		<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"alt","iconClass":"wc-block-customer-account__account-icon"} /-->
		<!-- wp:woocommerce/mini-cart {"textColor":"background","style":{"typography":{"fontSize":"14px"}}} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
