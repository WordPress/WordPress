<?php

	wp_enqueue_script( 'wc-add-to-cart-variation' );

	// Load the template
	/*
    woocommerce_get_template( 'single-product/add-to-cart/variable.php', array(
            'available_variations'  => $product->get_available_variations(),
            'attributes'            => $product->get_variation_attributes(),
            'selected_attributes'   => $product->get_variation_default_attributes()
        ) );
*/
	$attributes = $product->get_variation_attributes();
	$available_variations = $product->get_available_variations();
	$selected_attributes = $product->get_variation_default_attributes();
?>
<div class='evotx_orderonline_trigger'>
	<p class='evotx_price_line'><?php echo __('Price','foodpress').' '.$product->get_price_html(); ?></p>
	<a class='evcal_btn evotx_show_variations'>Order Now</a>
</div>
<form style='display:none' class="variations_form cart evotx_orderonline_variable" method="post" enctype='multipart/form-data' data-product_id="<?php echo $woo_product_id; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
	
		<table class="variations" cellspacing="0">
		<tbody>
	<?php
		$loop = 0;foreach ($attributes as $name => $options ):
		$loop++; 
	?>
		
		<tr>
			<td class='label'><label for="<?php echo sanitize_title($name); ?>"><?php echo wc_attribute_label( $name ); ?></label></td>
			<td class="value"><select id="<?php echo esc_attr( sanitize_title($name) ); ?>" name="attribute_<?php echo sanitize_title($name); ?>">
						<option value=""><?php echo __( 'Choose an option', 'woocommerce' ) ?>&hellip;</option>

					<?php
							if ( is_array( $options ) ) {

								if ( empty( $_POST ) )
									$selected_value = ( isset( $selected_attributes[ sanitize_title( $name ) ] ) ) ? $selected_attributes[ sanitize_title( $name ) ] : '';
								else
									$selected_value = isset( $_POST[ 'attribute_' . sanitize_title( $name ) ] ) ? $_POST[ 'attribute_' . sanitize_title( $name ) ] : '';

								// Get terms if this is a taxonomy - ordered
								if ( taxonomy_exists( $name ) ) {

									$orderby = $woocommerce->attribute_orderby( $name );

									switch ( $orderby ) {
										case 'name' :
											$args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
										break;
										case 'id' :
											$args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => false );
										break;
										case 'menu_order' :
											$args = array( 'menu_order' => 'ASC' );
										break;
									}

									$terms = get_terms( $name, $args );

									foreach ( $terms as $term ) {
										if ( ! in_array( $term->slug, $options ) )
											continue;

										echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $selected_value, $term->slug, false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
									}
								} else {

									foreach ( $options as $option ) {
										echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
									}

								}
							}
						?>

				</select> <?php
						if ( sizeof($attributes) == $loop )
							echo '<a class="reset_variations" href="#reset">' . __( 'Clear selection', 'woocommerce' ) . '</a>';
					?></td>
				</tr>
	        <?php endforeach;?>
		</tbody>
	</table>
		
		<div class="single_variation_wrap evotx_orderonline_add_cart" style="display:none;">
			<div class="single_variation"></div>
			<div class="variations_button">
				<input type="hidden" name="variation_id" value="" />
				<?php woocommerce_quantity_input(array(), $product); ?>
				
				<button data-product_id='<?php echo $woo_product_id;?>' id='cart_btn_v' type="submit" class="evcal_btn single_add_to_cart_button button alt variable_add_to_cart_button"><?php  _e( 'Add to cart', 'woocommerce' ); ?></button>
				<div class="clear"></div>
			</div>
		</div>
		<div>
			<input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>" />
			<input type="hidden" name="product_id" value="<?php echo esc_attr( $woo_product_id ); ?>" />
		</div>

	</form>

