<?php
/**
 * Customer
 *
 * The WooCommerce customer class handles storage of the current customer's data, such as location.
 *
 * @class 		WC_Customer
 * @version		1.6.4
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Customer {

	/** Stores customer data as an array */
	protected $_data;

	/** Stores bool when data is changed */
	private $_changed = false;

	/**
	 * Constructor for the customer class loads the customer data.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		if ( empty( WC()->session->customer ) ) {

			$default = apply_filters( 'woocommerce_customer_default_location', get_option( 'woocommerce_default_country' ) );

        	if ( strstr( $default, ':' ) ) {
        		list( $country, $state ) = explode( ':', $default );
        	} else {
        		$country = $default;
        		$state   = '';
        	}

			$this->_data = array(
				'country' 				=> esc_html( $country ),
				'state' 				=> '',
				'postcode' 				=> '',
				'city'					=> '',
				'address' 				=> '',
				'address_2' 			=> '',
				'shipping_country' 		=> esc_html( $country ),
				'shipping_state' 		=> '',
				'shipping_postcode' 	=> '',
				'shipping_city'			=> '',
				'shipping_address'		=> '',
				'shipping_address_2'	=> '',
				'is_vat_exempt' 		=> false,
				'calculated_shipping'	=> false
			);

		} else {

			$this->_data = WC()->session->customer;

		}

		// When leaving or ending page load, store data
    	add_action( 'shutdown', array( $this, 'save_data' ), 10 );
	}

	/**
	 * save_data function.
	 *
	 * @access public
	 * @return void
	 */
	public function save_data() {
		if ( $this->_changed ) {
			$GLOBALS['woocommerce']->session->customer = $this->_data;
		}
	}

	/**
	 * __set function.
	 * @access   public
	 * @param mixed $property
	 * @return bool
	 */
    public function __isset( $property ) {
        return isset( $this->_data[ $property ] );
    }

    /**
     * __get function.
     *
     * @access public
     * @param mixed $property
     * @return mixed|null
     */
    public function __get( $property ) {
        return isset( $this->_data[ $property ] ) ? $this->_data[ $property ] : null;
    }

    /**
     * __set function.
     *
     * @access public
     * @param mixed $property
     * @param mixed $value
     * @return void
     */
    public function __set( $property, $value ) {
        $this->_data[ $property ] = $value;
        $this->_changed = true;
    }

	/**
	 * has_calculated_shipping function.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_calculated_shipping() {
		return ( ! empty( $this->calculated_shipping ) ) ? true : false;
	}


	/**
	 * Set customer address to match shop base address.
	 *
	 * @access public
	 * @return void
	 */
	public function set_to_base() {
		$default = apply_filters( 'woocommerce_customer_default_location', get_option('woocommerce_default_country') );
    	if ( strstr( $default, ':' ) ) {
    		list( $country, $state ) = explode( ':', $default );
    	} else {
    		$country = $default;
    		$state = '';
    	}
    	$this->country  = $country;
    	$this->state    = $state;
    	$this->postcode = '';
    	$this->city     = '';
	}


	/**
	 * Set customer shipping address to base address.
	 *
	 * @access public
	 * @return void
	 */
	public function set_shipping_to_base() {
		$default = get_option('woocommerce_default_country');
    	if ( strstr( $default, ':' ) ) {
    		list( $country, $state ) = explode( ':', $default );
    	} else {
    		$country = $default;
    		$state = '';
    	}
    	$this->shipping_country  = $country;
    	$this->shipping_state    = $state;
    	$this->shipping_postcode = '';
    	$this->shipping_city     = '';
	}


	/**
	 * Is customer outside base country (for tax purposes)?
	 *
	 * @access public
	 * @return bool
	 */
	public function is_customer_outside_base() {
		list( $country, $state, $postcode, $city ) = $this->get_taxable_address();

		if ( $country ) {

			$default = get_option('woocommerce_default_country');

        	if ( strstr( $default, ':' ) ) {
	    		list( $default_country, $default_state ) = explode( ':', $default );
	    	} else {
	    		$default_country = $default;
	    		$default_state = '';
	    	}

			if ( $default_country !== $country ) return true;
			if ( $default_state && $default_state !== $state ) return true;

		}
		return false;
	}
	
	/**
	 * Is the user a paying customer?
	 *
	 * @access public
	 * @return bool
	 */
	function is_paying_customer( $user_id ) {
		return '1' === get_user_meta( $user_id, 'paying_customer', true );
	}


	/**
	 * Is customer VAT exempt?
	 *
	 * @access public
	 * @return bool
	 */
	public function is_vat_exempt() {
		return ( ! empty( $this->is_vat_exempt ) ) ? true : false;
	}


	/**
	 * Gets the state from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_state() {
		if ( isset( $this->state ) ) return $this->state;
	}


	/**
	 * Gets the country from the current session
	 *
	 * @access public
	 * @return string
	 */
	public function get_country() {
		if ( isset( $this->country ) ) return $this->country;
	}


	/**
	 * Gets the postcode from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_postcode() {
		if ( isset( $this->postcode ) && $this->postcode !== false )
			return wc_format_postcode( $this->postcode, $this->get_country() );
	}


	/**
	 * Get the city from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_city() {
		if ( isset( $this->city ) ) return $this->city;
	}

	/**
	 * Gets the address from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_address() {
		if ( isset( $this->address ) ) return $this->address;
	}

	/**
	 * Gets the address_2 from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_address_2() {
		if ( isset( $this->address_2 ) ) return $this->address_2;
	}

	/**
	 * Gets the state from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_shipping_state() {
		if ( isset( $this->shipping_state ) ) return $this->shipping_state;
	}


	/**
	 * Gets the country from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_shipping_country() {
		if ( isset( $this->shipping_country ) ) return $this->shipping_country;
	}


	/**
	 * Gets the postcode from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_shipping_postcode() {
		if ( isset( $this->shipping_postcode ) )
			return wc_format_postcode( $this->shipping_postcode, $this->get_shipping_country() );
	}


	/**
	 * Gets the city from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_shipping_city() {
		if ( isset( $this->shipping_city ) ) return $this->shipping_city;
	}

	/**
	 * Gets the address from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_shipping_address() {
		if ( isset( $this->shipping_address ) ) return $this->shipping_address;
	}

	/**
	 * Gets the address_2 from the current session.
	 *
	 * @access public
	 * @return string
	 */
	public function get_shipping_address_2() {
		if ( isset( $this->shipping_address_2 ) ) return $this->shipping_address_2;
	}

	/**
	 * get_taxable_address function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_taxable_address() {
		$tax_based_on = get_option( 'woocommerce_tax_based_on' );

		// Check shipping method at this point to see if we need special handling
		if ( apply_filters( 'woocommerce_apply_base_tax_for_local_pickup', true ) == true && sizeof( array_intersect( WC()->session->get( 'chosen_shipping_methods', array( get_option( 'woocommerce_default_shipping_method' ) ) ), apply_filters( 'woocommerce_local_pickup_methods', array( 'local_pickup' ) ) ) ) > 0 ) {
			$tax_based_on = 'base';
		}

		if ( $tax_based_on == 'base' ) {

			$default = get_option( 'woocommerce_default_country' );
	    	if ( strstr( $default, ':' ) ) {
	    		list( $country, $state ) = explode( ':', $default );
	    	} else {
	    		$country = $default;
	    		$state = '';
	    	}

			$postcode   = '';
			$city   	= '';

		} elseif ( $tax_based_on == 'billing' ) {

			$country 	= $this->get_country();
			$state 		= $this->get_state();
			$postcode   = $this->get_postcode();
			$city   	= $this->get_city();

		} else {

			$country 	= $this->get_shipping_country();
			$state 		= $this->get_shipping_state();
			$postcode   = $this->get_shipping_postcode();
			$city   	= $this->get_shipping_city();

		}

		return apply_filters( 'woocommerce_customer_taxable_address', array( $country, $state, $postcode, $city ) );
	}


	/**
	 * Sets session data for the location.
	 *
	 * @access public
	 * @param mixed $country
	 * @param mixed $state
	 * @param string $postcode (default: '')
	 * @param string $city (default: '')
	 * @return void
	 */
	public function set_location( $country, $state, $postcode = '', $city = '' ) {
		$this->country = $country;
		$this->state = $state;
		$this->postcode = $postcode;
		$this->city = $city;
	}


	/**
	 * Sets session data for the country.
	 *
	 * @access public
	 * @param mixed $country
	 * @return void
	 */
	public function set_country( $country ) {
		$this->country = $country;
	}


	/**
	 * Sets session data for the state.
	 *
	 * @access public
	 * @param mixed $state
	 * @return void
	 */
	public function set_state( $state ) {
		$this->state = $state;
	}


	/**
	 * Sets session data for the postcode.
	 *
	 * @access public
	 * @param mixed $postcode
	 * @return void
	 */
	public function set_postcode( $postcode ) {
		$this->postcode = $postcode;
	}


	/**
	 * Sets session data for the city.
	 *
	 * @access public
	 * @param mixed $postcode
	 * @return void
	 */
	public function set_city( $city ) {
		$this->city = $city;
	}

	/**
	 * Sets session data for the address.
	 *
	 * @access public
	 * @param mixed $address
	 * @return void
	 */
	public function set_address( $address ) {
		$this->address = $address;
	}

	/**
	 * Sets session data for the address_2.
	 *
	 * @access public
	 * @param mixed $address_2
	 * @return void
	 */
	public function set_address_2( $address_2 ) {
		$this->address_2 = $address_2;
	}

	/**
	 * Sets session data for the location.
	 *
	 * @access public
	 * @param mixed $country
	 * @param string $state (default: '')
	 * @param string $postcode (default: '')
	 * @param string $city (default: '')
	 * @return void
	 */
	public function set_shipping_location( $country, $state = '', $postcode = '', $city = '' ) {
		$this->shipping_country = $country;
		$this->shipping_state = $state;
		$this->shipping_postcode = $postcode;
		$this->shipping_city = $city;
	}


	/**
	 * Sets session data for the country.
	 *
	 * @access public
	 * @param mixed $country
	 * @return void
	 */
	public function set_shipping_country( $country ) {
		$this->shipping_country = $country;
	}


	/**
	 * Sets session data for the state.
	 *
	 * @access public
	 * @param mixed $state
	 * @return void
	 */
	public function set_shipping_state( $state ) {
		$this->shipping_state = $state;
	}


	/**
	 * Sets session data for the postcode.
	 *
	 * @access public
	 * @param mixed $postcode
	 * @return void
	 */
	public function set_shipping_postcode( $postcode ) {
		$this->shipping_postcode = $postcode;
	}


	/**
	 * Sets session data for the city.
	 *
	 * @access public
	 * @param mixed $postcode
	 * @return void
	 */
	public function set_shipping_city( $city ) {
		$this->shipping_city = $city;
	}

	/**
	 * Sets session data for the address.
	 *
	 * @access public
	 * @param mixed $address
	 * @return void
	 */
	public function set_shipping_address( $address ) {
		$this->shipping_address = $address;
	}

	/**
	 * Sets session data for the address_2.
	 *
	 * @access public
	 * @param mixed $address_2
	 * @return void
	 */
	public function set_shipping_address_2( $address_2 ) {
		$this->shipping_address_2 = $address_2;
	}


	/**
	 * Sets session data for the tax exemption.
	 *
	 * @access public
	 * @param mixed $is_vat_exempt
	 * @return void
	 */
	public function set_is_vat_exempt( $is_vat_exempt ) {
		$this->is_vat_exempt = $is_vat_exempt;
	}


	/**
	 * calculated_shipping function.
	 *
	 * @access public
	 * @param mixed $calculated
	 * @return void
	 */
	public function calculated_shipping( $calculated = true ) {
		$this->calculated_shipping = $calculated;
	}


	/**
	 * Gets a user's downloadable products if they are logged in.
	 *
	 * @access public
	 * @return array Array of downloadable products
	 */
	public function get_downloadable_products() {
		global $wpdb;

		$downloads   = array();
		$_product    = null;
		$order       = null;
		$file_number = 0;

		if ( is_user_logged_in() ) {

			// Get results from valid orders only
			$results = $wpdb->get_results( $wpdb->prepare( "
				SELECT permissions.* 
				FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions as permissions
				LEFT JOIN {$wpdb->posts} as posts ON permissions.order_id = posts.ID
				WHERE user_id = %s 
				AND permissions.order_id > 0
				AND posts.post_status = 'publish'
				AND 
					(
						permissions.downloads_remaining > 0
						OR 
						permissions.downloads_remaining = ''
					)
				AND 
					(
						permissions.access_expires IS NULL
						OR 
						permissions.access_expires >= %s
					)
				GROUP BY permissions.download_id
				ORDER BY permissions.order_id, permissions.product_id, permissions.permission_id;
				", get_current_user_id(), date( 'Y-m-d', current_time( 'timestamp' ) ) ) );

			if ( $results ) {
				foreach ( $results as $result ) {
					if ( ! $order || $order->id != $result->order_id ) {
						// new order
						$order    = new WC_Order( $result->order_id );
						$_product = null;
					}

					// Downloads permitted?
					if ( ! $order->is_download_permitted() ) {
						continue;
					}

					if ( ! $_product || $_product->id != $result->product_id ) {
						// new product
						$file_number = 0;
						$_product    = get_product( $result->product_id );
					}

					// Check product exists and has the file
					if ( ! $_product || ! $_product->exists() || ! $_product->has_file( $result->download_id ) ) {
						continue;
					}

					$download_file = $_product->get_file( $result->download_id );
					// Download name will be 'Product Name' for products with a single downloadable file, and 'Product Name - File X' for products with multiple files
					$download_name = apply_filters(
						'woocommerce_downloadable_product_name',
						$_product->get_title() . ' &ndash; ' . $download_file['name'],
						$_product,
						$result->download_id,
						$file_number
					);

					$downloads[] = array(
						'download_url'        => add_query_arg( array( 'download_file' => $result->product_id, 'order' => $result->order_key, 'email' => $result->user_email, 'key' => $result->download_id ), home_url( '/', 'http' ) ),
						'download_id'         => $result->download_id,
						'product_id'          => $result->product_id,
						'download_name'       => $download_name,
						'order_id'            => $order->id,
						'order_key'           => $order->order_key,
						'downloads_remaining' => $result->downloads_remaining
					);

					$file_number++;
				}
			}
		}

		return apply_filters( 'woocommerce_customer_get_downloadable_products', $downloads );
	}
}