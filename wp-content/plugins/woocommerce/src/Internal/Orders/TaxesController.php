<?php

namespace Automattic\WooCommerce\Internal\Orders;

/**
 * Class with methods for handling order taxes.
 */
class TaxesController {

	/**
	 * Calculate line taxes via Ajax call.
	 */
	public function calc_line_taxes_via_ajax(): void {
		check_ajax_referer( 'calc-totals', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['order_id'], $_POST['items'] ) ) {
			wp_die( -1 );
		}

		$order = $this->calc_line_taxes( $_POST );

		include __DIR__ . '/../../../includes/admin/meta-boxes/views/html-order-items.php';
		wp_die();
	}

	/**
	 * Calculate line taxes programmatically.
	 *
	 * @param array $post_variables Contents of the $_POST array that would be passed in an Ajax call.
	 * @return object The retrieved order object.
	 */
	public function calc_line_taxes( array $post_variables ): object {
		$order_id           = absint( $post_variables['order_id'] );
		$calculate_tax_args = array(
			'country'  => isset( $post_variables['country'] ) ? wc_strtoupper( wc_clean( wp_unslash( $post_variables['country'] ) ) ) : '',
			'state'    => isset( $post_variables['state'] ) ? wc_strtoupper( wc_clean( wp_unslash( $post_variables['state'] ) ) ) : '',
			'postcode' => isset( $post_variables['postcode'] ) ? wc_strtoupper( wc_clean( wp_unslash( $post_variables['postcode'] ) ) ) : '',
			'city'     => isset( $post_variables['city'] ) ? wc_strtoupper( wc_clean( wp_unslash( $post_variables['city'] ) ) ) : '',
		);

		// Parse the jQuery serialized items.
		$items = array();
		parse_str( wp_unslash( $post_variables['items'] ), $items );

		// Save order items first.
		wc_save_order_items( $order_id, $items );

		// Grab the order and recalculate taxes.
		$order = wc_get_order( $order_id );
		$order->calculate_taxes( $calculate_tax_args );
		$order->calculate_totals( false );

		return $order;
	}
}
