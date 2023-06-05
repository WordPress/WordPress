<?php
/**
 * Title: WooCommerce Simple Footer
 * Slug: woocommerce-blocks/footer-simple
 * Categories: WooCommerce
 * Block Types: core/template-part/footer
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"32px","right":"48px","bottom":"32px","left":"48px"},"blockGap":"40px"}}} -->
<div class="wp-block-group alignfull" style="padding-top:32px;padding-right:48px;padding-bottom:32px;padding-left:48px">
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"50%"} -->
		<div class="wp-block-column" style="flex-basis:50%">
			<!-- wp:group {"style":{"spacing":{"blockGap":"32px"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center"}} -->
			<div class="wp-block-group">
				<!-- wp:site-logo /-->
				<!-- wp:woocommerce/customer-account /-->
				<!-- wp:navigation -->
				<!-- /wp:navigation -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
			<!-- wp:social-links {"className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"center"}} -->
			<ul class="wp-block-social-links is-style-logos-only">
				<!-- wp:social-link {"service":"facebook"} /-->
				<!-- wp:social-link {"service":"instagram"} /-->
				<!-- wp:social-link {"service":"twitter"} /-->
			</ul>
			<!-- /wp:social-links -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"blockGap":"12px"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center"}} -->
	<div class="wp-block-group alignfull" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
		<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"12px"}}} -->
			<p style="font-size:12px">@ 2022</p>
			<!-- /wp:paragraph -->
			<!-- wp:site-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"400","fontSize":"12px"}}} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
		<p style="font-size:14px"><em>Built with <a href="https://woocommerce.com/">WooCommerce</a> </em></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
