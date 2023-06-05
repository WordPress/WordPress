<?php
/**
 * Show order refund
 *
 * @var object $refund The refund object.
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$who_refunded = new WP_User( $refund->get_refunded_by() );
?>
<tr class="refund <?php echo ( ! empty( $class ) ) ? esc_attr( $class ) : ''; ?>" data-order_refund_id="<?php echo esc_attr( $refund->get_id() ); ?>">
	<td class="thumb"><div></div></td>

	<td class="name">
		<?php
		if ( $who_refunded->exists() ) {
			printf(
				/* translators: 1: refund id 2: refund date 3: username */
				esc_html__( 'Refund #%1$s - %2$s by %3$s', 'woocommerce' ),
				esc_html( $refund->get_id() ),
				esc_html( wc_format_datetime( $refund->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) ) ),
				sprintf(
					'<abbr class="refund_by" title="%1$s">%2$s</abbr>',
					/* translators: 1: ID who refunded */
					sprintf( esc_attr__( 'ID: %d', 'woocommerce' ), absint( $who_refunded->ID ) ),
					esc_html( $who_refunded->display_name )
				)
			);
		} else {
			printf(
				/* translators: 1: refund id 2: refund date */
				esc_html__( 'Refund #%1$s - %2$s', 'woocommerce' ),
				esc_html( $refund->get_id() ),
				esc_html( wc_format_datetime( $refund->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) ) )
			);
		}
		?>
		<?php if ( $refund->get_reason() ) : ?>
			<p class="description"><?php echo esc_html( $refund->get_reason() ); ?></p>
		<?php endif; ?>
		<input type="hidden" class="order_refund_id" name="order_refund_id[]" value="<?php echo esc_attr( $refund->get_id() ); ?>" />

		<?php do_action( 'woocommerce_after_order_refund_item_name', $refund ); ?>
	</td>

	<?php do_action( 'woocommerce_admin_order_item_values', null, $refund, $refund->get_id() ); ?>

	<td class="item_cost" width="1%">&nbsp;</td>
	<td class="quantity" width="1%">&nbsp;</td>

	<td class="line_cost" width="1%">
		<div class="view">
			<?php
			echo wp_kses_post(
				wc_price( '-' . $refund->get_amount(), array( 'currency' => $refund->get_currency() ) )
			);
			?>
		</div>
	</td>

	<?php
	if ( wc_tax_enabled() ) :
		$total_taxes = count( $order_taxes );
		?>
		<?php for ( $i = 0;  $i < $total_taxes; $i++ ) : ?>
			<td class="line_tax" width="1%"></td>
		<?php endfor; ?>
	<?php endif; ?>

	<td class="wc-order-edit-line-item">
		<div class="wc-order-edit-line-item-actions">
			<a class="delete_refund" href="#"></a>
		</div>
	</td>
</tr>
