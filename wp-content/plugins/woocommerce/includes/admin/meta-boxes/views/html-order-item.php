<?php
/**
 * Shows an order item
 *
 * @package WooCommerce\Admin
 * @var WC_Order_Item $item The item being displayed
 * @var int $item_id The id of the item being displayed
 */

defined( 'ABSPATH' ) || exit;

$product      = $item->get_product();
$product_link = $product ? admin_url( 'post.php?post=' . $item->get_product_id() . '&action=edit' ) : '';
$thumbnail    = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
$row_class    = apply_filters( 'woocommerce_admin_html_order_item_class', ! empty( $class ) ? $class : '', $item, $order );
?>
<tr class="item <?php echo esc_attr( $row_class ); ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
	<td class="thumb">
		<?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post( $thumbnail ) . '</div>'; ?>
	</td>
	<td class="name" data-sort-value="<?php echo esc_attr( $item->get_name() ); ?>">
		<?php
		echo $product_link ? '<a href="' . esc_url( $product_link ) . '" class="wc-order-item-name">' . wp_kses_post( $item->get_name() ) . '</a>' : '<div class="wc-order-item-name">' . wp_kses_post( $item->get_name() ) . '</div>';

		if ( $product && $product->get_sku() ) {
			echo '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'woocommerce' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>';
		}

		if ( $item->get_variation_id() ) {
			echo '<div class="wc-order-item-variation"><strong>' . esc_html__( 'Variation ID:', 'woocommerce' ) . '</strong> ';
			if ( 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
				echo esc_html( $item->get_variation_id() );
			} else {
				/* translators: %s: variation id */
				printf( esc_html__( '%s (No longer exists)', 'woocommerce' ), esc_html( $item->get_variation_id() ) );
			}
			echo '</div>';
		}
		?>
		<input type="hidden" class="order_item_id" name="order_item_id[]" value="<?php echo esc_attr( $item_id ); ?>" />
		<input type="hidden" name="order_item_tax_class[<?php echo absint( $item_id ); ?>]" value="<?php echo esc_attr( $item->get_tax_class() ); ?>" />

		<?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, $product ); ?>
		<?php require __DIR__ . '/html-order-item-meta.php'; ?>
		<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, $product ); ?>
	</td>

	<?php do_action( 'woocommerce_admin_order_item_values', $product, $item, absint( $item_id ) ); ?>

	<td class="item_cost" width="1%" data-sort-value="<?php echo esc_attr( $order->get_item_subtotal( $item, false, true ) ); ?>">
		<div class="view">
			<?php
			echo wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</td>
	<td class="quantity" width="1%">
		<div class="view">
			<?php
			echo '<small class="times">&times;</small> ' . esc_html( $item->get_quantity() );

			$refunded_qty = -1 * $order->get_qty_refunded_for_item( $item_id );

			if ( $refunded_qty ) {
				echo '<small class="refunded">' . esc_html( $refunded_qty * -1 ) . '</small>';
			}
			?>
		</div>
		<?php
			$step = apply_filters( 'woocommerce_quantity_input_step', '1', $product );

			/**
			* Filter to change the product quantity stepping in the order editor of the admin area.
			*
			* @since   5.8.0
			* @param   string      $step    The current step amount to be used in the quantity editor.
			* @param   WC_Product  $product The product that is being edited.
			* @param   string      $context The context in which the quantity editor is shown, 'edit' or 'refund'.
			*/
			$step_edit   = apply_filters( 'woocommerce_quantity_input_step_admin', $step, $product, 'edit' );
			$step_refund = apply_filters( 'woocommerce_quantity_input_step_admin', $step, $product, 'refund' );

			/**
			* Filter to change the product quantity minimum in the order editor of the admin area.
			*
			* @since   5.8.0
			* @param   string      $step    The current minimum amount to be used in the quantity editor.
			* @param   WC_Product  $product The product that is being edited.
			* @param   string      $context The context in which the quantity editor is shown, 'edit' or 'refund'.
			*/
			$min_edit   = apply_filters( 'woocommerce_quantity_input_min_admin', '0', $product, 'edit' );
			$min_refund = apply_filters( 'woocommerce_quantity_input_min_admin', '0', $product, 'refund' );
		?>
		<div class="edit" style="display: none;">
			<input type="number" step="<?php echo esc_attr( $step_edit ); ?>" min="<?php echo esc_attr( $min_edit ); ?>" autocomplete="off" name="order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" value="<?php echo esc_attr( $item->get_quantity() ); ?>" data-qty="<?php echo esc_attr( $item->get_quantity() ); ?>" size="4" class="quantity" />
		</div>
		<div class="refund" style="display: none;">
			<input type="number" step="<?php echo esc_attr( $step_refund ); ?>" min="<?php echo esc_attr( $min_refund ); ?>" max="<?php echo absint( $item->get_quantity() ); ?>" autocomplete="off" name="refund_order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" size="4" class="refund_order_item_qty" />
		</div>
	</td>
	<td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( $item->get_total() ); ?>">
		<div class="view">
			<?php
			echo wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( $item->get_subtotal() !== $item->get_total() ) {
				/* translators: %s: discount amount */
				echo '<span class="wc-order-item-discount">' . sprintf( esc_html__( '%s discount', 'woocommerce' ), wc_price( wc_format_decimal( $item->get_subtotal() - $item->get_total(), '' ), array( 'currency' => $order->get_currency() ) ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			$refunded = -1 * $order->get_total_refunded_for_item( $item_id );

			if ( $refunded ) {
				echo '<small class="refunded">' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
		<div class="edit" style="display: none;">
			<div class="split-input">
				<div class="input">
					<label><?php esc_attr_e( 'Before discount', 'woocommerce' ); ?></label>
					<input type="text" name="line_subtotal[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->get_subtotal() ) ); ?>" class="line_subtotal wc_input_price" data-subtotal="<?php echo esc_attr( wc_format_localized_price( $item->get_subtotal() ) ); ?>" />
				</div>
				<div class="input">
					<label><?php esc_attr_e( 'Total', 'woocommerce' ); ?></label>
					<input type="text" name="line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->get_total() ) ); ?>" class="line_total wc_input_price" data-tip="<?php esc_attr_e( 'After pre-tax discounts.', 'woocommerce' ); ?>" data-total="<?php echo esc_attr( wc_format_localized_price( $item->get_total() ) ); ?>" />
				</div>
			</div>
		</div>
		<div class="refund" style="display: none;">
			<input type="text" name="refund_line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="refund_line_total wc_input_price" />
		</div>
	</td>

	<?php
	$tax_data = wc_tax_enabled() ? $item->get_taxes() : false;

	if ( $tax_data ) {
		foreach ( $order_taxes as $tax_item ) {
			$tax_item_id       = $tax_item->get_rate_id();
			$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
			$tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : '';

			?>
			<td class="line_tax" width="1%">
				<div class="view">
					<?php
					if ( '' !== $tax_item_total ) {
						echo wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						echo '&ndash;';
					}

					$refunded = -1 * $order->get_tax_refunded_for_item( $item_id, $tax_item_id );

					if ( $refunded ) {
						echo '<small class="refunded">' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<div class="edit" style="display: none;">
					<div class="split-input">
						<div class="input">
							<label><?php esc_attr_e( 'Before discount', 'woocommerce' ); ?></label>
							<input type="text" name="line_subtotal_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" class="line_subtotal_tax wc_input_price" data-subtotal_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
						</div>
						<div class="input">
							<label><?php esc_attr_e( 'Total', 'woocommerce' ); ?></label>
							<input type="text" name="line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" class="line_tax wc_input_price" data-total_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
						</div>
					</div>
				</div>
				<div class="refund" style="display: none;">
					<input type="text" name="refund_line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="refund_line_tax wc_input_price" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
				</div>
			</td>
			<?php
		}
	}
	?>
	<td class="wc-order-edit-line-item" width="1%">
		<div class="wc-order-edit-line-item-actions">
			<?php if ( $order->is_editable() ) : ?>
				<a class="edit-order-item tips" href="#" data-tip="<?php esc_attr_e( 'Edit item', 'woocommerce' ); ?>"></a><a class="delete-order-item tips" href="#" data-tip="<?php esc_attr_e( 'Delete item', 'woocommerce' ); ?>"></a>
			<?php endif; ?>
		</div>
	</td>
</tr>
