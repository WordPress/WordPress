<?php
/**
 * Style Engine: WP_Style_Engine_CSS_Rules_Store class
 *
 * @package WordPress
 * @subpackage StyleEngine
 * @since 6.1.0
 */

/**
 * Core class used as a store for WP_Style_Engine_CSS_Rule objects.
 *
 * Holds, sanitizes, processes, and prints CSS declarations for the style engine.
 *
 * @since 6.1.0
 */
#[AllowDynamicProperties]
class WP_Style_Engine_CSS_Rules_Store {

	/**
	 * An array of named WP_Style_Engine_CSS_Rules_Store objects.
	 *
	 * @static
	 *
	 * @since 6.1.0
	 * @var WP_Style_Engine_CSS_Rules_Store[]
	 */
	protected static $stores = array();

	/**
	 * The store name.
	 *
	 * @since 6.1.0
	 * @var string
	 */
	protected $name = '';

	/**
	 * An array of CSS Rules objects assigned to the store.
	 *
	 * @since 6.1.0
	 * @var WP_Style_Engine_CSS_Rule[]
	 */
	protected $rules = array();

	/**
	 * Gets an instance of the store.
	 *
	 * @since 6.1.0
	 *
	 * @param string $store_name The name of the store.
	 * @return WP_Style_Engine_CSS_Rules_Store|void
	 */
	public static function get_store( $store_name = 'default' ) {
		if ( ! is_string( $store_name ) || empty( $store_name ) ) {
			return;
		}
		if ( ! isset( static::$stores[ $store_name ] ) ) {
			static::$stores[ $store_name ] = new static();
			// Set the store name.
			static::$stores[ $store_name ]->set_name( $store_name );
		}
		return static::$stores[ $store_name ];
	}

	/**
	 * Gets an array of all available stores.
	 *
	 * @since 6.1.0
	 *
	 * @return WP_Style_Engine_CSS_Rules_Store[]
	 */
	public static function get_stores() {
		return static::$stores;
	}

	/**
	 * Clears all stores from static::$stores.
	 *
	 * @since 6.1.0
	 */
	public static function remove_all_stores() {
		static::$stores = array();
	}

	/**
	 * Sets the store name.
	 *
	 * @since 6.1.0
	 *
	 * @param string $name The store name.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * Gets the store name.
	 *
	 * @since 6.1.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Gets an array of all rules.
	 *
	 * @since 6.1.0
	 *
	 * @return WP_Style_Engine_CSS_Rule[]
	 */
	public function get_all_rules() {
		return $this->rules;
	}

	/**
	 * Gets a WP_Style_Engine_CSS_Rule object by its selector.
	 * If the rule does not exist, it will be created.
	 *
	 * @since 6.1.0
	 *
	 * @param string $selector The CSS selector.
	 * @return WP_Style_Engine_CSS_Rule|void Returns a WP_Style_Engine_CSS_Rule object,
	 *                                       or void if the selector is empty.
	 */
	public function add_rule( $selector ) {
		$selector = trim( $selector );

		// Bail early if there is no selector.
		if ( empty( $selector ) ) {
			return;
		}

		// Create the rule if it doesn't exist.
		if ( empty( $this->rules[ $selector ] ) ) {
			$this->rules[ $selector ] = new WP_Style_Engine_CSS_Rule( $selector );
		}

		return $this->rules[ $selector ];
	}

	/**
	 * Removes a selector from the store.
	 *
	 * @since 6.1.0
	 *
	 * @param string $selector The CSS selector.
	 */
	public function remove_rule( $selector ) {
		unset( $this->rules[ $selector ] );
	}
}
