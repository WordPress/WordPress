<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Ecommerce;

use EDD_Download;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class EasyDigitalDownloads extends Base {
	public function register_hooks() {
		if ( ! is_admin() ) {
			add_action( 'template_redirect', [ $this, 'on_product_view' ], 99999, 0 );
		}

		parent::register_hooks();

		// these actions may be triggered in admin when ajax is used
		add_action( 'edd_payment_receipt_after_table', [ $this, 'on_order' ], 99999, 2 );
		add_action( 'edd_post_remove_from_cart', [ $this, 'on_cart_update' ], 99999, 0 );
		add_action( 'edd_post_add_to_cart', [ $this, 'on_cart_update' ], 99999, 0 );
		add_action( 'edd_cart_discounts_removed', [ $this, 'on_cart_update' ], 99999, 0 );
		add_action( 'edd_after_set_cart_item_quantity', [ $this, 'on_cart_update' ], 99999, 0 );
		add_action( 'edd_cart_discount_set', [ $this, 'on_cart_update' ], 99999, 0 );
	}

	public function on_cart_update() {
		if ( ! function_exists( 'EDD' )
			 || ! class_exists( '\EDD_Download' ) ) {
			return;
		}

		$cart = EDD()->cart;

		$contents = $cart->get_contents();

		$tracking_code = '';
		foreach ( $contents as $key => $item ) {
			$download = new EDD_Download( $item['id'] );

			// If the item is not a download or it's status has changed since it was added to the cart.
			if ( empty( $download->ID ) || ! $download->can_purchase() ) {
				unset( $cart[ $key ] );
			}

			$name = $download->get_name();

			$price_id = edd_get_cart_item_price_id( $item );
			$price    = $download->get_price();

			if ( isset( $price_id ) ) {
				// variation
				$name .= ' - ' . edd_get_price_option_name( $item['id'], $price_id );
				$price = edd_get_price_option_amount( $download->ID, $price_id );
			}
			$sku        = $this->get_sku( $download, $item['id'] );
			$categories = $this->get_product_categories( $download->ID );
			$quantity   = isset( $item['quantity'] ) ? $item['quantity'] : 0;

			$params         = [ 'addEcommerceItem', $sku, $name, $categories, $price, $quantity ];
			$tracking_code .= $this->make_matomo_js_tracker_call( $params );
		}

		$total = 0;
		if ( ! empty( $cart->get_total_fees() ) ) {
			$total = $cart->get_total_fees();
		} elseif ( ! empty( $cart->get_total() ) ) {
			$total = $cart->get_total();
		}

		$tracking_code .= $this->make_matomo_js_tracker_call( [ 'trackEcommerceCartUpdate', $total ] );

		// we can't echo directly as we wouldn't know where in the template rendering stage we are and whether
		// we're supposed to print or not etc
		$this->cart_update_queue = $this->wrap_script( $tracking_code );
		$this->logger->log( 'Tracked ecommerce cart update: ' );
	}

	private function get_product_categories( $download_id ) {
		$categories = (array) get_the_terms( $download_id, 'download_category' );

		return array_values( array_filter( wp_list_pluck( $categories, 'name' ) ) );
	}

	/**
	 * @param EDD_Download $download
	 *
	 * @return mixed
	 */
	private function get_sku( $download, $download_id ) {
		$sku = $download->get_sku();
		if ( ! edd_use_skus() || empty( $sku ) || '-' === $sku ) {
			$sku = $download_id;
		}

		return '' . $sku;
	}

	public function on_product_view() {
		if ( ! is_singular( 'download' ) ) {
			return;
		}

		$download_id = get_the_ID();

		if ( empty( $download_id ) ) {
			return;
		}

		if ( ! class_exists( '\EDD_Download' ) ) {
			return;
		}

		$download = new EDD_Download( $download_id );

		$sku = $this->get_sku( $download, $download_id );

		$params = [
			'setEcommerceView',
			$sku,
			$download->get_name(),
			$this->get_product_categories( $download_id ),
			$download->get_price(),
		];
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->wrap_script( $this->make_matomo_js_tracker_call( $params ) );
	}

	public function on_order( $payment, $edd_receipt_args ) {
		if ( $edd_receipt_args['payment_id'] ) {
			if ( 'publish' !== $payment->post_status
				 && 'complete' !== $payment->post_status
				 && 'edd_subscription' !== $payment->post_status ) {
				return;
			}
			// Use a meta value so we only send the beacon once.
			if ( $this->has_order_been_tracked_already( $payment->ID ) ) {
				return;
			}

			if ( ! class_exists( '\EDD_Download' ) ) {
				return;
			}

			if ( ! function_exists( 'edd_get_payment_meta_cart_details' ) ) {
				return;
			}

			$this->set_order_been_tracked( $payment->ID );

			if ( function_exists( 'edd_get_payment_number' ) ) {
				$order_id_to_track = edd_get_payment_number( $payment->ID );
			} else {
				$order_id_to_track = $payment->ID;
			}

			$tracking_code = '';

			if ( ! empty( $edd_receipt_args['products'] ) ) {
				$cart = edd_get_payment_meta_cart_details( $payment->ID, true );
				if ( $cart ) {
					foreach ( $cart as $key => $item ) {
						if ( empty( $item['in_bundle'] ) ) {
							$price_id = edd_get_cart_item_price_id( $item );
							$name     = $item['name'];
							if ( isset( $price_id ) ) {
								// variation
								$name .= ' - ' . edd_get_price_option_name( $item['id'], $price_id );
							}

							$download = new EDD_Download( $item['id'] );
							$sku      = $this->get_sku( $download, $item['id'] );

							$price = 0;
							if ( isset( $item['item_price'] ) && is_numeric( $item['item_price'] ) ) {
								$price = $item['item_price'];
							}

							$params         = [
								'addEcommerceItem',
								$sku,
								$name,
								$this->get_product_categories( $item['id'] ),
								$price,
								$item['quantity'],
							];
							$tracking_code .= $this->make_matomo_js_tracker_call( $params );
						}
					}
				}
			}

			$grand_total = edd_get_payment_amount( $payment->ID );

			$payment_meta = edd_get_payment_meta( $payment->ID );
			$discount     = 0;
			if ( ! empty( $payment_meta['user_info']['discount'] )
				 && 'none' !== $payment_meta['user_info']['discount'] ) {
				$discount = $payment_meta['user_info']['discount'];
				$discount = explode( ',', $discount );
				$discount = reset( $discount );
			}

			$params         = [
				'trackEcommerceOrder',
				'' . $order_id_to_track,
				$grand_total ? $grand_total : 0,
				edd_payment_subtotal( $payment->ID ),
				edd_use_taxes() ? edd_get_payment_tax( $payment->ID, $payment_meta ) : '0',
				$shipping = 0,
				$discount,
			];
			$tracking_code .= $this->make_matomo_js_tracker_call( $params );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->wrap_script( $tracking_code );
		}
	}
}
