<?php
/**
 * Regular order
 *
 * @package WooCommerce\Classes
 * @version 2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Order Class.
 *
 * These are regular WooCommerce orders, which extend the abstract order class.
 */
class WC_Order extends WC_Abstract_Order {

	/**
	 * Stores data about status changes so relevant hooks can be fired.
	 *
	 * @var bool|array
	 */
	protected $status_transition = false;

	/**
	 * Order Data array. This is the core order data exposed in APIs since 3.0.0.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $data = array(
		// Abstract order props.
		'parent_id'                    => 0,
		'status'                       => '',
		'currency'                     => '',
		'version'                      => '',
		'prices_include_tax'           => false,
		'date_created'                 => null,
		'date_modified'                => null,
		'discount_total'               => 0,
		'discount_tax'                 => 0,
		'shipping_total'               => 0,
		'shipping_tax'                 => 0,
		'cart_tax'                     => 0,
		'total'                        => 0,
		'total_tax'                    => 0,

		// Order props.
		'customer_id'                  => 0,
		'order_key'                    => '',
		'billing'                      => array(
			'first_name' => '',
			'last_name'  => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'email'      => '',
			'phone'      => '',
		),
		'shipping'                     => array(
			'first_name' => '',
			'last_name'  => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'phone'      => '',
		),
		'payment_method'               => '',
		'payment_method_title'         => '',
		'transaction_id'               => '',
		'customer_ip_address'          => '',
		'customer_user_agent'          => '',
		'created_via'                  => '',
		'customer_note'                => '',
		'date_completed'               => null,
		'date_paid'                    => null,
		'cart_hash'                    => '',

		// Operational data.
		'order_stock_reduced'          => false,
		'download_permissions_granted' => false,
		'new_order_email_sent'         => false,
		'recorded_sales'               => false,
		'recorded_coupon_usage_counts' => false,
	);

	/**
	 * List of properties that were earlier managed by data store. However, since DataStore is a not a stored entity in itself, they used to store data in metadata of the data object.
	 * With custom tables, some of these are moved from metadata to their own columns, but existing code will still try to add them to metadata. This array is used to keep track of such properties.
	 *
	 * Only reason to add a property here is that you are moving properties from DataStore instance to data object. Otherwise, if you are adding a new property, consider adding it to $data array instead.
	 *
	 * @var array
	 */
	protected $legacy_datastore_props = array(
		'_recorded_sales',
		'_recorded_coupon_usage_counts',
		'_download_permissions_granted',
		'_order_stock_reduced',
		'_new_order_email_sent',
	);

	/**
	 * When a payment is complete this function is called.
	 *
	 * Most of the time this should mark an order as 'processing' so that admin can process/post the items.
	 * If the cart contains only downloadable items then the order is 'completed' since the admin needs to take no action.
	 * Stock levels are reduced at this point.
	 * Sales are also recorded for products.
	 * Finally, record the date of payment.
	 *
	 * @param string $transaction_id Optional transaction id to store in post meta.
	 * @return bool success
	 */
	public function payment_complete( $transaction_id = '' ) {
		if ( ! $this->get_id() ) { // Order must exist.
			return false;
		}

		try {
			do_action( 'woocommerce_pre_payment_complete', $this->get_id(), $transaction_id );

			if ( WC()->session ) {
				WC()->session->set( 'order_awaiting_payment', false );
			}

			if ( $this->has_status( apply_filters( 'woocommerce_valid_order_statuses_for_payment_complete', array( 'on-hold', 'pending', 'failed', 'cancelled' ), $this ) ) ) {
				if ( ! empty( $transaction_id ) ) {
					$this->set_transaction_id( $transaction_id );
				}
				if ( ! $this->get_date_paid( 'edit' ) ) {
					$this->set_date_paid( time() );
				}
				$this->set_status( apply_filters( 'woocommerce_payment_complete_order_status', $this->needs_processing() ? 'processing' : 'completed', $this->get_id(), $this ) );
				$this->save();

				do_action( 'woocommerce_payment_complete', $this->get_id(), $transaction_id );
			} else {
				do_action( 'woocommerce_payment_complete_order_status_' . $this->get_status(), $this->get_id(), $transaction_id );
			}
		} catch ( Exception $e ) {
			/**
			 * If there was an error completing the payment, log to a file and add an order note so the admin can take action.
			 */
			$logger = wc_get_logger();
			$logger->error(
				sprintf(
					'Error completing payment for order #%d',
					$this->get_id()
				),
				array(
					'order' => $this,
					'error' => $e,
				)
			);
			$this->add_order_note( __( 'Payment complete event failed.', 'woocommerce' ) . ' ' . $e->getMessage() );
			return false;
		}
		return true;
	}

	/**
	 * Gets order total - formatted for display.
	 *
	 * @param string $tax_display      Type of tax display.
	 * @param bool   $display_refunded If should include refunded value.
	 *
	 * @return string
	 */
	public function get_formatted_order_total( $tax_display = '', $display_refunded = true ) {
		$formatted_total = wc_price( $this->get_total(), array( 'currency' => $this->get_currency() ) );
		$order_total     = $this->get_total();
		$total_refunded  = $this->get_total_refunded();
		$tax_string      = '';

		// Tax for inclusive prices.
		if ( wc_tax_enabled() && 'incl' === $tax_display ) {
			$tax_string_array = array();
			$tax_totals       = $this->get_tax_totals();

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( $tax_totals as $code => $tax ) {
					$tax_amount         = ( $total_refunded && $display_refunded ) ? wc_price( WC_Tax::round( $tax->amount - $this->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ), array( 'currency' => $this->get_currency() ) ) : $tax->formatted_amount;
					$tax_string_array[] = sprintf( '%s %s', $tax_amount, $tax->label );
				}
			} elseif ( ! empty( $tax_totals ) ) {
				$tax_amount         = ( $total_refunded && $display_refunded ) ? $this->get_total_tax() - $this->get_total_tax_refunded() : $this->get_total_tax();
				$tax_string_array[] = sprintf( '%s %s', wc_price( $tax_amount, array( 'currency' => $this->get_currency() ) ), WC()->countries->tax_or_vat() );
			}

			if ( ! empty( $tax_string_array ) ) {
				/* translators: %s: taxes */
				$tax_string = ' <small class="includes_tax">' . sprintf( __( '(includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) ) . '</small>';
			}
		}

		if ( $total_refunded && $display_refunded ) {
			$formatted_total = '<del aria-hidden="true">' . wp_strip_all_tags( $formatted_total ) . '</del> <ins>' . wc_price( $order_total - $total_refunded, array( 'currency' => $this->get_currency() ) ) . $tax_string . '</ins>';
		} else {
			$formatted_total .= $tax_string;
		}

		/**
		 * Filter WooCommerce formatted order total.
		 *
		 * @param string   $formatted_total  Total to display.
		 * @param WC_Order $order            Order data.
		 * @param string   $tax_display      Type of tax display.
		 * @param bool     $display_refunded If should include refunded value.
		 */
		return apply_filters( 'woocommerce_get_formatted_order_total', $formatted_total, $this, $tax_display, $display_refunded );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete orders from the database.
	| Written in abstract fashion so that the way orders are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/

	/**
	 * Save data to the database.
	 *
	 * @since 3.0.0
	 * @return int order ID
	 */
	public function save() {
		$this->maybe_set_user_billing_email();
		parent::save();
		$this->status_transition();

		return $this->get_id();
	}

	/**
	 * Log an error about this order is exception is encountered.
	 *
	 * @param Exception $e Exception object.
	 * @param string    $message Message regarding exception thrown.
	 * @since 3.7.0
	 */
	protected function handle_exception( $e, $message = 'Error' ) {
		wc_get_logger()->error(
			$message,
			array(
				'order' => $this,
				'error' => $e,
			)
		);
		$this->add_order_note( $message . ' ' . $e->getMessage() );
	}

	/**
	 * Set order status.
	 *
	 * @since 3.0.0
	 * @param string $new_status    Status to change the order to. No internal wc- prefix is required.
	 * @param string $note          Optional note to add.
	 * @param bool   $manual_update Is this a manual order status change?.
	 * @return array
	 */
	public function set_status( $new_status, $note = '', $manual_update = false ) {
		$result = parent::set_status( $new_status );

		if ( true === $this->object_read && ! empty( $result['from'] ) && $result['from'] !== $result['to'] ) {
			$this->status_transition = array(
				'from'   => ! empty( $this->status_transition['from'] ) ? $this->status_transition['from'] : $result['from'],
				'to'     => $result['to'],
				'note'   => $note,
				'manual' => (bool) $manual_update,
			);

			if ( $manual_update ) {
				do_action( 'woocommerce_order_edit_status', $this->get_id(), $result['to'] );
			}

			$this->maybe_set_date_paid();
			$this->maybe_set_date_completed();
		}

		return $result;
	}

	/**
	 * Maybe set date paid.
	 *
	 * Sets the date paid variable when transitioning to the payment complete
	 * order status. This is either processing or completed. This is not filtered
	 * to avoid infinite loops e.g. if loading an order via the filter.
	 *
	 * Date paid is set once in this manner - only when it is not already set.
	 * This ensures the data exists even if a gateway does not use the
	 * `payment_complete` method.
	 *
	 * @since 3.0.0
	 */
	public function maybe_set_date_paid() {
		// This logic only runs if the date_paid prop has not been set yet.
		if ( ! $this->get_date_paid( 'edit' ) ) {
			$payment_completed_status = apply_filters( 'woocommerce_payment_complete_order_status', $this->needs_processing() ? 'processing' : 'completed', $this->get_id(), $this );

			if ( $this->has_status( $payment_completed_status ) ) {
				// If payment complete status is reached, set paid now.
				$this->set_date_paid( time() );

			} elseif ( 'processing' === $payment_completed_status && $this->has_status( 'completed' ) ) {
				// If payment complete status was processing, but we've passed that and still have no date, set it now.
				$this->set_date_paid( time() );
			}
		}
	}

	/**
	 * Maybe set date completed.
	 *
	 * Sets the date completed variable when transitioning to completed status.
	 *
	 * @since 3.0.0
	 */
	protected function maybe_set_date_completed() {
		if ( $this->has_status( 'completed' ) ) {
			$this->set_date_completed( time() );
		}
	}

	/**
	 * Updates status of order immediately.
	 *
	 * @uses WC_Order::set_status()
	 * @param string $new_status    Status to change the order to. No internal wc- prefix is required.
	 * @param string $note          Optional note to add.
	 * @param bool   $manual        Is this a manual order status change?.
	 * @return bool
	 */
	public function update_status( $new_status, $note = '', $manual = false ) {
		if ( ! $this->get_id() ) { // Order must exist.
			return false;
		}

		try {
			$this->set_status( $new_status, $note, $manual );
			$this->save();
		} catch ( Exception $e ) {
			$logger = wc_get_logger();
			$logger->error(
				sprintf(
					'Error updating status for order #%d',
					$this->get_id()
				),
				array(
					'order' => $this,
					'error' => $e,
				)
			);
			$this->add_order_note( __( 'Update status event failed.', 'woocommerce' ) . ' ' . $e->getMessage() );
			return false;
		}
		return true;
	}

	/**
	 * Handle the status transition.
	 */
	protected function status_transition() {
		$status_transition = $this->status_transition;

		// Reset status transition variable.
		$this->status_transition = false;

		if ( $status_transition ) {
			try {
				do_action( 'woocommerce_order_status_' . $status_transition['to'], $this->get_id(), $this );

				if ( ! empty( $status_transition['from'] ) ) {
					/* translators: 1: old order status 2: new order status */
					$transition_note = sprintf( __( 'Order status changed from %1$s to %2$s.', 'woocommerce' ), wc_get_order_status_name( $status_transition['from'] ), wc_get_order_status_name( $status_transition['to'] ) );

					// Note the transition occurred.
					$this->add_status_transition_note( $transition_note, $status_transition );

					do_action( 'woocommerce_order_status_' . $status_transition['from'] . '_to_' . $status_transition['to'], $this->get_id(), $this );
					do_action( 'woocommerce_order_status_changed', $this->get_id(), $status_transition['from'], $status_transition['to'], $this );

					// Work out if this was for a payment, and trigger a payment_status hook instead.
					if (
						in_array( $status_transition['from'], apply_filters( 'woocommerce_valid_order_statuses_for_payment', array( 'pending', 'failed' ), $this ), true )
						&& in_array( $status_transition['to'], wc_get_is_paid_statuses(), true )
					) {
						/**
						 * Fires when the order progresses from a pending payment status to a paid one.
						 *
						 * @since 3.9.0
						 * @param int Order ID
						 * @param WC_Order Order object
						 */
						do_action( 'woocommerce_order_payment_status_changed', $this->get_id(), $this );
					}
				} else {
					/* translators: %s: new order status */
					$transition_note = sprintf( __( 'Order status set to %s.', 'woocommerce' ), wc_get_order_status_name( $status_transition['to'] ) );

					// Note the transition occurred.
					$this->add_status_transition_note( $transition_note, $status_transition );
				}
			} catch ( Exception $e ) {
				$logger = wc_get_logger();
				$logger->error(
					sprintf(
						'Status transition of order #%d errored!',
						$this->get_id()
					),
					array(
						'order' => $this,
						'error' => $e,
					)
				);
				$this->add_order_note( __( 'Error during status transition.', 'woocommerce' ) . ' ' . $e->getMessage() );
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the order object.
	|
	*/

	/**
	 * Get basic order data in array format.
	 *
	 * @return array
	 */
	public function get_base_data() {
		return array_merge(
			array( 'id' => $this->get_id() ),
			$this->data,
			array( 'number' => $this->get_order_number() )
		);
	}

	/**
	 * Get all class data in array format.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_data() {
		return array_merge(
			$this->get_base_data(),
			array(
				'meta_data'      => $this->get_meta_data(),
				'line_items'     => $this->get_items( 'line_item' ),
				'tax_lines'      => $this->get_items( 'tax' ),
				'shipping_lines' => $this->get_items( 'shipping' ),
				'fee_lines'      => $this->get_items( 'fee' ),
				'coupon_lines'   => $this->get_items( 'coupon' ),
			)
		);
	}

	/**
	 * Expands the shipping and billing information in the changes array.
	 */
	public function get_changes() {
		$changed_props = parent::get_changes();
		$subs          = array( 'shipping', 'billing' );
		foreach ( $subs as $sub ) {
			if ( ! empty( $changed_props[ $sub ] ) ) {
				foreach ( $changed_props[ $sub ] as $sub_prop => $value ) {
					$changed_props[ $sub . '_' . $sub_prop ] = $value;
				}
			}
		}
		if ( isset( $changed_props['customer_note'] ) ) {
			$changed_props['post_excerpt'] = $changed_props['customer_note'];
		}
		return $changed_props;
	}

	/**
	 * Gets the order number for display (by default, order ID).
	 *
	 * @return string
	 */
	public function get_order_number() {
		return (string) apply_filters( 'woocommerce_order_number', $this->get_id(), $this );
	}

	/**
	 * Get order key.
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_order_key( $context = 'view' ) {
		return $this->get_prop( 'order_key', $context );
	}

	/**
	 * Get customer_id.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return int
	 */
	public function get_customer_id( $context = 'view' ) {
		return $this->get_prop( 'customer_id', $context );
	}

	/**
	 * Alias for get_customer_id().
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return int
	 */
	public function get_user_id( $context = 'view' ) {
		return $this->get_customer_id( $context );
	}

	/**
	 * Get the user associated with the order. False for guests.
	 *
	 * @return WP_User|false
	 */
	public function get_user() {
		return $this->get_user_id() ? get_user_by( 'id', $this->get_user_id() ) : false;
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @since  3.0.0
	 * @param  string $prop Name of prop to get.
	 * @param  string $address billing or shipping.
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return mixed
	 */
	protected function get_address_prop( $prop, $address = 'billing', $context = 'view' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data[ $address ] ) ) {
			$value = isset( $this->changes[ $address ][ $prop ] ) ? $this->changes[ $address ][ $prop ] : $this->data[ $address ][ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . $address . '_' . $prop, $value, $this );
			}
		}
		return $value;
	}

	/**
	 * Get billing first name.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_first_name( $context = 'view' ) {
		return $this->get_address_prop( 'first_name', 'billing', $context );
	}

	/**
	 * Get billing last name.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_last_name( $context = 'view' ) {
		return $this->get_address_prop( 'last_name', 'billing', $context );
	}

	/**
	 * Get billing company.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_company( $context = 'view' ) {
		return $this->get_address_prop( 'company', 'billing', $context );
	}

	/**
	 * Get billing address line 1.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_address_1( $context = 'view' ) {
		return $this->get_address_prop( 'address_1', 'billing', $context );
	}

	/**
	 * Get billing address line 2.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_address_2( $context = 'view' ) {
		return $this->get_address_prop( 'address_2', 'billing', $context );
	}

	/**
	 * Get billing city.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_city( $context = 'view' ) {
		return $this->get_address_prop( 'city', 'billing', $context );
	}

	/**
	 * Get billing state.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_state( $context = 'view' ) {
		return $this->get_address_prop( 'state', 'billing', $context );
	}

	/**
	 * Get billing postcode.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_postcode( $context = 'view' ) {
		return $this->get_address_prop( 'postcode', 'billing', $context );
	}

	/**
	 * Get billing country.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_country( $context = 'view' ) {
		return $this->get_address_prop( 'country', 'billing', $context );
	}

	/**
	 * Get billing email.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_email( $context = 'view' ) {
		return $this->get_address_prop( 'email', 'billing', $context );
	}

	/**
	 * Get billing phone.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_billing_phone( $context = 'view' ) {
		return $this->get_address_prop( 'phone', 'billing', $context );
	}

	/**
	 * Get shipping first name.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_first_name( $context = 'view' ) {
		return $this->get_address_prop( 'first_name', 'shipping', $context );
	}

	/**
	 * Get shipping_last_name.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_last_name( $context = 'view' ) {
		return $this->get_address_prop( 'last_name', 'shipping', $context );
	}

	/**
	 * Get shipping company.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_company( $context = 'view' ) {
		return $this->get_address_prop( 'company', 'shipping', $context );
	}

	/**
	 * Get shipping address line 1.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_address_1( $context = 'view' ) {
		return $this->get_address_prop( 'address_1', 'shipping', $context );
	}

	/**
	 * Get shipping address line 2.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_address_2( $context = 'view' ) {
		return $this->get_address_prop( 'address_2', 'shipping', $context );
	}

	/**
	 * Get shipping city.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_city( $context = 'view' ) {
		return $this->get_address_prop( 'city', 'shipping', $context );
	}

	/**
	 * Get shipping state.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_state( $context = 'view' ) {
		return $this->get_address_prop( 'state', 'shipping', $context );
	}

	/**
	 * Get shipping postcode.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_postcode( $context = 'view' ) {
		return $this->get_address_prop( 'postcode', 'shipping', $context );
	}

	/**
	 * Get shipping country.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_country( $context = 'view' ) {
		return $this->get_address_prop( 'country', 'shipping', $context );
	}

	/**
	 * Get shipping phone.
	 *
	 * @since  5.6.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_shipping_phone( $context = 'view' ) {
		return $this->get_address_prop( 'phone', 'shipping', $context );
	}

	/**
	 * Get the payment method.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_payment_method( $context = 'view' ) {
		return $this->get_prop( 'payment_method', $context );
	}

	/**
	 * Get payment method title.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_payment_method_title( $context = 'view' ) {
		return $this->get_prop( 'payment_method_title', $context );
	}

	/**
	 * Get transaction d.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_transaction_id( $context = 'view' ) {
		return $this->get_prop( 'transaction_id', $context );
	}

	/**
	 * Get customer ip address.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_customer_ip_address( $context = 'view' ) {
		return $this->get_prop( 'customer_ip_address', $context );
	}

	/**
	 * Get customer user agent.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_customer_user_agent( $context = 'view' ) {
		return $this->get_prop( 'customer_user_agent', $context );
	}

	/**
	 * Get created via.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_created_via( $context = 'view' ) {
		return $this->get_prop( 'created_via', $context );
	}

	/**
	 * Get customer note.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_customer_note( $context = 'view' ) {
		return $this->get_prop( 'customer_note', $context );
	}

	/**
	 * Get date completed.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_completed( $context = 'view' ) {
		return $this->get_prop( 'date_completed', $context );
	}

	/**
	 * Get date paid.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_paid( $context = 'view' ) {
		$date_paid = $this->get_prop( 'date_paid', $context );

		if ( 'view' === $context && ! $date_paid && version_compare( $this->get_version( 'edit' ), '3.0', '<' ) && $this->has_status( apply_filters( 'woocommerce_payment_complete_order_status', $this->needs_processing() ? 'processing' : 'completed', $this->get_id(), $this ) ) ) {
			// In view context, return a date if missing.
			$date_paid = $this->get_date_created( 'edit' );
		}
		return $date_paid;
	}

	/**
	 * Get cart hash.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_cart_hash( $context = 'view' ) {
		return $this->get_prop( 'cart_hash', $context );
	}

	/**
	 * Returns the requested address in raw, non-formatted way.
	 * Note: Merges raw data with get_prop data so changes are returned too.
	 *
	 * @since  2.4.0
	 * @param  string $type Billing or shipping. Anything else besides 'billing' will return shipping address.
	 * @return array The stored address after filter.
	 */
	public function get_address( $type = 'billing' ) {
		return apply_filters( 'woocommerce_get_order_address', array_merge( $this->data[ $type ], $this->get_prop( $type, 'view' ) ), $type, $this );
	}

	/**
	 * Get a formatted shipping address for the order.
	 *
	 * @return string
	 */
	public function get_shipping_address_map_url() {
		$address = $this->get_address( 'shipping' );

		// Remove name and company before generate the Google Maps URL.
		unset( $address['first_name'], $address['last_name'], $address['company'], $address['phone'] );

		$address = apply_filters( 'woocommerce_shipping_address_map_url_parts', $address, $this );

		return apply_filters( 'woocommerce_shipping_address_map_url', 'https://maps.google.com/maps?&q=' . rawurlencode( implode( ', ', $address ) ) . '&z=16', $this );
	}

	/**
	 * Get a formatted billing full name.
	 *
	 * @return string
	 */
	public function get_formatted_billing_full_name() {
		/* translators: 1: first name 2: last name */
		return sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), $this->get_billing_first_name(), $this->get_billing_last_name() );
	}

	/**
	 * Get a formatted shipping full name.
	 *
	 * @return string
	 */
	public function get_formatted_shipping_full_name() {
		/* translators: 1: first name 2: last name */
		return sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), $this->get_shipping_first_name(), $this->get_shipping_last_name() );
	}

	/**
	 * Get a formatted billing address for the order.
	 *
	 * @param string $empty_content Content to show if no address is present. @since 3.3.0.
	 * @return string
	 */
	public function get_formatted_billing_address( $empty_content = '' ) {
		$raw_address = apply_filters( 'woocommerce_order_formatted_billing_address', $this->get_address( 'billing' ), $this );
		$address     = WC()->countries->get_formatted_address( $raw_address );

		/**
		 * Filter orders formatted billing address.
		 *
		 * @since 3.8.0
		 * @param string   $address     Formatted billing address string.
		 * @param array    $raw_address Raw billing address.
		 * @param WC_Order $order       Order data. @since 3.9.0
		 */
		return apply_filters( 'woocommerce_order_get_formatted_billing_address', $address ? $address : $empty_content, $raw_address, $this );
	}

	/**
	 * Get a formatted shipping address for the order.
	 *
	 * @param string $empty_content Content to show if no address is present. @since 3.3.0.
	 * @return string
	 */
	public function get_formatted_shipping_address( $empty_content = '' ) {
		$address     = '';
		$raw_address = $this->get_address( 'shipping' );

		if ( $this->has_shipping_address() ) {
			$raw_address = apply_filters( 'woocommerce_order_formatted_shipping_address', $raw_address, $this );
			$address     = WC()->countries->get_formatted_address( $raw_address );
		}

		/**
		 * Filter orders formatted shipping address.
		 *
		 * @since 3.8.0
		 * @param string   $address     Formatted shipping address string.
		 * @param array    $raw_address Raw shipping address.
		 * @param WC_Order $order       Order data. @since 3.9.0
		 */
		return apply_filters( 'woocommerce_order_get_formatted_shipping_address', $address ? $address : $empty_content, $raw_address, $this );
	}

	/**
	 * Returns true if the order has a billing address.
	 *
	 * @since  3.0.4
	 * @return boolean
	 */
	public function has_billing_address() {
		return $this->get_billing_address_1() || $this->get_billing_address_2();
	}

	/**
	 * Returns true if the order has a shipping address.
	 *
	 * @since  3.0.4
	 * @return boolean
	 */
	public function has_shipping_address() {
		return $this->get_shipping_address_1() || $this->get_shipping_address_2();
	}

	/**
	 * Gets information about whether stock was reduced.
	 *
	 * @since 7.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return bool
	 */
	public function get_order_stock_reduced( string $context = 'view' ) {
		return wc_string_to_bool( $this->get_prop( 'order_stock_reduced', $context ) );
	}

	/**
	 * Gets information about whether permissions were generated yet.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return bool True if permissions were generated, false otherwise.
	 */
	public function get_download_permissions_granted( string $context = 'view' ) {
		return wc_string_to_bool( $this->get_prop( 'download_permissions_granted', $context ) );
	}

	/**
	 * Whether email have been sent for this order.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return bool
	 */
	public function get_new_order_email_sent( string $context = 'view' ) {
		return wc_string_to_bool( $this->get_prop( 'new_order_email_sent', $context ) );
	}

	/**
	 * Gets information about whether sales were recorded.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return bool True if sales were recorded, false otherwise.
	 */
	public function get_recorded_sales( string $context = 'view' ) {
		return wc_string_to_bool( $this->get_prop( 'recorded_sales', $context ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting order data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object. However, for backwards compatibility pre 3.0.0 some of these
	| setters may handle both.
	|
	*/

	/**
	 * Sets a prop for a setter method.
	 *
	 * @since 3.0.0
	 * @param string $prop Name of prop to set.
	 * @param string $address Name of address to set. billing or shipping.
	 * @param mixed  $value Value of the prop.
	 */
	protected function set_address_prop( $prop, $address, $value ) {
		if ( array_key_exists( $prop, $this->data[ $address ] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data[ $address ][ $prop ] || ( isset( $this->changes[ $address ] ) && array_key_exists( $prop, $this->changes[ $address ] ) ) ) {
					$this->changes[ $address ][ $prop ] = $value;
				}
			} else {
				$this->data[ $address ][ $prop ] = $value;
			}
		}
	}

	/**
	 * Setter for billing address, expects the $address parameter to be key value pairs for individual address props.
	 *
	 * @param array $address Address to set.
	 *
	 * @return void
	 */
	public function set_billing_address( array $address ) {
		foreach ( $address as $key => $value ) {
			$this->set_address_prop( $key, 'billing', $value );
		}
	}

	/**
	 * Shortcut for calling set_billing_address.
	 *
	 * This is useful in scenarios where set_$prop_name is invoked, and since we store the billing address as 'billing' prop in data, it can be called directly.
	 *
	 * @param array $address Address to set.
	 *
	 * @return void
	 */
	public function set_billing( array $address ) {
		$this->set_billing_address( $address );
	}

	/**
	 * Setter for shipping address, expects the $address parameter to be key value pairs for individual address props.
	 *
	 * @param array $address Address to set.
	 *
	 * @return void
	 */
	public function set_shipping_address( array $address ) {
		foreach ( $address as $key => $value ) {
			$this->set_address_prop( $key, 'shipping', $value );
		}
	}

	/**
	 * Shortcut for calling set_shipping_address. This is useful in scenarios where set_$prop_name is invoked, and since we store the shipping address as 'shipping' prop in data, it can be called directly.
	 *
	 * @param array $address Address to set.
	 *
	 * @return void
	 */
	public function set_shipping( array $address ) {
		$this->set_shipping_address( $address );
	}

	/**
	 * Set order key.
	 *
	 * @param string $value Max length 22 chars.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_order_key( $value ) {
		$this->set_prop( 'order_key', substr( $value, 0, 22 ) );
	}

	/**
	 * Set customer id.
	 *
	 * @param int $value Customer ID.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_customer_id( $value ) {
		$this->set_prop( 'customer_id', absint( $value ) );
	}

	/**
	 * Set billing first name.
	 *
	 * @param string $value Billing first name.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_first_name( $value ) {
		$this->set_address_prop( 'first_name', 'billing', $value );
	}

	/**
	 * Set billing last name.
	 *
	 * @param string $value Billing last name.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_last_name( $value ) {
		$this->set_address_prop( 'last_name', 'billing', $value );
	}

	/**
	 * Set billing company.
	 *
	 * @param string $value Billing company.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_company( $value ) {
		$this->set_address_prop( 'company', 'billing', $value );
	}

	/**
	 * Set billing address line 1.
	 *
	 * @param string $value Billing address line 1.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_address_1( $value ) {
		$this->set_address_prop( 'address_1', 'billing', $value );
	}

	/**
	 * Set billing address line 2.
	 *
	 * @param string $value Billing address line 2.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_address_2( $value ) {
		$this->set_address_prop( 'address_2', 'billing', $value );
	}

	/**
	 * Set billing city.
	 *
	 * @param string $value Billing city.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_city( $value ) {
		$this->set_address_prop( 'city', 'billing', $value );
	}

	/**
	 * Set billing state.
	 *
	 * @param string $value Billing state.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_state( $value ) {
		$this->set_address_prop( 'state', 'billing', $value );
	}

	/**
	 * Set billing postcode.
	 *
	 * @param string $value Billing postcode.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_postcode( $value ) {
		$this->set_address_prop( 'postcode', 'billing', $value );
	}

	/**
	 * Set billing country.
	 *
	 * @param string $value Billing country.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_country( $value ) {
		$this->set_address_prop( 'country', 'billing', $value );
	}

	/**
	 * Maybe set empty billing email to that of the user who owns the order.
	 */
	protected function maybe_set_user_billing_email() {
		$user = $this->get_user();
		if ( ! $this->get_billing_email() && $user ) {
			try {
				$this->set_billing_email( $user->user_email );
			} catch ( WC_Data_Exception $e ) {
				unset( $e );
			}
		}
	}

	/**
	 * Set billing email.
	 *
	 * @param string $value Billing email.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_email( $value ) {
		if ( $value && ! is_email( $value ) ) {
			$this->error( 'order_invalid_billing_email', __( 'Invalid billing email address', 'woocommerce' ) );
		}
		$this->set_address_prop( 'email', 'billing', sanitize_email( $value ) );
	}

	/**
	 * Set billing phone.
	 *
	 * @param string $value Billing phone.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_billing_phone( $value ) {
		$this->set_address_prop( 'phone', 'billing', $value );
	}

	/**
	 * Set shipping first name.
	 *
	 * @param string $value Shipping first name.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_first_name( $value ) {
		$this->set_address_prop( 'first_name', 'shipping', $value );
	}

	/**
	 * Set shipping last name.
	 *
	 * @param string $value Shipping last name.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_last_name( $value ) {
		$this->set_address_prop( 'last_name', 'shipping', $value );
	}

	/**
	 * Set shipping company.
	 *
	 * @param string $value Shipping company.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_company( $value ) {
		$this->set_address_prop( 'company', 'shipping', $value );
	}

	/**
	 * Set shipping address line 1.
	 *
	 * @param string $value Shipping address line 1.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_address_1( $value ) {
		$this->set_address_prop( 'address_1', 'shipping', $value );
	}

	/**
	 * Set shipping address line 2.
	 *
	 * @param string $value Shipping address line 2.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_address_2( $value ) {
		$this->set_address_prop( 'address_2', 'shipping', $value );
	}

	/**
	 * Set shipping city.
	 *
	 * @param string $value Shipping city.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_city( $value ) {
		$this->set_address_prop( 'city', 'shipping', $value );
	}

	/**
	 * Set shipping state.
	 *
	 * @param string $value Shipping state.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_state( $value ) {
		$this->set_address_prop( 'state', 'shipping', $value );
	}

	/**
	 * Set shipping postcode.
	 *
	 * @param string $value Shipping postcode.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_postcode( $value ) {
		$this->set_address_prop( 'postcode', 'shipping', $value );
	}

	/**
	 * Set shipping country.
	 *
	 * @param string $value Shipping country.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_country( $value ) {
		$this->set_address_prop( 'country', 'shipping', $value );
	}

	/**
	 * Set shipping phone.
	 *
	 * @since 5.6.0
	 * @param string $value Shipping phone.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_shipping_phone( $value ) {
		$this->set_address_prop( 'phone', 'shipping', $value );
	}

	/**
	 * Set the payment method.
	 *
	 * @param string $payment_method Supports WC_Payment_Gateway for bw compatibility with < 3.0.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_payment_method( $payment_method = '' ) {
		if ( is_object( $payment_method ) ) {
			$this->set_payment_method( $payment_method->id );
			$this->set_payment_method_title( $payment_method->get_title() );
		} elseif ( '' === $payment_method ) {
			$this->set_prop( 'payment_method', '' );
			$this->set_prop( 'payment_method_title', '' );
		} else {
			$this->set_prop( 'payment_method', $payment_method );
		}
	}

	/**
	 * Set payment method title.
	 *
	 * @param string $value Payment method title.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_payment_method_title( $value ) {
		$this->set_prop( 'payment_method_title', $value );
	}

	/**
	 * Set transaction id.
	 *
	 * @param string $value Transaction id.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_transaction_id( $value ) {
		$this->set_prop( 'transaction_id', $value );
	}

	/**
	 * Set customer ip address.
	 *
	 * @param string $value Customer ip address.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_customer_ip_address( $value ) {
		$this->set_prop( 'customer_ip_address', $value );
	}

	/**
	 * Set customer user agent.
	 *
	 * @param string $value Customer user agent.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_customer_user_agent( $value ) {
		$this->set_prop( 'customer_user_agent', $value );
	}

	/**
	 * Set created via.
	 *
	 * @param string $value Created via.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_created_via( $value ) {
		$this->set_prop( 'created_via', $value );
	}

	/**
	 * Set customer note.
	 *
	 * @param string $value Customer note.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_customer_note( $value ) {
		$this->set_prop( 'customer_note', $value );
	}

	/**
	 * Set date completed.
	 *
	 * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_date_completed( $date = null ) {
		$this->set_date_prop( 'date_completed', $date );
	}

	/**
	 * Set date paid.
	 *
	 * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_date_paid( $date = null ) {
		$this->set_date_prop( 'date_paid', $date );
	}

	/**
	 * Set cart hash.
	 *
	 * @param string $value Cart hash.
	 * @throws WC_Data_Exception Throws exception when invalid data is found.
	 */
	public function set_cart_hash( $value ) {
		$this->set_prop( 'cart_hash', $value );
	}

	/**
	 * Stores information about whether stock was reduced.
	 *
	 * @param bool|string $value True if stock was reduced, false if not.
	 *
	 * @return void
	 */
	public function set_order_stock_reduced( $value ) {
		$this->set_prop( 'order_stock_reduced', wc_string_to_bool( $value ) );
	}

	/**
	 * Stores information about whether permissions were generated yet.
	 *
	 * @param bool|string $value True if permissions were generated, false if not.
	 *
	 * @return void
	 */
	public function set_download_permissions_granted( $value ) {
		$this->set_prop( 'download_permissions_granted', wc_string_to_bool( $value ) );
	}

	/**
	 * Stores information about whether email was sent.
	 *
	 * @param bool|string $value True if email was sent, false if not.
	 *
	 * @return void
	 */
	public function set_new_order_email_sent( $value ) {
		$this->set_prop( 'new_order_email_sent', wc_string_to_bool( $value ) );
	}

	/**
	 * Stores information about whether sales were recorded.
	 *
	 * @param bool|string $value True if sales were recorded, false if not.
	 *
	 * @return void
	 */
	public function set_recorded_sales( $value ) {
		$this->set_prop( 'recorded_sales', wc_string_to_bool( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Checks if a condition is true or false.
	|
	*/

	/**
	 * Check if an order key is valid.
	 *
	 * @param string $key Order key.
	 * @return bool
	 */
	public function key_is_valid( $key ) {
		return hash_equals( $this->get_order_key(), $key );
	}

	/**
	 * See if order matches cart_hash.
	 *
	 * @param string $cart_hash Cart hash.
	 * @return bool
	 */
	public function has_cart_hash( $cart_hash = '' ) {
		return hash_equals( $this->get_cart_hash(), $cart_hash ); // @codingStandardsIgnoreLine
	}

	/**
	 * Checks if an order can be edited, specifically for use on the Edit Order screen.
	 *
	 * @return bool
	 */
	public function is_editable() {
		return apply_filters( 'wc_order_is_editable', in_array( $this->get_status(), array( 'pending', 'on-hold', 'auto-draft' ), true ), $this );
	}

	/**
	 * Returns if an order has been paid for based on the order status.
	 *
	 * @since 2.5.0
	 * @return bool
	 */
	public function is_paid() {
		return apply_filters( 'woocommerce_order_is_paid', $this->has_status( wc_get_is_paid_statuses() ), $this );
	}

	/**
	 * Checks if product download is permitted.
	 *
	 * @return bool
	 */
	public function is_download_permitted() {
		return apply_filters( 'woocommerce_order_is_download_permitted', $this->has_status( 'completed' ) || ( 'yes' === get_option( 'woocommerce_downloads_grant_access_after_payment' ) && $this->has_status( 'processing' ) ), $this );
	}

	/**
	 * Checks if an order needs display the shipping address, based on shipping method.
	 *
	 * @return bool
	 */
	public function needs_shipping_address() {
		if ( 'no' === get_option( 'woocommerce_calc_shipping' ) ) {
			return false;
		}

		$hide          = apply_filters( 'woocommerce_order_hide_shipping_address', array( 'local_pickup' ), $this );
		$needs_address = false;

		foreach ( $this->get_shipping_methods() as $shipping_method ) {
			$shipping_method_id = $shipping_method->get_method_id();

			if ( ! in_array( $shipping_method_id, $hide, true ) ) {
				$needs_address = true;
				break;
			}
		}

		return apply_filters( 'woocommerce_order_needs_shipping_address', $needs_address, $hide, $this );
	}

	/**
	 * Returns true if the order contains a downloadable product.
	 *
	 * @return bool
	 */
	public function has_downloadable_item() {
		foreach ( $this->get_items() as $item ) {
			if ( $item->is_type( 'line_item' ) ) {
				$product = $item->get_product();

				if ( $product && $product->has_file() ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Get downloads from all line items for this order.
	 *
	 * @since  3.2.0
	 * @return array
	 */
	public function get_downloadable_items() {
		$downloads = array();

		foreach ( $this->get_items() as $item ) {
			if ( ! is_object( $item ) ) {
				continue;
			}

			// Check item refunds.
			$refunded_qty = abs( $this->get_qty_refunded_for_item( $item->get_id() ) );
			if ( $refunded_qty && $item->get_quantity() === $refunded_qty ) {
				continue;
			}

			if ( $item->is_type( 'line_item' ) ) {
				$item_downloads = $item->get_item_downloads();
				$product        = $item->get_product();
				if ( $product && $item_downloads ) {
					foreach ( $item_downloads as $file ) {
						$downloads[] = array(
							'download_url'        => $file['download_url'],
							'download_id'         => $file['id'],
							'product_id'          => $product->get_id(),
							'product_name'        => $product->get_name(),
							'product_url'         => $product->is_visible() ? $product->get_permalink() : '', // Since 3.3.0.
							'download_name'       => $file['name'],
							'order_id'            => $this->get_id(),
							'order_key'           => $this->get_order_key(),
							'downloads_remaining' => $file['downloads_remaining'],
							'access_expires'      => $file['access_expires'],
							'file'                => array(
								'name' => $file['name'],
								'file' => $file['file'],
							),
						);
					}
				}
			}
		}

		return apply_filters( 'woocommerce_order_get_downloadable_items', $downloads, $this );
	}

	/**
	 * Checks if an order needs payment, based on status and order total.
	 *
	 * @return bool
	 */
	public function needs_payment() {
		$valid_order_statuses = apply_filters( 'woocommerce_valid_order_statuses_for_payment', array( 'pending', 'failed' ), $this );
		return apply_filters( 'woocommerce_order_needs_payment', ( $this->has_status( $valid_order_statuses ) && $this->get_total() > 0 ), $this, $valid_order_statuses );
	}

	/**
	 * See if the order needs processing before it can be completed.
	 *
	 * Orders which only contain virtual, downloadable items do not need admin
	 * intervention.
	 *
	 * Uses a transient so these calls are not repeated multiple times, and because
	 * once the order is processed this code/transient does not need to persist.
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	public function needs_processing() {
		$transient_name   = 'wc_order_' . $this->get_id() . '_needs_processing';
		$needs_processing = get_transient( $transient_name );

		if ( false === $needs_processing ) {
			$needs_processing = 0;

			if ( count( $this->get_items() ) > 0 ) {
				foreach ( $this->get_items() as $item ) {
					if ( $item->is_type( 'line_item' ) ) {
						$product = $item->get_product();

						if ( ! $product ) {
							continue;
						}

						$virtual_downloadable_item = $product->is_downloadable() && $product->is_virtual();

						if ( apply_filters( 'woocommerce_order_item_needs_processing', ! $virtual_downloadable_item, $product, $this->get_id() ) ) {
							$needs_processing = 1;
							break;
						}
					}
				}
			}

			set_transient( $transient_name, $needs_processing, DAY_IN_SECONDS );
		}

		return 1 === absint( $needs_processing );
	}

	/*
	|--------------------------------------------------------------------------
	| URLs and Endpoints
	|--------------------------------------------------------------------------
	*/

	/**
	 * Generates a URL so that a customer can pay for their (unpaid - pending) order. Pass 'true' for the checkout version which doesn't offer gateway choices.
	 *
	 * @param  bool $on_checkout If on checkout.
	 * @return string
	 */
	public function get_checkout_payment_url( $on_checkout = false ) {
		$pay_url = wc_get_endpoint_url( 'order-pay', $this->get_id(), wc_get_checkout_url() );

		if ( $on_checkout ) {
			$pay_url = add_query_arg( 'key', $this->get_order_key(), $pay_url );
		} else {
			$pay_url = add_query_arg(
				array(
					'pay_for_order' => 'true',
					'key'           => $this->get_order_key(),
				),
				$pay_url
			);
		}

		return apply_filters( 'woocommerce_get_checkout_payment_url', $pay_url, $this );
	}

	/**
	 * Generates a URL for the thanks page (order received).
	 *
	 * @return string
	 */
	public function get_checkout_order_received_url() {
		$order_received_url = wc_get_endpoint_url( 'order-received', $this->get_id(), wc_get_checkout_url() );
		$order_received_url = add_query_arg( 'key', $this->get_order_key(), $order_received_url );

		return apply_filters( 'woocommerce_get_checkout_order_received_url', $order_received_url, $this );
	}

	/**
	 * Generates a URL so that a customer can cancel their (unpaid - pending) order.
	 *
	 * @param string $redirect Redirect URL.
	 * @return string
	 */
	public function get_cancel_order_url( $redirect = '' ) {
		return apply_filters(
			'woocommerce_get_cancel_order_url',
			wp_nonce_url(
				add_query_arg(
					array(
						'cancel_order' => 'true',
						'order'        => $this->get_order_key(),
						'order_id'     => $this->get_id(),
						'redirect'     => $redirect,
					),
					$this->get_cancel_endpoint()
				),
				'woocommerce-cancel_order'
			)
		);
	}

	/**
	 * Generates a raw (unescaped) cancel-order URL for use by payment gateways.
	 *
	 * @param string $redirect Redirect URL.
	 * @return string The unescaped cancel-order URL.
	 */
	public function get_cancel_order_url_raw( $redirect = '' ) {
		return apply_filters(
			'woocommerce_get_cancel_order_url_raw',
			add_query_arg(
				array(
					'cancel_order' => 'true',
					'order'        => $this->get_order_key(),
					'order_id'     => $this->get_id(),
					'redirect'     => $redirect,
					'_wpnonce'     => wp_create_nonce( 'woocommerce-cancel_order' ),
				),
				$this->get_cancel_endpoint()
			)
		);
	}

	/**
	 * Helper method to return the cancel endpoint.
	 *
	 * @return string the cancel endpoint; either the cart page or the home page.
	 */
	public function get_cancel_endpoint() {
		$cancel_endpoint = wc_get_cart_url();
		if ( ! $cancel_endpoint ) {
			$cancel_endpoint = home_url();
		}

		if ( false === strpos( $cancel_endpoint, '?' ) ) {
			$cancel_endpoint = trailingslashit( $cancel_endpoint );
		}

		return $cancel_endpoint;
	}

	/**
	 * Generates a URL to view an order from the my account page.
	 *
	 * @return string
	 */
	public function get_view_order_url() {
		return apply_filters( 'woocommerce_get_view_order_url', wc_get_endpoint_url( 'view-order', $this->get_id(), wc_get_page_permalink( 'myaccount' ) ), $this );
	}

	/**
	 * Get's the URL to edit the order in the backend.
	 *
	 * @since 3.3.0
	 * @return string
	 */
	public function get_edit_order_url() {
		$edit_url = \Automattic\WooCommerce\Utilities\OrderUtil::get_order_admin_edit_url( $this->get_id() );
		/**
		 * Filter the URL to edit the order in the backend.
		 *
		 * @since 3.3.0
		 */
		return apply_filters( 'woocommerce_get_edit_order_url', $edit_url, $this );
	}

	/*
	|--------------------------------------------------------------------------
	| Order notes.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Adds a note (comment) to the order. Order must exist.
	 *
	 * @param  string $note              Note to add.
	 * @param  int    $is_customer_note  Is this a note for the customer?.
	 * @param  bool   $added_by_user     Was the note added by a user?.
	 * @return int                       Comment ID.
	 */
	public function add_order_note( $note, $is_customer_note = 0, $added_by_user = false ) {
		if ( ! $this->get_id() ) {
			return 0;
		}

		if ( is_user_logged_in() && current_user_can( 'edit_shop_orders', $this->get_id() ) && $added_by_user ) {
			$user                 = get_user_by( 'id', get_current_user_id() );
			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
		} else {
			$comment_author        = __( 'WooCommerce', 'woocommerce' );
			$comment_author_email  = strtolower( __( 'WooCommerce', 'woocommerce' ) ) . '@';
			$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : 'noreply.com'; // WPCS: input var ok.
			$comment_author_email  = sanitize_email( $comment_author_email );
		}
		$commentdata = apply_filters(
			'woocommerce_new_order_note_data',
			array(
				'comment_post_ID'      => $this->get_id(),
				'comment_author'       => $comment_author,
				'comment_author_email' => $comment_author_email,
				'comment_author_url'   => '',
				'comment_content'      => $note,
				'comment_agent'        => 'WooCommerce',
				'comment_type'         => 'order_note',
				'comment_parent'       => 0,
				'comment_approved'     => 1,
			),
			array(
				'order_id'         => $this->get_id(),
				'is_customer_note' => $is_customer_note,
			)
		);

		$comment_id = wp_insert_comment( $commentdata );

		if ( $is_customer_note ) {
			add_comment_meta( $comment_id, 'is_customer_note', 1 );

			do_action(
				'woocommerce_new_customer_note',
				array(
					'order_id'      => $this->get_id(),
					'customer_note' => $commentdata['comment_content'],
				)
			);
		}

		/**
		 * Action hook fired after an order note is added.
		 *
		 * @param int      $order_note_id Order note ID.
		 * @param WC_Order $order         Order data.
		 *
		 * @since 4.4.0
		 */
		do_action( 'woocommerce_order_note_added', $comment_id, $this );

		return $comment_id;
	}

	/**
	 * Add an order note for status transition
	 *
	 * @since 3.9.0
	 * @uses WC_Order::add_order_note()
	 * @param string $note          Note to be added giving status transition from and to details.
	 * @param bool   $transition    Details of the status transition.
	 * @return int                  Comment ID.
	 */
	private function add_status_transition_note( $note, $transition ) {
		return $this->add_order_note( trim( $transition['note'] . ' ' . $note ), 0, $transition['manual'] );
	}

	/**
	 * List order notes (public) for the customer.
	 *
	 * @return array
	 */
	public function get_customer_order_notes() {
		$notes = array();
		$args  = array(
			'post_id' => $this->get_id(),
			'approve' => 'approve',
			'type'    => '',
		);

		remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );

		$comments = get_comments( $args );

		foreach ( $comments as $comment ) {
			if ( ! get_comment_meta( $comment->comment_ID, 'is_customer_note', true ) ) {
				continue;
			}
			$comment->comment_content = make_clickable( $comment->comment_content );
			$notes[]                  = $comment;
		}

		add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );

		return $notes;
	}

	/*
	|--------------------------------------------------------------------------
	| Refunds
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get order refunds.
	 *
	 * @since 2.2
	 * @return array of WC_Order_Refund objects
	 */
	public function get_refunds() {
		$cache_key   = WC_Cache_Helper::get_cache_prefix( 'orders' ) . 'refunds' . $this->get_id();
		$cached_data = wp_cache_get( $cache_key, $this->cache_group );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		$this->refunds = wc_get_orders(
			array(
				'type'   => 'shop_order_refund',
				'parent' => $this->get_id(),
				'limit'  => -1,
			)
		);

		wp_cache_set( $cache_key, $this->refunds, $this->cache_group );

		return $this->refunds;
	}

	/**
	 * Get amount already refunded.
	 *
	 * @since 2.2
	 * @return string
	 */
	public function get_total_refunded() {
		$cache_key   = WC_Cache_Helper::get_cache_prefix( 'orders' ) . 'total_refunded' . $this->get_id();
		$cached_data = wp_cache_get( $cache_key, $this->cache_group );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		$total_refunded = $this->data_store->get_total_refunded( $this );

		wp_cache_set( $cache_key, $total_refunded, $this->cache_group );

		return $total_refunded;
	}

	/**
	 * Get the total tax refunded.
	 *
	 * @since  2.3
	 * @return float
	 */
	public function get_total_tax_refunded() {
		$cache_key   = WC_Cache_Helper::get_cache_prefix( 'orders' ) . 'total_tax_refunded' . $this->get_id();
		$cached_data = wp_cache_get( $cache_key, $this->cache_group );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		$total_refunded = $this->data_store->get_total_tax_refunded( $this );

		wp_cache_set( $cache_key, $total_refunded, $this->cache_group );

		return $total_refunded;
	}

	/**
	 * Get the total shipping refunded.
	 *
	 * @since  2.4
	 * @return float
	 */
	public function get_total_shipping_refunded() {
		$cache_key   = WC_Cache_Helper::get_cache_prefix( 'orders' ) . 'total_shipping_refunded' . $this->get_id();
		$cached_data = wp_cache_get( $cache_key, $this->cache_group );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		$total_refunded = $this->data_store->get_total_shipping_refunded( $this );

		wp_cache_set( $cache_key, $total_refunded, $this->cache_group );

		return $total_refunded;
	}

	/**
	 * Gets the count of order items of a certain type that have been refunded.
	 *
	 * @since  2.4.0
	 * @param string $item_type Item type.
	 * @return string
	 */
	public function get_item_count_refunded( $item_type = '' ) {
		if ( empty( $item_type ) ) {
			$item_type = array( 'line_item' );
		}
		if ( ! is_array( $item_type ) ) {
			$item_type = array( $item_type );
		}
		$count = 0;

		foreach ( $this->get_refunds() as $refund ) {
			foreach ( $refund->get_items( $item_type ) as $refunded_item ) {
				$count += abs( $refunded_item->get_quantity() );
			}
		}

		return apply_filters( 'woocommerce_get_item_count_refunded', $count, $item_type, $this );
	}

	/**
	 * Get the total number of items refunded.
	 *
	 * @since  2.4.0
	 *
	 * @param  string $item_type Type of the item we're checking, if not a line_item.
	 * @return int
	 */
	public function get_total_qty_refunded( $item_type = 'line_item' ) {
		$qty = 0;
		foreach ( $this->get_refunds() as $refund ) {
			foreach ( $refund->get_items( $item_type ) as $refunded_item ) {
				$qty += $refunded_item->get_quantity();
			}
		}
		return $qty;
	}

	/**
	 * Get the refunded amount for a line item.
	 *
	 * @param  int    $item_id   ID of the item we're checking.
	 * @param  string $item_type Type of the item we're checking, if not a line_item.
	 * @return int
	 */
	public function get_qty_refunded_for_item( $item_id, $item_type = 'line_item' ) {
		$qty = 0;
		foreach ( $this->get_refunds() as $refund ) {
			foreach ( $refund->get_items( $item_type ) as $refunded_item ) {
				if ( absint( $refunded_item->get_meta( '_refunded_item_id' ) ) === $item_id ) {
					$qty += $refunded_item->get_quantity();
				}
			}
		}
		return $qty;
	}

	/**
	 * Get the refunded amount for a line item.
	 *
	 * @param  int    $item_id   ID of the item we're checking.
	 * @param  string $item_type Type of the item we're checking, if not a line_item.
	 * @return int
	 */
	public function get_total_refunded_for_item( $item_id, $item_type = 'line_item' ) {
		$total = 0;
		foreach ( $this->get_refunds() as $refund ) {
			foreach ( $refund->get_items( $item_type ) as $refunded_item ) {
				if ( absint( $refunded_item->get_meta( '_refunded_item_id' ) ) === $item_id ) {
					$total += $refunded_item->get_total();
				}
			}
		}
		return $total * -1;
	}

	/**
	 * Get the refunded tax amount for a line item.
	 *
	 * @param  int    $item_id   ID of the item we're checking.
	 * @param  int    $tax_id    ID of the tax we're checking.
	 * @param  string $item_type Type of the item we're checking, if not a line_item.
	 * @return double
	 */
	public function get_tax_refunded_for_item( $item_id, $tax_id, $item_type = 'line_item' ) {
		$total = 0;
		foreach ( $this->get_refunds() as $refund ) {
			foreach ( $refund->get_items( $item_type ) as $refunded_item ) {
				$refunded_item_id = (int) $refunded_item->get_meta( '_refunded_item_id' );
				if ( $refunded_item_id === $item_id ) {
					$taxes  = $refunded_item->get_taxes();
					$total += isset( $taxes['total'][ $tax_id ] ) ? (float) $taxes['total'][ $tax_id ] : 0;
					break;
				}
			}
		}
		return wc_round_tax_total( $total ) * -1;
	}

	/**
	 * Get total tax refunded by rate ID.
	 *
	 * @param  int $rate_id Rate ID.
	 * @return float
	 */
	public function get_total_tax_refunded_by_rate_id( $rate_id ) {
		$total = 0;
		foreach ( $this->get_refunds() as $refund ) {
			foreach ( $refund->get_items( 'tax' ) as $refunded_item ) {
				if ( absint( $refunded_item->get_rate_id() ) === $rate_id ) {
					$total += abs( $refunded_item->get_tax_total() ) + abs( $refunded_item->get_shipping_tax_total() );
				}
			}
		}

		return $total;
	}

	/**
	 * How much money is left to refund?
	 *
	 * @return string
	 */
	public function get_remaining_refund_amount() {
		return wc_format_decimal( $this->get_total() - $this->get_total_refunded(), wc_get_price_decimals() );
	}

	/**
	 * How many items are left to refund?
	 *
	 * @return int
	 */
	public function get_remaining_refund_items() {
		return absint( $this->get_item_count() - $this->get_item_count_refunded() );
	}

	/**
	 * Add total row for the payment method.
	 *
	 * @param array  $total_rows  Total rows.
	 * @param string $tax_display Tax to display.
	 */
	protected function add_order_item_totals_payment_method_row( &$total_rows, $tax_display ) {
		if ( $this->get_total() > 0 && $this->get_payment_method_title() && 'other' !== $this->get_payment_method() ) {
			$total_rows['payment_method'] = array(
				'label' => __( 'Payment method:', 'woocommerce' ),
				'value' => $this->get_payment_method_title(),
			);
		}
	}

	/**
	 * Add total row for refunds.
	 *
	 * @param array  $total_rows  Total rows.
	 * @param string $tax_display Tax to display.
	 */
	protected function add_order_item_totals_refund_rows( &$total_rows, $tax_display ) {
		$refunds = $this->get_refunds();
		if ( $refunds ) {
			foreach ( $refunds as $id => $refund ) {
				$total_rows[ 'refund_' . $id ] = array(
					'label' => $refund->get_reason() ? $refund->get_reason() : __( 'Refund', 'woocommerce' ) . ':',
					'value' => wc_price( '-' . $refund->get_amount(), array( 'currency' => $this->get_currency() ) ),
				);
			}
		}
	}

	/**
	 * Get totals for display on pages and in emails.
	 *
	 * @param string $tax_display Tax to display.
	 * @return array
	 */
	public function get_order_item_totals( $tax_display = '' ) {
		$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );
		$total_rows  = array();

		$this->add_order_item_totals_subtotal_row( $total_rows, $tax_display );
		$this->add_order_item_totals_discount_row( $total_rows, $tax_display );
		$this->add_order_item_totals_shipping_row( $total_rows, $tax_display );
		$this->add_order_item_totals_fee_rows( $total_rows, $tax_display );
		$this->add_order_item_totals_tax_rows( $total_rows, $tax_display );
		$this->add_order_item_totals_payment_method_row( $total_rows, $tax_display );
		$this->add_order_item_totals_refund_rows( $total_rows, $tax_display );
		$this->add_order_item_totals_total_row( $total_rows, $tax_display );

		return apply_filters( 'woocommerce_get_order_item_totals', $total_rows, $this, $tax_display );
	}

	/**
	 * Check if order has been created via admin, checkout, or in another way.
	 *
	 * @since 4.0.0
	 * @param string $modus Way of creating the order to test for.
	 * @return bool
	 */
	public function is_created_via( $modus ) {
		return apply_filters( 'woocommerce_order_is_created_via', $modus === $this->get_created_via(), $this, $modus );
	}
}
