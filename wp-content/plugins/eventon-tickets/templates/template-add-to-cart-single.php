<?php

	// single product add to cart button for woocommerce
	
?>

<div class='tx_single'>
<p itemprop="price" class="price tx_price_line"><?php echo __('Price','eventon').': '. $product->get_price_html(); ?></p>
<form class='tx_orderonline_single' data-producttype='single' method="post" enctype='multipart/form-data'>

	<div class='tx_orderonline_add_cart'>
		<?php
	 		if ( ! $product->is_sold_individually() )
	 			woocommerce_quantity_input( array(
	 				'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
	 				'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
	 			), $product );
	 	?>
	 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />
	 	<button data-product_id='<?php echo $woo_product_id;?>' id='cart_btn' class="evcal_btn single_add_to_cart_button button alt"><?php echo apply_filters('single_add_to_cart_text', __( 'Add to cart', 'woocommerce' ), $product->product_type); ?></button>
	 	<div class="clear"></div>
 	</div>
</form>
</div>