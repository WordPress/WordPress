<?php
/**
 * Title: WooCommerce Large Footer
 * Slug: woocommerce-blocks/footer-large
 * Categories: WooCommerce
 * Block Types: core/template-part/footer
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"32px","right":"4%","bottom":"32px","left":"4%"},"blockGap":"40px"}}} -->
<div class="wp-block-group alignfull" style="padding-top:32px;padding-right:4%;padding-bottom:32px;padding-left:4%">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":"16px"}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"45%","style":{"spacing":{"padding":{"right":"50px"}}}} -->
		<div class="wp-block-column" style="padding-right:50px;flex-basis:45%">
			<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group">
				<!-- wp:site-logo /-->

				<!-- wp:spacer {"height":"30px"} -->
				<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->

				<!-- wp:heading {"level":5} -->
				<h5>Join the community</h5>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p>Learn about new products and discounts!</p>
				<!-- /wp:paragraph -->

				<!-- wp:spacer {"height":"20px"} -->
				<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->
			</div>
			<!-- /wp:group -->

			<!-- wp:social-links {"size":"has-small-icon-size","className":"is-style-logos-only"} -->
			<ul class="wp-block-social-links has-small-icon-size is-style-logos-only">
				<!-- wp:social-link {"url":"d","service":"facebook"} /-->
				<!-- wp:social-link {"url":"d","service":"twitter"} /-->
				<!-- wp:social-link {"url":"d","service":"instagram"} /-->
			</ul>
			<!-- /wp:social-links -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"20%","style":{"spacing":{"padding":{"top":"0px"}}}} -->
		<div class="wp-block-column" style="padding-top:0px;flex-basis:20%">
			<!-- wp:navigation {"layout":{"type":"flex","orientation":"vertical"}} /--></div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"top","width":"20%","style":{"spacing":{"blockGap":"16px"}}} -->
		<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:20%">
			<!-- wp:navigation {"layout":{"type":"flex","orientation":"vertical"}} -->
			<!-- /wp:navigation -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"top","width":"20%","style":{"spacing":{"blockGap":"16px"}}} -->
		<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:20%">
			<!-- wp:woocommerce/customer-account {"displayStyle":"text_only"} /-->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"blockGap":"10px"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center"}} -->
	<div class="wp-block-group alignfull" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
		<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
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
