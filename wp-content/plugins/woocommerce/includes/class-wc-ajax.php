<?php
/**
 * WooCommerce WC_AJAX. AJAX Event Handlers.
 *
 * @class   WC_AJAX
 * @package WooCommerce\Classes
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Internal\Orders\CouponsController;
use Automattic\WooCommerce\Internal\Orders\TaxesController;
use Automattic\WooCommerce\Internal\Admin\Orders\MetaBoxes\CustomMetaBox;
use Automattic\WooCommerce\Utilities\ArrayUtil;
use Automattic\WooCommerce\Utilities\NumberUtil;
use Automattic\WooCommerce\Utilities\OrderUtil;
use Automattic\WooCommerce\Utilities\StringUtil;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Ajax class.
 */
class WC_AJAX {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_wc_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Get WC Ajax Endpoint.
	 *
	 * @param string $request Optional.
	 *
	 * @return string
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( apply_filters( 'woocommerce_ajax_get_endpoint', add_query_arg( 'wc-ajax', $request, remove_query_arg( array( 'remove_item', 'add-to-cart', 'added-to-cart', 'order_again', '_wpnonce' ), home_url( '/', 'relative' ) ) ), $request ) );
	}

	/**
	 * Set WC AJAX constant and headers.
	 */
	public static function define_ajax() {
		// phpcs:disable
		if ( ! empty( $_GET['wc-ajax'] ) ) {
			wc_maybe_define_constant( 'DOING_AJAX', true );
			wc_maybe_define_constant( 'WC_DOING_AJAX', true );
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
			}
			$GLOBALS['wpdb']->hide_errors();
		}
		// phpcs:enable
	}

	/**
	 * Send headers for WC Ajax Requests.
	 *
	 * @since 2.5.0
	 */
	private static function wc_ajax_headers() {
		if ( ! headers_sent() ) {
			send_origin_headers();
			send_nosniff_header();
			wc_nocache_headers();
			header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
			header( 'X-Robots-Tag: noindex' );
			status_header( 200 );
		} elseif ( Constants::is_true( 'WP_DEBUG' ) ) {
			headers_sent( $file, $line );
			trigger_error( "wc_ajax_headers cannot set headers - headers already sent by {$file} on line {$line}", E_USER_NOTICE ); // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Check for WC Ajax request and fire action.
	 */
	public static function do_wc_ajax() {
		global $wp_query;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['wc-ajax'] ) ) {
			$wp_query->set( 'wc-ajax', sanitize_text_field( wp_unslash( $_GET['wc-ajax'] ) ) );
		}

		$action = $wp_query->get( 'wc-ajax' );

		if ( $action ) {
			self::wc_ajax_headers();
			$action = sanitize_text_field( $action );
			do_action( 'wc_ajax_' . $action );
			wp_die();
		}
		// phpcs:enable
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		$ajax_events_nopriv = array(
			'get_refreshed_fragments',
			'apply_coupon',
			'remove_coupon',
			'update_shipping_method',
			'get_cart_totals',
			'update_order_review',
			'add_to_cart',
			'remove_from_cart',
			'checkout',
			'get_variation',
			'get_customer_location',
		);

		foreach ( $ajax_events_nopriv as $ajax_event ) {
			add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			// WC AJAX can be used for frontend ajax requests.
			add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}

		$ajax_events = array(
			'feature_product',
			'mark_order_status',
			'get_order_details',
			'add_attribute',
			'add_new_attribute',
			'remove_variations',
			'save_attributes',
			'add_attributes_and_variations',
			'add_variation',
			'link_all_variations',
			'revoke_access_to_download',
			'grant_access_to_download',
			'get_customer_details',
			'add_order_item',
			'add_order_fee',
			'add_order_shipping',
			'add_order_tax',
			'add_coupon_discount',
			'remove_order_coupon',
			'remove_order_item',
			'remove_order_tax',
			'calc_line_taxes',
			'save_order_items',
			'load_order_items',
			'add_order_note',
			'delete_order_note',
			'json_search_products',
			'json_search_products_and_variations',
			'json_search_downloadable_products_and_variations',
			'json_search_customers',
			'json_search_categories',
			'json_search_categories_tree',
			'json_search_taxonomy_terms',
			'json_search_product_attributes',
			'json_search_pages',
			'term_ordering',
			'product_ordering',
			'refund_line_items',
			'delete_refund',
			'rated',
			'update_api_key',
			'load_variations',
			'save_variations',
			'bulk_edit_variations',
			'tax_rates_save_changes',
			'shipping_zones_save_changes',
			'shipping_zone_add_method',
			'shipping_zone_methods_save_changes',
			'shipping_zone_methods_save_settings',
			'shipping_classes_save_changes',
			'toggle_gateway_enabled',
		);

		foreach ( $ajax_events as $ajax_event ) {
			add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}

		$ajax_private_events = array(
			'order_add_meta',
			'order_delete_meta',
		);

		foreach ( $ajax_private_events as $ajax_event ) {
			add_action(
				'wp_ajax_woocommerce_' . $ajax_event,
				function() use ( $ajax_event ) {
					call_user_func( array( __CLASS__, $ajax_event ) );
				}
			);
		}

		// WP's heartbeat.
		$ajax_heartbeat_callbacks = array(
			'order_refresh_lock',
			'check_locked_orders',
		);
		foreach ( $ajax_heartbeat_callbacks as $ajax_callback ) {
			add_filter(
				'heartbeat_received',
				function( $response, $data ) use ( $ajax_callback ) {
					return call_user_func_array( array( __CLASS__, $ajax_callback ), func_get_args() );
				},
				10,
				2
			);
		}
	}

	/**
	 * Get a refreshed cart fragment, including the mini cart HTML.
	 */
	public static function get_refreshed_fragments() {
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		$data = array(
			'fragments' => apply_filters(
				'woocommerce_add_to_cart_fragments',
				array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				)
			),
			'cart_hash' => WC()->cart->get_cart_hash(),
		);

		wp_send_json( $data );
	}

	/**
	 * AJAX apply coupon on checkout page.
	 */
	public static function apply_coupon() {

		check_ajax_referer( 'apply-coupon', 'security' );

		$coupon_code = ArrayUtil::get_value_or_default( $_POST, 'coupon_code' );
		if ( ! StringUtil::is_null_or_whitespace( $coupon_code ) ) {
			WC()->cart->add_discount( wc_format_coupon_code( wp_unslash( $coupon_code ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} else {
			wc_add_notice( WC_Coupon::get_generic_coupon_error( WC_Coupon::E_WC_COUPON_PLEASE_ENTER ), 'error' );
		}

		wc_print_notices();
		wp_die();
	}

	/**
	 * AJAX remove coupon on cart and checkout page.
	 */
	public static function remove_coupon() {
		check_ajax_referer( 'remove-coupon', 'security' );

		$coupon = isset( $_POST['coupon'] ) ? wc_format_coupon_code( wp_unslash( $_POST['coupon'] ) ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( StringUtil::is_null_or_whitespace( $coupon ) ) {
			wc_add_notice( __( 'Sorry there was a problem removing this coupon.', 'woocommerce' ), 'error' );
		} else {
			WC()->cart->remove_coupon( $coupon );
			wc_add_notice( __( 'Coupon has been removed.', 'woocommerce' ) );
		}

		wc_print_notices();
		wp_die();
	}

	/**
	 * AJAX update shipping method on cart page.
	 */
	public static function update_shipping_method() {
		check_ajax_referer( 'update-shipping-method', 'security' );

		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );

		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		$posted_shipping_methods = isset( $_POST['shipping_method'] ) ? wc_clean( wp_unslash( $_POST['shipping_method'] ) ) : array();

		if ( is_array( $posted_shipping_methods ) ) {
			foreach ( $posted_shipping_methods as $i => $value ) {
				$chosen_shipping_methods[ $i ] = $value;
			}
		}

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );

		self::get_cart_totals();
	}

	/**
	 * AJAX receive updated cart_totals div.
	 */
	public static function get_cart_totals() {
		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
		WC()->cart->calculate_totals();
		woocommerce_cart_totals();
		wp_die();
	}

	/**
	 * Session has expired.
	 */
	private static function update_order_review_expired() {
		wp_send_json(
			array(
				'fragments' => apply_filters(
					'woocommerce_update_order_review_fragments',
					array(
						'form.woocommerce-checkout' => wc_print_notice(
							esc_html__( 'Sorry, your session has expired.', 'woocommerce' ) . ' <a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="wc-backward">' . esc_html__( 'Return to shop', 'woocommerce' ) . '</a>',
							'error',
							array(),
							true
						),
					)
				),
			)
		);
	}

	/**
	 * AJAX update order review on checkout.
	 */
	public static function update_order_review() {
		check_ajax_referer( 'update-order-review', 'security' );

		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

		if ( WC()->cart->is_empty() && ! is_customize_preview() && apply_filters( 'woocommerce_checkout_update_order_review_expired', true ) ) {
			self::update_order_review_expired();
		}

		do_action( 'woocommerce_checkout_update_order_review', isset( $_POST['post_data'] ) ? wp_unslash( $_POST['post_data'] ) : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		$posted_shipping_methods = isset( $_POST['shipping_method'] ) ? wc_clean( wp_unslash( $_POST['shipping_method'] ) ) : array();

		if ( is_array( $posted_shipping_methods ) ) {
			foreach ( $posted_shipping_methods as $i => $value ) {
				$chosen_shipping_methods[ $i ] = $value;
			}
		}

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		WC()->session->set( 'chosen_payment_method', empty( $_POST['payment_method'] ) ? '' : wc_clean( wp_unslash( $_POST['payment_method'] ) ) );
		WC()->customer->set_props(
			array(
				'billing_country'   => isset( $_POST['country'] ) ? wc_clean( wp_unslash( $_POST['country'] ) ) : null,
				'billing_state'     => isset( $_POST['state'] ) ? wc_clean( wp_unslash( $_POST['state'] ) ) : null,
				'billing_postcode'  => isset( $_POST['postcode'] ) ? wc_clean( wp_unslash( $_POST['postcode'] ) ) : null,
				'billing_city'      => isset( $_POST['city'] ) ? wc_clean( wp_unslash( $_POST['city'] ) ) : null,
				'billing_address_1' => isset( $_POST['address'] ) ? wc_clean( wp_unslash( $_POST['address'] ) ) : null,
				'billing_address_2' => isset( $_POST['address_2'] ) ? wc_clean( wp_unslash( $_POST['address_2'] ) ) : null,
			)
		);

		if ( wc_ship_to_billing_address_only() ) {
			WC()->customer->set_props(
				array(
					'shipping_country'   => isset( $_POST['country'] ) ? wc_clean( wp_unslash( $_POST['country'] ) ) : null,
					'shipping_state'     => isset( $_POST['state'] ) ? wc_clean( wp_unslash( $_POST['state'] ) ) : null,
					'shipping_postcode'  => isset( $_POST['postcode'] ) ? wc_clean( wp_unslash( $_POST['postcode'] ) ) : null,
					'shipping_city'      => isset( $_POST['city'] ) ? wc_clean( wp_unslash( $_POST['city'] ) ) : null,
					'shipping_address_1' => isset( $_POST['address'] ) ? wc_clean( wp_unslash( $_POST['address'] ) ) : null,
					'shipping_address_2' => isset( $_POST['address_2'] ) ? wc_clean( wp_unslash( $_POST['address_2'] ) ) : null,
				)
			);
		} else {
			WC()->customer->set_props(
				array(
					'shipping_country'   => isset( $_POST['s_country'] ) ? wc_clean( wp_unslash( $_POST['s_country'] ) ) : null,
					'shipping_state'     => isset( $_POST['s_state'] ) ? wc_clean( wp_unslash( $_POST['s_state'] ) ) : null,
					'shipping_postcode'  => isset( $_POST['s_postcode'] ) ? wc_clean( wp_unslash( $_POST['s_postcode'] ) ) : null,
					'shipping_city'      => isset( $_POST['s_city'] ) ? wc_clean( wp_unslash( $_POST['s_city'] ) ) : null,
					'shipping_address_1' => isset( $_POST['s_address'] ) ? wc_clean( wp_unslash( $_POST['s_address'] ) ) : null,
					'shipping_address_2' => isset( $_POST['s_address_2'] ) ? wc_clean( wp_unslash( $_POST['s_address_2'] ) ) : null,
				)
			);
		}

		if ( isset( $_POST['has_full_address'] ) && wc_string_to_bool( wc_clean( wp_unslash( $_POST['has_full_address'] ) ) ) ) {
			WC()->customer->set_calculated_shipping( true );
		} else {
			WC()->customer->set_calculated_shipping( false );
		}

		WC()->customer->save();

		// Calculate shipping before totals. This will ensure any shipping methods that affect things like taxes are chosen prior to final totals being calculated. Ref: #22708.
		WC()->cart->calculate_shipping();
		WC()->cart->calculate_totals();

		// Get order review fragment.
		ob_start();
		woocommerce_order_review();
		$woocommerce_order_review = ob_get_clean();

		// Get checkout payment fragment.
		ob_start();
		woocommerce_checkout_payment();
		$woocommerce_checkout_payment = ob_get_clean();

		// Get messages if reload checkout is not true.
		$reload_checkout = isset( WC()->session->reload_checkout );
		if ( ! $reload_checkout ) {
			$messages = wc_print_notices( true );
		} else {
			$messages = '';
		}

		unset( WC()->session->refresh_totals, WC()->session->reload_checkout );

		wp_send_json(
			array(
				'result'    => empty( $messages ) ? 'success' : 'failure',
				'messages'  => $messages,
				'reload'    => $reload_checkout,
				'fragments' => apply_filters(
					'woocommerce_update_order_review_fragments',
					array(
						'.woocommerce-checkout-review-order-table' => $woocommerce_order_review,
						'.woocommerce-checkout-payment' => $woocommerce_checkout_payment,
					)
				),
			)
		);
	}

	/**
	 * AJAX add to cart.
	 */
	public static function add_to_cart() {
		ob_start();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product           = wc_get_product( $product_id );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		$variation_id      = 0;
		$variation         = array();

		if ( $product && 'variation' === $product->get_type() ) {
			$variation_id = $product_id;
			$product_id   = $product->get_parent_id();
			$variation    = $product->get_variation_attributes();
		}

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			self::get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
		// phpcs:enable
	}

	/**
	 * AJAX remove from cart.
	 */
	public static function remove_from_cart() {
		ob_start();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$cart_item_key = wc_clean( isset( $_POST['cart_item_key'] ) ? wp_unslash( $_POST['cart_item_key'] ) : '' );

		if ( $cart_item_key && false !== WC()->cart->remove_cart_item( $cart_item_key ) ) {
			self::get_refreshed_fragments();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Process ajax checkout form.
	 */
	public static function checkout() {
		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );
		WC()->checkout()->process_checkout();
		wp_die( 0 );
	}

	/**
	 * Get a matching variation based on posted attributes.
	 */
	public static function get_variation() {
		ob_start();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['product_id'] ) ) {
			wp_die();
		}

		$variable_product = wc_get_product( absint( $_POST['product_id'] ) );

		if ( ! $variable_product ) {
			wp_die();
		}

		$data_store   = WC_Data_Store::load( 'product' );
		$variation_id = $data_store->find_matching_product_variation( $variable_product, wp_unslash( $_POST ) );
		$variation    = $variation_id ? $variable_product->get_available_variation( $variation_id ) : false;
		wp_send_json( $variation );
		// phpcs:enable
	}

	/**
	 * Locate user via AJAX.
	 */
	public static function get_customer_location() {
		$location_hash = WC_Cache_Helper::geolocation_ajax_get_location_hash();
		wp_send_json_success( array( 'hash' => $location_hash ) );
	}

	/**
	 * Toggle Featured status of a product from admin.
	 */
	public static function feature_product() {
		if ( current_user_can( 'edit_products' ) && check_admin_referer( 'woocommerce-feature-product' ) && isset( $_GET['product_id'] ) ) {
			$product = wc_get_product( absint( $_GET['product_id'] ) );

			if ( $product ) {
				$product->set_featured( ! $product->get_featured() );
				$product->save();
			}
		}

		wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php?post_type=product' ) );
		exit;
	}

	/**
	 * Mark an order with a status.
	 */
	public static function mark_order_status() {
		if ( current_user_can( 'edit_shop_orders' ) && check_admin_referer( 'woocommerce-mark-order-status' ) && isset( $_GET['status'], $_GET['order_id'] ) ) {
			$status = sanitize_text_field( wp_unslash( $_GET['status'] ) );
			$order  = wc_get_order( absint( wp_unslash( $_GET['order_id'] ) ) );

			if ( wc_is_order_status( 'wc-' . $status ) && $order ) {
				// Initialize payment gateways in case order has hooked status transition actions.
				WC()->payment_gateways();

				$order->update_status( $status, '', true );
				do_action( 'woocommerce_order_edit_status', $order->get_id(), $status );
			}
		}

		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' ) );
		exit;
	}

	/**
	 * Get order details.
	 */
	public static function get_order_details() {
		check_admin_referer( 'woocommerce-preview-order', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_GET['order_id'] ) ) {
			wp_die( -1 );
		}

		$order = wc_get_order( absint( $_GET['order_id'] ) );

		if ( $order ) {
			include_once __DIR__ . '/admin/list-tables/class-wc-admin-list-table-orders.php';

			wp_send_json_success( WC_Admin_List_Table_Orders::order_preview_get_order_details( $order ) );
		}
		wp_die();
	}

	/**
	 * Add an attribute row.
	 */
	public static function add_attribute() {
		ob_start();

		check_ajax_referer( 'add-attribute', 'security' );

		if ( ! current_user_can( 'edit_products' ) || ! isset( $_POST['taxonomy'], $_POST['i'] ) ) {
			wp_die( -1 );
		}

		$i             = absint( $_POST['i'] );
		$metabox_class = array();
		$attribute     = new WC_Product_Attribute();

		$attribute->set_id( wc_attribute_taxonomy_id_by_name( sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) ) );
		$attribute->set_name( sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) );
		/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
		$attribute->set_visible( apply_filters( 'woocommerce_attribute_default_visibility', 1 ) );
		$attribute->set_variation( apply_filters( 'woocommerce_attribute_default_is_variation', 1 ) );
		/* phpcs: enable */

		if ( $attribute->is_taxonomy() ) {
			$metabox_class[] = 'taxonomy';
			$metabox_class[] = $attribute->get_name();
		}

		include __DIR__ . '/admin/meta-boxes/views/html-product-attribute.php';
		wp_die();
	}

	/**
	 * Add a new attribute via ajax function.
	 */
	public static function add_new_attribute() {
		check_ajax_referer( 'add-attribute', 'security' );

		if ( current_user_can( 'manage_product_terms' ) && isset( $_POST['taxonomy'], $_POST['term'] ) ) {
			$taxonomy = esc_attr( wp_unslash( $_POST['taxonomy'] ) ); // phpcs:ignore
			$term     = wc_clean( wp_unslash( $_POST['term'] ) );

			if ( taxonomy_exists( $taxonomy ) ) {

				$result = wp_insert_term( $term, $taxonomy );

				if ( is_wp_error( $result ) ) {
					wp_send_json(
						array(
							'error' => $result->get_error_message(),
						)
					);
				} else {
					$term = get_term_by( 'id', $result['term_id'], $taxonomy );
					wp_send_json(
						array(
							'term_id' => $term->term_id,
							'name'    => $term->name,
							'slug'    => $term->slug,
						)
					);
				}
			}
		}
		wp_die( -1 );
	}

	/**
	 * Delete variations via ajax function.
	 */
	public static function remove_variations() {
		check_ajax_referer( 'delete-variations', 'security' );

		if ( current_user_can( 'edit_products' ) && isset( $_POST['variation_ids'] ) ) {
			$variation_ids = array_map( 'absint', (array) wp_unslash( $_POST['variation_ids'] ) );

			foreach ( $variation_ids as $variation_id ) {
				if ( 'product_variation' === get_post_type( $variation_id ) ) {
					$variation = wc_get_product( $variation_id );
					$variation->delete( true );
				}
			}
		}

		wp_die( -1 );
	}

	/**
	 * Save attributes via ajax.
	 */
	public static function save_attributes() {
		check_ajax_referer( 'save-attributes', 'security' );

		if ( ! current_user_can( 'edit_products' ) || ! isset( $_POST['data'], $_POST['post_id'] ) ) {
			wp_die( -1 );
		}

		$response = array();

		try {
			parse_str( wp_unslash( $_POST['data'] ), $data ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$product = self::create_product_with_attributes( $data );

			ob_start();
			$attributes = $product->get_attributes( 'edit' );
			$i          = -1;
			if ( ! empty( $data['attribute_names'] ) ) {
				foreach ( $data['attribute_names'] as $attribute_name ) {
					$attribute = isset( $attributes[ sanitize_title( $attribute_name ) ] ) ? $attributes[ sanitize_title( $attribute_name ) ] : false;
					if ( ! $attribute ) {
						continue;
					}
					$i++;
					$metabox_class = array();

					if ( $attribute->is_taxonomy() ) {
						$metabox_class[] = 'taxonomy';
						$metabox_class[] = $attribute->get_name();
					}

					include __DIR__ . '/admin/meta-boxes/views/html-product-attribute.php';
				}
			}

			$response['html'] = ob_get_clean();
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Save attributes and variations via ajax.
	 */
	public static function add_attributes_and_variations() {
		check_ajax_referer( 'add-attributes-and-variations', 'security' );

		if ( ! current_user_can( 'edit_products' ) || ! isset( $_POST['data'], $_POST['post_id'] ) ) {
			wp_die( -1 );
		}

		try {
			parse_str( wp_unslash( $_POST['data'] ), $data ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$product = self::create_product_with_attributes( $data );
			self::create_all_product_variations( $product );

			wp_send_json_success();
			wp_die();

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}
	/**
	 * Create product with attributes from POST data.
	 *
	 * @param  array $data Attribute data.
	 * @return mixed Product class.
	 */
	private static function create_product_with_attributes( $data ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['post_id'] ) ) {
			wp_die( -1 );
		}
		$attributes   = WC_Meta_Box_Product_Data::prepare_attributes( $data );
		$product_id   = absint( wp_unslash( $_POST['post_id'] ) );
		$product_type = ! empty( $_POST['product_type'] ) ? wc_clean( wp_unslash( $_POST['product_type'] ) ) : 'simple';
		$classname    = WC_Product_Factory::get_product_classname( $product_id, $product_type );
		$product      = new $classname( $product_id );
		$product->set_attributes( $attributes );
		$product->save();
		return $product;
	}
	/**
	 * Create all product variations from existing attributes.
	 *
	 * @param mixed $product Product class.
	 * @returns int Number of variations created.
	 */
	private static function create_all_product_variations( $product ) {
		$data_store = $product->get_data_store();
		if ( ! is_callable( array( $data_store, 'create_all_product_variations' ) ) ) {
			wp_die();
		}
		$number = $data_store->create_all_product_variations( $product, Constants::get_constant( 'WC_MAX_LINKED_VARIATIONS' ) );
		$data_store->sort_all_product_variations( $product->get_id() );
		return $number;
	}

	/**
	 * Add variation via ajax function.
	 */
	public static function add_variation() {
		check_ajax_referer( 'add-variation', 'security' );

		if ( ! current_user_can( 'edit_products' ) || ! isset( $_POST['post_id'], $_POST['loop'] ) ) {
			wp_die( -1 );
		}

		global $post; // Set $post global so its available, like within the admin screens.

		$product_id       = intval( $_POST['post_id'] );
		$post             = get_post( $product_id ); // phpcs:ignore
		$loop             = intval( $_POST['loop'] );
		$product_object   = wc_get_product_object( 'variable', $product_id ); // Forces type to variable in case product is unsaved.
		$variation_object = wc_get_product_object( 'variation' );
		$variation_object->set_parent_id( $product_id );
		$variation_object->set_attributes( array_fill_keys( array_map( 'sanitize_title', array_keys( $product_object->get_variation_attributes() ) ), '' ) );
		$variation_id   = $variation_object->save();
		$variation      = get_post( $variation_id );
		$variation_data = array_merge( get_post_custom( $variation_id ), wc_get_product_variation_attributes( $variation_id ) ); // kept for BW compatibility.
		include __DIR__ . '/admin/meta-boxes/views/html-variation-admin.php';
		wp_die();
	}

	/**
	 * Link all variations via ajax function.
	 */
	public static function link_all_variations() {
		check_ajax_referer( 'link-variations', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		wc_maybe_define_constant( 'WC_MAX_LINKED_VARIATIONS', 50 );
		wc_set_time_limit( 0 );

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

		if ( ! $post_id ) {
			wp_die();
		}

		$product        = wc_get_product( $post_id );
		$number_created = self::create_all_product_variations( $product );

		echo esc_html( $number_created );

		wp_die();
	}

	/**
	 * Delete download permissions via ajax function.
	 */
	public static function revoke_access_to_download() {
		check_ajax_referer( 'revoke-access', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['download_id'], $_POST['product_id'], $_POST['order_id'], $_POST['permission_id'] ) ) {
			wp_die( -1 );
		}
		$download_id   = wc_clean( wp_unslash( $_POST['download_id'] ) );
		$product_id    = intval( $_POST['product_id'] );
		$order_id      = intval( $_POST['order_id'] );
		$permission_id = absint( $_POST['permission_id'] );
		$data_store    = WC_Data_Store::load( 'customer-download' );
		$data_store->delete_by_id( $permission_id );

		do_action( 'woocommerce_ajax_revoke_access_to_product_download', $download_id, $product_id, $order_id, $permission_id );

		wp_die();
	}

	/**
	 * Grant download permissions via ajax function.
	 */
	public static function grant_access_to_download() {

		check_ajax_referer( 'grant-access', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['loop'], $_POST['order_id'], $_POST['product_ids'] ) ) {
			wp_die( -1 );
		}

		global $wpdb;

		$wpdb->hide_errors();

		$order_id     = intval( $_POST['order_id'] );
		$product_ids  = array_filter( array_map( 'absint', (array) wp_unslash( $_POST['product_ids'] ) ) );
		$loop         = intval( $_POST['loop'] );
		$file_counter = 0;
		$order        = wc_get_order( $order_id );

		if ( ! $order->get_billing_email() ) {
			wp_die();
		}

		$data  = array();
		$items = $order->get_items();

		// Check against order items first.
		foreach ( $items as $item ) {
			$product = $item->get_product();

			if ( $product && $product->exists() && in_array( $product->get_id(), $product_ids, true ) && $product->is_downloadable() ) {
				$data[ $product->get_id() ] = array(
					'files'      => $product->get_downloads(),
					'quantity'   => $item->get_quantity(),
					'order_item' => $item,
				);
			}
		}

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( isset( $data[ $product->get_id() ] ) ) {
				$download_data = $data[ $product->get_id() ];
			} else {
				$download_data = array(
					'files'      => $product->get_downloads(),
					'quantity'   => 1,
					'order_item' => null,
				);
			}

			if ( ! empty( $download_data['files'] ) ) {
				foreach ( $download_data['files'] as $download_id => $file ) {
					$inserted_id = wc_downloadable_file_permission( $download_id, $product->get_id(), $order, $download_data['quantity'], $download_data['order_item'] );
					if ( $inserted_id ) {
						$download = new WC_Customer_Download( $inserted_id );
						$loop ++;
						$file_counter ++;

						if ( $file->get_name() ) {
							$file_count = $file->get_name();
						} else {
							/* translators: %d file count */
							$file_count = sprintf( __( 'File %d', 'woocommerce' ), $file_counter );
						}
						include __DIR__ . '/admin/meta-boxes/views/html-order-download-permission.php';
					}
				}
			}
		}
		wp_die();
	}

	/**
	 * Get customer details via ajax.
	 */
	public static function get_customer_details() {
		check_ajax_referer( 'get-customer-details', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['user_id'] ) ) {
			wp_die( -1 );
		}

		$user_id  = absint( $_POST['user_id'] );
		$customer = new WC_Customer( $user_id );

		if ( has_filter( 'woocommerce_found_customer_details' ) ) {
			wc_deprecated_function( 'The woocommerce_found_customer_details filter', '3.0', 'woocommerce_ajax_get_customer_details' );
		}

		$data                  = $customer->get_data();
		$data['date_created']  = $data['date_created'] ? $data['date_created']->getTimestamp() : null;
		$data['date_modified'] = $data['date_modified'] ? $data['date_modified']->getTimestamp() : null;

		$customer_data = apply_filters( 'woocommerce_ajax_get_customer_details', $data, $customer, $user_id );
		wp_send_json( $customer_data );
	}

	/**
	 * Add order item via ajax. Used on the edit order screen in WP Admin.
	 *
	 * @throws Exception If order is invalid.
	 */
	public static function add_order_item() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		if ( ! isset( $_POST['order_id'] ) ) {
			throw new Exception( __( 'Invalid order', 'woocommerce' ) );
		}
		$order_id = absint( wp_unslash( $_POST['order_id'] ) );

		// If we passed through items it means we need to save first before adding a new one.
		$items = ( ! empty( $_POST['items'] ) ) ? wp_unslash( $_POST['items'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$items_to_add = isset( $_POST['data'] ) ? array_filter( wp_unslash( (array) $_POST['data'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		try {
			$response = self::maybe_add_order_item( $order_id, $items, $items_to_add );
			wp_send_json_success( $response );
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Add order item via AJAX. This is refactored for better unit testing.
	 *
	 * @param int          $order_id     ID of order to add items to.
	 * @param string|array $items        Existing items in order. Empty string if no items to add.
	 * @param array        $items_to_add Array of items to add.
	 *
	 * @return array     Fragments to render and notes HTML.
	 * @throws Exception When unable to add item.
	 */
	private static function maybe_add_order_item( $order_id, $items, $items_to_add ) {
		try {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new Exception( __( 'Invalid order', 'woocommerce' ) );
			}

			if ( ! empty( $items ) ) {
				$save_items = array();
				parse_str( $items, $save_items );
				wc_save_order_items( $order->get_id(), $save_items );
			}

			// Add items to order.
			$order_notes = array();
			$added_items = array();

			foreach ( $items_to_add as $item ) {
				if ( ! isset( $item['id'], $item['qty'] ) || empty( $item['id'] ) ) {
					continue;
				}
				$product_id = absint( $item['id'] );
				$qty        = wc_stock_amount( $item['qty'] );
				$product    = wc_get_product( $product_id );

				if ( ! $product ) {
					throw new Exception( __( 'Invalid product ID', 'woocommerce' ) . ' ' . $product_id );
				}
				if ( 'variable' === $product->get_type() ) {
					/* translators: %s product name */
					throw new Exception( sprintf( __( '%s is a variable product parent and cannot be added.', 'woocommerce' ), $product->get_name() ) );
				}
				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'woocommerce_ajax_add_order_item_validation', $validation_error, $product, $order, $qty );

				if ( $validation_error->get_error_code() ) {
					/* translators: %s: error message */
					throw new Exception( sprintf( __( 'Error: %s', 'woocommerce' ), $validation_error->get_error_message() ) );
				}
				$item_id                 = $order->add_product( $product, $qty, array( 'order' => $order ) );
				$item                    = apply_filters( 'woocommerce_ajax_order_item', $order->get_item( $item_id ), $item_id, $order, $product );
				$added_items[ $item_id ] = $item;
				$order_notes[ $item_id ] = $product->get_formatted_name();

				// We do not perform any stock operations here because they will be handled when order is moved to a status where stock operations are applied (like processing, completed etc).

				do_action( 'woocommerce_ajax_add_order_item_meta', $item_id, $item, $order );
			}

			/* translators: %s item name. */
			$order->add_order_note( sprintf( __( 'Added line items: %s', 'woocommerce' ), implode( ', ', $order_notes ) ), false, true );

			do_action( 'woocommerce_ajax_order_items_added', $added_items, $order );

			$data = get_post_meta( $order_id );

			// Get HTML to return.
			ob_start();
			include __DIR__ . '/admin/meta-boxes/views/html-order-items.php';
			$items_html = ob_get_clean();

			ob_start();
			$notes = wc_get_order_notes( array( 'order_id' => $order_id ) );
			include __DIR__ . '/admin/meta-boxes/views/html-order-notes.php';
			$notes_html = ob_get_clean();

			return array(
				'html'       => $items_html,
				'notes_html' => $notes_html,
			);
		} catch ( Exception $e ) {
			throw $e; // Forward exception to caller.
		}
	}

	/**
	 * Add order fee via ajax.
	 *
	 * @throws Exception If order is invalid.
	 */
	public static function add_order_fee() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$response = array();

		try {
			$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new Exception( __( 'Invalid order', 'woocommerce' ) );
			}

			$amount = isset( $_POST['amount'] ) ? wc_clean( wp_unslash( $_POST['amount'] ) ) : 0;

			$calculate_tax_args = array(
				'country'  => isset( $_POST['country'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['country'] ) ) ) : '',
				'state'    => isset( $_POST['state'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['state'] ) ) ) : '',
				'postcode' => isset( $_POST['postcode'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['postcode'] ) ) ) : '',
				'city'     => isset( $_POST['city'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['city'] ) ) ) : '',
			);

			if ( strstr( $amount, '%' ) ) {
				// We need to calculate totals first, so that $order->get_total() is correct.
				$order->calculate_totals( false );
				$formatted_amount = $amount;
				$percent          = floatval( trim( $amount, '%' ) );
				$amount           = $order->get_total() * ( $percent / 100 );
			} else {
				$amount           = floatval( $amount );
				$formatted_amount = wc_price( $amount, array( 'currency' => $order->get_currency() ) );
			}

			$fee = new WC_Order_Item_Fee();
			$fee->set_amount( $amount );
			$fee->set_total( $amount );
			/* translators: %s fee amount */
			$fee->set_name( sprintf( __( '%s fee', 'woocommerce' ), wc_clean( $formatted_amount ) ) );

			$order->add_item( $fee );
			$order->calculate_taxes( $calculate_tax_args );
			$order->calculate_totals( false );
			$order->save();

			ob_start();
			include __DIR__ . '/admin/meta-boxes/views/html-order-items.php';
			$response['html'] = ob_get_clean();
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Add order shipping cost via ajax.
	 *
	 * @throws Exception If order is invalid.
	 */
	public static function add_order_shipping() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$response = array();

		try {
			$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new Exception( __( 'Invalid order', 'woocommerce' ) );
			}

			$order_taxes      = $order->get_taxes();
			$shipping_methods = WC()->shipping() ? WC()->shipping()->load_shipping_methods() : array();

			// Add new shipping.
			$item = new WC_Order_Item_Shipping();
			$item->set_shipping_rate( new WC_Shipping_Rate() );
			$item->set_order_id( $order_id );
			$item_id = $item->save();

			ob_start();
			include __DIR__ . '/admin/meta-boxes/views/html-order-shipping.php';
			$response['html'] = ob_get_clean();
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Add order tax column via ajax.
	 *
	 * @throws Exception If order or tax rate is invalid.
	 */
	public static function add_order_tax() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$response = array();

		try {
			$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new Exception( __( 'Invalid order', 'woocommerce' ) );
			}

			$rate_id = isset( $_POST['rate_id'] ) ? absint( $_POST['rate_id'] ) : '';

			if ( ! $rate_id ) {
				throw new Exception( __( 'Invalid rate', 'woocommerce' ) );
			}

			$data = get_post_meta( $order_id );

			// Add new tax.
			$item = new WC_Order_Item_Tax();
			$item->set_rate( $rate_id );
			$item->set_order_id( $order_id );
			$item->save();

			ob_start();
			include __DIR__ . '/admin/meta-boxes/views/html-order-items.php';
			$response['html'] = ob_get_clean();
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Add order discount via ajax.
	 *
	 * @throws Exception If order or coupon is invalid.
	 */
	public static function add_coupon_discount() {
		wc_get_container()->get( CouponsController::class )->add_coupon_discount_via_ajax();
	}

	/**
	 * Remove coupon from an order via ajax.
	 *
	 * @throws Exception If order or coupon is invalid.
	 */
	public static function remove_order_coupon() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$response = array();

		try {
			$order_id           = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
			$order              = wc_get_order( $order_id );
			$calculate_tax_args = array(
				'country'  => isset( $_POST['country'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['country'] ) ) ) : '',
				'state'    => isset( $_POST['state'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['state'] ) ) ) : '',
				'postcode' => isset( $_POST['postcode'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['postcode'] ) ) ) : '',
				'city'     => isset( $_POST['city'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['city'] ) ) ) : '',
			);

			if ( ! $order ) {
				throw new Exception( __( 'Invalid order', 'woocommerce' ) );
			}

			$coupon = ArrayUtil::get_value_or_default( $_POST, 'coupon' );
			if ( StringUtil::is_null_or_whitespace( $coupon ) ) {
				throw new Exception( __( 'Invalid coupon', 'woocommerce' ) );
			}

			$code = wc_format_coupon_code( wp_unslash( $coupon ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( $order->remove_coupon( $code ) ) {
				// translators: %s coupon code.
				$order->add_order_note( esc_html( sprintf( __( 'Coupon removed: "%s".', 'woocommerce' ), $code ) ), 0, true );
			}
			$order->calculate_taxes( $calculate_tax_args );
			$order->calculate_totals( false );

			ob_start();
			include __DIR__ . '/admin/meta-boxes/views/html-order-items.php';
			$response['html'] = ob_get_clean();

			ob_start();
			$notes = wc_get_order_notes( array( 'order_id' => $order_id ) );
			include __DIR__ . '/admin/meta-boxes/views/html-order-notes.php';
			$response['notes_html'] = ob_get_clean();

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Remove an order item.
	 *
	 * @throws Exception If order is invalid.
	 */
	public static function remove_order_item() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['order_id'], $_POST['order_item_ids'] ) ) {
			wp_die( -1 );
		}

		$response = array();

		try {
			$order_id = absint( $_POST['order_id'] );
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new Exception( __( 'Invalid order', 'woocommerce' ) );
			}

			if ( ! isset( $_POST['order_item_ids'] ) ) {
				throw new Exception( __( 'Invalid items', 'woocommerce' ) );
			}

			$order_item_ids     = wp_unslash( $_POST['order_item_ids'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$items              = ( ! empty( $_POST['items'] ) ) ? wp_unslash( $_POST['items'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$calculate_tax_args = array(
				'country'  => isset( $_POST['country'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['country'] ) ) ) : '',
				'state'    => isset( $_POST['state'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['state'] ) ) ) : '',
				'postcode' => isset( $_POST['postcode'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['postcode'] ) ) ) : '',
				'city'     => isset( $_POST['city'] ) ? wc_strtoupper( wc_clean( wp_unslash( $_POST['city'] ) ) ) : '',
			);

			if ( is_numeric( $order_item_ids ) ) {
				$order_item_ids = array( $order_item_ids );
			}

			// If we passed through items it means we need to save first before deleting.
			if ( ! empty( $items ) ) {
				$save_items = array();
				parse_str( $items, $save_items );
				wc_save_order_items( $order->get_id(), $save_items );
			}

			if ( ! empty( $order_item_ids ) ) {

				foreach ( $order_item_ids as $item_id ) {
					$item_id = absint( $item_id );
					$item    = $order->get_item( $item_id );

					if ( ! $item ) {
						continue;
					}

					// Before deleting the item, adjust any stock values already reduced.
					if ( $item->is_type( 'line_item' ) ) {
						$changed_stock = wc_maybe_adjust_line_item_product_stock( $item, 0 );

						if ( $changed_stock && ! is_wp_error( $changed_stock ) ) {
							/* translators: %1$s: item name %2$s: stock change */
							$order->add_order_note( sprintf( __( 'Deleted %1$s and adjusted stock (%2$s)', 'woocommerce' ), $item->get_name(), $changed_stock['from'] . '&rarr;' . $changed_stock['to'] ), false, true );
						} else {
							/* translators: %s item name. */
							$order->add_order_note( sprintf( __( 'Deleted %s', 'woocommerce' ), $item->get_name() ), false, true );
						}
					}

					wc_delete_order_item( $item_id );
				}
			}

			$order = wc_get_order( $order_id );
			$order->calculate_taxes( $calculate_tax_args );
			$order->calculate_totals( false );

			/**
			 * Fires after order items are removed.
			 *
			 * @since 5.2.0
			 *
			 * @param int $item_id WC item ID.
			 * @param WC_Order_Item|false $item As returned by $order->get_item( $item_id ).
			 * @param bool|array|WP_Error $changed_store Result of wc_maybe_adjust_line_item_product_stock().
			 * @param bool|WC_Order|WC_Order_Refund $order As returned by wc_get_order().
			 */
			do_action( 'woocommerce_ajax_order_items_removed', $item_id ?? 0, $item ?? false, $changed_stock ?? false, $order );

			// Get HTML to return.
			ob_start();
			include __DIR__ . '/admin/meta-boxes/views/html-order-items.php';
			$items_html = ob_get_clean();

			ob_start();
			$notes = wc_get_order_notes( array( 'order_id' => $order_id ) );
			include __DIR__ . '/admin/meta-boxes/views/html-order-notes.php';
			$notes_html = ob_get_clean();

			wp_send_json_success(
				array(
					'html'       => $items_html,
					'notes_html' => $notes_html,
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Remove an order tax.
	 *
	 * @throws Exception If there is an error whilst deleting the rate.
	 */
	public static function remove_order_tax() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['order_id'], $_POST['rate_id'] ) ) {
			wp_die( -1 );
		}

		$response = array();

		try {
			$order_id = absint( $_POST['order_id'] );
			$rate_id  = absint( $_POST['rate_id'] );

			$order = wc_get_order( $order_id );
			if ( ! $order->is_editable() ) {
				throw new Exception( __( 'Order not editable', 'woocommerce' ) );
			}

			wc_delete_order_item( $rate_id );

			// Need to load order again after deleting to have latest items before calculating.
			$order = wc_get_order( $order_id );
			$order->calculate_totals( false );

			ob_start();
			include __DIR__ . '/admin/meta-boxes/views/html-order-items.php';
			$response['html'] = ob_get_clean();
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Calc line tax.
	 */
	public static function calc_line_taxes() {
		wc_get_container()->get( TaxesController::class )->calc_line_taxes_via_ajax();
	}

	/**
	 * Save order items via ajax.
	 */
	public static function save_order_items() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['order_id'], $_POST['items'] ) ) {
			wp_die( -1 );
		}

		if ( isset( $_POST['order_id'], $_POST['items'] ) ) {
			$order_id = absint( $_POST['order_id'] );

			// Parse the jQuery serialized items.
			$items = array();
			parse_str( wp_unslash( $_POST['items'] ), $items ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			// Save order items.
			wc_save_order_items( $order_id, $items );

			// Return HTML items.
			$order = wc_get_order( $order_id );

			// Get HTML to return.
			ob_start();
			include __DIR__ . '/admin/meta-boxes/views/html-order-items.php';
			$items_html = ob_get_clean();

			ob_start();
			$notes = wc_get_order_notes( array( 'order_id' => $order_id ) );
			include __DIR__ . '/admin/meta-boxes/views/html-order-notes.php';
			$notes_html = ob_get_clean();

			wp_send_json_success(
				array(
					'html'       => $items_html,
					'notes_html' => $notes_html,
				)
			);
		}
		wp_die();
	}

	/**
	 * Load order items via ajax.
	 */
	public static function load_order_items() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['order_id'] ) ) {
			wp_die( -1 );
		}

		// Return HTML items.
		$order_id = absint( $_POST['order_id'] );
		$order    = wc_get_order( $order_id );
		include __DIR__ . '/admin/meta-boxes/views/html-order-items.php';
		wp_die();
	}

	/**
	 * Add order note via ajax.
	 */
	public static function add_order_note() {
		check_ajax_referer( 'add-order-note', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['post_id'], $_POST['note'], $_POST['note_type'] ) ) {
			wp_die( -1 );
		}

		$post_id   = absint( $_POST['post_id'] );
		$note      = wp_kses_post( trim( wp_unslash( $_POST['note'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$note_type = wc_clean( wp_unslash( $_POST['note_type'] ) );

		$is_customer_note = ( 'customer' === $note_type ) ? 1 : 0;

		if ( $post_id > 0 ) {
			$order      = wc_get_order( $post_id );
			$comment_id = $order->add_order_note( $note, $is_customer_note, true );
			$note       = wc_get_order_note( $comment_id );

			$note_classes   = array( 'note' );
			$note_classes[] = $is_customer_note ? 'customer-note' : '';
			$note_classes   = apply_filters( 'woocommerce_order_note_class', array_filter( $note_classes ), $note );
			?>
			<li rel="<?php echo absint( $note->id ); ?>" class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
				<div class="note_content">
					<?php echo wp_kses_post( wpautop( wptexturize( make_clickable( $note->content ) ) ) ); ?>
				</div>
				<p class="meta">
					<abbr class="exact-date" title="<?php echo esc_attr( $note->date_created->date( 'y-m-d h:i:s' ) ); ?>">
						<?php
						/* translators: $1: Date created, $2 Time created */
						printf( esc_html__( 'added on %1$s at %2$s', 'woocommerce' ), esc_html( $note->date_created->date_i18n( wc_date_format() ) ), esc_html( $note->date_created->date_i18n( wc_time_format() ) ) );
						?>
					</abbr>
					<?php
					if ( 'system' !== $note->added_by ) :
						/* translators: %s: note author */
						printf( ' ' . esc_html__( 'by %s', 'woocommerce' ), esc_html( $note->added_by ) );
					endif;
					?>
					<a href="#" class="delete_note" role="button"><?php esc_html_e( 'Delete note', 'woocommerce' ); ?></a>
				</p>
			</li>
			<?php
		}
		wp_die();
	}

	/**
	 * Delete order note via ajax.
	 */
	public static function delete_order_note() {
		check_ajax_referer( 'delete-order-note', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['note_id'] ) ) {
			wp_die( -1 );
		}

		$note_id = (int) $_POST['note_id'];

		if ( $note_id > 0 ) {
			wc_delete_order_note( $note_id );
		}
		wp_die();
	}

	/**
	 * Search for products and echo json.
	 *
	 * @param string $term (default: '') Term to search for.
	 * @param bool   $include_variations in search or not.
	 */
	public static function json_search_products( $term = '', $include_variations = false ) {
		check_ajax_referer( 'search-products', 'security' );

		if ( empty( $term ) && isset( $_GET['term'] ) ) {
			$term = (string) wc_clean( wp_unslash( $_GET['term'] ) );
		}

		if ( empty( $term ) ) {
			wp_die();
		}

		if ( ! empty( $_GET['limit'] ) ) {
			$limit = absint( $_GET['limit'] );
		} else {
			$limit = absint( apply_filters( 'woocommerce_json_search_limit', 30 ) );
		}

		$include_ids = ! empty( $_GET['include'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['include'] ) ) : array();
		$exclude_ids = ! empty( $_GET['exclude'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['exclude'] ) ) : array();

		$exclude_types = array();
		if ( ! empty( $_GET['exclude_type'] ) ) {
			// Support both comma-delimited and array format inputs.
			$exclude_types = wp_unslash( $_GET['exclude_type'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! is_array( $exclude_types ) ) {
				$exclude_types = explode( ',', $exclude_types );
			}

			// Sanitize the excluded types against valid product types.
			foreach ( $exclude_types as &$exclude_type ) {
				$exclude_type = strtolower( trim( $exclude_type ) );
			}
			$exclude_types = array_intersect(
				array_merge( array( 'variation' ), array_keys( wc_get_product_types() ) ),
				$exclude_types
			);
		}

		$data_store = WC_Data_Store::load( 'product' );
		$ids        = $data_store->search_products( $term, '', (bool) $include_variations, false, $limit, $include_ids, $exclude_ids );

		$products = array();

		foreach ( $ids as $id ) {
			$product_object = wc_get_product( $id );

			if ( ! wc_products_array_filter_readable( $product_object ) ) {
				continue;
			}

			$formatted_name = $product_object->get_formatted_name();
			$managing_stock = $product_object->managing_stock();

			if ( in_array( $product_object->get_type(), $exclude_types, true ) ) {
				continue;
			}

			if ( $managing_stock && ! empty( $_GET['display_stock'] ) ) {
				$stock_amount = $product_object->get_stock_quantity();
				/* Translators: %d stock amount */
				$formatted_name .= ' &ndash; ' . sprintf( __( 'Stock: %d', 'woocommerce' ), wc_format_stock_quantity_for_display( $stock_amount, $product_object ) );
			}

			$products[ $product_object->get_id() ] = rawurldecode( wp_strip_all_tags( $formatted_name ) );
		}

		wp_send_json( apply_filters( 'woocommerce_json_search_found_products', $products ) );
	}

	/**
	 * Search for product variations and return json.
	 *
	 * @see WC_AJAX::json_search_products()
	 */
	public static function json_search_products_and_variations() {
		self::json_search_products( '', true );
	}

	/**
	 * Search for downloadable product variations and return json.
	 *
	 * @see WC_AJAX::json_search_products()
	 */
	public static function json_search_downloadable_products_and_variations() {
		check_ajax_referer( 'search-products', 'security' );

		if ( ! empty( $_GET['limit'] ) ) {
			$limit = absint( $_GET['limit'] );
		} else {
			$limit = absint( apply_filters( 'woocommerce_json_search_limit', 30 ) );
		}

		$include_ids = ! empty( $_GET['include'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['include'] ) ) : array();
		$exclude_ids = ! empty( $_GET['exclude'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['exclude'] ) ) : array();

		$term       = isset( $_GET['term'] ) ? (string) wc_clean( wp_unslash( $_GET['term'] ) ) : '';
		$data_store = WC_Data_Store::load( 'product' );
		$ids        = $data_store->search_products( $term, 'downloadable', true, false, $limit );

		$product_objects = array_filter( array_map( 'wc_get_product', $ids ), 'wc_products_array_filter_readable' );
		$products        = array();

		foreach ( $product_objects as $product_object ) {
			$products[ $product_object->get_id() ] = rawurldecode( wp_strip_all_tags( $product_object->get_formatted_name() ) );
		}

		wp_send_json( $products );
	}

	/**
	 * Search for customers and return json.
	 */
	public static function json_search_customers() {
		ob_start();

		check_ajax_referer( 'search-customers', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$term  = isset( $_GET['term'] ) ? (string) wc_clean( wp_unslash( $_GET['term'] ) ) : '';
		$limit = 0;

		if ( empty( $term ) ) {
			wp_die();
		}

		$ids = array();
		// Search by ID.
		if ( is_numeric( $term ) ) {
			$customer = new WC_Customer( intval( $term ) );

			// Customer does not exists.
			if ( 0 !== $customer->get_id() ) {
				$ids = array( $customer->get_id() );
			}
		}

		// Usernames can be numeric so we first check that no users was found by ID before searching for numeric username, this prevents performance issues with ID lookups.
		if ( empty( $ids ) ) {
			$data_store = WC_Data_Store::load( 'customer' );

			// If search is smaller than 3 characters, limit result set to avoid
			// too many rows being returned.
			if ( 3 > strlen( $term ) ) {
				$limit = 20;
			}
			$ids = $data_store->search_customers( $term, $limit );
		}

		$found_customers = array();

		if ( ! empty( $_GET['exclude'] ) ) {
			$ids = array_diff( $ids, array_map( 'absint', (array) wp_unslash( $_GET['exclude'] ) ) );
		}

		foreach ( $ids as $id ) {
			$customer = new WC_Customer( $id );
			/* translators: 1: user display name 2: user ID 3: user email */
			$found_customers[ $id ] = sprintf(
				/* translators: $1: customer name, $2 customer id, $3: customer email */
				esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
				$customer->get_first_name() . ' ' . $customer->get_last_name(),
				$customer->get_id(),
				$customer->get_email()
			);
		}

		wp_send_json( apply_filters( 'woocommerce_json_search_found_customers', $found_customers ) );
	}

	/**
	 * Search for categories and return json.
	 */
	public static function json_search_categories() {
		ob_start();

		check_ajax_referer( 'search-categories', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		$search_text = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';

		if ( ! $search_text ) {
			wp_die();
		}

		$show_empty       = isset( $_GET['show_empty'] ) ? wp_validate_boolean( wc_clean( wp_unslash( $_GET['show_empty'] ) ) ) : false;
		$found_categories = array();
		$args             = array(
			'taxonomy'   => array( 'product_cat' ),
			'orderby'    => 'id',
			'order'      => 'ASC',
			'hide_empty' => ! $show_empty,
			'fields'     => 'all',
			'name__like' => $search_text,
		);

		$terms = get_terms( $args );

		if ( $terms ) {
			foreach ( $terms as $term ) {
				$term->formatted_name = '';

				$ancestors = array();
				if ( $term->parent ) {
					$ancestors = array_reverse( get_ancestors( $term->term_id, 'product_cat' ) );
					foreach ( $ancestors as $ancestor ) {
						$ancestor_term = get_term( $ancestor, 'product_cat' );
						if ( $ancestor_term ) {
							$term->formatted_name .= $ancestor_term->name . ' > ';
						}
					}
				}

				$term->parents                      = $ancestors;
				$term->formatted_name              .= $term->name . ' (' . $term->count . ')';
				$found_categories[ $term->term_id ] = $term;
			}
		}

		wp_send_json( apply_filters( 'woocommerce_json_search_found_categories', $found_categories ) );
	}

	/**
	 * Search for categories and return json.
	 */
	public static function json_search_categories_tree() {
		ob_start();

		check_ajax_referer( 'search-categories', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		$search_text = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';
		$number      = isset( $_GET['number'] ) ? absint( $_GET['number'] ) : 50;

		$args = array(
			'taxonomy'   => array( 'product_cat' ),
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => false,
			'fields'     => 'all',
			'number'     => $number,
			'name__like' => $search_text,
		);

		$terms = get_terms( $args );

		$terms_map = array();

		if ( $terms ) {
			foreach ( $terms as $term ) {
				$terms_map[ $term->term_id ] = $term;

				if ( $term->parent ) {
					$ancestors     = get_ancestors( $term->term_id, 'product_cat' );
					$current_child = $term;
					foreach ( $ancestors as $ancestor ) {
						if ( ! isset( $terms_map[ $ancestor ] ) ) {
							$ancestor_term          = get_term( $ancestor, 'product_cat' );
							$terms_map[ $ancestor ] = $ancestor_term;
						}
						if ( ! $terms_map[ $ancestor ]->children ) {
							$terms_map[ $ancestor ]->children = array();
						}
						$item_exists = count(
							array_filter(
								$terms_map[ $ancestor ]->children,
								function( $term ) use ( $current_child ) {
									return $term->term_id === $current_child->term_id;
								}
							)
						) === 1;
						if ( ! $item_exists ) {
							$terms_map[ $ancestor ]->children[] = $current_child;
						}
						$current_child = $terms_map[ $ancestor ];
					}
				}
			}
		}
		$parent_terms = array_filter(
			array_values( $terms_map ),
			function( $term ) {
				return 0 === $term->parent;
			}
		);
		wp_send_json( apply_filters( 'woocommerce_json_search_found_categories', $parent_terms ) );
	}

	/**
	 * Search for taxonomy terms and return json.
	 */
	public static function json_search_taxonomy_terms() {
		ob_start();

		check_ajax_referer( 'search-taxonomy-terms', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		$search_text = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';
		$limit       = isset( $_GET['limit'] ) ? absint( wp_unslash( $_GET['limit'] ) ) : null;
		$taxonomy    = isset( $_GET['taxonomy'] ) ? wc_clean( wp_unslash( $_GET['taxonomy'] ) ) : '';
		$orderby     = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : 'name';
		$order       = isset( $_GET['order'] ) ? wc_clean( wp_unslash( $_GET['order'] ) ) : 'ASC';

		$args = array(
			'taxonomy'        => $taxonomy,
			'orderby'         => $orderby,
			'order'           => $order,
			'hide_empty'      => false,
			'fields'          => 'all',
			'number'          => $limit,
			'name__like'      => $search_text,
			'suppress_filter' => true,
		);

		/**
		 * Filter the product attribute term arguments used for search.
		 *
		 * @since 3.4.0
		 * @param array $args The search arguments.
		 */
		$terms = get_terms( apply_filters( 'woocommerce_product_attribute_terms', $args ) );

		/**
		 * Filter the product attribute terms search results.
		 *
		 * @since 7.0.0
		 * @param array  $terms    The list of matched terms.
		 * @param string $taxonomy The terms taxonomy.
		 */
		wp_send_json( apply_filters( 'woocommerce_json_search_found_product_attribute_terms', $terms, $taxonomy ) );
	}

	/**
	 * Search for product attributes and return json.
	 */
	public static function json_search_product_attributes() {
		ob_start();

		check_ajax_referer( 'search-product-attributes', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		$limit       = isset( $_GET['limit'] ) ? absint( wp_unslash( $_GET['limit'] ) ) : 100;
		$search_text = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';

		$attributes = wc_get_attribute_taxonomies();

		$found_product_categories = array();

		foreach ( $attributes as $attribute_obj ) {
			if ( ! $search_text || false !== stripos( $attribute_obj->attribute_label, $search_text ) ) {
				$found_product_categories[] = array(
					'id'           => (int) $attribute_obj->attribute_id,
					'name'         => $attribute_obj->attribute_label,
					'slug'         => wc_attribute_taxonomy_name( $attribute_obj->attribute_name ),
					'type'         => $attribute_obj->attribute_type,
					'order_by'     => $attribute_obj->attribute_orderby,
					'has_archives' => (bool) $attribute_obj->attribute_public,
				);
			}
			if ( count( $found_product_categories ) >= $limit ) {
				break;
			}
		}

		/**
		 * Filter the product category search results.
		 *
		 * @since 7.0.0
		 * @param array   $found_product_categories Array of matched product categories.
		 * @param string  $search_text              Search text.
		 */
		wp_send_json( apply_filters( 'woocommerce_json_search_found_product_categories', $found_product_categories, $search_text ) );
	}

	/**
	 * Ajax request handling for page searching.
	 */
	public static function json_search_pages() {
		ob_start();

		check_ajax_referer( 'search-pages', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( -1 );
		}

		$search_text = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';
		$limit       = isset( $_GET['limit'] ) ? absint( wp_unslash( $_GET['limit'] ) ) : -1;
		$exclude_ids = ! empty( $_GET['exclude'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['exclude'] ) ) : array();

		$args                 = array(
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'posts_per_page'         => $limit,
			'post_type'              => 'page',
			'post_status'            => array( 'publish', 'private', 'draft' ),
			's'                      => $search_text,
			'post__not_in'           => $exclude_ids,
		);
		$search_results_query = new WP_Query( $args );

		$pages_results = array();
		foreach ( $search_results_query->get_posts() as $post ) {
			$pages_results[ $post->ID ] = sprintf(
				/* translators: 1: page name 2: page ID */
				__( '%1$s (ID: %2$s)', 'woocommerce' ),
				get_the_title( $post ),
				$post->ID
			);
		}

		wp_send_json( apply_filters( 'woocommerce_json_search_found_pages', $pages_results ) );
	}

	/**
	 * Ajax request handling for categories ordering.
	 */
	public static function term_ordering() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['id'] ) ) {
			wp_die( -1 );
		}

		$id       = (int) $_POST['id'];
		$next_id  = isset( $_POST['nextid'] ) && (int) $_POST['nextid'] ? (int) $_POST['nextid'] : null;
		$taxonomy = isset( $_POST['thetaxonomy'] ) ? esc_attr( wp_unslash( $_POST['thetaxonomy'] ) ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$term     = get_term_by( 'id', $id, $taxonomy );

		if ( ! $id || ! $term || ! $taxonomy ) {
			wp_die( 0 );
		}

		wc_reorder_terms( $term, $next_id, $taxonomy );

		$children = get_terms( $taxonomy, "child_of=$id&menu_order=ASC&hide_empty=0" );

		if ( $term && count( $children ) ) {
			echo 'children';
			wp_die();
		}
		// phpcs:enable
	}

	/**
	 * Ajax request handling for product ordering.
	 *
	 * Based on Simple Page Ordering by 10up (https://wordpress.org/plugins/simple-page-ordering/).
	 */
	public static function product_ordering() {
		global $wpdb;

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['id'] ) ) {
			wp_die( -1 );
		}

		$sorting_id  = absint( $_POST['id'] );
		$previd      = absint( isset( $_POST['previd'] ) ? $_POST['previd'] : 0 );
		$nextid      = absint( isset( $_POST['nextid'] ) ? $_POST['nextid'] : 0 );
		$menu_orders = wp_list_pluck( $wpdb->get_results( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = 'product' ORDER BY menu_order ASC, post_title ASC" ), 'menu_order', 'ID' );
		$index       = 0;

		foreach ( $menu_orders as $id => $menu_order ) {
			$id = absint( $id );

			if ( $sorting_id === $id ) {
				continue;
			}
			if ( $nextid === $id ) {
				$index ++;
			}
			$index ++;
			$menu_orders[ $id ] = $index;

			if ( $wpdb->update( $wpdb->posts, array( 'menu_order' => $index ), array( 'ID' => $id ) ) ) {
				// We only need to clean the cache if the menu order was actually modified.
				clean_post_cache( $id );
			}

			/**
			 * When a single product has gotten it's ordering updated.
			 * $id The product ID
			 * $index The new menu order
			*/
			do_action( 'woocommerce_after_single_product_ordering', $id, $index );
		}

		if ( isset( $menu_orders[ $previd ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $previd ] + 1;
		} elseif ( isset( $menu_orders[ $nextid ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $nextid ] - 1;
		} else {
			$menu_orders[ $sorting_id ] = 0;
		}

		if ( $wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_orders[ $sorting_id ] ), array( 'ID' => $sorting_id ) ) ) {
			// We only need to clean the cache if the menu order was actually modified.
			clean_post_cache( $sorting_id );
		}

		WC_Post_Data::delete_product_query_transients();

		do_action( 'woocommerce_after_product_ordering', $sorting_id, $menu_orders );
		wp_send_json( $menu_orders );
		// phpcs:enable
	}

	/**
	 * Handle a refund via the edit order screen.
	 *
	 * @throws Exception To return errors.
	 */
	public static function refund_line_items() {
		ob_start();

		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$order_id               = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		$refund_amount          = isset( $_POST['refund_amount'] ) ? wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['refund_amount'] ) ), wc_get_price_decimals() ) : 0;
		$refunded_amount        = isset( $_POST['refunded_amount'] ) ? wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['refunded_amount'] ) ), wc_get_price_decimals() ) : 0;
		$refund_reason          = isset( $_POST['refund_reason'] ) ? sanitize_text_field( wp_unslash( $_POST['refund_reason'] ) ) : '';
		$line_item_qtys         = isset( $_POST['line_item_qtys'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_qtys'] ) ), true ) : array();
		$line_item_totals       = isset( $_POST['line_item_totals'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_totals'] ) ), true ) : array();
		$line_item_tax_totals   = isset( $_POST['line_item_tax_totals'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_tax_totals'] ) ), true ) : array();
		$api_refund             = isset( $_POST['api_refund'] ) && 'true' === $_POST['api_refund'];
		$restock_refunded_items = isset( $_POST['restock_refunded_items'] ) && 'true' === $_POST['restock_refunded_items'];
		$refund                 = false;
		$response               = array();

		try {
			$order      = wc_get_order( $order_id );
			$max_refund = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

			if ( ( ! $refund_amount && ( wc_format_decimal( 0, wc_get_price_decimals() ) !== $refund_amount ) ) || $max_refund < $refund_amount || 0 > $refund_amount ) {
				throw new Exception( __( 'Invalid refund amount', 'woocommerce' ) );
			}

			if ( wc_format_decimal( $order->get_total_refunded(), wc_get_price_decimals() ) !== $refunded_amount ) {
				throw new Exception( __( 'Error processing refund. Please try again.', 'woocommerce' ) );
			}

			// Prepare line items which we are refunding.
			$line_items = array();
			$item_ids   = array_unique( array_merge( array_keys( $line_item_qtys ), array_keys( $line_item_totals ) ) );

			foreach ( $item_ids as $item_id ) {
				$line_items[ $item_id ] = array(
					'qty'          => 0,
					'refund_total' => 0,
					'refund_tax'   => array(),
				);
			}
			foreach ( $line_item_qtys as $item_id => $qty ) {
				$line_items[ $item_id ]['qty'] = max( $qty, 0 );
			}
			foreach ( $line_item_totals as $item_id => $total ) {
				$line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
			}
			foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
				$line_items[ $item_id ]['refund_tax'] = array_filter( array_map( 'wc_format_decimal', $tax_totals ) );
			}

			// Create the refund object.
			$refund = wc_create_refund(
				array(
					'amount'         => $refund_amount,
					'reason'         => $refund_reason,
					'order_id'       => $order_id,
					'line_items'     => $line_items,
					'refund_payment' => $api_refund,
					'restock_items'  => $restock_refunded_items,
				)
			);

			if ( is_wp_error( $refund ) ) {
				throw new Exception( $refund->get_error_message() );
			}

			if ( did_action( 'woocommerce_order_fully_refunded' ) ) {
				$response['status'] = 'fully_refunded';
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Delete a refund.
	 */
	public static function delete_refund() {
		check_ajax_referer( 'order-item', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_POST['refund_id'] ) ) {
			wp_die( -1 );
		}

		$refund_ids = array_map( 'absint', is_array( $_POST['refund_id'] ) ? wp_unslash( $_POST['refund_id'] ) : array( wp_unslash( $_POST['refund_id'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( $refund_ids as $refund_id ) {
			if ( $refund_id && 'shop_order_refund' === OrderUtil::get_order_type( $refund_id ) ) {
				$refund   = wc_get_order( $refund_id );
				$order_id = $refund->get_parent_id();
				$refund->delete( true );
				do_action( 'woocommerce_refund_deleted', $refund_id, $order_id );
			}
		}
		wp_die();
	}

	/**
	 * Triggered when clicking the rating footer.
	 */
	public static function rated() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( -1 );
		}
		update_option( 'woocommerce_admin_footer_text_rated', 1 );
		wp_die();
	}

	/**
	 * Create/Update API key.
	 *
	 * @throws Exception On invalid or empty description, user, or permissions.
	 */
	public static function update_api_key() {
		ob_start();

		global $wpdb;

		check_ajax_referer( 'update-api-key', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( -1 );
		}

		$response = array();

		try {
			if ( empty( $_POST['description'] ) ) {
				throw new Exception( __( 'Description is missing.', 'woocommerce' ) );
			}
			if ( empty( $_POST['user'] ) ) {
				throw new Exception( __( 'User is missing.', 'woocommerce' ) );
			}
			if ( empty( $_POST['permissions'] ) ) {
				throw new Exception( __( 'Permissions is missing.', 'woocommerce' ) );
			}

			$key_id      = isset( $_POST['key_id'] ) ? absint( $_POST['key_id'] ) : 0;
			$description = sanitize_text_field( wp_unslash( $_POST['description'] ) );
			$permissions = ( in_array( wp_unslash( $_POST['permissions'] ), array( 'read', 'write', 'read_write' ), true ) ) ? sanitize_text_field( wp_unslash( $_POST['permissions'] ) ) : 'read';
			$user_id     = absint( $_POST['user'] );

			// Check if current user can edit other users.
			if ( $user_id && ! current_user_can( 'edit_user', $user_id ) ) {
				if ( get_current_user_id() !== $user_id ) {
					throw new Exception( __( 'You do not have permission to assign API Keys to the selected user.', 'woocommerce' ) );
				}
			}

			if ( 0 < $key_id ) {
				$data = array(
					'user_id'     => $user_id,
					'description' => $description,
					'permissions' => $permissions,
				);

				$wpdb->update(
					$wpdb->prefix . 'woocommerce_api_keys',
					$data,
					array( 'key_id' => $key_id ),
					array(
						'%d',
						'%s',
						'%s',
					),
					array( '%d' )
				);

				$response                    = $data;
				$response['consumer_key']    = '';
				$response['consumer_secret'] = '';
				$response['message']         = __( 'API Key updated successfully.', 'woocommerce' );
			} else {
				$consumer_key    = 'ck_' . wc_rand_hash();
				$consumer_secret = 'cs_' . wc_rand_hash();

				$data = array(
					'user_id'         => $user_id,
					'description'     => $description,
					'permissions'     => $permissions,
					'consumer_key'    => wc_api_hash( $consumer_key ),
					'consumer_secret' => $consumer_secret,
					'truncated_key'   => substr( $consumer_key, -7 ),
				);

				$wpdb->insert(
					$wpdb->prefix . 'woocommerce_api_keys',
					$data,
					array(
						'%d',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
					)
				);

				if ( 0 === $wpdb->insert_id ) {
					throw new Exception( __( 'There was an error generating your API Key.', 'woocommerce' ) );
				}

				$key_id                      = $wpdb->insert_id;
				$response                    = $data;
				$response['consumer_key']    = $consumer_key;
				$response['consumer_secret'] = $consumer_secret;
				$response['message']         = __( 'API Key generated successfully. Make sure to copy your new keys now as the secret key will be hidden once you leave this page.', 'woocommerce' );
				$response['revoke_url']      = '<a style="color: #a00; text-decoration: none;" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'revoke-key' => $key_id ), admin_url( 'admin.php?page=wc-settings&tab=advanced&section=keys' ) ), 'revoke' ) ) . '">' . __( 'Revoke key', 'woocommerce' ) . '</a>';
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Load variations via AJAX.
	 */
	public static function load_variations() {
		ob_start();

		check_ajax_referer( 'load-variations', 'security' );

		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['product_id'] ) ) {
			wp_die( -1 );
		}

		// Set $post global so its available, like within the admin screens.
		global $post;

		$loop           = 0;
		$product_id     = absint( $_POST['product_id'] );
		$post           = get_post( $product_id ); // phpcs:ignore
		$product_object = wc_get_product( $product_id );
		$per_page       = ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10;
		$page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$variations     = wc_get_products(
			array(
				'status'  => array( 'private', 'publish' ),
				'type'    => 'variation',
				'parent'  => $product_id,
				'limit'   => $per_page,
				'page'    => $page,
				'orderby' => array(
					'menu_order' => 'ASC',
					'ID'         => 'DESC',
				),
				'return'  => 'objects',
			)
		);

		if ( $variations ) {
			wc_render_invalid_variation_notice( $product_object );

			foreach ( $variations as $variation_object ) {
				$variation_id   = $variation_object->get_id();
				$variation      = get_post( $variation_id );
				$variation_data = array_merge( get_post_custom( $variation_id ), wc_get_product_variation_attributes( $variation_id ) ); // kept for BW compatibility.
				include __DIR__ . '/admin/meta-boxes/views/html-variation-admin.php';
				$loop++;
			}
		}
		wp_die();
	}

	/**
	 * Save variations via AJAX.
	 */
	public static function save_variations() {
		ob_start();

		check_ajax_referer( 'save-variations', 'security' );

		// Check permissions again and make sure we have what we need.
		if ( ! current_user_can( 'edit_products' ) || empty( $_POST ) || empty( $_POST['product_id'] ) ) {
			wp_die( -1 );
		}

		$product_id                           = absint( $_POST['product_id'] );
		WC_Admin_Meta_Boxes::$meta_box_errors = array();
		WC_Meta_Box_Product_Data::save_variations( $product_id, get_post( $product_id ) );

		do_action( 'woocommerce_ajax_save_product_variations', $product_id );

		$errors = WC_Admin_Meta_Boxes::$meta_box_errors;

		if ( $errors ) {
			echo '<div class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '<button type="button" class="notice-dismiss"><span class="screen-reader-text">' . esc_html__( 'Dismiss this notice.', 'woocommerce' ) . '</span></button>';
			echo '</div>';

			delete_option( WC_Admin_Meta_Boxes::ERROR_STORE );
		}

		wp_die();
	}

	/**
	 * Bulk action - Toggle Enabled.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_toggle_enabled( $variations, $data ) {
		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			$variation->set_status( 'private' === $variation->get_status( 'edit' ) ? 'publish' : 'private' );
			$variation->save();
		}
	}

	/**
	 * Bulk action - Toggle Downloadable Checkbox.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_toggle_downloadable( $variations, $data ) {
		self::variation_bulk_toggle( $variations, 'downloadable' );
	}

	/**
	 * Bulk action - Toggle Virtual Checkbox.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_toggle_virtual( $variations, $data ) {
		self::variation_bulk_toggle( $variations, 'virtual' );
	}

	/**
	 * Bulk action - Toggle Manage Stock Checkbox.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_toggle_manage_stock( $variations, $data ) {
		self::variation_bulk_toggle( $variations, 'manage_stock' );
	}

	/**
	 * Bulk action - Set Regular Prices.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_regular_price( $variations, $data ) {
		self::variation_bulk_set( $variations, 'regular_price', $data['value'] );
	}

	/**
	 * Bulk action - Set Sale Prices.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_sale_price( $variations, $data ) {
		self::variation_bulk_set( $variations, 'sale_price', $data['value'] );
	}

	/**
	 * Bulk action - Set Stock Status as In Stock.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_stock_status_instock( $variations, $data ) {
		self::variation_bulk_set( $variations, 'stock_status', 'instock' );
	}

	/**
	 * Bulk action - Set Stock Status as Out of Stock.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_stock_status_outofstock( $variations, $data ) {
		self::variation_bulk_set( $variations, 'stock_status', 'outofstock' );
	}

	/**
	 * Bulk action - Set Stock Status as On Backorder.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_stock_status_onbackorder( $variations, $data ) {
		self::variation_bulk_set( $variations, 'stock_status', 'onbackorder' );
	}

	/**
	 * Bulk action - Set Stock.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_stock( $variations, $data ) {
		if ( ! isset( $data['value'] ) ) {
			return;
		}

		$quantity = wc_stock_amount( wc_clean( $data['value'] ) );

		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			if ( $variation->managing_stock() ) {
				$variation->set_stock_quantity( $quantity );
			} else {
				$variation->set_stock_quantity( null );
			}
			$variation->save();
		}
	}

	/**
	 * Bulk action - Set Low Stock Amount.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_low_stock_amount( $variations, $data ) {
		if ( ! isset( $data['value'] ) ) {
			return;
		}

		$low_stock_amount = wc_stock_amount( wc_clean( $data['value'] ) );

		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			if ( $variation->managing_stock() ) {
				$variation->set_low_stock_amount( $low_stock_amount );
			} else {
				$variation->set_low_stock_amount( '' );
			}
			$variation->save();
		}
	}

	/**
	 * Bulk action - Set Weight.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_weight( $variations, $data ) {
		self::variation_bulk_set( $variations, 'weight', $data['value'] );
	}

	/**
	 * Bulk action - Set Length.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_length( $variations, $data ) {
		self::variation_bulk_set( $variations, 'length', $data['value'] );
	}

	/**
	 * Bulk action - Set Width.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_width( $variations, $data ) {
		self::variation_bulk_set( $variations, 'width', $data['value'] );
	}

	/**
	 * Bulk action - Set Height.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_height( $variations, $data ) {
		self::variation_bulk_set( $variations, 'height', $data['value'] );
	}

	/**
	 * Bulk action - Set Download Limit.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_download_limit( $variations, $data ) {
		self::variation_bulk_set( $variations, 'download_limit', $data['value'] );
	}

	/**
	 * Bulk action - Set Download Expiry.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_download_expiry( $variations, $data ) {
		self::variation_bulk_set( $variations, 'download_expiry', $data['value'] );
	}

	/**
	 * Bulk action - Delete all.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_delete_all( $variations, $data ) {
		if ( isset( $data['allowed'] ) && 'true' === $data['allowed'] ) {
			foreach ( $variations as $variation_id ) {
				$variation = wc_get_product( $variation_id );
				$variation->delete( true );
			}
		}
	}

	/**
	 * Bulk action - Sale Schedule.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_sale_schedule( $variations, $data ) {
		if ( ! isset( $data['date_from'] ) && ! isset( $data['date_to'] ) ) {
			return;
		}

		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if ( 'false' !== $data['date_from'] ) {
				$variation->set_date_on_sale_from( wc_clean( $data['date_from'] ) );
			}

			if ( 'false' !== $data['date_to'] ) {
				$variation->set_date_on_sale_to( wc_clean( $data['date_to'] ) );
			}

			$variation->save();
		}
	}

	/**
	 * Bulk action - Increase Regular Prices.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_regular_price_increase( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, 'regular_price', '+', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Decrease Regular Prices.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_regular_price_decrease( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, 'regular_price', '-', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Increase Sale Prices.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_sale_price_increase( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, 'sale_price', '+', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Decrease Sale Prices.
	 *
	 * @param array $variations List of variations.
	 * @param array $data Data to set.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_action_variable_sale_price_decrease( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, 'sale_price', '-', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Set Price.
	 *
	 * @param array  $variations List of variations.
	 * @param string $field price being adjusted _regular_price or _sale_price.
	 * @param string $operator + or -.
	 * @param string $value Price or Percent.
	 *
	 * @used-by bulk_edit_variations
	 */
	private static function variation_bulk_adjust_price( $variations, $field, $operator, $value ) {
		foreach ( $variations as $variation_id ) {
			$variation   = wc_get_product( $variation_id );
			$field_value = $variation->{"get_$field"}( 'edit' );

			if ( '%' === substr( $value, -1 ) ) {
				$percent      = wc_format_decimal( substr( $value, 0, -1 ) );
				$field_value += NumberUtil::round( ( $field_value / 100 ) * $percent, wc_get_price_decimals() ) * "{$operator}1";
			} else {
				$field_value += $value * "{$operator}1";
			}

			$variation->{"set_$field"}( $field_value );
			$variation->save();
		}
	}

	/**
	 * Bulk set convenience function.
	 *
	 * @param array  $variations List of variations.
	 * @param string $field Field to set.
	 * @param string $value to set.
	 */
	private static function variation_bulk_set( $variations, $field, $value ) {
		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			$variation->{ "set_$field" }( wc_clean( $value ) );
			$variation->save();
		}
	}

	/**
	 * Bulk toggle convenience function.
	 *
	 * @param array  $variations List of variations.
	 * @param string $field Field to toggle.
	 */
	private static function variation_bulk_toggle( $variations, $field ) {
		foreach ( $variations as $variation_id ) {
			$variation  = wc_get_product( $variation_id );
			$prev_value = $variation->{ "get_$field" }( 'edit' );
			$variation->{ "set_$field" }( ! $prev_value );
			$variation->save();
		}
	}

	/**
	 * Bulk edit variations via AJAX.
	 *
	 * @uses WC_AJAX::variation_bulk_set()
	 * @uses WC_AJAX::variation_bulk_adjust_price()
	 * @uses WC_AJAX::variation_bulk_action_variable_sale_price_decrease()
	 * @uses WC_AJAX::variation_bulk_action_variable_sale_price_increase()
	 * @uses WC_AJAX::variation_bulk_action_variable_regular_price_decrease()
	 * @uses WC_AJAX::variation_bulk_action_variable_regular_price_increase()
	 * @uses WC_AJAX::variation_bulk_action_variable_sale_schedule()
	 * @uses WC_AJAX::variation_bulk_action_delete_all()
	 * @uses WC_AJAX::variation_bulk_action_variable_download_expiry()
	 * @uses WC_AJAX::variation_bulk_action_variable_download_limit()
	 * @uses WC_AJAX::variation_bulk_action_variable_height()
	 * @uses WC_AJAX::variation_bulk_action_variable_width()
	 * @uses WC_AJAX::variation_bulk_action_variable_length()
	 * @uses WC_AJAX::variation_bulk_action_variable_weight()
	 * @uses WC_AJAX::variation_bulk_action_variable_stock()
	 * @uses WC_AJAX::variation_bulk_action_variable_sale_price()
	 * @uses WC_AJAX::variation_bulk_action_variable_regular_price()
	 * @uses WC_AJAX::variation_bulk_action_toggle_manage_stock()
	 * @uses WC_AJAX::variation_bulk_action_toggle_virtual()
	 * @uses WC_AJAX::variation_bulk_action_toggle_downloadable()
	 * @uses WC_AJAX::variation_bulk_action_toggle_enabled
	 * @uses WC_AJAX::variation_bulk_action_variable_low_stock_amount()
	 */
	public static function bulk_edit_variations() {
		ob_start();

		check_ajax_referer( 'bulk-edit-variations', 'security' );

		// Check permissions again and make sure we have what we need.
		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['product_id'] ) || empty( $_POST['bulk_action'] ) ) {
			wp_die( -1 );
		}

		$product_id  = absint( $_POST['product_id'] );
		$bulk_action = wc_clean( wp_unslash( $_POST['bulk_action'] ) );
		$data        = ! empty( $_POST['data'] ) ? wc_clean( wp_unslash( $_POST['data'] ) ) : array();
		$variations  = array();

		if ( apply_filters( 'woocommerce_bulk_edit_variations_need_children', true ) ) {
			$variations = get_posts(
				array(
					'post_parent'    => $product_id,
					'posts_per_page' => -1,
					'post_type'      => 'product_variation',
					'fields'         => 'ids',
					'post_status'    => array( 'publish', 'private' ),
				)
			);
		}

		if ( method_exists( __CLASS__, "variation_bulk_action_$bulk_action" ) ) {
			call_user_func( array( __CLASS__, "variation_bulk_action_$bulk_action" ), $variations, $data );
		} else {
			do_action( 'woocommerce_bulk_edit_variations_default', $bulk_action, $data, $product_id, $variations );
		}

		do_action( 'woocommerce_bulk_edit_variations', $bulk_action, $data, $product_id, $variations );
		WC_Product_Variable::sync( $product_id );
		wc_delete_product_transients( $product_id );
		wp_die();
	}

	/**
	 * Handle submissions from assets/js/settings-views-html-settings-tax.js Backbone model.
	 */
	public static function tax_rates_save_changes() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['wc_tax_nonce'], $_POST['changes'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		$current_class = ! empty( $_POST['current_class'] ) ? wp_unslash( $_POST['current_class'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! wp_verify_nonce( wp_unslash( $_POST['wc_tax_nonce'] ), 'wc_tax_nonce-class:' . $current_class ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		$current_class = WC_Tax::format_tax_rate_class( $current_class );

		// Check User Caps.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$changes = wp_unslash( $_POST['changes'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( $changes as $tax_rate_id => $data ) {
			if ( isset( $data['deleted'] ) ) {
				if ( isset( $data['newRow'] ) ) {
					// So the user added and deleted a new row.
					// That's fine, it's not in the database anyways. NEXT!
					continue;
				}
				WC_Tax::_delete_tax_rate( $tax_rate_id );
			}

			$tax_rate = array_intersect_key(
				$data,
				array(
					'tax_rate_country'  => 1,
					'tax_rate_state'    => 1,
					'tax_rate'          => 1,
					'tax_rate_name'     => 1,
					'tax_rate_priority' => 1,
					'tax_rate_compound' => 1,
					'tax_rate_shipping' => 1,
					'tax_rate_order'    => 1,
				)
			);

			if ( isset( $tax_rate['tax_rate'] ) ) {
				$tax_rate['tax_rate'] = wc_format_decimal( $tax_rate['tax_rate'] );
			}

			if ( isset( $data['newRow'] ) ) {
				$tax_rate['tax_rate_class'] = $current_class;
				$tax_rate_id                = WC_Tax::_insert_tax_rate( $tax_rate );
			} elseif ( ! empty( $tax_rate ) ) {
				WC_Tax::_update_tax_rate( $tax_rate_id, $tax_rate );
			}

			if ( isset( $data['postcode'] ) ) {
				$postcode = array_map( 'wc_clean', $data['postcode'] );
				$postcode = array_map( 'wc_normalize_postcode', $postcode );
				WC_Tax::_update_tax_rate_postcodes( $tax_rate_id, $postcode );
			}
			if ( isset( $data['city'] ) ) {
				WC_Tax::_update_tax_rate_cities( $tax_rate_id, array_map( 'wc_clean', array_map( 'wp_unslash', $data['city'] ) ) );
			}
		}

		WC_Cache_Helper::invalidate_cache_group( 'taxes' );
		WC_Cache_Helper::get_transient_version( 'shipping', true );

		wp_send_json_success(
			array(
				'rates' => WC_Tax::get_rates_for_tax_class( $current_class ),
			)
		);
		// phpcs:enable
	}

	/**
	 * Handle submissions from assets/js/wc-shipping-zones.js Backbone model.
	 */
	public static function shipping_zones_save_changes() {
		if ( ! isset( $_POST['wc_shipping_zones_nonce'], $_POST['changes'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( wp_unslash( $_POST['wc_shipping_zones_nonce'] ), 'wc_shipping_zones_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		// Check User Caps.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$changes = wp_unslash( $_POST['changes'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( $changes as $zone_id => $data ) {
			if ( isset( $data['deleted'] ) ) {
				if ( isset( $data['newRow'] ) ) {
					// So the user added and deleted a new row.
					// That's fine, it's not in the database anyways. NEXT!
					continue;
				}
				WC_Shipping_Zones::delete_zone( $zone_id );
				continue;
			}

			$zone_data = array_intersect_key(
				$data,
				array(
					'zone_id'    => 1,
					'zone_order' => 1,
				)
			);

			if ( isset( $zone_data['zone_id'] ) ) {
				$zone = new WC_Shipping_Zone( $zone_data['zone_id'] );

				if ( isset( $zone_data['zone_order'] ) ) {
					/**
					 * Notify that a non-option setting has been updated.
					 *
					 * @since 7.8.0
					 */
					do_action(
						'woocommerce_update_non_option_setting',
						array(
							'id' => 'zone_order',
						)
					);
					$zone->set_zone_order( $zone_data['zone_order'] );
				}

				global $current_tab;
				$current_tab = 'shipping';
				/**
				 * Completes the saving process for options.
				 *
				 * @since 7.8.0
				 */
				do_action( 'woocommerce_update_options' );
				$zone->save();
			}
		}

		wp_send_json_success(
			array(
				'zones' => WC_Shipping_Zones::get_zones( 'json' ),
			)
		);
	}

	/**
	 * Handle submissions from assets/js/wc-shipping-zone-methods.js Backbone model.
	 */
	public static function shipping_zone_add_method() {
		if ( ! isset( $_POST['wc_shipping_zones_nonce'], $_POST['zone_id'], $_POST['method_id'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( wp_unslash( $_POST['wc_shipping_zones_nonce'] ), 'wc_shipping_zones_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		// Check User Caps.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$zone_id = wc_clean( wp_unslash( $_POST['zone_id'] ) );
		$zone    = new WC_Shipping_Zone( $zone_id );
		/**
		 * Notify that a non-option setting has been updated.
		 *
		 * @since 7.8.0
		 */
		do_action(
			'woocommerce_update_non_option_setting',
			array(
				'id' => 'zone_method',
			)
		);
		$instance_id = $zone->add_shipping_method( wc_clean( wp_unslash( $_POST['method_id'] ) ) );

		global $current_tab;
		$current_tab = 'shipping';
		/**
		 * Completes the saving process for options.
		 *
		 * @since 7.8.0
		 */
		do_action( 'woocommerce_update_options' );

		wp_send_json_success(
			array(
				'instance_id' => $instance_id,
				'zone_id'     => $zone->get_id(),
				'zone_name'   => $zone->get_zone_name(),
				'methods'     => $zone->get_shipping_methods( false, 'json' ),
			)
		);
	}

	/**
	 * Handle submissions from assets/js/wc-shipping-zone-methods.js Backbone model.
	 */
	public static function shipping_zone_methods_save_changes() {
		if ( ! isset( $_POST['wc_shipping_zones_nonce'], $_POST['zone_id'], $_POST['changes'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( wp_unslash( $_POST['wc_shipping_zones_nonce'] ), 'wc_shipping_zones_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		global $wpdb;

		$zone_id = wc_clean( wp_unslash( $_POST['zone_id'] ) );
		$zone    = new WC_Shipping_Zone( $zone_id );
		$changes = wp_unslash( $_POST['changes'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( isset( $changes['zone_name'] ) ) {
			/**
			 * Completes the saving process for options.
			 *
			 * @since 7.8.0
			 */
			do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'zone_name' ) );
			$zone->set_zone_name( wc_clean( $changes['zone_name'] ) );
		}

		if ( isset( $changes['zone_locations'] ) ) {
			/**
			 * Completes the saving process for options.
			 *
			 * @since 7.8.0
			 */
			do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'zone_locations' ) );
			$zone->clear_locations( array( 'state', 'country', 'continent' ) );
			$locations = array_filter( array_map( 'wc_clean', (array) $changes['zone_locations'] ) );
			foreach ( $locations as $location ) {
				// Each posted location will be in the format type:code.
				$location_parts = explode( ':', $location );
				switch ( $location_parts[0] ) {
					case 'state':
						$zone->add_location( $location_parts[1] . ':' . $location_parts[2], 'state' );
						break;
					case 'country':
						$zone->add_location( $location_parts[1], 'country' );
						break;
					case 'continent':
						$zone->add_location( $location_parts[1], 'continent' );
						break;
				}
			}
		}

		if ( isset( $changes['zone_postcodes'] ) ) {
			/**
			 * Completes the saving process for options.
			 *
			 * @since 7.8.0
			 */
			do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'zone_postcodes' ) );
			$zone->clear_locations( 'postcode' );
			$postcodes = array_filter( array_map( 'strtoupper', array_map( 'wc_clean', explode( "\n", $changes['zone_postcodes'] ) ) ) );
			foreach ( $postcodes as $postcode ) {
				$zone->add_location( $postcode, 'postcode' );
			}
		}

		if ( isset( $changes['methods'] ) ) {
			/**
			 * Completes the saving process for options.
			 *
			 * @since 7.8.0
			 */
			do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'zone_methods' ) );
			foreach ( $changes['methods'] as $instance_id => $data ) {
				$method_id = $wpdb->get_var( $wpdb->prepare( "SELECT method_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE instance_id = %d", $instance_id ) );

				if ( isset( $data['deleted'] ) ) {
					$shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );
					$option_key      = $shipping_method->get_instance_option_key();
					if ( $wpdb->delete( "{$wpdb->prefix}woocommerce_shipping_zone_methods", array( 'instance_id' => $instance_id ) ) ) {
						delete_option( $option_key );
						do_action( 'woocommerce_shipping_zone_method_deleted', $instance_id, $method_id, $zone_id );
					}
					continue;
				}

				$method_data = array_intersect_key(
					$data,
					array(
						'method_order' => 1,
						'enabled'      => 1,
					)
				);

				if ( isset( $method_data['method_order'] ) ) {
					/**
					 * Completes the saving process for options.
					 *
					 * @since 7.8.0
					 */
					do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'zone_methods_order' ) );
					$wpdb->update( "{$wpdb->prefix}woocommerce_shipping_zone_methods", array( 'method_order' => absint( $method_data['method_order'] ) ), array( 'instance_id' => absint( $instance_id ) ) );
				}

				if ( isset( $method_data['enabled'] ) ) {
					/**
					 * Completes the saving process for options.
					 *
					 * @since 7.8.0
					 */
					do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'zone_methods_enabled' ) );
					$is_enabled = absint( 'yes' === $method_data['enabled'] );
					if ( $wpdb->update( "{$wpdb->prefix}woocommerce_shipping_zone_methods", array( 'is_enabled' => $is_enabled ), array( 'instance_id' => absint( $instance_id ) ) ) ) {
						do_action( 'woocommerce_shipping_zone_method_status_toggled', $instance_id, $method_id, $zone_id, $is_enabled );
					}
				}
			}
		}

		$zone->save();

		global $current_tab;
		$current_tab = 'shipping';
		/**
		 * Completes the saving process for options.
		 *
		 * @since 7.8.0
		 */
		do_action( 'woocommerce_update_options' );

		wp_send_json_success(
			array(
				'zone_id'   => $zone->get_id(),
				'zone_name' => $zone->get_zone_name(),
				'methods'   => $zone->get_shipping_methods( false, 'json' ),
			)
		);
	}

	/**
	 * Save method settings
	 */
	public static function shipping_zone_methods_save_settings() {
		if ( ! isset( $_POST['wc_shipping_zones_nonce'], $_POST['instance_id'], $_POST['data'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( wp_unslash( $_POST['wc_shipping_zones_nonce'] ), 'wc_shipping_zones_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$instance_id     = absint( $_POST['instance_id'] );
		$zone            = WC_Shipping_Zones::get_zone_by( 'instance_id', $instance_id );
		$shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );
		/**
		 * Notify that a non-option setting has been updated.
		 *
		 * @since 7.8.0
		 */
		do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'zone_method_settings' ) );
		$shipping_method->set_post_data( wp_unslash( $_POST['data'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		global $current_tab;
		$current_tab = 'shipping';
		/**
		 * Completes the saving process for options.
		 *
		 * @since 7.8.0
		 */
		do_action( 'woocommerce_update_options' );
		$shipping_method->process_admin_options();

		WC_Cache_Helper::get_transient_version( 'shipping', true );

		wp_send_json_success(
			array(
				'zone_id'   => $zone->get_id(),
				'zone_name' => $zone->get_zone_name(),
				'methods'   => $zone->get_shipping_methods( false, 'json' ),
				'errors'    => $shipping_method->get_errors(),
			)
		);
	}

	/**
	 * Handle submissions from assets/js/wc-shipping-classes.js Backbone model.
	 */
	public static function shipping_classes_save_changes() {
		if ( ! isset( $_POST['wc_shipping_classes_nonce'], $_POST['changes'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( wp_unslash( $_POST['wc_shipping_classes_nonce'] ), 'wc_shipping_classes_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$changes = wp_unslash( $_POST['changes'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		foreach ( $changes as $term_id => $data ) {
			$term_id = absint( $term_id );

			if ( isset( $data['deleted'] ) ) {
				if ( isset( $data['newRow'] ) ) {
					// So the user added and deleted a new row.
					// That's fine, it's not in the database anyways. NEXT!
					continue;
				}
				wp_delete_term( $term_id, 'product_shipping_class' );
				continue;
			}

			$update_args = array();

			if ( isset( $data['name'] ) ) {
				/**
				 * Notify that a non-option setting has been updated.
				 *
				 * @since 7.8.0
				 */
				do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'shipping_class_name' ) );
				$update_args['name'] = wc_clean( $data['name'] );
			}

			if ( isset( $data['slug'] ) ) {
				/**
				 * Notify that a non-option setting has been updated.
				 *
				 * @since 7.8.0
				 */
				do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'shipping_class_slug' ) );
				$update_args['slug'] = wc_clean( $data['slug'] );
			}

			if ( isset( $data['description'] ) ) {
				/**
				 * Notify that a non-option setting has been updated.
				 *
				 * @since 7.8.0
				 */
				do_action( 'woocommerce_update_non_option_setting', array( 'id' => 'shipping_class_description' ) );
				$update_args['description'] = wc_clean( $data['description'] );
			}

			if ( isset( $data['newRow'] ) ) {
				$update_args = array_filter( $update_args );
				if ( empty( $update_args['name'] ) ) {
					continue;
				}
				$inserted_term = wp_insert_term( $update_args['name'], 'product_shipping_class', $update_args );
				$term_id       = is_wp_error( $inserted_term ) ? 0 : $inserted_term['term_id'];
			} else {
				wp_update_term( $term_id, 'product_shipping_class', $update_args );
			}

			do_action( 'woocommerce_shipping_classes_save_class', $term_id, $data );
		}

		global $current_tab, $current_section;
		$current_tab     = 'shipping';
		$current_section = 'classes';
		/**
		 * Completes the saving process for options.
		 *
		 * @since 7.8.0
		 */
		do_action( 'woocommerce_update_options' );
		$wc_shipping = WC_Shipping::instance();

		wp_send_json_success(
			array(
				'shipping_classes' => $wc_shipping->get_shipping_classes(),
			)
		);
	}

	/**
	 * Toggle payment gateway on or off via AJAX.
	 *
	 * @since 3.4.0
	 */
	public static function toggle_gateway_enabled() {
		if ( current_user_can( 'manage_woocommerce' ) && check_ajax_referer( 'woocommerce-toggle-payment-gateway-enabled', 'security' ) && isset( $_POST['gateway_id'] ) ) {
			// Set current tab.
			$referer = wp_get_referer();
			if ( $referer ) {
				global $current_tab;
				parse_str( wp_parse_url( $referer, PHP_URL_QUERY ), $queries );
				$current_tab = $queries['tab'] ?? '';
			}

			// Load gateways.
			$payment_gateways = WC()->payment_gateways->payment_gateways();

			// Get posted gateway.
			$gateway_id = wc_clean( wp_unslash( $_POST['gateway_id'] ) );

			foreach ( $payment_gateways as $gateway ) {
				if ( ! in_array( $gateway_id, array( $gateway->id, sanitize_title( get_class( $gateway ) ) ), true ) ) {
					continue;
				}
				$enabled = $gateway->get_option( 'enabled', 'no' );
				$option  = array(
					'id' => $gateway->get_option_key(),
				);

				if ( ! wc_string_to_bool( $enabled ) ) {
					if ( $gateway->needs_setup() ) {
						wp_send_json_error( 'needs_setup' );
						wp_die();
					} else {
						do_action( 'woocommerce_update_option', $option );
						$gateway->update_option( 'enabled', 'yes' );
					}
				} else {
					do_action( 'woocommerce_update_option', $option );
					// Disable the gateway.
					$gateway->update_option( 'enabled', 'no' );
				}
				do_action( 'woocommerce_update_options' );
				wp_send_json_success( ! wc_string_to_bool( $enabled ) );
				wp_die();
			}
		}

		wp_send_json_error( 'invalid_gateway_id' );
		wp_die();
	}

	/**
	 * Reimplementation of WP core's `wp_ajax_add_meta` method to support order custom meta updates with custom tables.
	 */
	private static function order_add_meta() {
		wc_get_container()->get( CustomMetaBox::class )->add_meta_ajax();
	}

	/**
	 * Reimplementation of WP core's `wp_ajax_delete_meta` method to support order custom meta updates with custom tables.
	 *
	 * @return void
	 */
	private static function order_delete_meta() : void {
		wc_get_container()->get( CustomMetaBox::class )->delete_meta_ajax();
	}

	/**
	 * Hooked to 'heartbeat_received' on the edit order page to refresh the lock on an order being edited by the current user.
	 *
	 * @param array $response The heartbeat response to be sent.
	 * @param array $data     Data sent through the heartbeat.
	 * @return array Response to be sent.
	 */
	private static function order_refresh_lock( $response, $data ) : array {
		return wc_get_container()->get( Automattic\WooCommerce\Internal\Admin\Orders\EditLock::class )->refresh_lock_ajax( $response, $data );
	}

	/**
	 * Hooked to 'heartbeat_received' on the orders screen to refresh the locked status of orders in the list table.
	 *
	 * @since 7.8.0
	 *
	 * @param array $response The heartbeat response to be sent.
	 * @param array $data     Data sent through the heartbeat.
	 * @return array Response to be sent.
	 */
	private static function check_locked_orders( $response, $data ) : array {
		return wc_get_container()->get( Automattic\WooCommerce\Internal\Admin\Orders\EditLock::class )->check_locked_orders_ajax( $response, $data );
	}

}

WC_AJAX::init();
