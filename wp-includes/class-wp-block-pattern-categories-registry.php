<?php
/**
 * Blocks API: WP_Block_Pattern_Categories_Registry class
 *
 * @package WordPress
 * @subpackage Blocks
 * @since 5.5.0
 */

/**
 * Class used for interacting with block pattern categories.
 */
final class WP_Block_Pattern_Categories_Registry {
	/**
	 * Registered block pattern categories array.
	 *
	 * @var array
	 */
	private $registered_categories = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @var WP_Block_Pattern_Categories_Registry|null
	 */
	private static $instance = null;

	/**
	 * Registers a pattern category.
	 *
	 * @since 5.5.0
	 *
	 * @param string $category_name       Pattern category name.
	 * @param array  $category_properties Array containing the properties of the category: label.
	 * @return bool True if the pattern was registered with success and false otherwise.
	 */
	public function register( $category_name, $category_properties ) {
		if ( ! isset( $category_name ) || ! is_string( $category_name ) ) {
			_doing_it_wrong( __METHOD__, __( 'Block pattern category name must be a string.' ), '5.5.0' );
			return false;
		}

		$this->registered_categories[ $category_name ] = array_merge(
			array( 'name' => $category_name ),
			$category_properties
		);

		return true;
	}

	/**
	 * Unregisters a pattern category.
	 *
	 * @since 5.5.0
	 *
	 * @param string $category_name     Pattern name including namespace.
	 * @return bool True if the pattern was unregistered with success and false otherwise.
	 */
	public function unregister( $category_name ) {
		if ( ! $this->is_registered( $category_name ) ) {
			/* translators: 1: Block pattern name. */
			$message = sprintf( __( 'Block pattern category "%1$s" not found.' ), $category_name );
			_doing_it_wrong( __METHOD__, $message, '5.5.0' );
			return false;
		}

		unset( $this->registered_categories[ $category_name ] );

		return true;
	}

	/**
	 * Retrieves an array containing the properties of a registered pattern category.
	 *
	 * @since 5.5.0
	 *
	 * @param string $category_name Pattern category name.
	 * @return array Registered pattern properties.
	 */
	public function get_registered( $category_name ) {
		if ( ! $this->is_registered( $category_name ) ) {
			return null;
		}

		return $this->registered_categories[ $category_name ];
	}

	/**
	 * Retrieves all registered pattern categories.
	 *
	 * @since 5.5.0
	 *
	 * @return array Array of arrays containing the registered pattern categories properties.
	 */
	public function get_all_registered() {
		return array_values( $this->registered_categories );
	}

	/**
	 * Checks if a pattern category is registered.
	 *
	 * @since 5.5.0
	 *
	 * @param string $category_name       Pattern category name.
	 * @return bool True if the pattern category is registered, false otherwise.
	 */
	public function is_registered( $category_name ) {
		return isset( $this->registered_categories[ $category_name ] );
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 5.5.0
	 *
	 * @return WP_Block_Pattern_Categories_Registry The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

/**
 * Registers a new pattern category.
 *
 * @since 5.5.0
 *
 * @param string $category_name       Pattern category name.
 * @param array  $category_properties Array containing the properties of the category.
 * @return bool True if the pattern category was registered with success and false otherwise.
 */
function register_block_pattern_category( $category_name, $category_properties ) {
	return WP_Block_Pattern_Categories_Registry::get_instance()->register( $category_name, $category_properties );
}

/**
 * Unregisters a pattern category.
 *
 * @since 5.5.0
 *
 * @param string $category_name       Pattern category name including namespace.
 * @return bool True if the pattern category was unregistered with success and false otherwise.
 */
function unregister_block_pattern_category( $category_name ) {
	return WP_Block_Pattern_Categories_Registry::get_instance()->unregister( $category_name );
}
