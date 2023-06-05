<?php
/**
 * Order Line Item (product)
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Order item product class.
 */
class WC_Order_Item_Product extends WC_Order_Item {

	/**
	 * Order Data array. This is the core order data exposed in APIs since 3.0.0.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $extra_data = array(
		'product_id'   => 0,
		'variation_id' => 0,
		'quantity'     => 1,
		'tax_class'    => '',
		'subtotal'     => 0,
		'subtotal_tax' => 0,
		'total'        => 0,
		'total_tax'    => 0,
		'taxes'        => array(
			'subtotal' => array(),
			'total'    => array(),
		),
	);

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set quantity.
	 *
	 * @param int $value Quantity.
	 */
	public function set_quantity( $value ) {
		$this->set_prop( 'quantity', wc_stock_amount( $value ) );
	}

	/**
	 * Set tax class.
	 *
	 * @param string $value Tax class.
	 */
	public function set_tax_class( $value ) {
		if ( $value && ! in_array( $value, WC_Tax::get_tax_class_slugs(), true ) ) {
			$this->error( 'order_item_product_invalid_tax_class', __( 'Invalid tax class', 'woocommerce' ) );
		}
		$this->set_prop( 'tax_class', $value );
	}

	/**
	 * Set Product ID
	 *
	 * @param int $value Product ID.
	 */
	public function set_product_id( $value ) {
		if ( $value > 0 && 'product' !== get_post_type( absint( $value ) ) ) {
			$this->error( 'order_item_product_invalid_product_id', __( 'Invalid product ID', 'woocommerce' ) );
		}
		$this->set_prop( 'product_id', absint( $value ) );
	}

	/**
	 * Set variation ID.
	 *
	 * @param int $value Variation ID.
	 */
	public function set_variation_id( $value ) {
		if ( $value > 0 && 'product_variation' !== get_post_type( $value ) ) {
			$this->error( 'order_item_product_invalid_variation_id', __( 'Invalid variation ID', 'woocommerce' ) );
		}
		$this->set_prop( 'variation_id', absint( $value ) );
	}

	/**
	 * Line subtotal (before discounts).
	 *
	 * @param string $value Subtotal.
	 */
	public function set_subtotal( $value ) {
		$value = wc_format_decimal( $value );

		if ( ! is_numeric( $value ) ) {
			$value = 0;
		}

		$this->set_prop( 'subtotal', $value );
	}

	/**
	 * Line total (after discounts).
	 *
	 * @param string $value Total.
	 */
	public function set_total( $value ) {
		$value = wc_format_decimal( $value );

		if ( ! is_numeric( $value ) ) {
			$value = 0;
		}

		$this->set_prop( 'total', $value );

		// Subtotal cannot be less than total.
		if ( '' === $this->get_subtotal() || $this->get_subtotal() < $this->get_total() ) {
			$this->set_subtotal( $value );
		}
	}

	/**
	 * Line subtotal tax (before discounts).
	 *
	 * @param string $value Subtotal tax.
	 */
	public function set_subtotal_tax( $value ) {
		$this->set_prop( 'subtotal_tax', wc_format_decimal( $value ) );
	}

	/**
	 * Line total tax (after discounts).
	 *
	 * @param string $value Total tax.
	 */
	public function set_total_tax( $value ) {
		$this->set_prop( 'total_tax', wc_format_decimal( $value ) );
	}

	/**
	 * Set line taxes and totals for passed in taxes.
	 *
	 * @param array $raw_tax_data Raw tax data.
	 */
	public function set_taxes( $raw_tax_data ) {
		$raw_tax_data = maybe_unserialize( $raw_tax_data );
		$tax_data     = array(
			'total'    => array(),
			'subtotal' => array(),
		);
		if ( ! empty( $raw_tax_data['total'] ) && ! empty( $raw_tax_data['subtotal'] ) ) {
			$tax_data['subtotal'] = array_map( 'wc_format_decimal', $raw_tax_data['subtotal'] );
			$tax_data['total']    = array_map( 'wc_format_decimal', $raw_tax_data['total'] );

			// Subtotal cannot be less than total!
			if ( array_sum( $tax_data['subtotal'] ) < array_sum( $tax_data['total'] ) ) {
				$tax_data['subtotal'] = $tax_data['total'];
			}
		}
		$this->set_prop( 'taxes', $tax_data );

		if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
			$this->set_total_tax( array_sum( $tax_data['total'] ) );
			$this->set_subtotal_tax( array_sum( $tax_data['subtotal'] ) );
		} else {
			$this->set_total_tax( array_sum( array_map( 'wc_round_tax_total', $tax_data['total'] ) ) );
			$this->set_subtotal_tax( array_sum( array_map( 'wc_round_tax_total', $tax_data['subtotal'] ) ) );
		}
	}

	/**
	 * Set variation data (stored as meta data - write only).
	 *
	 * @param array $data Key/Value pairs.
	 */
	public function set_variation( $data = array() ) {
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$this->add_meta_data( str_replace( 'attribute_', '', $key ), $value, true );
			}
		}
	}

	/**
	 * Set properties based on passed in product object.
	 *
	 * @param WC_Product $product Product instance.
	 */
	public function set_product( $product ) {
		if ( ! is_a( $product, 'WC_Product' ) ) {
			$this->error( 'order_item_product_invalid_product', __( 'Invalid product', 'woocommerce' ) );
		}
		if ( $product->is_type( 'variation' ) ) {
			$this->set_product_id( $product->get_parent_id() );
			$this->set_variation_id( $product->get_id() );
			$this->set_variation( is_callable( array( $product, 'get_variation_attributes' ) ) ? $product->get_variation_attributes() : array() );
		} else {
			$this->set_product_id( $product->get_id() );
		}
		$this->set_name( $product->get_name() );
		$this->set_tax_class( $product->get_tax_class() );
	}

	/**
	 * Set meta data for backordered products.
	 */
	public function set_backorder_meta() {
		$product = $this->get_product();
		if ( $product && $product->backorders_require_notification() && $product->is_on_backorder( $this->get_quantity() ) ) {
			$this->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ), $this ), $this->get_quantity() - max( 0, $product->get_stock_quantity() ), true );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get order item type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'line_item';
	}

	/**
	 * Get product ID.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_product_id( $context = 'view' ) {
		return $this->get_prop( 'product_id', $context );
	}

	/**
	 * Get variation ID.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_variation_id( $context = 'view' ) {
		return $this->get_prop( 'variation_id', $context );
	}

	/**
	 * Get quantity.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_quantity( $context = 'view' ) {
		return $this->get_prop( 'quantity', $context );
	}

	/**
	 * Get tax class.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_tax_class( $context = 'view' ) {
		return $this->get_prop( 'tax_class', $context );
	}

	/**
	 * Get subtotal.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_subtotal( $context = 'view' ) {
		return $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Get subtotal tax.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_subtotal_tax( $context = 'view' ) {
		return $this->get_prop( 'subtotal_tax', $context );
	}

	/**
	 * Get total.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_total( $context = 'view' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Get total tax.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_total_tax( $context = 'view' ) {
		return $this->get_prop( 'total_tax', $context );
	}

	/**
	 * Get taxes.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return array
	 */
	public function get_taxes( $context = 'view' ) {
		return $this->get_prop( 'taxes', $context );
	}

	/**
	 * Get the associated product.
	 *
	 * @return WC_Product|bool
	 */
	public function get_product() {
		if ( $this->get_variation_id() ) {
			$product = wc_get_product( $this->get_variation_id() );
		} else {
			$product = wc_get_product( $this->get_product_id() );
		}

		// Backwards compatible filter from WC_Order::get_product_from_item().
		if ( has_filter( 'woocommerce_get_product_from_item' ) ) {
			$product = apply_filters( 'woocommerce_get_product_from_item', $product, $this, $this->get_order() );
		}

		return apply_filters( 'woocommerce_order_item_product', $product, $this );
	}

	/**
	 * Get the Download URL.
	 *
	 * @param  int $download_id Download ID.
	 * @return string
	 */
	public function get_item_download_url( $download_id ) {
		$order = $this->get_order();

		return $order ? add_query_arg(
			array(
				'download_file' => $this->get_variation_id() ? $this->get_variation_id() : $this->get_product_id(),
				'order'         => $order->get_order_key(),
				'email'         => rawurlencode( $order->get_billing_email() ),
				'key'           => $download_id,
			),
			trailingslashit( home_url() )
		) : '';
	}

	/**
	 * Get any associated downloadable files.
	 *
	 * @return array
	 */
	public function get_item_downloads() {
		$files      = array();
		$product    = $this->get_product();
		$order      = $this->get_order();
		$product_id = $this->get_variation_id() ? $this->get_variation_id() : $this->get_product_id();

		if ( $product && $order && $product->is_downloadable() && $order->is_download_permitted() ) {
			$email_hash         = function_exists( 'hash' ) ? hash( 'sha256', $order->get_billing_email() ) : sha1( $order->get_billing_email() );
			$data_store         = WC_Data_Store::load( 'customer-download' );
			$customer_downloads = $data_store->get_downloads(
				array(
					'user_email' => $order->get_billing_email(),
					'order_id'   => $order->get_id(),
					'product_id' => $product_id,
				)
			);
			foreach ( $customer_downloads as $customer_download ) {
				$download_id = $customer_download->get_download_id();

				if ( $product->has_file( $download_id ) ) {
					$file                  = $product->get_file( $download_id );
					$files[ $download_id ] = $file->get_data();
					$files[ $download_id ]['downloads_remaining'] = $customer_download->get_downloads_remaining();
					$files[ $download_id ]['access_expires']      = $customer_download->get_access_expires();
					$files[ $download_id ]['download_url']        = add_query_arg(
						array(
							'download_file' => $product_id,
							'order'         => $order->get_order_key(),
							'uid'           => $email_hash,
							'key'           => $download_id,
						),
						trailingslashit( home_url() )
					);
				}
			}
		}

		return apply_filters( 'woocommerce_get_item_downloads', $files, $this, $order );
	}

	/**
	 * Get tax status.
	 *
	 * @return string
	 */
	public function get_tax_status() {
		$product = $this->get_product();
		return $product ? $product->get_tax_status() : 'taxable';
	}

	/*
	|--------------------------------------------------------------------------
	| Array Access Methods
	|--------------------------------------------------------------------------
	|
	| For backwards compatibility with legacy arrays.
	|
	*/

	/**
	 * OffsetGet for ArrayAccess/Backwards compatibility.
	 *
	 * @param string $offset Offset.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		if ( 'line_subtotal' === $offset ) {
			$offset = 'subtotal';
		} elseif ( 'line_subtotal_tax' === $offset ) {
			$offset = 'subtotal_tax';
		} elseif ( 'line_total' === $offset ) {
			$offset = 'total';
		} elseif ( 'line_tax' === $offset ) {
			$offset = 'total_tax';
		} elseif ( 'line_tax_data' === $offset ) {
			$offset = 'taxes';
		} elseif ( 'qty' === $offset ) {
			$offset = 'quantity';
		}
		return parent::offsetGet( $offset );
	}

	/**
	 * OffsetSet for ArrayAccess/Backwards compatibility.
	 *
	 * @deprecated 4.4.0
	 * @param string $offset Offset.
	 * @param mixed  $value  Value.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		wc_deprecated_function( 'WC_Order_Item_Product::offsetSet', '4.4.0', '' );
		if ( 'line_subtotal' === $offset ) {
			$offset = 'subtotal';
		} elseif ( 'line_subtotal_tax' === $offset ) {
			$offset = 'subtotal_tax';
		} elseif ( 'line_total' === $offset ) {
			$offset = 'total';
		} elseif ( 'line_tax' === $offset ) {
			$offset = 'total_tax';
		} elseif ( 'line_tax_data' === $offset ) {
			$offset = 'taxes';
		} elseif ( 'qty' === $offset ) {
			$offset = 'quantity';
		}
		parent::offsetSet( $offset, $value );
	}

	/**
	 * OffsetExists for ArrayAccess.
	 *
	 * @param string $offset Offset.
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		if ( in_array( $offset, array( 'line_subtotal', 'line_subtotal_tax', 'line_total', 'line_tax', 'line_tax_data', 'item_meta_array', 'item_meta', 'qty' ), true ) ) {
			return true;
		}
		return parent::offsetExists( $offset );
	}
}
