<?php
/**
 * Title: Hero Product - Split
 * Slug: woocommerce-blocks/hero-product-split
 * Categories: WooCommerce
 */
?>

<!-- wp:media-text {"align":"full","mediaPosition":"right","mediaType":"image","mediaSizeSlug":"full","imageFill":false,"style":{"color":{"background":"#000000","text":"#ffffff"}}} -->
<div class="wp-block-media-text alignfull has-media-on-the-right is-stacked-on-mobile has-text-color has-background" style="color:#ffffff;background-color:#000000">
	<div class="wp-block-media-text__content">
		<!-- wp:heading {"style":{"color":{"text":"#ffffff"}}} -->
		<h2 class="wp-block-heading has-text-color" style="color:#ffffff;"><?php esc_html_e( 'Get cozy this fall with knit sweaters', 'woocommerce' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:buttons {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} -->
		<div class="wp-block-buttons" style="margin-bottom:var(--wp--preset--spacing--40)">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Shop now</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>

	<figure class="wp-block-media-text__media">
		<img src="<?php echo esc_url( plugins_url( 'images/pattern-placeholders/pattern-fashion-clothing-outerwear-wool-scarf.png', dirname( __FILE__ ) ) ); ?>" alt="<?php esc_attr_e( 'Woman in red, black, and white plaid hoodie.', 'woocommerce' ); ?>" />
	</figure>
</div>
<!-- /wp:media-text -->
