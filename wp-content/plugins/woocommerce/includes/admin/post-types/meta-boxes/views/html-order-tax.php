<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="total_row tax_row" data-order_item_id="<?php echo $item_id; ?>">
	<p class="wide">
		<select name="order_taxes_rate_id[<?php echo $item_id ? $item_id : 'new][]'; ?>]">
			<optgroup label="<?php _e( 'Tax Rate', 'woocommerce' ); ?>">
				<option value=""><?php _e( 'N/A', 'woocommerce' ); ?></option>
				<?php foreach( $tax_codes as $tax_id => $tax_code ) : ?>
					<option value="<?php echo $tax_id; ?>" <?php selected( $tax_id, isset( $item['rate_id'] ) ? $item['rate_id'] : '' ); ?>><?php echo esc_html( urldecode( $tax_code ) ); ?></option>
				<?php endforeach; ?>
			</optgroup>
		</select>
		<input type="hidden" name="order_taxes_id[<?php echo $item_id ? $item_id : 'new][]'; ?>]" value="<?php echo esc_attr( $item_id ); ?>" />
	</p>
	<p class="first">
		<label><?php _e( 'Sales Tax', 'woocommerce' ) ?></label>
		<input type="text" class="order_taxes_amount wc_input_price" name="order_taxes_amount[<?php echo $item_id ? $item_id : 'new][]'; ?>]" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php if ( isset( $item['tax_amount'] ) ) echo esc_attr( wc_format_localized_price( $item['tax_amount'] ) ); ?>" />
	</p>
	<p class="last">
		<label><?php _e( 'Shipping Tax', 'woocommerce' ) ?></label>
		<input type="text" class="order_taxes_shipping_amount wc_input_price" name="order_taxes_shipping_amount[<?php echo $item_id ? $item_id : 'new][]'; ?>]" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php if ( isset( $item['shipping_tax_amount'] ) ) echo esc_attr( wc_format_localized_price( $item['shipping_tax_amount'] ) ); ?>" />
	</p>
	<a href="#" class="delete_total_row">&times;</a>
	<div class="clear"></div>
</div>
