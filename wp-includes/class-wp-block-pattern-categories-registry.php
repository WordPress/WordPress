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
#[AllowDynamicProperties]
final class WP_Block_Pattern_Categories_Registry {
	/**
	 * Registered block pattern categories array.
	 *
	 * @since 5.5.0
	 * @var array[]
	 */
	private $registered_categories = array();

	/**
	 * Pattern categories registered outside the `init` action.
	 *
	 * @since 6.0.0
	 * @var array[]
	 */
	private $registered_categories_outside_init = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @since 5.5.0
	 * @var WP_Block_Pattern_Categories_Registry|null
	 */
	private static $instance = null;

	/**
	 * Registers a pattern category.
	 *
	 * @since 5.5.0
	 *
	 * @param string $category_name       Pattern category name including namespace.
	 * @param array  $category_properties {
	 *     List of properties for the block pattern category.
	 *
	 *     @type string $label Required. A human-readable label for the pattern category.
	 * }
	 * @return bool True if the pattern was registered with success and false otherwise.
	 */
	public function register( $category_name, $category_properties ) {
		if ( ! isset( $category_name ) || ! is_string( $category_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Block pattern category name must be a string.' ),
				'5.5.0'
			);
			return false;
		}

		$category = array_merge(
			array( 'name' => $category_name ),
			$category_properties
		);

		$this->registered_categories[ $category_name ] = $category;

		// If the category is registered inside an action other than `init`, store it
		// also to a dedicated array. Used to detect deprecated registrations inside
		// `admin_init` or `current_screen`.
		if ( current_action() && 'init' !== current_action() ) {
			$this->registered_categories_outside_init[ $category_name ] = $category;
		}

		return true;
	}

	/**
	 * Unregisters a pattern category.
	 *
	 * @since 5.5.0
	 *
	 * @param string $category_name Pattern category name including namespace.
	 * @return bool True if the pattern was unregistered with success and false otherwise.
	 */
	public function unregister( $category_name ) {
		if ( ! $this->is_registered( $category_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Block pattern name. */
				sprintf( __( 'Block pattern category "%s" not found.' ), $category_name ),
				'5.5.0'
			);
			return false;
		}

		unset( $this->registered_categories[ $category_name ] );
		unset( $this->registered_categories_outside_init[ $category_name ] );

		return true;
	}

	/**
	 * Retrieves an array containing the properties of a registered pattern category.
	 *
	 * @since 5.5.0
	 *
	 * @param string $category_name Pattern category name including namespace.
	 * @return array|null Registered pattern properties, or `null` if the pattern category is not registered.
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
	 * @param bool $outside_init_only Return only categories registered outside the `init` action.
	 * @return array[] Array of arrays containing the registered pattern categories properties.
	 */
	public function get_all_registered( $outside_init_only = false ) {
		return array_values(
			$outside_init_only
				? $this->registered_categories_outside_init
				: $this->registered_categories
		);
	}

	/**
	 * Checks if a pattern category is registered.
	 *
	 * @since 5.5.0
	 *
	 * @param string|null $category_name Pattern category name including namespace.
	 * @return bool True if the pattern category is registered, false otherwise.
	 */
	public function is_registered( $category_name ) {
		return isset( $category_name, $this->registered_categories[ $category_name ] );
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
 * @param string $category_name       Pattern category name including namespace.
 * @param array  $category_properties List of properties for the block pattern.
 *                                    See WP_Block_Pattern_Categories_Registry::register() for
 *                                    accepted arguments.
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
 * @param string $category_name Pattern category name including namespace.
 * @return bool True if the pattern category was unregistered with success and false otherwise.
 */
function unregister_block_pattern_category( $category_name ) {
	return WP_Block_Pattern_Categories_Registry::get_instance()->unregister( $category_name );
}
