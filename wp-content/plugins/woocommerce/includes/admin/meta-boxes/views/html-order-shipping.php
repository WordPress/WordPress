<?php
/**
 * Shows a shipping line
 *
 * @package WooCommerce\Admin
 *
 * @var object $item The item being displayed
 * @var int $item_id The id of the item being displayed
 *
 * @package WooCommerce\Admin\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="shipping <?php echo ( ! empty( $class ) ) ? esc_attr( $class ) : ''; ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
	<td class="thumb"><div></div></td>

	<td class="name">
		<div class="view">
			<?php echo esc_html( $item->get_name() ? $item->get_name() : __( 'Shipping', 'woocommerce' ) ); ?>
		</div>
		<div class="edit" style="display: none;">
			<input type="hidden" name="shipping_method_id[]" value="<?php echo esc_attr( $item_id ); ?>" />
			<input type="text" class="shipping_method_name" placeholder="<?php esc_attr_e( 'Shipping name', 'woocommerce' ); ?>" name="shipping_method_title[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->get_name() ); ?>" />
			<select class="shipping_method" name="shipping_method[<?php echo esc_attr( $item_id ); ?>]">
				<optgroup label="<?php esc_attr_e( 'Shipping method', 'woocommerce' ); ?>">
					<option value=""><?php esc_html_e( 'N/A', 'woocommerce' ); ?></option>
					<?php
					$found_method = false;

					foreach ( $shipping_methods as $method ) {
						$is_active = $item->get_method_id() === $method->id;

						echo '<option value="' . esc_attr( $method->id ) . '" ' . selected( true, $is_active, false ) . '>' . esc_html( $method->get_method_title() ) . '</option>';

						if ( $is_active ) {
							$found_method = true;
						}
					}

					if ( ! $found_method && $item->get_method_id() ) {
						echo '<option value="' . esc_attr( $item->get_method_id() ) . '" selected="selected">' . esc_html__( 'Other', 'woocommerce' ) . '</option>';
					} else {
						echo '<option value="other">' . esc_html__( 'Other', 'woocommerce' ) . '</option>';
					}
					?>
				</optgroup>
			</select>
		</div>

		<?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, null ); ?>
		<?php require __DIR__ . '/html-order-item-meta.php'; ?>
		<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, null ); ?>
	</td>

	<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>

	<td class="item_cost" width="1%">&nbsp;</td>
	<td class="quantity" width="1%">&nbsp;</td>

	<td class="line_cost" width="1%">
		<div class="view">
			<?php
			echo wp_kses_post( wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) ) );
			$refunded = -1 * $order->get_total_refunded_for_item( $item_id, 'shipping' );
			if ( $refunded ) {
				echo wp_kses_post( '<small class="refunded">' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>' );
			}
			?>
		</div>
		<div class="edit" style="display: none;">
			<input type="text" name="shipping_cost[<?php echo esc_attr( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->get_total() ) ); ?>" class="line_total wc_input_price" />
		</div>
		<div class="refund" style="display: none;">
			<input type="text" name="refund_line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="refund_line_total wc_input_price" />
		</div>
	</td>

	<?php
	$tax_data = $item->get_taxes();
	if ( $tax_data && wc_tax_enabled() ) {
		foreach ( $order_taxes as $tax_item ) {
			$tax_item_id    = $tax_item->get_rate_id();
			$tax_item_total = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
			?>
			<td class="line_tax" width="1%">
				<div class="view">
					<?php
					echo wp_kses_post( ( '' !== $tax_item_total ) ? wc_price( $tax_item_total, array( 'currency' => $order->get_currency() ) ) : '&ndash;' );
					$refunded = -1 * $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'shipping' );
					if ( $refunded ) {
						echo wp_kses_post( '<small class="refunded">' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>' );
					}
					?>
				</div>
				<div class="edit" style="display: none;">
					<input type="text" name="shipping_taxes[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo ( isset( $tax_item_total ) ) ? esc_attr( wc_format_localized_price( $tax_item_total ) ) : ''; ?>" class="line_tax wc_input_price" />
				</div>
				<div class="refund" style="display: none;">
					<input type="text" name="refund_line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="refund_line_tax wc_input_price" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
				</div>
			</td>
			<?php
		}
	}
	?>
	<td class="wc-order-edit-line-item">
		<?php if ( $order->is_editable() ) : ?>
			<div class="wc-order-edit-line-item-actions">
				<a class="edit-order-item" href="#"></a><a class="delete-order-item" href="#"></a>
			</div>
		<?php endif; ?>
	</td>
</tr>
