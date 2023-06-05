<?php
/**
 * WC Queue
 *
 * @version 3.5.0
 * @package WooCommerce\Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC Queue
 *
 * Singleton for managing the WC queue instance.
 *
 * @version 3.5.0
 */
class WC_Queue {

	/**
	 * The single instance of the queue.
	 *
	 * @var WC_Queue_Interface|null
	 */
	protected static $instance = null;

	/**
	 * The default queue class to initialize
	 *
	 * @var string
	 */
	protected static $default_cass = 'WC_Action_Queue';

	/**
	 * Single instance of WC_Queue_Interface
	 *
	 * @return WC_Queue_Interface
	 */
	final public static function instance() {

		if ( is_null( self::$instance ) ) {
			$class          = self::get_class();
			self::$instance = new $class();
			self::$instance = self::validate_instance( self::$instance );
		}
		return self::$instance;
	}

	/**
	 * Get class to instantiate
	 *
	 * And make sure 3rd party code has the chance to attach a custom queue class.
	 *
	 * @return string
	 */
	protected static function get_class() {
		if ( ! did_action( 'plugins_loaded' ) ) {
			wc_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before plugins_loaded.', 'woocommerce' ), '3.5.0' );
		}

		return apply_filters( 'woocommerce_queue_class', self::$default_cass );
	}

	/**
	 * Enforce a WC_Queue_Interface
	 *
	 * @param WC_Queue_Interface $instance Instance class.
	 * @return WC_Queue_Interface
	 */
	protected static function validate_instance( $instance ) {
		if ( false === ( $instance instanceof WC_Queue_Interface ) ) {
			$default_class = self::$default_cass;
			/* translators: %s: Default class name */
			wc_doing_it_wrong( __FUNCTION__, sprintf( __( 'The class attached to the "woocommerce_queue_class" does not implement the WC_Queue_Interface interface. The default %s class will be used instead.', 'woocommerce' ), $default_class ), '3.5.0' );
			$instance = new $default_class();
		}

		return $instance;
	}
}
