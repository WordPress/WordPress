<?php
/**
 * The WooCommerce customer class handles storage of the current customer's data, such as location.
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

require_once dirname( __FILE__ ) . '/legacy/class-wc-legacy-customer.php';

/**
 * Customer class.
 */
class WC_Customer extends WC_Legacy_Customer {

	/**
	 * Stores customer data.
	 *
	 * @var array
	 */
	protected $data = array(
		'date_created'       => null,
		'date_modified'      => null,
		'email'              => '',
		'first_name'         => '',
		'last_name'          => '',
		'display_name'       => '',
		'role'               => 'customer',
		'username'           => '',
		'billing'            => array(
			'first_name' => '',
			'last_name'  => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'city'       => '',
			'postcode'   => '',
			'country'    => '',
			'state'      => '',
			'email'      => '',
			'phone'      => '',
		),
		'shipping'           => array(
			'first_name' => '',
			'last_name'  => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'city'       => '',
			'postcode'   => '',
			'country'    => '',
			'state'      => '',
			'phone'      => '',
		),
		'is_paying_customer' => false,
	);

	/**
	 * Stores a password if this needs to be changed. Write-only and hidden from _data.
	 *
	 * @var string
	 */
	protected $password = '';

	/**
	 * Stores if user is VAT exempt for this session.
	 *
	 * @var string
	 */
	protected $is_vat_exempt = false;

	/**
	 * Stores if user has calculated shipping in this session.
	 *
	 * @var string
	 */
	protected $calculated_shipping = false;

	/**
	 * This is the name of this object type.
	 *
	 * @since 5.6.0
	 * @var string
	 */
	protected $object_type = 'customer';

	/**
	 * Load customer data based on how WC_Customer is called.
	 *
	 * If $customer is 'new', you can build a new WC_Customer object. If it's empty, some
	 * data will be pulled from the session for the current user/customer.
	 *
	 * @param WC_Customer|int $data       Customer ID or data.
	 * @param bool            $is_session True if this is the customer session.
	 * @throws Exception If customer cannot be read/found and $data is set.
	 */
	public function __construct( $data = 0, $is_session = false ) {
		parent::__construct( $data );

		if ( $data instanceof WC_Customer ) {
			$this->set_id( absint( $data->get_id() ) );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		}

		$this->data_store = WC_Data_Store::load( 'customer' );

		// If we have an ID, load the user from the DB.
		if ( $this->get_id() ) {
			try {
				$this->data_store->read( $this );
			} catch ( Exception $e ) {
				$this->set_id( 0 );
				$this->set_object_read( true );
			}
		} else {
			$this->set_object_read( true );
		}

		// If this is a session, set or change the data store to sessions. Changes do not persist in the database.
		if ( $is_session && isset( WC()->session ) ) {
			$this->data_store = WC_Data_Store::load( 'customer-session' );
			$this->data_store->read( $this );
		}
	}

	/**
	 * Delete a customer and reassign posts..
	 *
	 * @param int $reassign Reassign posts and links to new User ID.
	 * @since 3.0.0
	 * @return bool
	 */
	public function delete_and_reassign( $reassign = null ) {
		if ( $this->data_store ) {
			$this->data_store->delete(
				$this,
				array(
					'force_delete' => true,
					'reassign'     => $reassign,
				)
			);
			$this->set_id( 0 );
			return true;
		}
		return false;
	}

	/**
	 * Is customer outside base country (for tax purposes)?
	 *
	 * @return bool
	 */
	public function is_customer_outside_base() {
		list( $country, $state ) = $this->get_taxable_address();
		if ( $country ) {
			$default = wc_get_base_location();
			if ( $default['country'] !== $country ) {
				return true;
			}
			if ( $default['state'] && $default['state'] !== $state ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Return this customer's avatar.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_avatar_url() {
		return get_avatar_url( $this->get_email() );
	}

	/**
	 * Get taxable address.
	 *
	 * @return array
	 */
	public function get_taxable_address() {
		$tax_based_on = get_option( 'woocommerce_tax_based_on' );

		// Check shipping method at this point to see if we need special handling.
		if ( true === apply_filters( 'woocommerce_apply_base_tax_for_local_pickup', true ) && count( array_intersect( wc_get_chosen_shipping_method_ids(), apply_filters( 'woocommerce_local_pickup_methods', array( 'legacy_local_pickup', 'local_pickup' ) ) ) ) > 0 ) {
			$tax_based_on = 'base';
		}

		if ( 'base' === $tax_based_on ) {
			$country  = WC()->countries->get_base_country();
			$state    = WC()->countries->get_base_state();
			$postcode = WC()->countries->get_base_postcode();
			$city     = WC()->countries->get_base_city();
		} elseif ( 'billing' === $tax_based_on ) {
			$country  = $this->get_billing_country();
			$state    = $this->get_billing_state();
			$postcode = $this->get_billing_postcode();
			$city     = $this->get_billing_city();
		} else {
			$country  = $this->get_shipping_country();
			$state    = $this->get_shipping_state();
			$postcode = $this->get_shipping_postcode();
			$city     = $this->get_shipping_city();
		}

		/**
		 * Filters the taxable address for a given customer.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $taxable_address An array of country, state, postcode, and city for the customer's taxable address.
		 * @param object $customer        The customer object for which the taxable address is being requested.
		 *
		 * @return array The filtered taxable address for the customer.
		 */
		return apply_filters( 'woocommerce_customer_taxable_address', array( $country, $state, $postcode, $city ), $this );
	}

	/**
	 * Gets a customer's downloadable products.
	 *
	 * @return array Array of downloadable products
	 */
	public function get_downloadable_products() {
		$downloads = array();
		if ( $this->get_id() ) {
			$downloads = wc_get_customer_available_downloads( $this->get_id() );
		}
		return apply_filters( 'woocommerce_customer_get_downloadable_products', $downloads );
	}

	/**
	 * Is customer VAT exempt?
	 *
	 * @return bool
	 */
	public function is_vat_exempt() {
		return $this->get_is_vat_exempt();
	}

	/**
	 * Has calculated shipping?
	 *
	 * @return bool
	 */
	public function has_calculated_shipping() {
		return $this->get_calculated_shipping();
	}

	/**
	 * Indicates if the customer has a non-empty shipping address.
	 *
	 * Note that this does not indicate if the customer's shipping address
	 * is complete, only that one or more fields are populated.
	 *
	 * @since 5.3.0
	 *
	 * @return bool
	 */
	public function has_shipping_address() {
		foreach ( $this->get_shipping() as $address_field ) {
			// Trim guards against a case where a subset of saved shipping address fields contain whitespace.
			if ( strlen( trim( $address_field ) ) > 0 ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get if customer is VAT exempt?
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	public function get_is_vat_exempt() {
		return $this->is_vat_exempt;
	}

	/**
	 * Get password (only used when updating the user object).
	 *
	 * @return string
	 */
	public function get_password() {
		return $this->password;
	}

	/**
	 * Has customer calculated shipping?
	 *
	 * @return bool
	 */
	public function get_calculated_shipping() {
		return $this->calculated_shipping;
	}

	/**
	 * Set if customer has tax exemption.
	 *
	 * @param bool $is_vat_exempt If is vat exempt.
	 */
	public function set_is_vat_exempt( $is_vat_exempt ) {
		$this->is_vat_exempt = wc_string_to_bool( $is_vat_exempt );
	}

	/**
	 * Calculated shipping?
	 *
	 * @param bool $calculated If shipping is calculated.
	 */
	public function set_calculated_shipping( $calculated = true ) {
		$this->calculated_shipping = wc_string_to_bool( $calculated );
	}

	/**
	 * Set customer's password.
	 *
	 * @since 3.0.0
	 * @param string $password Password.
	 */
	public function set_password( $password ) {
		$this->password = $password;
	}

	/**
	 * Gets the customers last order.
	 *
	 * @return WC_Order|false
	 */
	public function get_last_order() {
		return $this->data_store->get_last_order( $this );
	}

	/**
	 * Return the number of orders this customer has.
	 *
	 * @return integer
	 */
	public function get_order_count() {
		return $this->data_store->get_order_count( $this );
	}

	/**
	 * Return how much money this customer has spent.
	 *
	 * @return float
	 */
	public function get_total_spent() {
		return $this->data_store->get_total_spent( $this );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the customer's username.
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_username( $context = 'view' ) {
		return $this->get_prop( 'username', $context );
	}

	/**
	 * Return the customer's email.
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_email( $context = 'view' ) {
		return $this->get_prop( 'email', $context );
	}

	/**
	 * Return customer's first name.
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_first_name( $context = 'view' ) {
		return $this->get_prop( 'first_name', $context );
	}

	/**
	 * Return customer's last name.
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_last_name( $context = 'view' ) {
		return $this->get_prop( 'last_name', $context );
	}

	/**
	 * Return customer's display name.
	 *
	 * @since  3.1.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_display_name( $context = 'view' ) {
		return $this->get_prop( 'display_name', $context );
	}

	/**
	 * Return customer's user role.
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_role( $context = 'view' ) {
		return $this->get_prop( 'role', $context );
	}

	/**
	 * Return the date this customer was created.
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|null object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Return the date this customer was last updated.
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|null object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ) {
		return $this->get_prop( 'date_modified', $context );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @since  3.0.0
	 * @param  string $prop Name of prop to get.
	 * @param  string $address billing or shipping.
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'. What the value is for. Valid values are view and edit.
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
	 * Get billing.
	 *
	 * @since  3.2.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return array
	 */
	public function get_billing( $context = 'view' ) {
		$value = null;
		$prop  = 'billing';

		if ( array_key_exists( $prop, $this->data ) ) {
			$changes = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : array();
			$value   = array_merge( $this->data[ $prop ], $changes );

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Get billing_first_name.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_first_name( $context = 'view' ) {
		return $this->get_address_prop( 'first_name', 'billing', $context );
	}

	/**
	 * Get billing_last_name.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_last_name( $context = 'view' ) {
		return $this->get_address_prop( 'last_name', 'billing', $context );
	}

	/**
	 * Get billing_company.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_company( $context = 'view' ) {
		return $this->get_address_prop( 'company', 'billing', $context );
	}

	/**
	 * Get billing_address_1.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_address( $context = 'view' ) {
		return $this->get_billing_address_1( $context );
	}

	/**
	 * Get billing_address_1.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_address_1( $context = 'view' ) {
		return $this->get_address_prop( 'address_1', 'billing', $context );
	}

	/**
	 * Get billing_address_2.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string $value
	 */
	public function get_billing_address_2( $context = 'view' ) {
		return $this->get_address_prop( 'address_2', 'billing', $context );
	}

	/**
	 * Get billing_city.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string $value
	 */
	public function get_billing_city( $context = 'view' ) {
		return $this->get_address_prop( 'city', 'billing', $context );
	}

	/**
	 * Get billing_state.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_state( $context = 'view' ) {
		return $this->get_address_prop( 'state', 'billing', $context );
	}

	/**
	 * Get billing_postcode.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_postcode( $context = 'view' ) {
		return $this->get_address_prop( 'postcode', 'billing', $context );
	}

	/**
	 * Get billing_country.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_country( $context = 'view' ) {
		return $this->get_address_prop( 'country', 'billing', $context );
	}

	/**
	 * Get billing_email.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_email( $context = 'view' ) {
		return $this->get_address_prop( 'email', 'billing', $context );
	}

	/**
	 * Get billing_phone.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_billing_phone( $context = 'view' ) {
		return $this->get_address_prop( 'phone', 'billing', $context );
	}

	/**
	 * Get shipping.
	 *
	 * @since  3.2.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return array
	 */
	public function get_shipping( $context = 'view' ) {
		$value = null;
		$prop  = 'shipping';

		if ( array_key_exists( $prop, $this->data ) ) {
			$changes = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : array();
			$value   = array_merge( $this->data[ $prop ], $changes );

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Get shipping_first_name.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_first_name( $context = 'view' ) {
		return $this->get_address_prop( 'first_name', 'shipping', $context );
	}

	/**
	 * Get shipping_last_name.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_last_name( $context = 'view' ) {
		return $this->get_address_prop( 'last_name', 'shipping', $context );
	}

	/**
	 * Get shipping_company.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_company( $context = 'view' ) {
		return $this->get_address_prop( 'company', 'shipping', $context );
	}

	/**
	 * Get shipping_address_1.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_address( $context = 'view' ) {
		return $this->get_shipping_address_1( $context );
	}

	/**
	 * Get shipping_address_1.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_address_1( $context = 'view' ) {
		return $this->get_address_prop( 'address_1', 'shipping', $context );
	}

	/**
	 * Get shipping_address_2.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_address_2( $context = 'view' ) {
		return $this->get_address_prop( 'address_2', 'shipping', $context );
	}

	/**
	 * Get shipping_city.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_city( $context = 'view' ) {
		return $this->get_address_prop( 'city', 'shipping', $context );
	}

	/**
	 * Get shipping_state.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_state( $context = 'view' ) {
		return $this->get_address_prop( 'state', 'shipping', $context );
	}

	/**
	 * Get shipping_postcode.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_postcode( $context = 'view' ) {
		return $this->get_address_prop( 'postcode', 'shipping', $context );
	}

	/**
	 * Get shipping_country.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_country( $context = 'view' ) {
		return $this->get_address_prop( 'country', 'shipping', $context );
	}

	/**
	 * Get shipping phone.
	 *
	 * @since 5.6.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_phone( $context = 'view' ) {
		return $this->get_address_prop( 'phone', 'shipping', $context );
	}

	/**
	 * Is the user a paying customer?
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return bool
	 */
	public function get_is_paying_customer( $context = 'view' ) {
		return $this->get_prop( 'is_paying_customer', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set customer's username.
	 *
	 * @since 3.0.0
	 * @param string $username Username.
	 */
	public function set_username( $username ) {
		$this->set_prop( 'username', $username );
	}

	/**
	 * Set customer's email.
	 *
	 * @since 3.0.0
	 * @param string $value Email.
	 */
	public function set_email( $value ) {
		if ( $value && ! is_email( $value ) ) {
			$this->error( 'customer_invalid_email', __( 'Invalid email address', 'woocommerce' ) );
		}
		$this->set_prop( 'email', sanitize_email( $value ) );
	}

	/**
	 * Set customer's first name.
	 *
	 * @since 3.0.0
	 * @param string $first_name First name.
	 */
	public function set_first_name( $first_name ) {
		$this->set_prop( 'first_name', $first_name );
	}

	/**
	 * Set customer's last name.
	 *
	 * @since 3.0.0
	 * @param string $last_name Last name.
	 */
	public function set_last_name( $last_name ) {
		$this->set_prop( 'last_name', $last_name );
	}

	/**
	 * Set customer's display name.
	 *
	 * @since 3.1.0
	 * @param string $display_name Display name.
	 */
	public function set_display_name( $display_name ) {
		/* translators: 1: first name 2: last name */
		$this->set_prop( 'display_name', is_email( $display_name ) ? sprintf( _x( '%1$s %2$s', 'display name', 'woocommerce' ), $this->get_first_name(), $this->get_last_name() ) : $display_name );
	}

	/**
	 * Set customer's user role(s).
	 *
	 * @since 3.0.0
	 * @param mixed $role User role.
	 */
	public function set_role( $role ) {
		global $wp_roles;

		if ( $role && ! empty( $wp_roles->roles ) && ! in_array( $role, array_keys( $wp_roles->roles ), true ) ) {
			$this->error( 'customer_invalid_role', __( 'Invalid role', 'woocommerce' ) );
		}
		$this->set_prop( 'role', $role );
	}

	/**
	 * Set the date this customer was last updated.
	 *
	 * @since  3.0.0
	 * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_created( $date = null ) {
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set the date this customer was last updated.
	 *
	 * @since  3.0.0
	 * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_modified( $date = null ) {
		$this->set_date_prop( 'date_modified', $date );
	}

	/**
	 * Set customer address to match shop base address.
	 *
	 * @since 3.0.0
	 */
	public function set_billing_address_to_base() {
		$base = wc_get_customer_default_location();
		$this->set_billing_location( $base['country'], $base['state'], '', '' );
	}

	/**
	 * Set customer shipping address to base address.
	 *
	 * @since 3.0.0
	 */
	public function set_shipping_address_to_base() {
		$base = wc_get_customer_default_location();
		$this->set_shipping_location( $base['country'], $base['state'], '', '' );
	}

	/**
	 * Sets all address info at once.
	 *
	 * @param string $country  Country.
	 * @param string $state    State.
	 * @param string $postcode Postcode.
	 * @param string $city     City.
	 */
	public function set_billing_location( $country, $state = '', $postcode = '', $city = '' ) {
		$address_data = $this->get_prop( 'billing', 'edit' );

		$address_data['address_1'] = '';
		$address_data['address_2'] = '';
		$address_data['city']      = $city;
		$address_data['state']     = $state;
		$address_data['postcode']  = $postcode;
		$address_data['country']   = $country;

		$this->set_prop( 'billing', $address_data );
	}

	/**
	 * Sets all shipping info at once.
	 *
	 * @param string $country  Country.
	 * @param string $state    State.
	 * @param string $postcode Postcode.
	 * @param string $city     City.
	 */
	public function set_shipping_location( $country, $state = '', $postcode = '', $city = '' ) {
		$address_data = $this->get_prop( 'shipping', 'edit' );

		$address_data['address_1'] = '';
		$address_data['address_2'] = '';
		$address_data['city']      = $city;
		$address_data['state']     = $state;
		$address_data['postcode']  = $postcode;
		$address_data['country']   = $country;

		$this->set_prop( 'shipping', $address_data );
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @since 3.0.0
	 * @param string $prop    Name of prop to set.
	 * @param string $address Name of address to set. billing or shipping.
	 * @param mixed  $value   Value of the prop.
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
	 * Set billing_first_name.
	 *
	 * @param string $value Billing first name.
	 */
	public function set_billing_first_name( $value ) {
		$this->set_address_prop( 'first_name', 'billing', $value );
	}

	/**
	 * Set billing_last_name.
	 *
	 * @param string $value Billing last name.
	 */
	public function set_billing_last_name( $value ) {
		$this->set_address_prop( 'last_name', 'billing', $value );
	}

	/**
	 * Set billing_company.
	 *
	 * @param string $value Billing company.
	 */
	public function set_billing_company( $value ) {
		$this->set_address_prop( 'company', 'billing', $value );
	}

	/**
	 * Set billing_address_1.
	 *
	 * @param string $value Billing address line 1.
	 */
	public function set_billing_address( $value ) {
		$this->set_billing_address_1( $value );
	}

	/**
	 * Set billing_address_1.
	 *
	 * @param string $value Billing address line 1.
	 */
	public function set_billing_address_1( $value ) {
		$this->set_address_prop( 'address_1', 'billing', $value );
	}

	/**
	 * Set billing_address_2.
	 *
	 * @param string $value Billing address line 2.
	 */
	public function set_billing_address_2( $value ) {
		$this->set_address_prop( 'address_2', 'billing', $value );
	}

	/**
	 * Set billing_city.
	 *
	 * @param string $value Billing city.
	 */
	public function set_billing_city( $value ) {
		$this->set_address_prop( 'city', 'billing', $value );
	}

	/**
	 * Set billing_state.
	 *
	 * @param string $value Billing state.
	 */
	public function set_billing_state( $value ) {
		$this->set_address_prop( 'state', 'billing', $value );
	}

	/**
	 * Set billing_postcode.
	 *
	 * @param string $value Billing postcode.
	 */
	public function set_billing_postcode( $value ) {
		$this->set_address_prop( 'postcode', 'billing', $value );
	}

	/**
	 * Set billing_country.
	 *
	 * @param string $value Billing country.
	 */
	public function set_billing_country( $value ) {
		$this->set_address_prop( 'country', 'billing', $value );
	}

	/**
	 * Set billing_email.
	 *
	 * @param string $value Billing email.
	 */
	public function set_billing_email( $value ) {
		if ( $value && ! is_email( $value ) ) {
			$this->error( 'customer_invalid_billing_email', __( 'Invalid billing email address', 'woocommerce' ) );
		}
		$this->set_address_prop( 'email', 'billing', sanitize_email( $value ) );
	}

	/**
	 * Set billing_phone.
	 *
	 * @param string $value Billing phone.
	 */
	public function set_billing_phone( $value ) {
		$this->set_address_prop( 'phone', 'billing', $value );
	}

	/**
	 * Set shipping_first_name.
	 *
	 * @param string $value Shipping first name.
	 */
	public function set_shipping_first_name( $value ) {
		$this->set_address_prop( 'first_name', 'shipping', $value );
	}

	/**
	 * Set shipping_last_name.
	 *
	 * @param string $value Shipping last name.
	 */
	public function set_shipping_last_name( $value ) {
		$this->set_address_prop( 'last_name', 'shipping', $value );
	}

	/**
	 * Set shipping_company.
	 *
	 * @param string $value Shipping company.
	 */
	public function set_shipping_company( $value ) {
		$this->set_address_prop( 'company', 'shipping', $value );
	}

	/**
	 * Set shipping_address_1.
	 *
	 * @param string $value Shipping address line 1.
	 */
	public function set_shipping_address( $value ) {
		$this->set_shipping_address_1( $value );
	}

	/**
	 * Set shipping_address_1.
	 *
	 * @param string $value Shipping address line 1.
	 */
	public function set_shipping_address_1( $value ) {
		$this->set_address_prop( 'address_1', 'shipping', $value );
	}

	/**
	 * Set shipping_address_2.
	 *
	 * @param string $value Shipping address line 2.
	 */
	public function set_shipping_address_2( $value ) {
		$this->set_address_prop( 'address_2', 'shipping', $value );
	}

	/**
	 * Set shipping_city.
	 *
	 * @param string $value Shipping city.
	 */
	public function set_shipping_city( $value ) {
		$this->set_address_prop( 'city', 'shipping', $value );
	}

	/**
	 * Set shipping_state.
	 *
	 * @param string $value Shipping state.
	 */
	public function set_shipping_state( $value ) {
		$this->set_address_prop( 'state', 'shipping', $value );
	}

	/**
	 * Set shipping_postcode.
	 *
	 * @param string $value Shipping postcode.
	 */
	public function set_shipping_postcode( $value ) {
		$this->set_address_prop( 'postcode', 'shipping', $value );
	}

	/**
	 * Set shipping_country.
	 *
	 * @param string $value Shipping country.
	 */
	public function set_shipping_country( $value ) {
		$this->set_address_prop( 'country', 'shipping', $value );
	}

	/**
	 * Set shipping phone.
	 *
	 * @since 5.6.0
	 * @param string $value Shipping phone.
	 */
	public function set_shipping_phone( $value ) {
		$this->set_address_prop( 'phone', 'shipping', $value );
	}

	/**
	 * Set if the user a paying customer.
	 *
	 * @since 3.0.0
	 * @param bool $is_paying_customer If is a paying customer.
	 */
	public function set_is_paying_customer( $is_paying_customer ) {
		$this->set_prop( 'is_paying_customer', (bool) $is_paying_customer );
	}
}
