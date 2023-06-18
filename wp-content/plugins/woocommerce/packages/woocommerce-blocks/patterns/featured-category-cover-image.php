<?php
/**
 * Title: Featured Category Cover Image
 * Slug: woocommerce-blocks/featured-category-cover-image
 * Categories: WooCommerce
 */
?>
<!-- wp:cover {"url":"<?php echo esc_url( plugins_url( 'images/pattern-placeholders/wood-leather-fur-shop-jeans-shelf.png', dirname( __FILE__ ) ) ); ?>","id":1,"dimRatio":0,"focalPoint":{"x":0,"y":0},"contentPosition":"top left","align":"wide","style":{"spacing":{"padding":{"top":"2em","right":"2.25em","bottom":"2.25em","left":"2.25em"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignwide has-custom-content-position is-position-top-left" style="padding-top:2em;padding-right:2.25em;padding-bottom:2.25em;padding-left:2.25em">
	<span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span>
	<img class="wp-block-cover__image-background wp-image-1" alt="" src="<?php echo esc_url( plugins_url( 'images/pattern-placeholders/wood-leather-fur-shop-jeans-shelf.png', dirname( __FILE__ ) ) ); ?>" style="object-position:0% 0%" data-object-fit="cover" data-object-position="0% 0%"/>

	<div class="wp-block-cover__inner-container">
		<!-- wp:paragraph {"align":"left","placeholder":"Write title…","style":{"typography":{"lineHeight":"1.5","fontSize":"2.2em","textColor":"background"},"color":{"text":"#ffffff"},"spacing":{"margin":{"bottom":"0px","top":"0px"}}}} -->
		<p class="has-text-align-left has-text-color" style="color:#ffffff;margin-top:0px;margin-bottom:0px;font-size:2.2em;line-height:1.5"><strong>100% natural denim</strong></p>
		<!-- /wp:paragraph -->

	<!-- wp:paragraph {"style":{"typography":{"lineHeight":"3"}},"textColor":"background"} -->
	<p style="line-height:3">Only the finest goes into our products. You deserve it.</p>
	<!-- /wp:paragraph -->

		<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"30px"}}}} -->
		<div class="wp-block-buttons" style="margin-top:30px">
			<!-- wp:button {"style":{"border":{"width":"0px","style":"none"},"color":{"text":"#000000","background":"#ffffff"}},"className":"is-style-fill"} -->
			<div class="wp-block-button is-style-fill"><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="wp-block-button__link has-text-color has-background wp-element-button" style="border-style:none;border-width:0px;color:#000000;background-color:#ffffff">Shop jeans</a></div>
			<!-- /wp:button --></div>
		<!-- /wp:buttons --></div>
</div>
<!-- /wp:cover -->
