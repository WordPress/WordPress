<?php
/**
 * Order Data
 *
 * Functions for displaying the order items meta box.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Meta_Box_Order_Items {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $wpdb, $thepostid, $theorder, $woocommerce;

		if ( ! is_object( $theorder ) )
			$theorder = new WC_Order( $thepostid );

		$order = $theorder;
		?>
		<div class="woocommerce_order_items_wrapper">
			<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
				<thead>
					<tr>
						<th><input type="checkbox" class="check-column" /></th>
						<th class="item" colspan="2"><?php _e( 'Item', 'woocommerce' ); ?></th>

						<?php do_action( 'woocommerce_admin_order_item_headers' ); ?>

						<?php if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) : ?>
							<th class="tax_class"><?php _e( 'Tax&nbsp;Class', 'woocommerce' ); ?></th>
						<?php endif; ?>

						<th class="quantity"><?php _e( 'Qty', 'woocommerce' ); ?></th>

						<th class="line_cost"><?php _e( 'Total', 'woocommerce' ); ?></th>

						<?php if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) : ?>
							<th class="line_tax"><?php _e( 'Tax', 'woocommerce' ); ?></th>
						<?php endif; ?>

						<th width="1%">&nbsp;</th>
					</tr>
				</thead>
				<tbody id="order_items_list">

					<?php
						// List order items
						$order_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', array( 'line_item', 'fee' ) ) );

						foreach ( $order_items as $item_id => $item ) {

							switch ( $item['type'] ) {
								case 'line_item' :
									$_product 	= $order->get_product_from_item( $item );
									$item_meta 	= $order->get_item_meta( $item_id );

									include( 'views/html-order-item.php' );
								break;
								case 'fee' :
									include( 'views/html-order-fee.php' );
								break;
							}

							do_action( 'woocommerce_order_item_' . $item['type'] . '_html', $item_id, $item );
						}
					?>
				</tbody>
			</table>
		</div>

		<p class="bulk_actions">
			<select>
				<option value=""><?php _e( 'Actions', 'woocommerce' ); ?></option>
				<optgroup label="<?php _e( 'Edit', 'woocommerce' ); ?>">
					<option value="delete"><?php _e( 'Delete Lines', 'woocommerce' ); ?></option>
				</optgroup>
				<optgroup label="<?php _e( 'Stock Actions', 'woocommerce' ); ?>">
					<option value="reduce_stock"><?php _e( 'Reduce Line Stock', 'woocommerce' ); ?></option>
					<option value="increase_stock"><?php _e( 'Increase Line Stock', 'woocommerce' ); ?></option>
				</optgroup>
			</select>

			<button type="button" class="button do_bulk_action wc-reload" title="<?php _e( 'Apply', 'woocommerce' ); ?>"><span><?php _e( 'Apply', 'woocommerce' ); ?></span></button>
		</p>

		<p class="add_items">
			<select id="add_item_id" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>" style="width: 400px"></select>

			<button type="button" class="button add_order_item"><?php _e( 'Add item(s)', 'woocommerce' ); ?></button>
			<button type="button" class="button add_order_fee"><?php _e( 'Add fee', 'woocommerce' ); ?></button>
		</p>
		<div class="clear"></div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		global $wpdb;

		// Order items + fees
		$subtotal = 0;
		$total    = 0;

		if ( isset( $_POST['order_item_id'] ) ) {

			$get_values = array( 'order_item_id', 'order_item_name', 'order_item_qty', 'line_subtotal', 'line_subtotal_tax', 'line_total', 'line_tax', 'order_item_tax_class' );

			foreach( $get_values as $value )
				$$value = isset( $_POST[ $value ] ) ? $_POST[ $value ] : array();

			foreach ( $order_item_id as $item_id ) {

				$item_id = absint( $item_id );

				if ( isset( $order_item_name[ $item_id ] ) )
					$wpdb->update(
						$wpdb->prefix . "woocommerce_order_items",
						array( 'order_item_name' => wc_clean( $order_item_name[ $item_id ] ) ),
						array( 'order_item_id' => $item_id ),
						array( '%s' ),
						array( '%d' )
					);

				if ( isset( $order_item_qty[ $item_id ] ) )
			 		wc_update_order_item_meta( $item_id, '_qty', apply_filters( 'woocommerce_stock_amount', $order_item_qty[ $item_id ] ) );

			 	if ( isset( $order_item_tax_class[ $item_id ] ) )
			 		wc_update_order_item_meta( $item_id, '_tax_class', wc_clean( $order_item_tax_class[ $item_id ] ) );

			 	// Get values. Subtotals might not exist, in which case copy value from total field
				$line_total[ $item_id ]        = isset( $line_total[ $item_id ] ) ? $line_total[ $item_id ] : 0;
				$line_tax[ $item_id ]          = isset( $line_tax[ $item_id ] ) ? $line_tax[ $item_id ] : 0;
				$line_subtotal[ $item_id ]     = isset( $line_subtotal[ $item_id ] ) ? $line_subtotal[ $item_id ] : $line_total[ $item_id ];
				$line_subtotal_tax[ $item_id ] = isset( $line_subtotal_tax[ $item_id ] ) ? $line_subtotal_tax[ $item_id ] : $line_tax[ $item_id ];

				// Update values
				wc_update_order_item_meta( $item_id, '_line_subtotal', wc_format_decimal( $line_subtotal[ $item_id ] ) );
				wc_update_order_item_meta( $item_id, '_line_subtotal_tax', wc_format_decimal( $line_subtotal_tax[ $item_id ] ) );
				wc_update_order_item_meta( $item_id, '_line_total', wc_format_decimal( $line_total[ $item_id ] ) );
				wc_update_order_item_meta( $item_id, '_line_tax', wc_format_decimal( $line_tax[ $item_id ] ) );

				// Total up
				$subtotal += wc_format_decimal( $line_subtotal[ $item_id ] );
				$total    += wc_format_decimal( $line_total[ $item_id ] );

			 	// Clear meta cache
			 	wp_cache_delete( $item_id, 'order_item_meta' );
			}
		}

		// Save meta
		$meta_keys 		= isset( $_POST['meta_key'] ) ? $_POST['meta_key'] : array();
		$meta_values 	= isset( $_POST['meta_value'] ) ? $_POST['meta_value'] : array();

		foreach ( $meta_keys as $id => $meta_key ) {
			$meta_value = ( empty( $meta_values[ $id ] ) && ! is_numeric( $meta_values[ $id ] ) ) ? '' : $meta_values[ $id ];
			$meta_value = stripslashes( $meta_value );
			$wpdb->update(
				$wpdb->prefix . "woocommerce_order_itemmeta",
				array(
					'meta_key'   => $meta_key,
					'meta_value' => $meta_value
				),
				array( 'meta_id' => $id ),
				array( '%s', '%s' ),
				array( '%d' )
			);
		}

		// Update cart discount from item totals
		update_post_meta( $post_id, '_cart_discount', $subtotal - $total );
	}
}