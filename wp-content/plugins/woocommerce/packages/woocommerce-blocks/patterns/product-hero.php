<?php
/**
 * Title: Product Hero
 * Slug: woocommerce-blocks/product-hero
 * Categories: WooCommerce
 */
?>
<!-- wp:group {"align":"wide","style":{"color":{"background":"#6b7ba8"},"spacing":{"blockGap":"0","padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}}},"textColor":"background","layout":{"type":"constrained","contentSize":"100%","justifyContent":"left"}} -->
<div class="wp-block-group alignwide has-background-color has-text-color has-background" style="background-color:#6b7ba8;padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em">
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"40%"} -->
		<div class="wp-block-column" style="flex-basis:40%"><!-- wp:image {"id":1,"sizeSlug":"full","linkDestination":"none"} -->
			<figure class="wp-block-image size-full"><img src="<?php echo esc_url( plugins_url( 'images/pattern-placeholders/leather-guitar-typewriter-red-gadget-sofa.png', dirname( __FILE__ ) ) ); ?>" alt="" class="wp-image-1"/></figure>
			<!-- /wp:image --></div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"60%","style":{"spacing":{"padding":{"top":"3em","left":"0em","bottom":"3em"}}}} -->
		<div class="wp-block-column" style="padding-top:3em;padding-bottom:3em;padding-left:0em;flex-basis:60%">
			<!-- wp:heading {"style":{"typography":{"lineHeight":"0"}},"textColor":"base","fontSize":"large"} -->
			<h2 class="wp-block-heading has-base-color has-text-color has-large-font-size" style="line-height:0">Mini 5 Dreamer</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"1em"}},"textColor":"base"} -->
			<p class="has-base-color has-text-color" style="font-size:1em"><strong>$1,999</strong></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"style":{"typography":{"fontSize":"0.8em"},"spacing":{"padding":{"left":"2em","right":"2em"}},"color":{"text":"#ffffff","background":"#000000"},"border":{"width":"0px","style":"none"}}} -->
				<div class="wp-block-button has-custom-font-size" style="font-size:0.8em">
					<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="wp-block-button__link has-text-color has-background wp-element-button" style="border-style:none;border-width:0px;color:#ffffff;background-color:#000000;padding-right:2em;padding-left:2em">Add to cart</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.8em"},"color":{"text":"#ffffff"}}} -->
			<p class="has-text-color" style="color:#ffffff;font-size:0.8em">The Bluetooth keyboard, a striking homage to classic typewriters with its round, tactile keys and vintage aesthetic, merges nostalgia with modern connectivity for a unique typing experience.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		</div>
	<!-- /wp:columns -->
	</div>
<!-- /wp:group -->
