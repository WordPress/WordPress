<?php
/**
 * WooCommerce Autoloader.
 *
 * @package WooCommerce\Classes
 * @version 2.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader class.
 */
class WC_Autoloader {

	/**
	 * Path to the includes directory.
	 *
	 * @var string
	 */
	private $include_path = '';

	/**
	 * The Constructor.
	 */
	public function __construct() {
		if ( function_exists( '__autoload' ) ) {
			spl_autoload_register( '__autoload' );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = untrailingslashit( plugin_dir_path( WC_PLUGIN_FILE ) ) . '/includes/';
	}

	/**
	 * Take a class name and turn it into a file name.
	 *
	 * @param  string $class Class name.
	 * @return string
	 */
	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}

	/**
	 * Include a class file.
	 *
	 * @param  string $path File path.
	 * @return bool Successful or not.
	 */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once $path;
			return true;
		}
		return false;
	}

	/**
	 * Auto-load WC classes on demand to reduce memory consumption.
	 *
	 * @param string $class Class name.
	 */
	public function autoload( $class ) {
		$class = strtolower( $class );

		if ( 0 !== strpos( $class, 'wc_' ) ) {
			return;
		}

		$file = $this->get_file_name_from_class( $class );
		$path = '';

		if ( 0 === strpos( $class, 'wc_addons_gateway_' ) ) {
			$path = $this->include_path . 'gateways/' . substr( str_replace( '_', '-', $class ), 18 ) . '/';
		} elseif ( 0 === strpos( $class, 'wc_gateway_' ) ) {
			$path = $this->include_path . 'gateways/' . substr( str_replace( '_', '-', $class ), 11 ) . '/';
		} elseif ( 0 === strpos( $class, 'wc_shipping_' ) ) {
			$path = $this->include_path . 'shipping/' . substr( str_replace( '_', '-', $class ), 12 ) . '/';
		} elseif ( 0 === strpos( $class, 'wc_shortcode_' ) ) {
			$path = $this->include_path . 'shortcodes/';
		} elseif ( 0 === strpos( $class, 'wc_meta_box' ) ) {
			$path = $this->include_path . 'admin/meta-boxes/';
		} elseif ( 0 === strpos( $class, 'wc_admin' ) ) {
			$path = $this->include_path . 'admin/';
		} elseif ( 0 === strpos( $class, 'wc_payment_token_' ) ) {
			$path = $this->include_path . 'payment-tokens/';
		} elseif ( 0 === strpos( $class, 'wc_log_handler_' ) ) {
			$path = $this->include_path . 'log-handlers/';
		} elseif ( 0 === strpos( $class, 'wc_integration' ) ) {
			$path = $this->include_path . 'integrations/' . substr( str_replace( '_', '-', $class ), 15 ) . '/';
		} elseif ( 0 === strpos( $class, 'wc_notes_' ) ) {
			$path = $this->include_path . 'admin/notes/';
		}

		if ( empty( $path ) || ! $this->load_file( $path . $file ) ) {
			$this->load_file( $this->include_path . $file );
		}
	}
}

new WC_Autoloader();
