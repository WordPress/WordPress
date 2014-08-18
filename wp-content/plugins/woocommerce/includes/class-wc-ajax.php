<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooCommerce WC_AJAX
 *
 * AJAX Event Handler
 *
 * @class 		WC_AJAX
 * @version		2.1.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_AJAX {

	/**
	 * Hook into ajax events
	 */
	public function __construct() {

		// woocommerce_EVENT => nopriv
		$ajax_events = array(
			'get_refreshed_fragments'             				=> true,
			'apply_coupon'                        				=> true,
			'update_shipping_method'              				=> true,
			'update_order_review'                 				=> true,
			'add_to_cart'                         				=> true,
			'checkout'                            				=> true,
			'feature_product'                     				=> false,
			'mark_order_complete'                 				=> false,
			'mark_order_processing'               				=> false,
			'add_new_attribute'                   				=> false,
			'remove_variation'                    				=> false,
			'remove_variations'                   				=> false,
			'save_attributes'                     				=> false,
			'add_variation'                       				=> false,
			'link_all_variations'                 				=> false,
			'revoke_access_to_download'           				=> false,
			'grant_access_to_download'            				=> false,
			'get_customer_details'                				=> false,
			'add_order_item'                      				=> false,
			'add_order_fee'                       				=> false,
			'remove_order_item'                   				=> false,
			'reduce_order_item_stock'             				=> false,
			'increase_order_item_stock'           				=> false,
			'add_order_item_meta'                 				=> false,
			'remove_order_item_meta'              				=> false,
			'calc_line_taxes'                     				=> false,
			'add_order_note'                      				=> false,
			'delete_order_note'                  			 	=> false,
			'json_search_products'                				=> false,
			'json_search_products_and_variations' 				=> false,
			'json_search_downloadable_products_and_variations' 	=> false,
			'json_search_customers'               				=> false,
			'term_ordering'                       				=> false,
			'product_ordering'                    				=> false
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( $this, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( $this, $ajax_event ) );
			}
		}
	}

	/**
	 * Output headers for JSON requests
	 */
	private function json_headers() {
		header( 'Content-Type: application/json; charset=utf-8' );
	}


	/**
	 * Get a refreshed cart fragment
	 */
	public function get_refreshed_fragments() {

		$this->json_headers();

		// Get mini cart
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'fragments' => apply_filters( 'add_to_cart_fragments', array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
				)
			),
			'cart_hash' => WC()->cart->get_cart() ? md5( json_encode( WC()->cart->get_cart() ) ) : ''
		);

		echo json_encode( $data );

		die();
	}

	/**
	 * AJAX apply coupon on checkout page
	 */
	public function apply_coupon() {

		check_ajax_referer( 'apply-coupon', 'security' );

		if ( ! empty( $_POST['coupon_code'] ) ) {
			WC()->cart->add_discount( sanitize_text_field( $_POST['coupon_code'] ) );
		} else {
			wc_add_notice( WC_Coupon::get_generic_coupon_error( WC_Coupon::E_WC_COUPON_PLEASE_ENTER ), 'error' );
		}

		wc_print_notices();

		die();
	}

	/**
	 * AJAX update shipping method on cart page
	 */
	public function update_shipping_method() {

		check_ajax_referer( 'update-shipping-method', 'security' );

		if ( ! defined('WOOCOMMERCE_CART') ) {
			define( 'WOOCOMMERCE_CART', true );
		}

		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

		if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ) {
			foreach ( $_POST['shipping_method'] as $i => $value ) {
				$chosen_shipping_methods[ $i ] = wc_clean( $value );
			}
		}

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );

		WC()->cart->calculate_totals();

		woocommerce_cart_totals();

		die();
	}

	/**
	 * AJAX update order review on checkout
	 */
	public function update_order_review() {

		check_ajax_referer( 'update-order-review', 'security' );

		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		if ( 0 == sizeof( WC()->cart->get_cart() ) ) {
			echo '<div class="woocommerce-error">' . __( 'Sorry, your session has expired.', 'woocommerce' ) . ' <a href="' . home_url() . '" class="wc-backward">' . __( 'Return to homepage', 'woocommerce' ) . '</a></div>';
			die();
		}

		do_action( 'woocommerce_checkout_update_order_review', $_POST['post_data'] );

		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

		if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ) {
			foreach ( $_POST['shipping_method'] as $i => $value ) {
				$chosen_shipping_methods[ $i ] = wc_clean( $value );
			}
		}

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		WC()->session->set( 'chosen_payment_method', empty( $_POST['payment_method'] ) ? '' : $_POST['payment_method'] );

		if ( isset( $_POST['country'] ) ) {
			WC()->customer->set_country( $_POST['country'] );
		}

		if ( isset( $_POST['state'] ) ) {
			WC()->customer->set_state( $_POST['state'] );
		}

		if ( isset( $_POST['postcode'] ) ) {
			WC()->customer->set_postcode( $_POST['postcode'] );
		}

		if ( isset( $_POST['city'] ) ) {
			WC()->customer->set_city( $_POST['city'] );
		}

		if ( isset( $_POST['address'] ) ) {
			WC()->customer->set_address( $_POST['address'] );
		}

		if ( isset( $_POST['address_2'] ) ) {
			WC()->customer->set_address_2( $_POST['address_2'] );
		}

		if ( "yes" == get_option( 'woocommerce_ship_to_billing_address_only' ) ) {

			if ( isset( $_POST['country'] ) ) {
				WC()->customer->set_shipping_country( $_POST['country'] );
			}

			if ( isset( $_POST['state'] ) ) {
				WC()->customer->set_shipping_state( $_POST['state'] );
			}

			if ( isset( $_POST['postcode'] ) ) {
				WC()->customer->set_shipping_postcode( $_POST['postcode'] );
			}

			if ( isset( $_POST['city'] ) ) {
				WC()->customer->set_shipping_city( $_POST['city'] );
			}

			if ( isset( $_POST['address'] ) ) {
				WC()->customer->set_shipping_address( $_POST['address'] );
			}

			if ( isset( $_POST['address_2'] ) ) {
				WC()->customer->set_shipping_address_2( $_POST['address_2'] );
			}
		} else {

			if ( isset( $_POST['s_country'] ) ) {
				WC()->customer->set_shipping_country( $_POST['s_country'] );
			}

			if ( isset( $_POST['s_state'] ) ) {
				WC()->customer->set_shipping_state( $_POST['s_state'] );
			}

			if ( isset( $_POST['s_postcode'] ) ) {
				WC()->customer->set_shipping_postcode( $_POST['s_postcode'] );
			}

			if ( isset( $_POST['s_city'] ) ) {
				WC()->customer->set_shipping_city( $_POST['s_city'] );
			}

			if ( isset( $_POST['s_address'] ) ) {
				WC()->customer->set_shipping_address( $_POST['s_address'] );
			}

			if ( isset( $_POST['s_address_2'] ) ) {
				WC()->customer->set_shipping_address_2( $_POST['s_address_2'] );
			}
		}

		WC()->cart->calculate_totals();

		do_action( 'woocommerce_checkout_order_review' ); // Display review order table

		die();
	}

	/**
	 * AJAX add to cart
	 */
	public function add_to_cart() {
		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity ) ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				wc_add_to_cart_message( $product_id );
			}

			// Return fragments
			$this->get_refreshed_fragments();

		} else {

			$this->json_headers();

			// If there was an error adding to the cart, redirect to the product page to show any errors
			$data = array(
				'error' => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
			);

			echo json_encode( $data );
		}

		die();
	}

	/**
	 * Process ajax checkout form
	 */
	public function checkout() {
		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		$woocommerce_checkout = WC()->checkout();
		$woocommerce_checkout->process_checkout();

		die(0);
	}

	/**
	 * Feature a product from admin
	 */
	public function feature_product() {
		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce' ) );
		}

		if ( ! check_admin_referer( 'woocommerce-feature-product' ) ) {
			wp_die( __( 'You have taken too long. Please go back and retry.', 'woocommerce' ) );
		}

		$post_id = ! empty( $_GET['product_id'] ) ? (int) $_GET['product_id'] : '';

		if ( ! $post_id || get_post_type( $post_id ) !== 'product' ) {
			die;
		}

		$featured = get_post_meta( $post_id, '_featured', true );

		if ( 'yes' === $featured ) {
			update_post_meta( $post_id, '_featured', 'no' );
		} else {
			update_post_meta( $post_id, '_featured', 'yes' );
		}

		wc_delete_product_transients();

		wp_safe_redirect( remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) );

		die();
	}

	/**
	 * Mark an order as complete
	 */
	public function mark_order_complete() {
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce' ) );
		}

		if ( ! check_admin_referer( 'woocommerce-mark-order-complete' ) ) {
			wp_die( __( 'You have taken too long. Please go back and retry.', 'woocommerce' ) );
		}

		$order_id = isset( $_GET['order_id'] ) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : '';
		if ( ! $order_id ) {
			die();
		}

		$order = new WC_Order( $order_id );
		$order->update_status( 'completed' );

		wp_safe_redirect( wp_get_referer() );

		die();
	}

	/**
	 * Mark an order as processing
	 */
	public function mark_order_processing() {
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce' ) );
		}

		if ( ! check_admin_referer( 'woocommerce-mark-order-processing' ) ) {
			wp_die( __( 'You have taken too long. Please go back and retry.', 'woocommerce' ) );
		}

		$order_id = isset( $_GET['order_id'] ) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : '';
		if ( ! $order_id ) {
			die();
		}

		$order = new WC_Order( $order_id );
		$order->update_status( 'processing' );

		wp_safe_redirect( wp_get_referer() );

		die();
	}

	/**
	 * Add a new attribute via ajax function
	 */
	public function add_new_attribute() {

		check_ajax_referer( 'add-attribute', 'security' );

		$this->json_headers();

		$taxonomy = esc_attr( $_POST['taxonomy'] );
		$term     = stripslashes( $_POST['term'] );

		if ( taxonomy_exists( $taxonomy ) ) {

			$result = wp_insert_term( $term, $taxonomy );

			if ( is_wp_error( $result ) ) {
				echo json_encode( array(
					'error' => $result->get_error_message()
				));
			} else {
				echo json_encode( array(
					'term_id' => $result['term_id'],
					'name'    => $term,
					'slug'    => sanitize_title( $term ),
				));
			}
		}

		die();
	}

	/**
	 * Delete variation via ajax function
	 */
	public function remove_variation() {

		check_ajax_referer( 'delete-variation', 'security' );

		$variation_id = intval( $_POST['variation_id'] );
		$variation = get_post( $variation_id );

		if ( $variation && 'product_variation' == $variation->post_type ) {
			wp_delete_post( $variation_id );
		}

		die();
	}

	/**
	 * Delete variations via ajax function
	 */
	public function remove_variations() {

		check_ajax_referer( 'delete-variations', 'security' );

		$variation_ids = (array) $_POST['variation_ids'];

		foreach ( $variation_ids as $variation_id ) {
			$variation = get_post( $variation_id );

			if ( $variation && 'product_variation' == $variation->post_type ) {
				wp_delete_post( $variation_id );
			}
		}

		die();
	}

	/**
	 * Save attributes via ajax
	 */
	public function save_attributes() {

		check_ajax_referer( 'save-attributes', 'security' );

		// Get post data
		parse_str( $_POST['data'], $data );
		$post_id = absint( $_POST['post_id'] );

		// Save Attributes
		$attributes = array();

		if ( isset( $data['attribute_names'] ) ) {

			$attribute_names  = array_map( 'stripslashes', $data['attribute_names'] );
			$attribute_values = isset( $data['attribute_values'] ) ? $data['attribute_values'] : array();

			if ( isset( $data['attribute_visibility'] ) ) {
				$attribute_visibility = $data['attribute_visibility'];
			}

			if ( isset( $data['attribute_variation'] ) ) {
				$attribute_variation = $data['attribute_variation'];
			}

			$attribute_is_taxonomy = $data['attribute_is_taxonomy'];
			$attribute_position    = $data['attribute_position'];
			$attribute_names_count = sizeof( $attribute_names );

			for ( $i = 0; $i < $attribute_names_count; $i++ ) {
				if ( ! $attribute_names[ $i ] ) {
					continue;
				}

				$is_visible   = isset( $attribute_visibility[ $i ] ) ? 1 : 0;
				$is_variation = isset( $attribute_variation[ $i ] ) ? 1 : 0;
				$is_taxonomy  = $attribute_is_taxonomy[ $i ] ? 1 : 0;

				if ( $is_taxonomy ) {

					if ( isset( $attribute_values[ $i ] ) ) {

						// Select based attributes - Format values (posted values are slugs)
						if ( is_array( $attribute_values[ $i ] ) ) {
							$values = array_map( 'sanitize_title', $attribute_values[ $i ] );

						// Text based attributes - Posted values are term names - don't change to slugs
						} else {
							$values = array_map( 'stripslashes', array_map( 'strip_tags', explode( WC_DELIMITER, $attribute_values[ $i ] ) ) );
						}

						// Remove empty items in the array
						$values = array_filter( $values, 'strlen' );

					} else {
						$values = array();
					}

					// Update post terms
					if ( taxonomy_exists( $attribute_names[ $i ] ) ) {
						wp_set_object_terms( $post_id, $values, $attribute_names[ $i ] );
					}

					if ( $values ) {
						// Add attribute to array, but don't set values
						$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
							'name' 			=> wc_clean( $attribute_names[ $i ] ),
							'value' 		=> '',
							'position' 		=> $attribute_position[ $i ],
							'is_visible' 	=> $is_visible,
							'is_variation' 	=> $is_variation,
							'is_taxonomy' 	=> $is_taxonomy
						);
					}

				} elseif ( isset( $attribute_values[ $i ] ) ) {

					// Text based, separate by pipe
					$values = implode( ' ' . WC_DELIMITER . ' ', array_map( 'wc_clean', array_map( 'stripslashes', explode( WC_DELIMITER, $attribute_values[ $i ] ) ) ) );

					// Custom attribute - Add attribute to array and set the values
					$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
						'name' 			=> wc_clean( $attribute_names[ $i ] ),
						'value' 		=> $values,
						'position' 		=> $attribute_position[ $i ],
						'is_visible' 	=> $is_visible,
						'is_variation' 	=> $is_variation,
						'is_taxonomy' 	=> $is_taxonomy
					);
				}

			 }
		}

		if ( ! function_exists( 'attributes_cmp' ) ) {
			function attributes_cmp( $a, $b ) {
				if ( $a['position'] == $b['position'] ) {
					return 0;
				}

				return ( $a['position'] < $b['position'] ) ? -1 : 1;
			}
		}
		uasort( $attributes, 'attributes_cmp' );

		update_post_meta( $post_id, '_product_attributes', $attributes );

		die();
	}

	/**
	 * Add variation via ajax function
	 */
	public function add_variation() {

		check_ajax_referer( 'add-variation', 'security' );

		$post_id = intval( $_POST['post_id'] );
		$loop = intval( $_POST['loop'] );

		$variation = array(
			'post_title'   => 'Product #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_parent'  => $post_id,
			'post_type'    => 'product_variation'
		);

		$variation_id = wp_insert_post( $variation );

		do_action( 'woocommerce_create_product_variation', $variation_id );

		if ( $variation_id ) {

			$variation_post_status = 'publish';
			$variation_data = get_post_meta( $variation_id );
			$variation_data['variation_post_id'] = $variation_id;

			// Get attributes
			$attributes = (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

			// Get tax classes
			$tax_classes                 = array_filter(array_map('trim', explode("\n", get_option('woocommerce_tax_classes'))));
			$tax_class_options           = array();
			$tax_class_options['parent'] =__( 'Same as parent', 'woocommerce' );
			$tax_class_options['']       = __( 'Standard', 'woocommerce' );

			if ( $tax_classes ) {
				foreach ( $tax_classes as $class ) {
					$tax_class_options[ sanitize_title( $class ) ] = $class;
				}
			}

			// Get parent data
			$parent_data = array(
				'id'                => $post_id,
				'attributes'        => $attributes,
				'tax_class_options' => $tax_class_options,
				'sku'               => get_post_meta( $post_id, '_sku', true ),
				'weight'            => get_post_meta( $post_id, '_weight', true ),
				'length'            => get_post_meta( $post_id, '_length', true ),
				'width'             => get_post_meta( $post_id, '_width', true ),
				'height'           => get_post_meta( $post_id, '_height', true ),
				'tax_class'        => get_post_meta( $post_id, '_tax_class', true )
			);

			if ( ! $parent_data['weight'] ) {
				$parent_data['weight'] = '0.00';
			}

			if ( ! $parent_data['length'] ) {
				$parent_data['length'] = '0';
			}

			if ( ! $parent_data['width'] ) {
				$parent_data['width'] = '0';
			}

			if ( ! $parent_data['height'] ) {
				$parent_data['height'] = '0';
			}

			$_tax_class          = '';
			$_downloadable_files = '';
			$image_id            = 0;
			$variation           = get_post( $variation_id ); // Get the variation object

			include( 'admin/post-types/meta-boxes/views/html-variation-admin.php' );
		}

		die();
	}

	/**
	 * Link all variations via ajax function
	 */
	public function link_all_variations() {

		if ( ! defined( 'WC_MAX_LINKED_VARIATIONS' ) ) {
			define( 'WC_MAX_LINKED_VARIATIONS', 49 );
		}

		check_ajax_referer( 'link-variations', 'security' );

		@set_time_limit(0);

		$post_id = intval( $_POST['post_id'] );

		if ( ! $post_id ) {
			die();
		}

		$variations = array();
		$_product   = get_product( $post_id, array( 'product_type' => 'variable' ) );

		// Put variation attributes into an array
		foreach ( $_product->get_attributes() as $attribute ) {

			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			$attribute_field_name = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( $attribute['is_taxonomy'] ) {
				$options = wc_get_product_terms( $post_id, $attribute['name'], array( 'fields' => 'slugs' ) );
			} else {
				$options = explode( WC_DELIMITER, $attribute['value'] );
			}

			$options = array_map( 'sanitize_title', array_map( 'trim', $options ) );

			$variations[ $attribute_field_name ] = $options;
		}

		// Quit out if none were found
		if ( sizeof( $variations ) == 0 ) {
			die();
		}

		// Get existing variations so we don't create duplicates
		$available_variations = array();

		foreach( $_product->get_children() as $child_id ) {
			$child = $_product->get_child( $child_id );

			if ( ! empty( $child->variation_id ) ) {
				$available_variations[] = $child->get_variation_attributes();
			}
		}

		// Created posts will all have the following data
		$variation_post_data = array(
			'post_title'   => 'Product #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_parent'  => $post_id,
			'post_type'    => 'product_variation'
		);

		// Now find all combinations and create posts
		if ( ! function_exists( 'array_cartesian' ) ) {

			/**
			 * @param array $input
			 * @return array
			 */
			function array_cartesian( $input ) {
				$result = array();

				while ( list( $key, $values ) = each( $input ) ) {
					// If a sub-array is empty, it doesn't affect the cartesian product
					if ( empty( $values ) ) {
						continue;
					}

					// Special case: seeding the product array with the values from the first sub-array
					if ( empty( $result ) ) {
						foreach ( $values as $value ) {
							$result[] = array( $key => $value );
						}
					}
					else {
						// Second and subsequent input sub-arrays work like this:
						//   1. In each existing array inside $product, add an item with
						//      key == $key and value == first item in input sub-array
						//   2. Then, for each remaining item in current input sub-array,
						//      add a copy of each existing array inside $product with
						//      key == $key and value == first item in current input sub-array

						// Store all items to be added to $product here; adding them on the spot
						// inside the foreach will result in an infinite loop
						$append = array();
						foreach ( $result as &$product ) {
							// Do step 1 above. array_shift is not the most efficient, but it
							// allows us to iterate over the rest of the items with a simple
							// foreach, making the code short and familiar.
							$product[ $key ] = array_shift( $values );

							// $product is by reference (that's why the key we added above
							// will appear in the end result), so make a copy of it here
							$copy = $product;

							// Do step 2 above.
							foreach ( $values as $item ) {
								$copy[ $key ] = $item;
								$append[] = $copy;
							}

							// Undo the side effecst of array_shift
							array_unshift( $values, $product[ $key ] );
						}

						// Out of the foreach, we can add to $results now
						$result = array_merge( $result, $append );
					}
				}

				return $result;
			}
		}

		$variation_ids       = array();
		$added               = 0;
		$possible_variations = array_cartesian( $variations );

		foreach ( $possible_variations as $variation ) {

			// Check if variation already exists
			if ( in_array( $variation, $available_variations ) ) {
				continue;
			}

			$variation_id = wp_insert_post( $variation_post_data );

			$variation_ids[] = $variation_id;

			foreach ( $variation as $key => $value ) {
				update_post_meta( $variation_id, $key, $value );
			}

			$added++;

			do_action( 'product_variation_linked', $variation_id );

			if ( $added > WC_MAX_LINKED_VARIATIONS ) {
				break;
			}
		}

		wc_delete_product_transients( $post_id );

		echo $added;

		die();
	}

	/**
	 * Delete download permissions via ajax function
	 */
	public function revoke_access_to_download() {

		check_ajax_referer( 'revoke-access', 'security' );

		global $wpdb;

		$download_id = $_POST['download_id'];
		$product_id  = intval( $_POST['product_id'] );
		$order_id    = intval( $_POST['order_id'] );

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s;", $order_id, $product_id, $download_id ) );

		do_action( 'woocommerce_ajax_revoke_access_to_product_download', $download_id, $product_id, $order_id );

		die();
	}

	/**
	 * Grant download permissions via ajax function
	 */
	public function grant_access_to_download() {

		check_ajax_referer( 'grant-access', 'security' );

		global $wpdb;

		$wpdb->hide_errors();

		$order_id     = intval( $_POST['order_id'] );
		$product_ids  = $_POST['product_ids'];
		$loop         = intval( $_POST['loop'] );
		$file_counter = 0;
		$order        = new WC_Order( $order_id );

		if ( ! is_array( $product_ids ) ) {
			$product_ids = array( $product_ids );
		}

		foreach ( $product_ids as $product_id ) {
			$product = get_product( $product_id );
			$files   = $product->get_files();

			if ( ! $order->billing_email ) {
				die();
			}

			if ( $files ) {
				foreach ( $files as $download_id => $file ) {
					if ( $inserted_id = wc_downloadable_file_permission( $download_id, $product_id, $order ) ) {

						// insert complete - get inserted data
						$download = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE permission_id = %d", $inserted_id ) );

						$loop ++;
						$file_counter ++;

						if ( isset( $file['name'] ) ) {
							$file_count = $file['name'];
						} else {
							$file_count = sprintf( __( 'File %d', 'woocommerce' ), $file_counter );
						}
						include( 'admin/post-types/meta-boxes/views/html-order-download-permission.php' );
					}
				}
			}
		}

		die();
	}

	/**
	 * Get customer details via ajax
	 */
	public function get_customer_details() {

		check_ajax_referer( 'get-customer-details', 'security' );

		$this->json_headers();

		$user_id      = (int) trim(stripslashes($_POST['user_id']));
		$type_to_load = esc_attr(trim(stripslashes($_POST['type_to_load'])));

		$customer_data = array(
			$type_to_load . '_first_name' => get_user_meta( $user_id, $type_to_load . '_first_name', true ),
			$type_to_load . '_last_name'  => get_user_meta( $user_id, $type_to_load . '_last_name', true ),
			$type_to_load . '_company'    => get_user_meta( $user_id, $type_to_load . '_company', true ),
			$type_to_load . '_address_1'  => get_user_meta( $user_id, $type_to_load . '_address_1', true ),
			$type_to_load . '_address_2'  => get_user_meta( $user_id, $type_to_load . '_address_2', true ),
			$type_to_load . '_city'       => get_user_meta( $user_id, $type_to_load . '_city', true ),
			$type_to_load . '_postcode'   => get_user_meta( $user_id, $type_to_load . '_postcode', true ),
			$type_to_load . '_country'    => get_user_meta( $user_id, $type_to_load . '_country', true ),
			$type_to_load . '_state'      => get_user_meta( $user_id, $type_to_load . '_state', true ),
			$type_to_load . '_email'      => get_user_meta( $user_id, $type_to_load . '_email', true ),
			$type_to_load . '_phone'      => get_user_meta( $user_id, $type_to_load . '_phone', true ),
		);

		$customer_data = apply_filters( 'woocommerce_found_customer_details', $customer_data );

		echo json_encode( $customer_data );

		// Quit out
		die();
	}

	/**
	 * Add order item via ajax
	 */
	public function add_order_item() {
		global $wpdb;

		check_ajax_referer( 'order-item', 'security' );

		$item_to_add = sanitize_text_field( $_POST['item_to_add'] );
		$order_id    = absint( $_POST['order_id'] );

		// Find the item
		if ( ! is_numeric( $item_to_add ) ) {
			die();
		}

		$post = get_post( $item_to_add );

		if ( ! $post || ( 'product' !== $post->post_type && 'product_variation' !== $post->post_type ) ) {
			die();
		}

		$_product = get_product( $post->ID );
		$order    = new WC_Order( $order_id );
		$class    = 'new_row';

		// Set values
		$item = array();

		$item['product_id']        = $_product->id;
		$item['variation_id']      = isset( $_product->variation_id ) ? $_product->variation_id : '';
		$item['variation_data']    = isset( $_product->variation_data ) ? $_product->variation_data : '';
		$item['name']              = $_product->get_title();
		$item['tax_class']         = $_product->get_tax_class();
		$item['qty']               = 1;
		$item['line_subtotal']     = wc_format_decimal( $_product->get_price_excluding_tax() );
		$item['line_subtotal_tax'] = '';
		$item['line_total']        = wc_format_decimal( $_product->get_price_excluding_tax() );
		$item['line_tax']          = '';

		// Add line item
		$item_id = wc_add_order_item( $order_id, array(
			'order_item_name' 		=> $item['name'],
			'order_item_type' 		=> 'line_item'
		) );

		// Add line item meta
		if ( $item_id ) {
			wc_add_order_item_meta( $item_id, '_qty', $item['qty'] );
			wc_add_order_item_meta( $item_id, '_tax_class', $item['tax_class'] );
			wc_add_order_item_meta( $item_id, '_product_id', $item['product_id'] );
			wc_add_order_item_meta( $item_id, '_variation_id', $item['variation_id'] );
			wc_add_order_item_meta( $item_id, '_line_subtotal', $item['line_subtotal'] );
			wc_add_order_item_meta( $item_id, '_line_subtotal_tax', $item['line_subtotal_tax'] );
			wc_add_order_item_meta( $item_id, '_line_total', $item['line_total'] );
			wc_add_order_item_meta( $item_id, '_line_tax', $item['line_tax'] );

			// Store variation data in meta
			if ( $item['variation_data'] && is_array( $item['variation_data'] ) ) {
				foreach ( $item['variation_data'] as $key => $value ) {
					wc_add_order_item_meta( $item_id, str_replace( 'attribute_', '', $key ), $value );
				}
			}

			do_action( 'woocommerce_ajax_add_order_item_meta', $item_id, $item );
		}

		$item = apply_filters( 'woocommerce_ajax_order_item', $item, $item_id );

		include( 'admin/post-types/meta-boxes/views/html-order-item.php' );

		// Quit out
		die();
	}

	/**
	 * Add order fee via ajax
	 */
	public function add_order_fee() {

		check_ajax_referer( 'order-item', 'security' );

		$order_id = absint( $_POST['order_id'] );
		$order    = new WC_Order( $order_id );

		// Add line item
		$item_id = wc_add_order_item( $order_id, array(
			'order_item_name' => '',
			'order_item_type' => 'fee'
		) );

		// Add line item meta
		if ( $item_id ) {
			wc_add_order_item_meta( $item_id, '_tax_class', '' );
			wc_add_order_item_meta( $item_id, '_line_total', '' );
			wc_add_order_item_meta( $item_id, '_line_tax', '' );
		}

		include( 'admin/post-types/meta-boxes/views/html-order-fee.php' );

		// Quit out
		die();
	}

	/**
	 * Remove an order item
	 */
	public function remove_order_item() {
		global $wpdb;

		check_ajax_referer( 'order-item', 'security' );

		$order_item_ids = $_POST['order_item_ids'];

		if ( sizeof( $order_item_ids ) > 0 ) {
			foreach( $order_item_ids as $id ) {
				wc_delete_order_item( absint( $id ) );
			}
		}

		die();
	}

	/**
	 * Reduce order item stock
	 */
	public function reduce_order_item_stock() {
		global $wpdb;

		check_ajax_referer( 'order-item', 'security' );

		$order_id       = absint( $_POST['order_id'] );
		$order_item_ids = isset( $_POST['order_item_ids'] ) ? $_POST['order_item_ids'] : array();
		$order_item_qty = isset( $_POST['order_item_qty'] ) ? $_POST['order_item_qty'] : array();
		$order          = new WC_Order( $order_id );
		$order_items    = $order->get_items();
		$return         = array();

		if ( $order && ! empty( $order_items ) && sizeof( $order_item_ids ) > 0 ) {

			foreach ( $order_items as $item_id => $order_item ) {

				// Only reduce checked items
				if ( ! in_array( $item_id, $order_item_ids ) ) {
					continue;
				}

				$_product = $order->get_product_from_item( $order_item );

				if ( $_product->exists() && $_product->managing_stock() && isset( $order_item_qty[ $item_id ] ) && $order_item_qty[ $item_id ] > 0 ) {

					$old_stock 		= $_product->stock;
					$stock_change   = apply_filters( 'woocommerce_reduce_order_stock_quantity', $order_item_qty[ $item_id ], $item_id );
					$new_quantity 	= $_product->reduce_stock( $stock_change );

					$return[] = sprintf( __( 'Item #%s stock reduced from %s to %s.', 'woocommerce' ), $order_item['product_id'], $old_stock, $new_quantity );
					$order->add_order_note( sprintf( __( 'Item #%s stock reduced from %s to %s.', 'woocommerce' ), $order_item['product_id'], $old_stock, $new_quantity) );
					$order->send_stock_notifications( $_product, $new_quantity, $order_item_qty[ $item_id ] );
				}
			}

			do_action( 'woocommerce_reduce_order_stock', $order );

			if ( empty( $return ) ) {
				$return[] = __( 'No products had their stock reduced - they may not have stock management enabled.', 'woocommerce' );
			}

			echo implode( ', ', $return );
		}

		die();
	}

	/**
	 * Increase order item stock
	 */
	public function increase_order_item_stock() {
		global $wpdb;

		check_ajax_referer( 'order-item', 'security' );

		$order_id       = absint( $_POST['order_id'] );
		$order_item_ids = isset( $_POST['order_item_ids'] ) ? $_POST['order_item_ids'] : array();
		$order_item_qty = isset( $_POST['order_item_qty'] ) ? $_POST['order_item_qty'] : array();
		$order          = new WC_Order( $order_id );
		$order_items    = $order->get_items();
		$return         = array();

		if ( $order && ! empty( $order_items ) && sizeof( $order_item_ids ) > 0 ) {

			foreach ( $order_items as $item_id => $order_item ) {

				// Only reduce checked items
				if ( ! in_array( $item_id, $order_item_ids ) ) {
					continue;
				}

				$_product = $order->get_product_from_item( $order_item );

				if ( $_product->exists() && $_product->managing_stock() && isset( $order_item_qty[ $item_id ] ) && $order_item_qty[ $item_id ] > 0 ) {

					$old_stock    = $_product->stock;
					$stock_change = apply_filters( 'woocommerce_restore_order_stock_quantity', $order_item_qty[ $item_id ], $item_id );
					$new_quantity = $_product->increase_stock( $stock_change );

					$return[] = sprintf( __( 'Item #%s stock increased from %s to %s.', 'woocommerce' ), $order_item['product_id'], $old_stock, $new_quantity );
					$order->add_order_note( sprintf( __( 'Item #%s stock increased from %s to %s.', 'woocommerce' ), $order_item['product_id'], $old_stock, $new_quantity ) );
				}
			}

			do_action( 'woocommerce_restore_order_stock', $order );

			if ( empty( $return ) ) {
				$return[] = __( 'No products had their stock increased - they may not have stock management enabled.', 'woocommerce' );
			}

			echo implode( ', ', $return );
		}

		die();
	}

	/**
	 * Add some meta to a line item
	 */
	public function add_order_item_meta() {
		global $wpdb;

		check_ajax_referer( 'order-item', 'security' );

		$meta_id = wc_add_order_item_meta( absint( $_POST['order_item_id'] ), __( 'Name', 'woocommerce' ), __( 'Value', 'woocommerce' ) );

		if ( $meta_id ) {
			echo '<tr data-meta_id="' . esc_attr( $meta_id ) . '"><td><input type="text" name="meta_key[' . $meta_id . ']" /><textarea name="meta_value[' . $meta_id . ']"></textarea></td><td width="1%"><button class="remove_order_item_meta button">&times;</button></td></tr>';
		}

		die();
	}

	/**
	 * Remove meta from a line item
	 */
	public function remove_order_item_meta() {
		global $wpdb;

		check_ajax_referer( 'order-item', 'security' );

		$meta_id = absint( $_POST['meta_id'] );

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_id = %d", $meta_id ) );

		die();
	}

	/**
	 * Calc line tax
	 */
	public function calc_line_taxes() {
		global $wpdb;

		check_ajax_referer( 'calc-totals', 'security' );

		$this->json_headers();

		$tax      = new WC_Tax();
		$taxes    = $tax_rows = $item_taxes = $shipping_taxes = array();
		$order_id = absint( $_POST['order_id'] );
		$order    = new WC_Order( $order_id );
		$country  = strtoupper( esc_attr( $_POST['country'] ) );
		$state    = strtoupper( esc_attr( $_POST['state'] ) );
		$postcode = strtoupper( esc_attr( $_POST['postcode'] ) );
		$city     = sanitize_title( esc_attr( $_POST['city'] ) );
		$items    = isset( $_POST['items'] ) ? $_POST['items'] : array();
		$shipping = $_POST['shipping'];
		$item_tax = 0;

		// Calculate sales tax first
		if ( sizeof( $items ) > 0 ) {
			foreach( $items as $item_id => $item ) {

				$item_id       = absint( $item_id );
				$line_subtotal = isset( $item['line_subtotal'] ) ? wc_format_decimal( $item['line_subtotal'] ) : 0;
				$line_total    = wc_format_decimal( $item['line_total'] );
				$tax_class     = sanitize_text_field( $item['tax_class'] );
				$product_id    = $order->get_item_meta( $item_id, '_product_id', true );

				if ( ! $item_id || '0' == $tax_class ) {
					continue;
				}

				// Get product details
				if ( get_post_type( $product_id ) == 'product' ) {
					$_product        = get_product( $product_id );
					$item_tax_status = $_product->get_tax_status();
				} else {
					$item_tax_status = 'taxable';
				}

				// Only calc if taxable
				if ( 'taxable' == $item_tax_status ) {

					$tax_rates = $tax->find_rates( array(
						'country'   => $country,
						'state'     => $state,
						'postcode'  => $postcode,
						'city'      => $city,
						'tax_class' => $tax_class
					) );

					$line_subtotal_taxes = $tax->calc_tax( $line_subtotal, $tax_rates, false );
					$line_taxes          = $tax->calc_tax( $line_total, $tax_rates, false );
					$line_subtotal_tax   = array_sum( $line_subtotal_taxes );
					$line_tax            = array_sum( $line_taxes );

					if ( $line_subtotal_tax < 0 ) {
						$line_subtotal_tax = 0;
					}

					if ( $line_tax < 0 ) {
						$line_tax = 0;
					}

					$item_taxes[ $item_id ] = array(
						'line_subtotal_tax' => wc_format_localized_price( $line_subtotal_tax ),
						'line_tax'          => wc_format_localized_price( $line_tax )
					);

					$item_tax += $line_tax;

					// Sum the item taxes
					foreach ( array_keys( $taxes + $line_taxes ) as $key ) {
						$taxes[ $key ] = ( isset( $line_taxes[ $key ] ) ? $line_taxes[ $key ] : 0 ) + ( isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0 );
					}
				}

			}
		}

		// Now calculate shipping tax
		$matched_tax_rates = array();

		$tax_rates = $tax->find_rates( array(
			'country'   => $country,
			'state'     => $state,
			'postcode'  => $postcode,
			'city'      => $city,
			'tax_class' => ''
		) );

		if ( $tax_rates ) {
			foreach ( $tax_rates as $key => $rate ) {
				if ( isset( $rate['shipping'] ) && 'yes' == $rate['shipping'] ) {
					$matched_tax_rates[ $key ] = $rate;
				}
			}
		}

		$shipping_taxes = $tax->calc_shipping_tax( $shipping, $matched_tax_rates );
		$shipping_tax   = $tax->round( array_sum( $shipping_taxes ) );

		// Remove old tax rows
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d AND order_item_type = 'tax' )", $order_id ) );

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d AND order_item_type = 'tax'", $order_id ) );

		// Get tax rates
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

		// Now merge to keep tax rows
		ob_start();

		foreach ( array_keys( $taxes + $shipping_taxes ) as $key ) {

			$item                        = array();
			$item['rate_id']             = $key;
			$item['name']                = $tax_codes[ $key ];
			$item['label']               = $tax->get_rate_label( $key );
			$item['compound']            = $tax->is_compound( $key ) ? 1 : 0;
			$item['tax_amount']          = wc_format_decimal( isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0 );
			$item['shipping_tax_amount'] = wc_format_decimal( isset( $shipping_taxes[ $key ] ) ? $shipping_taxes[ $key ] : 0 );

			if ( ! $item['label'] ) {
				$item['label'] = WC()->countries->tax_or_vat();
			}

			// Add line item
			$item_id = wc_add_order_item( $order_id, array(
				'order_item_name' => $item['name'],
				'order_item_type' => 'tax'
			) );

			// Add line item meta
			if ( $item_id ) {
				wc_add_order_item_meta( $item_id, 'rate_id', $item['rate_id'] );
				wc_add_order_item_meta( $item_id, 'label', $item['label'] );
				wc_add_order_item_meta( $item_id, 'compound', $item['compound'] );
				wc_add_order_item_meta( $item_id, 'tax_amount', $item['tax_amount'] );
				wc_add_order_item_meta( $item_id, 'shipping_tax_amount', $item['shipping_tax_amount'] );
			}

			include( 'admin/post-types/meta-boxes/views/html-order-tax.php' );
		}

		$tax_row_html = ob_get_clean();

		// Return
		echo json_encode( array(
			'item_tax'     => $item_tax,
			'item_taxes'   => $item_taxes,
			'shipping_tax' => $shipping_tax,
			'tax_row_html' => $tax_row_html
		) );

		// Quit out
		die();
	}

	/**
	 * Add order note via ajax
	 */
	public function add_order_note() {

		check_ajax_referer( 'add-order-note', 'security' );

		$post_id   = (int) $_POST['post_id'];
		$note      = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
		$note_type = $_POST['note_type'];

		$is_customer_note = $note_type == 'customer' ? 1 : 0;

		if ( $post_id > 0 ) {
			$order      = new WC_Order( $post_id );
			$comment_id = $order->add_order_note( $note, $is_customer_note );

			echo '<li rel="' . esc_attr( $comment_id ) . '" class="note ';
			if ( $is_customer_note ) {
				echo 'customer-note';
			}
			echo '"><div class="note_content">';
			echo wpautop( wptexturize( $note ) );
			echo '</div><p class="meta"><a href="#" class="delete_note">'.__( 'Delete note', 'woocommerce' ).'</a></p>';
			echo '</li>';
		}

		// Quit out
		die();
	}

	/**
	 * Delete order note via ajax
	 */
	public function delete_order_note() {

		check_ajax_referer( 'delete-order-note', 'security' );

		$note_id = (int) $_POST['note_id'];

		if ( $note_id > 0 ) {
			wp_delete_comment( $note_id );
		}

		// Quit out
		die();
	}

	/**
	 * Search for products and echo json
	 *
	 * @param string $x (default: '')
	 * @param string $post_types (default: array('product'))
	 */
	public function json_search_products( $x = '', $post_types = array('product') ) {

		check_ajax_referer( 'search-products', 'security' );

		$this->json_headers();

		$term = (string) wc_clean( stripslashes( $_GET['term'] ) );

		if ( empty( $term ) ) {
			die();
		}

		if ( is_numeric( $term ) ) {

			$args = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__in'       => array(0, $term),
				'fields'         => 'ids'
			);

			$args2 = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post_parent'    => $term,
				'fields'         => 'ids'
			);

			$args3 = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_sku',
						'value'   => $term,
						'compare' => 'LIKE'
					)
				),
				'fields'         => 'ids'
			);

			$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ), get_posts( $args3 ) ) );

		} else {

			$args = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				's'              => $term,
				'fields'         => 'ids'
			);

			$args2 = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
					'key'     => '_sku',
					'value'   => $term,
					'compare' => 'LIKE'
					)
				),
				'fields'         => 'ids'
			);

			$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ) ) );

		}

		$found_products = array();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$product = get_product( $post );

				$found_products[ $post ] = $product->get_formatted_name();
			}
		}

		$found_products = apply_filters( 'woocommerce_json_search_found_products', $found_products );

		echo json_encode( $found_products );

		die();
	}

	/**
	 * Search for product variations and return json
	 *
	 * @access public
	 * @return void
	 * @see WC_AJAX::json_search_products()
	 */
	public function json_search_products_and_variations() {
		$this->json_search_products( '', array('product', 'product_variation') );
	}

	/**
	 * Search for customers and return json
	 */
	public function json_search_customers() {

		check_ajax_referer( 'search-customers', 'security' );

		$this->json_headers();

		$term = wc_clean( stripslashes( $_GET['term'] ) );

		if ( empty( $term ) ) {
			die();
		}

		$default = isset( $_GET['default'] ) ? $_GET['default'] : __( 'Guest', 'woocommerce' );

		$found_customers = array( '' => $default );

		add_action( 'pre_user_query', array( $this, 'json_search_customer_name' ) );

		$customers_query = new WP_User_Query( apply_filters( 'woocommerce_json_search_customers_query', array(
			'fields'         => 'all',
			'orderby'        => 'display_name',
			'search'         => '*' . $term . '*',
			'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' )
		) ) );

		remove_action( 'pre_user_query', array( $this, 'json_search_customer_name' ) );

		$customers = $customers_query->get_results();

		if ( $customers ) {
			foreach ( $customers as $customer ) {
				$found_customers[ $customer->ID ] = $customer->display_name . ' (#' . $customer->ID . ' &ndash; ' . sanitize_email( $customer->user_email ) . ')';
			}
		}

		echo json_encode( $found_customers );
		die();
	}

	/**
	 * Search for downloadable product variations and return json
	 *
	 * @access public
	 * @return void
	 * @see WC_AJAX::json_search_products()
	 */
	public function json_search_downloadable_products_and_variations() {
		$term = (string) wc_clean( stripslashes( $_GET['term'] ) );

		$args = array(
			'post_type'      => array( 'product', 'product_variation' ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'orderby'        => 'parent title',
			'meta_query'     => array(
				array(
					'key'   => '_downloadable',
					'value' => 'yes'
				)
			),
			's'              => $term
		);

		$posts = get_posts( $args );
		$found_products = array();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$product = get_product( $post->ID );
				$found_products[ $post->ID ] = $product->get_formatted_name();
			}
		}

		echo json_encode( $found_products );
		die();
	}

	/**
	 * When searching using the WP_User_Query, search names (user meta) too
	 * @param  object $query
	 * @return object
	 */
	public function json_search_customer_name( $query ) {
		global $wpdb;

		$term = wc_clean( stripslashes( $_GET['term'] ) );

		$query->query_from  .= " INNER JOIN {$wpdb->usermeta} AS user_name ON {$wpdb->users}.ID = user_name.user_id AND ( user_name.meta_key = 'first_name' OR user_name.meta_key = 'last_name' ) ";
		$query->query_where .= $wpdb->prepare( " OR user_name.meta_value LIKE %s ", '%' . like_escape( $term ) . '%' );
	}

	/**
	 * Ajax request handling for categories ordering
	 */
	public function term_ordering() {
		global $wpdb;

		$id       = (int) $_POST['id'];
		$next_id  = isset( $_POST['nextid'] ) && (int) $_POST['nextid'] ? (int) $_POST['nextid'] : null;
		$taxonomy = isset( $_POST['thetaxonomy'] ) ? esc_attr( $_POST['thetaxonomy'] ) : null;
		$term     = get_term_by('id', $id, $taxonomy);

		if ( ! $id || ! $term || ! $taxonomy ) {
			die(0);
		}

		wc_reorder_terms( $term, $next_id, $taxonomy );

		$children = get_terms( $taxonomy, "child_of=$id&menu_order=ASC&hide_empty=0" );

		if ( $term && sizeof( $children ) ) {
			echo 'children';
			die();
		}
	}

	/**
	 * Ajax request handling for product ordering
	 *
	 * Based on Simple Page Ordering by 10up (http://wordpress.org/extend/plugins/simple-page-ordering/)
	 */
	public function product_ordering() {
		global $wpdb;

		// check permissions again and make sure we have what we need
		if ( ! current_user_can('edit_products') || empty( $_POST['id'] ) || ( ! isset( $_POST['previd'] ) && ! isset( $_POST['nextid'] ) ) ) {
			die(-1);
		}

		// real post?
		if ( ! $post = get_post( $_POST['id'] ) ) {
			die(-1);
		}

		$this->json_headers();

		$previd  = isset( $_POST['previd'] ) ? $_POST['previd'] : false;
		$nextid  = isset( $_POST['nextid'] ) ? $_POST['nextid'] : false;
		$new_pos = array(); // store new positions for ajax

		$siblings = $wpdb->get_results( $wpdb->prepare('
			SELECT ID, menu_order FROM %1$s AS posts
			WHERE 	posts.post_type 	= \'product\'
			AND 	posts.post_status 	IN ( \'publish\', \'pending\', \'draft\', \'future\', \'private\' )
			AND 	posts.ID			NOT IN (%2$d)
			ORDER BY posts.menu_order ASC, posts.ID DESC
		', $wpdb->posts, $post->ID) );

		$menu_order = 0;

		foreach ( $siblings as $sibling ) {

			// if this is the post that comes after our repositioned post, set our repositioned post position and increment menu order
			if ( $nextid == $sibling->ID ) {
				$wpdb->update(
					$wpdb->posts,
					array(
						'menu_order' => $menu_order
					),
					array( 'ID' => $post->ID ),
					array( '%d' ),
					array( '%d' )
				);
				$new_pos[ $post->ID ] = $menu_order;
				$menu_order++;
			}

			// if repositioned post has been set, and new items are already in the right order, we can stop
			if ( isset( $new_pos[ $post->ID ] ) && $sibling->menu_order >= $menu_order ) {
				break;
			}

			// set the menu order of the current sibling and increment the menu order
			$wpdb->update(
				$wpdb->posts,
				array(
					'menu_order' => $menu_order
				),
				array( 'ID' => $sibling->ID ),
				array( '%d' ),
				array( '%d' )
			);
			$new_pos[ $sibling->ID ] = $menu_order;
			$menu_order++;

			if ( ! $nextid && $previd == $sibling->ID ) {
				$wpdb->update(
					$wpdb->posts,
					array(
						'menu_order' => $menu_order
					),
					array( 'ID' => $post->ID ),
					array( '%d' ),
					array( '%d' )
				);
				$new_pos[$post->ID] = $menu_order;
				$menu_order++;
			}

		}

		die( json_encode( $new_pos ) );
	}
}

new WC_AJAX();
