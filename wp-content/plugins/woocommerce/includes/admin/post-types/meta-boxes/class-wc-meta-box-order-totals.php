<?php
/**
 * Order Totals
 *
 * Functions for displaying the order totals meta box.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Meta_Box_Order_Totals {

	/**
	 * Output the metabox
	 */
	public static function output() {
		global $woocommerce, $theorder, $wpdb, $post;

		if ( ! is_object( $theorder ) )
			$theorder = new WC_Order( $post->ID );

		$order = $theorder;

		$data = get_post_meta( $post->ID );
		?>
		<div class="totals_group">
			<h4><span class="tax_total_display inline_total"></span><?php _e( 'Shipping', 'woocommerce' ); ?></h4>

			<div id="shipping_rows" class="total_rows">
				<?php
					if ( WC()->shipping() )
						$shipping_methods = WC()->shipping->load_shipping_methods();

					foreach ( $order->get_shipping_methods() as $item_id => $item ) {
						$chosen_method  = $item['method_id'];
						$shipping_title = $item['name'];
						$shipping_cost  = $item['cost'];

						include( 'views/html-order-shipping.php' );
					}

					// Pre 2.1
					if ( isset( $data['_shipping_method'] ) ) {
						$item_id        = '';
						$chosen_method  = ! empty( $data['_shipping_method'][0] ) ? $data['_shipping_method'][0] : '';
						$shipping_title = ! empty( $data['_shipping_method_title'][0] ) ? $data['_shipping_method_title'][0] : '';
						$shipping_cost  = ! empty( $data['_order_shipping'][0] ) ? $data['_order_shipping'][0] : '';

						include( 'views/html-order-shipping.php' );
					}
				?>
			</div>

			<h4><a href="#" class="add_total_row" data-row="<?php
				$item_id        = '';
				$chosen_method  = '';
				$shipping_cost  = '';
				$shipping_title = __( 'Shipping', 'woocommerce' );
				ob_start();
				include( 'views/html-order-shipping.php' );
				echo esc_attr( ob_get_clean() );
			?>"><?php _e( '+ Add shipping cost', 'woocommerce' ); ?> <span class="tips" data-tip="<?php _e( 'These are the shipping and handling costs for the order.', 'woocommerce' ); ?>">[?]</span></a></a></h4>
			<div class="clear"></div>

			<?php do_action( 'woocommerce_admin_order_totals_after_shipping', $post->ID ) ?>
		</div>

		<?php if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) : ?>

		<div class="totals_group tax_rows_group">
			<h4><span class="tax_total_display inline_total"></span><?php _e( 'Taxes', 'woocommerce' ); ?></h4>
			<div id="tax_rows" class="total_rows">
				<?php
					global $wpdb;

					$rates = $wpdb->get_results( "SELECT tax_rate_id, tax_rate_country, tax_rate_state, tax_rate_name, tax_rate_priority FROM {$wpdb->prefix}woocommerce_tax_rates ORDER BY tax_rate_name" );

					$tax_codes = array();

					foreach( $rates as $rate ) {
						$code = array();

						$code[] = $rate->tax_rate_country;
						$code[] = $rate->tax_rate_state;
						$code[] = $rate->tax_rate_name ? sanitize_title( $rate->tax_rate_name ) : 'TAX';
						$code[] = absint( $rate->tax_rate_priority );

						$tax_codes[ $rate->tax_rate_id ] = strtoupper( implode( '-', array_filter( $code ) ) );
					}

					foreach ( $order->get_taxes() as $item_id => $item ) {
						include( 'views/html-order-tax.php' );
					}
				?>
			</div>
			<h4><a href="#" class="add_total_row" data-row="<?php
				$item_id = '';
				$item    = '';
				ob_start();
				include( 'views/html-order-tax.php' );
				echo esc_attr( ob_get_clean() );
				?>"><?php _e( '+ Add tax row', 'woocommerce' ); ?> <span class="tips" data-tip="<?php _e( 'These rows contain taxes for this order. This allows you to display multiple or compound taxes rather than a single total.', 'woocommerce' ); ?>">[?]</span></a></a></h4>
			<div class="clear"></div>
		</div>

		<?php endif; ?>

		<div class="totals_group">
			<h4><label for="_order_discount"><?php _e( 'Order Discount', 'woocommerce' ); ?> <span class="tips" data-tip="<?php _e( 'This is the total discount applied after tax.', 'woocommerce' ); ?>">[?]</span></label></h4>
			<input type="text" class="wc_input_price" id="_order_discount" name="_order_discount" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php
				if ( isset( $data['_order_discount'][0] ) )
					echo esc_attr( wc_format_localized_price( $data['_order_discount'][0] ) );
			?>" />
		</div>
		<div class="totals_group">
			<h4><label for="_order_total"><?php _e( 'Order Total', 'woocommerce' ); ?></label></h4>
			<input type="text" class="wc_input_price" id="_order_total" name="_order_total" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php
						if ( isset( $data['_order_total'][0] ) )
							echo esc_attr( wc_format_localized_price( $data['_order_total'][0] ) );
			?>" />
		</div>
		<?php
		$coupons = $order->get_items( array( 'coupon' ) );

		if ( $coupons ) {
			?>
			<div class="totals_group">
				<ul class="wc_coupon_list"><?php
					foreach ( $coupons as $item_id => $item ) {

						$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item['name'] ) );

						$link = $post_id ? add_query_arg( array( 'post' => $post_id, 'action' => 'edit' ), admin_url( 'post.php' ) ) : add_query_arg( array( 's' => $item['name'], 'post_status' => 'all', 'post_type' => 'shop_coupon' ), admin_url( 'edit.php' ) );

						echo '<li class="tips code" data-tip="' . esc_attr( wc_price( $item['discount_amount'] ) ) . '"><a href="' . esc_url( $link ) . '"><span>' . esc_html( $item['name'] ). '</span></a></li>';
					}
				?></ul>
			</div>
			<?php
		}
		?>
		<p class="buttons">
			<?php if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) : ?>
				<button type="button" class="button calc_line_taxes"><?php _e( 'Calculate Tax', 'woocommerce' ); ?></button>
			<?php endif; ?>
			<button type="button" class="button calc_totals button-primary"><?php _e( 'Calculate Total', 'woocommerce' ); ?></button>
		</p>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		global $wpdb;

		// Save tax rows
		$total_tax          = 0;
		$total_shipping_tax = 0;

		if ( isset( $_POST['order_taxes_id'] ) ) {

			$get_values = array( 'order_taxes_id', 'order_taxes_rate_id', 'order_taxes_amount', 'order_taxes_shipping_amount' );

			foreach( $get_values as $value )
				$$value = isset( $_POST[ $value ] ) ? $_POST[ $value ] : array();

			foreach( $order_taxes_id as $item_id => $value ) {

				if ( $item_id == 'new' ) {

					foreach ( $value as $new_key => $new_value ) {
						$rate_id  = absint( $order_taxes_rate_id[ $item_id ][ $new_key ] );

						if ( $rate_id ) {
							$rate     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %s", $rate_id ) );
							$label    = $rate->tax_rate_name ? $rate->tax_rate_name : WC()->countries->tax_or_vat();
							$compound = $rate->tax_rate_compound ? 1 : 0;

							$code = array();

							$code[] = $rate->tax_rate_country;
							$code[] = $rate->tax_rate_state;
							$code[] = $rate->tax_rate_name ? $rate->tax_rate_name : 'TAX';
							$code[] = absint( $rate->tax_rate_priority );
							$code   = strtoupper( implode( '-', array_filter( $code ) ) );
						} else {
							$code  = '';
							$label = WC()->countries->tax_or_vat();
						}

						// Add line item
					   	$new_id = wc_add_order_item( $post_id, array(
								'order_item_name' => wc_clean( $code ),
								'order_item_type' => 'tax'
					 	) );

					 	// Add line item meta
					 	if ( $new_id ) {
							wc_update_order_item_meta( $new_id, 'rate_id', $rate_id );
							wc_update_order_item_meta( $new_id, 'label', $label );
							wc_update_order_item_meta( $new_id, 'compound', $compound );

							if ( isset( $order_taxes_amount[ $item_id ][ $new_key ] ) ) {
						 		wc_update_order_item_meta( $new_id, 'tax_amount', wc_format_decimal( $order_taxes_amount[ $item_id ][ $new_key ] ) );

						 		$total_tax          += wc_format_decimal( $order_taxes_amount[ $item_id ][ $new_key ] );
						 	}

						 	if ( isset( $order_taxes_shipping_amount[ $item_id ][ $new_key ] ) ) {
						 		wc_update_order_item_meta( $new_id, 'shipping_tax_amount', wc_format_decimal( $order_taxes_shipping_amount[ $item_id ][ $new_key ] ) );

						 		$total_shipping_tax += wc_format_decimal( $order_taxes_shipping_amount[ $item_id ][ $new_key ] );
						 	}
					 	}
					}

				} else {

					$item_id  = absint( $item_id );
					$rate_id  = absint( $order_taxes_rate_id[ $item_id ] );

					if ( $rate_id ) {
						$rate     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %s", $rate_id ) );
						$label    = $rate->tax_rate_name ? $rate->tax_rate_name : WC()->countries->tax_or_vat();
						$compound = $rate->tax_rate_compound ? 1 : 0;

						$code = array();

						$code[] = $rate->tax_rate_country;
						$code[] = $rate->tax_rate_state;
						$code[] = $rate->tax_rate_name ? $rate->tax_rate_name : 'TAX';
						$code[] = absint( $rate->tax_rate_priority );
						$code   = strtoupper( implode( '-', array_filter( $code ) ) );
					} else {
						$code  = '';
						$label = WC()->countries->tax_or_vat();
					}

					$wpdb->update(
						$wpdb->prefix . "woocommerce_order_items",
						array( 'order_item_name' => wc_clean( $code ) ),
						array( 'order_item_id' => $item_id ),
						array( '%s' ),
						array( '%d' )
					);

					wc_update_order_item_meta( $item_id, 'rate_id', $rate_id );
					wc_update_order_item_meta( $item_id, 'label', $label );
					wc_update_order_item_meta( $item_id, 'compound', $compound );

					if ( isset( $order_taxes_amount[ $item_id ] ) ) {
				 		wc_update_order_item_meta( $item_id, 'tax_amount', wc_format_decimal( $order_taxes_amount[ $item_id ] ) );

				 		$total_tax += wc_format_decimal( $order_taxes_amount[ $item_id ] );
				 	}

				 	if ( isset( $order_taxes_shipping_amount[ $item_id ] ) ) {
				 		wc_update_order_item_meta( $item_id, 'shipping_tax_amount', wc_format_decimal( $order_taxes_shipping_amount[ $item_id ] ) );

				 		$total_shipping_tax += wc_format_decimal( $order_taxes_shipping_amount[ $item_id ] );
				 	}
				}
			}
		}

		// Update totals
		update_post_meta( $post_id, '_order_tax', wc_format_decimal( $total_tax ) );
		update_post_meta( $post_id, '_order_shipping_tax', wc_format_decimal( $total_shipping_tax ) );
		update_post_meta( $post_id, '_order_discount', wc_format_decimal( $_POST['_order_discount'] ) );
		update_post_meta( $post_id, '_order_total', wc_format_decimal( $_POST['_order_total'] ) );

		// Shipping Rows
		$order_shipping = 0;

		if ( isset( $_POST['shipping_method_id'] ) ) {

			$get_values = array( 'shipping_method_id', 'shipping_method_title', 'shipping_method', 'shipping_cost' );

			foreach( $get_values as $value )
				$$value = isset( $_POST[ $value ] ) ? $_POST[ $value ] : array();

			foreach( $shipping_method_id as $item_id => $value ) {

				if ( $item_id == 'new' ) {

					foreach ( $value as $new_key => $new_value ) {
						$method_id    = wc_clean( $shipping_method[ $item_id ][ $new_key ] );
						$method_title = wc_clean( $shipping_method_title[ $item_id ][ $new_key ] );
						$cost         = wc_format_decimal( $shipping_cost[ $item_id ][ $new_key ] );

						$new_id = wc_add_order_item( $post_id, array(
					 		'order_item_name' 		=> $method_title,
					 		'order_item_type' 		=> 'shipping'
					 	) );

						if ( $new_id ) {
					 		wc_add_order_item_meta( $new_id, 'method_id', $method_id );
				 			wc_add_order_item_meta( $new_id, 'cost', $cost );
				 		}

				 		$order_shipping += $cost;
					}

				} else {

					$item_id      = absint( $item_id );
					$method_id    = wc_clean( $shipping_method[ $item_id ] );
					$method_title = wc_clean( $shipping_method_title[ $item_id ] );
					$cost         = wc_format_decimal( $shipping_cost[ $item_id ] );

					$wpdb->update(
						$wpdb->prefix . "woocommerce_order_items",
						array( 'order_item_name' => $method_title ),
						array( 'order_item_id' => $item_id ),
						array( '%s' ),
						array( '%d' )
					);

					wc_update_order_item_meta( $item_id, 'method_id', $method_id );
					wc_update_order_item_meta( $item_id, 'cost', $cost );

					$order_shipping += $cost;
				}
			}
		}

		// Delete rows
		if ( isset( $_POST['delete_order_item_id'] ) ) {
			$delete_ids = $_POST['delete_order_item_id'];

			foreach ( $delete_ids as $id )
				wc_delete_order_item( absint( $id ) );
		}

		delete_post_meta( $post_id, '_shipping_method' );
		delete_post_meta( $post_id, '_shipping_method_title' );
		update_post_meta( $post_id, '_order_shipping', $order_shipping );
		add_post_meta( $post_id, '_order_currency', get_woocommerce_currency(), true );
	}
}