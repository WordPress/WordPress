<?php
/**
 * Title: Footer with 3 menus
 * Slug: woocommerce-blocks/footer-with-3-menus
 * Categories: WooCommerce
 * Block Types: core/template-part/footer
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"40px","padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
	<!-- wp:columns -->
	<div class="wp-block-columns are-vertically-aligned-top">
		<!-- wp:column {"verticalAlignment":"top","width":"70%"} -->
		<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:70%">
			<!-- wp:group {"style":{"spacing":{"blockGap":"32px"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"top"}} -->
			<div class="wp-block-group"><!-- wp:site-logo {"shouldSyncIcon":false} /-->

				<!-- wp:columns {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":{"top":"var:preset|spacing|70","left":"var:preset|spacing|70"}}}} -->
				<div class="wp-block-columns" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">
					<!-- wp:column -->
					<div class="wp-block-column">
						<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"10px"}}} /-->
					</div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column">
						<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"10px"}}} /-->
					</div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column">
						<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"10px"}}} /-->
					</div>
					<!-- /wp:column -->
				</div>
				<!-- /wp:columns -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"top","style":{"spacing":{"blockGap":"60px"}},"layout":{"type":"default"}} -->
		<div class="wp-block-column is-vertically-aligned-top">
			<!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"5px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"right","verticalAlignment":"space-between"}} -->
			<div class="wp-block-group" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">
				<!-- wp:group {"style":{"border":{"top":{"width":"1px","style":"solid"},"right":{"width":"1px","style":"solid"},"bottom":{"width":"1px","style":"solid"},"left":{"width":"1px","style":"solid"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group" style="border-top-style:solid;border-top-width:1px;border-right-style:solid;border-right-width:1px;border-bottom-style:solid;border-bottom-width:1px;border-left-style:solid;border-left-width:1px">
					<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search our store","width":280,"widthUnit":"px","buttonText":"Search","buttonUseIcon":true,"query":{"post_type":"product"},"style":{"border":{"top":{"width":"0px","style":"none"},"right":{"width":"0px","style":"none"},"bottom":{"width":"0px","style":"none"},"left":{"width":"0px","style":"none"}}},"backgroundColor":"background","textColor":"foreground"} /-->
				</div>
				<!-- /wp:group -->

				<!-- wp:site-title {"level":0,"textAlign":"right","style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"14px"},"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"anchor":""} /-->

				<!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":"12px"}}} -->
				<p class="has-text-align-right" style="font-size:12px">
					<?php echo sprintf(
						__( 'Powered by %s with %s', 'woocommerce' ),
						'<a href="https://wordpress.org">WordPress</a>',
						'<a href="https://woocommerce.com">WooCommerce</a>'
					) ?>
				</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
