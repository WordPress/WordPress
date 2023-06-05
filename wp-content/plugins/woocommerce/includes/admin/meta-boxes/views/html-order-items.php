<?php
/**
 * Order items HTML for meta box.
 *
 * @package WooCommerce\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Allow plugins to determine whether refunds UI should be rendered in the template.
 *
 * @since 6.4.0
 *
 * @param bool     $render_refunds If the refunds UI should be rendered.
 * @param int      $order_id       The Order ID.
 * @param WC_Order $order          The Order object.
 */
$render_refunds = (bool) apply_filters( 'woocommerce_admin_order_should_render_refunds', 0 < $order->get_total() - $order->get_total_refunded() || 0 < absint( $order->get_item_count() - $order->get_item_count_refunded() ), $order->get_id(), $order );

global $wpdb;

$payment_gateway     = wc_get_payment_gateway_by_order( $order );
$line_items          = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$discounts           = $order->get_items( 'discount' );
$line_items_fee      = $order->get_items( 'fee' );
$line_items_shipping = $order->get_items( 'shipping' );

if ( wc_tax_enabled() ) {
	$order_taxes      = $order->get_taxes();
	$tax_classes      = WC_Tax::get_tax_classes();
	$classes_options  = wc_get_product_tax_class_options();
	$show_tax_columns = count( $order_taxes ) === 1;
}
?>
<div class="woocommerce_order_items_wrapper wc-order-items-editable">
	<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
		<thead>
			<tr>
				<th class="item sortable" colspan="2" data-sort="string-ins"><?php esc_html_e( 'Item', 'woocommerce' ); ?></th>
				<?php do_action( 'woocommerce_admin_order_item_headers', $order ); ?>
				<th class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Cost', 'woocommerce' ); ?></th>
				<th class="quantity sortable" data-sort="int"><?php esc_html_e( 'Qty', 'woocommerce' ); ?></th>
				<th class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
				<?php
				if ( ! empty( $order_taxes ) ) :
					foreach ( $order_taxes as $tax_id => $tax_item ) :
						$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
						$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'woocommerce' );
						$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'woocommerce' );
						/* translators: %1$s: tax item name %2$s: tax class name  */
						$column_tip = sprintf( esc_html__( '%1$s (%2$s)', 'woocommerce' ), $tax_item['name'], $tax_class_name );
						?>
						<th class="line_tax tips" data-tip="<?php echo esc_attr( $column_tip ); ?>">
							<?php echo esc_attr( $column_label ); ?>
							<input type="hidden" class="order-tax-id" name="order_taxes[<?php echo esc_attr( $tax_id ); ?>]" value="<?php echo esc_attr( $tax_item['rate_id'] ); ?>">
							<?php if ( $order->is_editable() ) : ?>
								<a class="delete-order-tax" href="#" data-rate_id="<?php echo esc_attr( $tax_id ); ?>"></a>
							<?php endif; ?>
						</th>
						<?php
					endforeach;
				endif;
				?>
				<th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="order_line_items">
			<?php
			foreach ( $line_items as $item_id => $item ) {
				do_action( 'woocommerce_before_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );

				include __DIR__ . '/html-order-item.php';

				do_action( 'woocommerce_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );
			}
			do_action( 'woocommerce_admin_order_items_after_line_items', $order->get_id() );
			?>
		</tbody>
		<tbody id="order_fee_line_items">
			<?php
			foreach ( $line_items_fee as $item_id => $item ) {
				include __DIR__ . '/html-order-fee.php';
			}
			do_action( 'woocommerce_admin_order_items_after_fees', $order->get_id() );
			?>
		</tbody>
		<tbody id="order_shipping_line_items">
			<?php
			$shipping_methods = WC()->shipping() ? WC()->shipping()->load_shipping_methods() : array();
			foreach ( $line_items_shipping as $item_id => $item ) {
				include __DIR__ . '/html-order-shipping.php';
			}
			do_action( 'woocommerce_admin_order_items_after_shipping', $order->get_id() );
			?>
		</tbody>
		<tbody id="order_refunds">
			<?php
			$refunds = $order->get_refunds();

			if ( $refunds ) {
				foreach ( $refunds as $refund ) {
					include __DIR__ . '/html-order-refund.php';
				}
				do_action( 'woocommerce_admin_order_items_after_refunds', $order->get_id() );
			}
			?>
		</tbody>
	</table>
</div>
<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
	<?php
	$coupons = $order->get_items( 'coupon' );
	if ( $coupons ) :
		?>
		<div class="wc-used-coupons">
			<ul class="wc_coupon_list">
				<li><strong><?php esc_html_e( 'Coupon(s)', 'woocommerce' ); ?></strong></li>
				<?php
				foreach ( $coupons as $item_id => $item ) :
					$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item->get_code() ) ); // phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
					$class   = $order->is_editable() ? 'code editable' : 'code';
					?>
					<li class="<?php echo esc_attr( $class ); ?>">
						<?php if ( $post_id ) : ?>
							<?php
							$post_url = apply_filters(
								'woocommerce_admin_order_item_coupon_url',
								add_query_arg(
									array(
										'post'   => $post_id,
										'action' => 'edit',
									),
									admin_url( 'post.php' )
								),
								$item,
								$order
							);
							?>
							<a href="<?php echo esc_url( $post_url ); ?>" class="tips" data-tip="<?php echo esc_attr( wc_price( $item->get_discount(), array( 'currency' => $order->get_currency() ) ) ); ?>">
								<span><?php echo esc_html( $item->get_code() ); ?></span>
							</a>
						<?php else : ?>
							<span class="tips" data-tip="<?php echo esc_attr( wc_price( $item->get_discount(), array( 'currency' => $order->get_currency() ) ) ); ?>">
								<span><?php echo esc_html( $item->get_code() ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( $order->is_editable() ) : ?>
							<a class="remove-coupon" href="javascript:void(0)" aria-label="Remove" data-code="<?php echo esc_attr( $item->get_code() ); ?>"></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<table class="wc-order-totals">
			<tr>
				<td class="label"><?php esc_html_e( 'Items Subtotal:', 'woocommerce' ); ?></td>
				<td width="1%"></td>
				<td class="total">
					<?php echo wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
		<?php if ( 0 < $order->get_total_discount() ) : ?>
			<tr>
				<td class="label"><?php esc_html_e( 'Coupon(s):', 'woocommerce' ); ?></td>
				<td width="1%"></td>
				<td class="total">-
					<?php echo wc_price( $order->get_total_discount(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( 0 < $order->get_total_fees() ) : ?>
			<tr>
				<td class="label"><?php esc_html_e( 'Fees:', 'woocommerce' ); ?></td>
				<td width="1%"></td>
				<td class="total">
					<?php echo wc_price( $order->get_total_fees(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php do_action( 'woocommerce_admin_order_totals_after_discount', $order->get_id() ); ?>

		<?php if ( $order->get_shipping_methods() ) : ?>
			<tr>
				<td class="label"><?php esc_html_e( 'Shipping:', 'woocommerce' ); ?></td>
				<td width="1%"></td>
				<td class="total">
					<?php echo wc_price( $order->get_shipping_total(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php do_action( 'woocommerce_admin_order_totals_after_shipping', $order->get_id() ); ?>

		<?php if ( wc_tax_enabled() ) : ?>
			<?php foreach ( $order->get_tax_totals() as $code => $tax_total ) : ?>
				<tr>
					<td class="label"><?php echo esc_html( $tax_total->label ); ?>:</td>
					<td width="1%"></td>
					<td class="total">
						<?php
							// We use wc_round_tax_total here because tax may need to be round up or round down depending upon settings, whereas wc_price alone will always round it down.
							echo wc_price( wc_round_tax_total( $tax_total->amount ), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_admin_order_totals_after_tax', $order->get_id() ); ?>

		<tr>
			<td class="label"><?php esc_html_e( 'Order Total', 'woocommerce' ); ?>:</td>
			<td width="1%"></td>
			<td class="total">
				<?php echo wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</td>
		</tr>

	</table>

	<div class="clear"></div>

	<?php if ( in_array( $order->get_status(), array( 'processing', 'completed', 'refunded' ), true ) && ! empty( $order->get_date_paid() ) ) : ?>

		<table class="wc-order-totals" style="border-top: 1px solid #999; margin-top:12px; padding-top:12px">
			<tr>
				<td class="<?php echo $order->get_total_refunded() ? 'label' : 'label label-highlight'; ?>"><?php esc_html_e( 'Paid', 'woocommerce' ); ?>: <br /></td>
				<td width="1%"></td>
				<td class="total">
					<?php echo wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
			<tr>
				<td>
					<span class="description">
					<?php
					if ( $order->get_payment_method_title() ) {
						/* translators: 1: payment date. 2: payment method */
						echo esc_html( sprintf( __( '%1$s via %2$s', 'woocommerce' ), $order->get_date_paid()->date_i18n( get_option( 'date_format' ) ), $order->get_payment_method_title() ) );
					} else {
						echo esc_html( $order->get_date_paid()->date_i18n( get_option( 'date_format' ) ) );
					}
					?>
					</span>
				</td>
				<td colspan="2"></td>
			</tr>
		</table>

	<div class="clear"></div>

	<?php endif; ?>

	<?php if ( $order->get_total_refunded() ) : ?>
		<table class="wc-order-totals" style="border-top: 1px solid #999; margin-top:12px; padding-top:12px">
			<tr>
				<td class="label refunded-total"><?php esc_html_e( 'Refunded', 'woocommerce' ); ?>:</td>
				<td width="1%"></td>
				<td class="total refunded-total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>

			<?php do_action( 'woocommerce_admin_order_totals_after_refunded', $order->get_id() ); ?>

			<tr>
				<td class="label label-highlight"><?php esc_html_e( 'Net Payment', 'woocommerce' ); ?>:</td>
				<td width="1%"></td>
				<td class="total">
				<?php echo wc_price( $order->get_total() - $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>

		</table>
	<?php endif; ?>

	<div class="clear"></div>

	<table class="wc-order-totals">
		<?php do_action( 'woocommerce_admin_order_totals_after_total', $order->get_id() ); ?>
	</table>

	<div class="clear"></div>
</div>
<div class="wc-order-data-row wc-order-bulk-actions wc-order-data-row-toggle">
	<p class="add-items">
		<?php if ( $order->is_editable() ) : ?>
			<button type="button" class="button add-line-item"><?php esc_html_e( 'Add item(s)', 'woocommerce' ); ?></button>
			<?php if ( wc_coupons_enabled() ) : ?>
				<button type="button" class="button add-coupon"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
			<?php endif; ?>
		<?php else : ?>
			<span class="description"><?php echo wc_help_tip( __( 'To edit this order change the status back to "Pending payment"', 'woocommerce' ) ); ?> <?php esc_html_e( 'This order is no longer editable.', 'woocommerce' ); ?></span>
		<?php endif; ?>
		<?php if ( $render_refunds ) : ?>
			<button type="button" class="button refund-items"><?php esc_html_e( 'Refund', 'woocommerce' ); ?></button>
		<?php endif; ?>
		<?php
			// Allow adding custom buttons.
			do_action( 'woocommerce_order_item_add_action_buttons', $order );
		?>
		<?php if ( $order->is_editable() ) : ?>
			<button type="button" class="button button-primary calculate-action"><?php esc_html_e( 'Recalculate', 'woocommerce' ); ?></button>
		<?php endif; ?>
	</p>
</div>
<div class="wc-order-data-row wc-order-add-item wc-order-data-row-toggle" style="display:none;">
	<button type="button" class="button add-order-item"><?php esc_html_e( 'Add product(s)', 'woocommerce' ); ?></button>
	<button type="button" class="button add-order-fee"><?php esc_html_e( 'Add fee', 'woocommerce' ); ?></button>
	<button type="button" class="button add-order-shipping"><?php esc_html_e( 'Add shipping', 'woocommerce' ); ?></button>
	<?php if ( wc_tax_enabled() ) : ?>
		<button type="button" class="button add-order-tax"><?php esc_html_e( 'Add tax', 'woocommerce' ); ?></button>
	<?php endif; ?>
	<?php
		// Allow adding custom buttons.
		do_action( 'woocommerce_order_item_add_line_buttons', $order );
	?>
	<button type="button" class="button cancel-action"><?php esc_html_e( 'Cancel', 'woocommerce' ); ?></button>
	<button type="button" class="button button-primary save-action"><?php esc_html_e( 'Save', 'woocommerce' ); ?></button>
</div>
<?php if ( $render_refunds ) : ?>
<div class="wc-order-data-row wc-order-refund-items wc-order-data-row-toggle" style="display: none;">
	<table class="wc-order-totals">
		<?php if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) : ?>
			<tr>
				<td class="label"><label for="restock_refunded_items"><?php esc_html_e( 'Restock refunded items', 'woocommerce' ); ?>:</label></td>
				<td class="total"><input type="checkbox" id="restock_refunded_items" name="restock_refunded_items" <?php checked( apply_filters( 'woocommerce_restock_refunded_items', true ) ); ?> /></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td class="label"><?php esc_html_e( 'Amount already refunded', 'woocommerce' ); ?>:</td>
			<td class="total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
		</tr>
		<tr>
			<td class="label"><?php esc_html_e( 'Total available to refund', 'woocommerce' ); ?>:</td>
			<td class="total"><?php echo wc_price( $order->get_total() - $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
		</tr>
		<tr>
			<td class="label">
				<label for="refund_amount">
					<?php echo wc_help_tip( __( 'Refund the line items above. This will show the total amount to be refunded', 'woocommerce' ) ); ?>
					<?php esc_html_e( 'Refund amount', 'woocommerce' ); ?>:
				</label>
			</td>
			<td class="total">
				<input type="text" id="refund_amount" name="refund_amount" class="wc_input_price"
				<?php
				if ( wc_tax_enabled() ) {
					// If taxes are enabled, using this refund amount can cause issues due to taxes not being refunded also.
					// The refunds should be added to the line items, not the order as a whole.
					echo 'readonly';
				}
				?>
				/>
				<div class="clear"></div>
			</td>
		</tr>
		<tr>
			<td class="label">
				<label for="refund_reason">
					<?php echo wc_help_tip( __( 'Note: the refund reason will be visible by the customer.', 'woocommerce' ) ); ?>
					<?php esc_html_e( 'Reason for refund (optional):', 'woocommerce' ); ?>
				</label>
			</td>
			<td class="total">
				<input type="text" id="refund_reason" name="refund_reason" />
				<div class="clear"></div>
			</td>
		</tr>
	</table>
	<div class="clear"></div>
	<div class="refund-actions">
		<?php
		$refund_amount = '<span class="wc-order-refund-amount">' . wc_price( 0, array( 'currency' => $order->get_currency() ) ) . '</span>';
		$gateway_name  = false !== $payment_gateway ? ( ! empty( $payment_gateway->method_title ) ? $payment_gateway->method_title : $payment_gateway->get_title() ) : __( 'Payment gateway', 'woocommerce' );

		if ( false !== $payment_gateway && $payment_gateway->can_refund_order( $order ) ) {
			/* translators: refund amount, gateway name */
			echo '<button type="button" class="button button-primary do-api-refund">' . sprintf( esc_html__( 'Refund %1$s via %2$s', 'woocommerce' ), wp_kses_post( $refund_amount ), esc_html( $gateway_name ) ) . '</button>';
		}
		?>
		<?php /* translators: refund amount  */ ?>
		<button type="button" class="button button-primary do-manual-refund tips" data-tip="<?php esc_attr_e( 'You will need to manually issue a refund through your payment gateway after using this.', 'woocommerce' ); ?>"><?php printf( esc_html__( 'Refund %s manually', 'woocommerce' ), wp_kses_post( $refund_amount ) ); ?></button>
		<button type="button" class="button cancel-action"><?php esc_html_e( 'Cancel', 'woocommerce' ); ?></button>
		<input type="hidden" id="refunded_amount" name="refunded_amount" value="<?php echo esc_attr( $order->get_total_refunded() ); ?>" />
		<div class="clear"></div>
	</div>
</div>
<?php endif; ?>

<script type="text/template" id="tmpl-wc-modal-add-products">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1><?php esc_html_e( 'Add products', 'woocommerce' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text">Close modal panel</span>
					</button>
				</header>
				<article>
					<form action="" method="post">
						<table class="widefat">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
									<th><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
								</tr>
							</thead>
							<?php
								$row = '
									<td><select class="wc-product-search" name="item_id" data-allow_clear="true" data-display_stock="true" data-exclude_type="variable" data-placeholder="' . esc_attr__( 'Search for a product&hellip;', 'woocommerce' ) . '"></select></td>
									<td><input type="number" step="1" min="0" max="9999" autocomplete="off" name="item_qty" placeholder="1" size="4" class="quantity" /></td>';
							?>
							<tbody data-row="<?php echo esc_attr( $row ); ?>">
								<tr>
									<?php echo $row; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</tr>
							</tbody>
						</table>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php esc_html_e( 'Add', 'woocommerce' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>

<script type="text/template" id="tmpl-wc-modal-add-tax">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1><?php esc_html_e( 'Add tax', 'woocommerce' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text">Close modal panel</span>
					</button>
				</header>
				<article>
					<form action="" method="post">
						<table class="widefat">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th><?php esc_html_e( 'Rate name', 'woocommerce' ); ?></th>
									<th><?php esc_html_e( 'Tax class', 'woocommerce' ); ?></th>
									<th><?php esc_html_e( 'Rate code', 'woocommerce' ); ?></th>
									<th><?php esc_html_e( 'Rate %', 'woocommerce' ); ?></th>
								</tr>
							</thead>
						<?php
						$rates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates ORDER BY tax_rate_name LIMIT 100" );
						foreach ( $rates as $rate ) {
							echo '
									<tr>
										<td><input type="radio" id="add_order_tax_' . absint( $rate->tax_rate_id ) . '" name="add_order_tax" value="' . absint( $rate->tax_rate_id ) . '" /></td>
										<td><label for="add_order_tax_' . absint( $rate->tax_rate_id ) . '">' . esc_html( WC_Tax::get_rate_label( $rate ) ) . '</label></td>
										<td>' . ( isset( $classes_options[ $rate->tax_rate_class ] ) ? esc_html( $classes_options[ $rate->tax_rate_class ] ) : '-' ) . '</td>
										<td>' . esc_html( WC_Tax::get_rate_code( $rate ) ) . '</td>
										<td>' . esc_html( WC_Tax::get_rate_percent( $rate ) ) . '</td>
									</tr>
								';
						}
						?>
						</table>
						<?php if ( absint( $wpdb->get_var( "SELECT COUNT(tax_rate_id) FROM {$wpdb->prefix}woocommerce_tax_rates;" ) ) > 100 ) : ?>
							<p>
								<label for="manual_tax_rate_id"><?php esc_html_e( 'Or, enter tax rate ID:', 'woocommerce' ); ?></label><br/>
								<input type="number" name="manual_tax_rate_id" id="manual_tax_rate_id" step="1" placeholder="<?php esc_attr_e( 'Optional', 'woocommerce' ); ?>" />
							</p>
						<?php endif; ?>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php esc_html_e( 'Add', 'woocommerce' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
