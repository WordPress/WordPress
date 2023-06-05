<?php
/**
 * Title: WooCommerce Large Header
 * Slug: woocommerce-blocks/header-large
 * Categories: WooCommerce
 * Block Types: core/template-part/header
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"0px","padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}}},"className":"has-link-color","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull has-link-color" style="padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
	<!-- wp:group {"style":{"spacing":{"blockGap":"1rem"}},"className":"has-small-font-size","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group has-small-font-size">
		<!-- wp:search {"label":"Search","showLabel":false,"width":100,"widthUnit":"%","buttonText":"Search","buttonUseIcon":true} /-->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group">
			<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","fontSize":"small","iconClass":"wc-block-customer-account__account-icon"} /-->
			<!-- wp:woocommerce/mini-cart {"hasHiddenPrice":true,"style":{"typography":{"fontSize":"14px"}}} /-->
		</div>
		<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"16px","padding":{"top":"1rem","left":"0px","bottom":"2rem"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group" style="padding-top:1rem;padding-bottom:2rem;padding-left:0px">
	<!-- wp:site-logo {"shouldSyncIcon":true} /-->
	<!-- wp:site-title /-->
</div>
<!-- /wp:group -->

<!-- wp:navigation {"layout":{"type":"flex","justifyContent":"center"}} /-->
</div>
<!-- /wp:group -->
