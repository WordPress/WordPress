<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="total_row shipping_row" data-order_item_id="<?php echo $item_id; ?>">
	<p class="wide">
		<input type="text" name="shipping_method_title[<?php echo $item_id ? $item_id : 'new][]'; ?>]" placeholder="<?php _e( 'Label', 'woocommerce' ); ?>" value="<?php echo esc_attr( $shipping_title ); ?>" class="first" />
		<input type="hidden" name="shipping_method_id[<?php echo $item_id ? $item_id : 'new][]'; ?>]" value="<?php echo esc_attr( $item_id ); ?>" />
	</p>
	<p class="first">
		<select name="shipping_method[<?php echo $item_id ? $item_id : 'new][]'; ?>]" class="first">
			<optgroup label="<?php _e( 'Shipping Method', 'woocommerce' ); ?>">
				<option value=""><?php _e( 'N/A', 'woocommerce' ); ?></option>
				<?php
					$found_method 	= false;

					foreach ( $shipping_methods as $method ) {

						if ( strpos( $chosen_method, $method->id ) === 0 )
							$value = $chosen_method;
						else
							$value = $method->id;

						echo '<option value="' . esc_attr( $value ) . '" ' . selected( $chosen_method == $value, true, false ) . '>' . esc_html( $method->get_title() ) . '</option>';

						if ( $chosen_method == $value )
							$found_method = true;
					}

					if ( ! $found_method && ! empty( $chosen_method ) ) {
						echo '<option value="' . esc_attr( $chosen_method ) . '" selected="selected">' . __( 'Other', 'woocommerce' ) . '</option>';
					} else {
						echo '<option value="other">' . __( 'Other', 'woocommerce' ) . '</option>';
					}
				?>
			</optgroup>
		</select>
	</p>
	<p class="last">
		<input type="text" class="shipping_cost wc_input_price" name="shipping_cost[<?php echo $item_id ? $item_id : 'new][]'; ?>]" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $shipping_cost ) ); ?>" />
	</p>
	<?php do_action( 'woocommerce_admin_order_totals_after_shipping_item', $item_id ); ?>
	<a href="#" class="delete_total_row">&times;</a>
	<div class="clear"></div>
</div>
