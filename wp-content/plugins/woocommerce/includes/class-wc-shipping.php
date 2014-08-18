<?php
/**
 * WooCommerce Shipping Class
 *
 * Handles shipping and loads shipping methods via hooks.
 *
 * @class 		WC_Shipping
 * @version		1.6.4
 * @package		WooCommerce/Classes/Shipping
 * @category	Class
 * @author 		WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Shipping {

	/** @var bool True if shipping is enabled. */
	var $enabled					= false;

	/** @var array Stores methods loaded into woocommerce. */
	var $shipping_methods 			= array();

	/** @var float Stores the cost of shipping */
	var $shipping_total 			= 0;

	/**  @var array Stores an array of shipping taxes. */
	var $shipping_taxes				= array();

	/** @var array Stores the shipping classes. */
	var $shipping_classes			= array();

	/** @var array Stores packages to ship and to get quotes for. */
	var $packages					= array();

	/**
	 * @var WooCommerce The single instance of the class
	 * @since 2.1
	 */
	protected static $_instance = null;

	/**
	 * Main WooCommerce Instance
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @see WC()
	 * @return Main WooCommerce instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->init();
	}

    /**
     * init function.
     *
     * @access public
     */
    public function init() {
		do_action( 'woocommerce_shipping_init' );

		$this->enabled = ( get_option('woocommerce_calc_shipping') == 'no' ) ? false : true;
	}

	/**
	 * load_shipping_methods function.
	 *
	 * Loads all shipping methods which are hooked in. If a $package is passed some methods may add themselves conditionally.
	 *
	 * Methods are sorted into their user-defined order after being loaded.
	 *
	 * @access public
	 * @param array $package
	 * @return array
	 */
	public function load_shipping_methods( $package = array() ) {

		$this->unregister_shipping_methods();

		// Methods can register themselves through this hook
		do_action( 'woocommerce_load_shipping_methods', $package );

		// Register methods through a filter
		$shipping_methods_to_load = apply_filters( 'woocommerce_shipping_methods', array(
			'WC_Shipping_Flat_Rate',
			'WC_Shipping_Free_Shipping',
			'WC_Shipping_International_Delivery',
			'WC_Shipping_Local_Delivery',
			'WC_Shipping_Local_Pickup'
		) );

		foreach ( $shipping_methods_to_load as $method )
			$this->register_shipping_method( $method );

		$this->sort_shipping_methods();

		return $this->shipping_methods;
	}

	/**
	 * Register a shipping method for use in calculations.
	 *
	 * @access public
	 * @param  object|string $method Either the name of the method's class, or an instance of the method's class
	 * @return void
	 */
	public function register_shipping_method( $method ) {

		if ( ! is_object( $method ) )
			$method = new $method();

		$id = empty( $method->instance_id ) ? $method->id : $method->instance_id;

		$this->shipping_methods[ $id ] = $method;
	}

	/**
	 * unregister_shipping_methods function.
	 *
	 * @access public
	 * @return void
	 */
	public function unregister_shipping_methods() {
		unset( $this->shipping_methods );
	}

	/**
	 * sort_shipping_methods function.
	 *
	 * Sorts shipping methods into the user defined order.
	 *
	 * @access public
	 * @return array
	 */
	public function sort_shipping_methods() {

		$sorted_shipping_methods = array();

		// Get order option
		$ordering 	= (array) get_option('woocommerce_shipping_method_order');
		$order_end 	= 999;

		// Load shipping methods in order
		foreach ( $this->shipping_methods as $method ) {

			if ( isset( $ordering[ $method->id ] ) && is_numeric( $ordering[ $method->id ] ) ) {
				// Add in position
				$sorted_shipping_methods[ $ordering[ $method->id ] ][] = $method;
			} else {
				// Add to end of the array
				$sorted_shipping_methods[ $order_end ][] = $method;
			}
		}

		ksort( $sorted_shipping_methods );

		$this->shipping_methods = array();

		foreach ( $sorted_shipping_methods as $methods )
			foreach ( $methods as $method ) {
				$id = empty( $method->instance_id ) ? $method->id : $method->instance_id;
				$this->shipping_methods[ $id ] = $method;
			}

		return $this->shipping_methods;
	}

	/**
	 * get_shipping_methods function.
	 *
	 * Returns all registered shipping methods for usage.
	 *
	 * @access public
	 * @return array
	 */
	public function get_shipping_methods() {
		return $this->shipping_methods;
	}

	/**
	 * get_shipping_classes function.
	 *
	 * Load shipping classes taxonomy terms.
	 *
	 * @access public
	 * @return array
	 */
	public function get_shipping_classes() {
		if ( empty( $this->shipping_classes ) )
			$this->shipping_classes = ( $classes = get_terms( 'product_shipping_class', array( 'hide_empty' => '0' ) ) ) ? $classes : array();

		return $this->shipping_classes;
	}

	/**
	 * calculate_shipping function.
	 *
	 * Calculate shipping for (multiple) packages of cart items.
	 *
	 * @access public
	 * @param array $packages multi-dimensional array of cart items to calc shipping for
	 */
	public function calculate_shipping( $packages = array() ) {
		if ( ! $this->enabled || empty( $packages ) )
			return;

		$this->shipping_total 	= null;
		$this->shipping_taxes 	= array();
		$this->packages 		= array();

		// Calculate costs for passed packages
		$package_keys 		= array_keys( $packages );
		$package_keys_size 	= sizeof( $package_keys );

		for ( $i = 0; $i < $package_keys_size; $i ++ )
			$this->packages[ $package_keys[ $i ] ] = $this->calculate_shipping_for_package( $packages[ $package_keys[ $i ] ] );

		// Get all chosen methods
		$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
		$method_counts  = WC()->session->get( 'shipping_method_counts' );

		// Get chosen methods for each package
		foreach ( $this->packages as $i => $package ) {

			$_cheapest_cost   = false;
			$_cheapest_method = false;
			$chosen_method    = false;
			$method_count     = false;

			if ( ! empty( $chosen_methods[ $i ] ) )
				$chosen_method = $chosen_methods[ $i ];

			if ( ! empty( $method_counts[ $i ] ) )
				$method_count = $method_counts[ $i ];

			// Get available methods for package
			$_available_methods = $package['rates'];

			if ( sizeof( $_available_methods ) > 0 ) {

				// If not set, not available, or available methods have changed, set to the default option
				if ( empty( $chosen_method ) || ! isset( $_available_methods[ $chosen_method ] ) || $method_count != sizeof( $_available_methods ) ) {

					$chosen_method = apply_filters( 'woocommerce_shipping_chosen_method', get_option( 'woocommerce_default_shipping_method' ), $_available_methods );

					// Loops methods and find a match
					if ( ! empty( $chosen_method ) && ! isset( $_available_methods[ $chosen_method ] ) ) {
						foreach ( $_available_methods as $method_id => $method ) {
							if ( strpos( $method->id, $chosen_method ) === 0 ) {
								$chosen_method = $method->id;
								break;
							}
						}
					}

					if ( empty( $chosen_method ) || ! isset( $_available_methods[ $chosen_method ] ) ) {
						// Default to cheapest
						foreach ( $_available_methods as $method_id => $method ) {
							if ( $method->cost < $_cheapest_cost || ! is_numeric( $_cheapest_cost ) ) {
								$_cheapest_cost 	= $method->cost;
								$_cheapest_method 	= $method_id;
							}
						}
						$chosen_method = $_cheapest_method;
					}

					// Store chosen method
					$chosen_methods[ $i ] = $chosen_method;
					$method_counts[ $i ]  = sizeof( $_available_methods );

					// Do action for this chosen method
					do_action( 'woocommerce_shipping_method_chosen', $chosen_method );
				}

				// Store total costs
				if ( $chosen_method ) {
					$rate = $_available_methods[ $chosen_method ];

					// Merge cost and taxes - label and ID will be the same
					$this->shipping_total += $rate->cost;

					foreach ( array_keys( $this->shipping_taxes + $rate->taxes ) as $key ) {
					    $this->shipping_taxes[ $key ] = ( isset( $rate->taxes[$key] ) ? $rate->taxes[$key] : 0 ) + ( isset( $this->shipping_taxes[$key] ) ? $this->shipping_taxes[$key] : 0 );
					}
				}
			}
		}

		// Save all chosen methods (array)
		WC()->session->set( 'chosen_shipping_methods', $chosen_methods );
		WC()->session->set( 'shipping_method_counts', $method_counts );
	}

	/**
	 * calculate_shipping_for_package function.
	 *
	 * Calculates each shipping methods cost. Rates are cached based on the package to speed up calculations.
	 *
	 * @access public
	 * @param array $package cart items
	 * @return array
	 * @todo Return array() instead of false for consistent return type?
	 */
	public function calculate_shipping_for_package( $package = array() ) {
		if ( ! $this->enabled ) return false;
		if ( ! $package ) return false;

		// Check if we need to recalculate shipping for this package
		$package_hash   = 'wc_ship_' . md5( json_encode( $package ) );
		$status_options = get_option( 'woocommerce_status_options', array() );

		if ( false === ( $stored_rates = get_transient( $package_hash ) ) || ( ! empty( $status_options['shipping_debug_mode'] ) && current_user_can( 'manage_options' ) ) ) {

			// Calculate shipping method rates
			$package['rates'] = array();

			foreach ( $this->load_shipping_methods( $package ) as $shipping_method ) {

				if ( $shipping_method->is_available( $package ) && ( empty( $package['ship_via'] ) || in_array( $shipping_method->id, $package['ship_via'] ) ) ) {

					// Reset Rates
					$shipping_method->rates = array();

					// Calculate Shipping for package
					$shipping_method->calculate_shipping( $package );

					// Place rates in package array
					if ( ! empty( $shipping_method->rates ) && is_array( $shipping_method->rates ) )
						foreach ( $shipping_method->rates as $rate )
							$package['rates'][ $rate->id ] = $rate;
				}
			}

			// Filter the calculated rates
			$package['rates'] = apply_filters( 'woocommerce_package_rates', $package['rates'], $package );

			// Store
			set_transient( $package_hash, $package['rates'], 60 * 60 ); // Cached for an hour

		} else {

			$package['rates'] = $stored_rates;

		}

		return $package;
	}

	/**
	 * Get packages
	 * @return array
	 */
	public  function get_packages() {
		return $this->packages;
	}


	/**
	 * reset_shipping function.
	 *
	 * Reset the totals for shipping as a whole.
	 *
	 * @access public
	 * @return void
	 */
	public function reset_shipping() {
		unset( WC()->session->chosen_shipping_methods );
		$this->shipping_total = null;
		$this->shipping_taxes = array();
		$this->packages = array();
	}


	/**
	 * process_admin_options function.
	 *
	 * Saves options on the shipping setting page.
	 *
	 * @access public
	 * @return void
	 */
	public function process_admin_options() {

		$default_shipping_method = ( isset( $_POST['default_shipping_method'] ) ) ? esc_attr( $_POST['default_shipping_method'] ) : '';
		$method_order = ( isset( $_POST['method_order'] ) ) ? $_POST['method_order'] : '';

		$order = array();

		if ( is_array( $method_order ) && sizeof( $method_order ) > 0 ) {
			$loop = 0;
			foreach ($method_order as $method_id) {
				$order[$method_id] = $loop;
				$loop++;
			}
		}

		update_option( 'woocommerce_default_shipping_method', $default_shipping_method );
		update_option( 'woocommerce_shipping_method_order', $order );
	}

}

/**
 * Register a shipping method
 *
 * Registers a shipping method ready to be loaded. Accepts a class name (string) or a class object.
 *
 * @package		WooCommerce/Classes/Shipping
 * @since 1.5.7
 */
function woocommerce_register_shipping_method( $shipping_method ) {
	$GLOBALS['woocommerce']->shipping->register_shipping_method( $shipping_method );
}